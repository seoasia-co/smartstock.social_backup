<?php

namespace App\Http\Controllers;

use \stdClass;
use App\Models\OpenAIGenerator;
use GuzzleHttp\Client;
use App\Models\Settings;
use App\Models\SettingTwo;
use App\Models\UserOpenai;
use App\Models\UserOpenaiChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI;
use OpenAI\Laravel\Facades\OpenAI as FacadesOpenAI;

use App\Models\UserMain;
use App\Http\Controllers\APIs\SMAISyncTokenController;

//use App\Http\Controllers\APIs\APIsController;

class AIController extends Controller
{
    public $client;
    public $settings;
    const STABLEDIFFUSION = 'stablediffusion';
    const STORAGE_S3 = 's3';
    const STORAGE_LOCAL = 'public';

    public function __construct()
    {
        //Settings
        $this->settings = Settings::first();
        $this->settings_two = SettingTwo::first();
        // Fetch the Site Settings object with openai_api_secret
        $apiKeys = explode(',', $this->settings->openai_api_secret);
        $apiKey = $apiKeys[array_rand($apiKeys)];
        config(['openai.api_key' => $apiKey]);
        //$this->client = FacadesOpenAI::client($this->settings->openai_api_secret);
        $this->settings->openai_default_model = 'gpt-3.5-turbo';
    }

    public function get_s3_size($file_path)
{
    return Storage::disk('s3')->size($file_path);
}

    public function buildOutput($request)
    {

        //$obj = json_decode($request);
        $obj = $request;

        $newObj = new stdClass();
        foreach ($obj as $key => $value) {
            $newObj->$key = $value;
        }

        if(!isset($request->post_type))
        $request= $newObj;

        //Log::debug('request inside function buildOutput '.$request);

        if(isset($request->user_id) && $request->user_id > 0)
        $user_id=$request->user_id;
        /* else if(isset($request['user_id']) && $request['user_id'] > 0)
        $user_id=$request['user_id']; */
        else
        $user_id=1;

        $user = UserMain::where('id',$user_id)->first();

        if ($request->post_type != 'ai_image_generator' && $user->remaining_words <= 0 && $user->remaining_words != -1) {
            return response()->json(['errors' => 'You have no remaining words. Please upgrade your plan.'], 400);
        }
        
        $image_generator = $request->image_generator;
        $post_type = $request->post_type;

        //SETTINGS
        $number_of_results = $request->number_of_results;
        $maximum_length = $request->maximum_length;
        $creativity = $request->creativity;
        $language = $request->language;
        $negative_prompt = $request->negative_prompt;
        $tone_of_voice = $request->tone_of_voice;

        //POST TITLE GENERATOR
        if ($post_type == 'post_title_generator') {
            $description = $request->description;
            $prompt = "Post title about $description in language $language .Generate $number_of_results post titles. Tone $tone_of_voice.";
        }

        //ARTICLE GENERATOR
        if ($post_type == 'article_generator') {
            $article_title = $request->article_title;
            $focus_keywords = $request->focus_keywords;
            $prompt = "Generate article about $article_title. Focus on $focus_keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice";
        }

        //SUMMARY GENERATOR SUMMARIZER SUMMARIZE TEXT
        if ($post_type == 'summarize_text') {
            $text_to_summary = $request->text_to_summary;
            $tone_of_voice = $request->tone_of_voice;

            $prompt = "Summarize the following text: $text_to_summary in $language using a tone of voice that is $tone_of_voice. The summary should be no longer than $maximum_length words and set the creativity to $creativity in terms of creativity. Generate $number_of_results different summaries.";
        }

        //PRODUCT DESCRIPTION
        if ($post_type == 'product_description') {
            $product_name = $request->product_name;
            $description = $request->description;

            $prompt = "Write product description for $product_name. The language is $language. Maximum length is $maximum_length. Creativity is $creativity between 0 to 1. see the following information as a starting point: $description. Generate $number_of_results different product descriptions. Tone $tone_of_voice.";
        }

        //PRODUCT NAME
        if ($post_type == 'product_name') {
            $seed_words = $request->seed_words;
            $product_description = $request->product_description;

            $prompt = "Generate product names that will appeal to customers who are interested in $seed_words. These products should be related to $product_description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different product names. Tone of voice must be $tone_of_voice";
        }

        //TESTIMONIAL REVIEW GENERATOR
        if ($post_type == 'testimonial_review') {
            $subject = $request->subject;
            $prompt = "Generate testimonial for $subject. Include details about how it helped you and what you like best about it. Be honest and specific, and feel free to get creative with your wording Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different testimonials. Tone of voice must be $tone_of_voice";
        }

        //PROBLEM AGITATE SOLUTION
        if ($post_type == 'problem_agitate_solution') {
            $description = $request->description;

            $prompt = "Write Problem-Agitate-Solution copy for the $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. problem-agitate-solution. Tone of voice must be $tone_of_voice Generate $number_of_results different Problem-Afitate-Solution.";
        }

        //BLOG SECTION
        if ($post_type == 'blog_section') {
            $description = $request->description;

            $prompt = " Write me blog section about $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog sections. Tone of voice must be $tone_of_voice";
        }

        //BLOG POST IDEAS
        if ($post_type == 'blog_post_ideas') {
            $description = $request->description;

            $prompt = "Write blog post article ideas about $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog post ideas. Tone of voice must be $tone_of_voice";
        }

        //BLOG INTROS
        if ($post_type == 'blog_intros') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write blog post intro about title: $title. And the description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog intros. Tone of voice must be $tone_of_voice";
        }

        //BLOG CONCLUSION
        if ($post_type == 'blog_conclusion') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write blog post conclusion about title: $title. And the description is $description.Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog conclusions. Tone of voice must be $tone_of_voice";
        }


        //FACEBOOK ADS
        if ($post_type == 'facebook_ads') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write facebook ads text about title: $title. And the description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different facebook ads text. Tone of voice must be $tone_of_voice";
        }

        //YOUTUBE VIDEO DESCRIPTION
        if ($post_type == 'youtube_video_description') {
            $title = $request->title;

            $prompt = "write youtube video description about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video descriptions. Tone of voice must be $tone_of_voice";
        }

        //YOUTUBE VIDEO TITLE
        if ($post_type == 'youtube_video_title') {
            $description = $request->description;

            $prompt = "Craft captivating, attention-grabbing video titles about $description for YouTube rankings. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video titles. Tone of voice must be $tone_of_voice";
        }

        //YOUTUBE VIDEO TAG
        if ($post_type == 'youtube_video_tag') {
            $title = $request->title;

            $prompt = "Generate tags and keywords about $title for youtube video. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video tags. Tone of voice must be $tone_of_voice";
        }

        //INSTAGRAM CAPTIONS
        if ($post_type == 'instagram_captions') {
            $title = $request->title;

            $prompt = "Write instagram post caption about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different instagram captions. Tone of voice must be $tone_of_voice";
        }

        //INSTAGRAM HASHTAG
        if ($post_type == 'instagram_hashtag') {
            $keywords = $request->keywords;

            $prompt = "Write instagram hastags for $keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different instagram hashtags. Tone of voice must be $tone_of_voice";
        }

        //SOCIAL MEDIA POST TWEET
        if ($post_type == 'social_media_post_tweet') {
            $title = $request->title;

            $prompt = "Write in 1st person tweet about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different tweets. Tone of voice must be $tone_of_voice";
        }

        //SOCIAL MEDIA POST BUSINESS
        if ($post_type == 'social_media_post_business') {
            $company_name = $request->company_name;
            $provide = $request->provide;
            $description = $request->description;

            $prompt = "Write in company social media post, company name: $company_name. About: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different social media posts. Tone of voice must be $tone_of_voice";
        }

        //FACEBOOK HEADLINES
        if ($post_type == 'facebook_headlines') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write Facebook ads title about title: $title. And description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different facebook ads title. Tone of voice must be $tone_of_voice";
        }

        //GOOGLE ADS HEADLINES
        if ($post_type == 'google_ads_headlines') {
            $product_name = $request->product_name;
            $description = $request->description;
            $audience = $request->audience;

            $prompt = "Write Google ads headline product name: $product_name. Description is $description. Audience is $audience. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different google ads headlines. Tone of voice must be $tone_of_voice";
        }

        //GOOGLE ADS DESCRIPTION
        if ($post_type == 'google_ads_description') {
            $product_name = $request->product_name;
            $description = $request->description;
            $audience = $request->audience;

            $prompt = "Write google ads description product name: $product_name. Description is $description. Audience is $audience. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different google ads description. Tone of voice must be $tone_of_voice";
        }

        //CONTENT REWRITE
        if ($post_type == 'content_rewrite') {
            $contents = $request->contents;

            $prompt = "Rewrite content:  '$contents'. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different rewrited content. Tone of voice must be $tone_of_voice";
        }

        //PARAGRAPH GENERATOR
        if ($post_type == 'paragraph_generator') {
            $description = $request->description;
            $keywords = $request->keywords;

            $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords. Maximum $maximum_length words. Creativity is $creativity Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice";
        }

        //Pros & Cons
        if ($post_type == 'pros_cons') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Generate pros & cons about title:  '$title'. Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different pros&cons. Tone of voice must be $tone_of_voice";
        }

        // META DESCRIPTION
        if ($post_type == 'meta_description') {
            $title = $request->title;
            $description = $request->description;
            $keywords = $request->keywords;

            $prompt = "Generate website meta description site name: $title. Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different meta descriptions. Tone of voice must be $tone_of_voice";
        }

        // FAQ Generator (All datas)
        if ($post_type == 'faq_generator') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Answer like faq about subject: $title Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different faqs. Tone of voice must be $tone_of_voice";
        }

        // Email Generator
        if ($post_type == 'email_generator') {
            $subject = $request->subject;
            $description = $request->description;

            $prompt = "Write email about title: $subject, description: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different emails. Tone of voice must be $tone_of_voice";
        }

        // Email Answer Generator
        if ($post_type == 'email_answer_generator') {
            $description = $request->description;

            $prompt = "answer this email content: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different email answers. Tone of voice must be $tone_of_voice";
        }

        // Newsletter Generator
        if ($post_type == 'newsletter_generator') {
            $description = $request->description;
            $subject = $request->subject;
            $title = $request->title;

            $prompt = "generate newsletter template about product_title: $title, reason: $subject description: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different newsletter template. Tone of voice must be $tone_of_voice";
        }

        // Grammar Correction
        if ($post_type == 'grammar_correction') {
            $description = $request->description;

            $prompt = "Correct this to standard $language. Text is '$description'. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different grammar correction. Tone of voice must be $tone_of_voice";
        }

        // TL;DR summarization
        if ($post_type ==  'tldr_summarization') {
            $description = $request->description;

            $prompt = "$description. Tl;dr Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different tl;dr. Tone of voice must be $tone_of_voice";
        }

        // Language Translation 
        if($post_type == 'language_translation')
        {

           // $language='Indonesian'; 
            $description = $request->description;
            $prompt = "Translate $description. from English to $language." ;

           // $prompt = "Translate the following English text to $language. Note: Preserve the HTML tags: $description";

            //Log::debug(' description inside function buildOutput '.$description);

            //Log::debug('Check post_type in start success '.$post_type);
        }


        if ($post_type == 'ai_image_generator') {
            $description = $request->description;
            $prompt = "$description";
            $size = $request->size;
            $style = $request->image_style;
            $lighting = $request->image_lighting;
            $mood = $request->image_mood;
            $number_of_images = (int)$request->image_number_of_images;
        }

        if ($post_type == 'ai_code_generator') {
            $description = $request->description;
            $code_language = $request->code_language;
            $prompt = "Write a code about $description, in $code_language";
        }

        $post = OpenAIGenerator::where('slug', $post_type)->first();

        if ($post->custom_template == 1) {
            $custom_template = OpenAIGenerator::where('id', $request->openai_id)->first();
            $prompt = $custom_template->prompt;
            foreach (json_decode($custom_template->questions) as $question) {
                $question_name = '**' . $question->name . '**';
                $prompt = str_replace($question_name, $request[$question->name], $prompt);
            }

            $prompt .= " in $language language. Number of results should be $number_of_results. And the maximum length of $maximum_length characters";
        }

        if ($post->type == 'text') {
            
            if($post_type == 'language_translation')
            return $this->textOutput($prompt, $post, $creativity, $maximum_length, $number_of_results, $user,$user_id,$post_type);
            else
            return $this->textOutput($prompt, $post, $creativity, $maximum_length, $number_of_results, $user,$user_id,$post_type);
        }

        if ($post->type == 'code') {
            return $this->codeOutput($prompt, $post, $user,$user_id);
        }

        if ($post->type == 'image') {
            return $this->imageOutput($prompt, $size, $post, $user, $style, $lighting, $mood, $number_of_images, $image_generator, $negative_prompt,$user_id,$request);
        }

        if ($post->type == 'audio') {
            $file = $request->file('file');
            return $this->audioOutput($file, $post, $user,$user_id);
        }
    }

    //SMAI Synced
    public function streamedTextOutput(Request $request)
    {
        $settings = $this->settings;
        $settings_two = $this->settings_two;
        $message_id = $request->message_id;
        $message = UserOpenai::whereId($message_id)->first();
        $prompt = $message->input;

        $creativity = $request->creativity;
        $maximum_length = $request->maximum_length;
        $number_of_results = $request->number_of_results;

        return response()->stream(function () use ($prompt, $message_id, $settings, $creativity, $maximum_length, $number_of_results) {

            try {
                if ($this->settings->openai_default_model == 'gpt-3.5-turbo' or $this->settings->openai_default_model == 'gpt-4') {
                    if ((int)$number_of_results > 1) {
                        $prompt = $prompt . ' number of results should be ' . (int)$number_of_results;
                    }
                    $stream = FacadesOpenAI::chat()->createStreamed([
                        'model' => $this->settings->openai_default_model,
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt]
                        ],
                    ]);
                    $send_smai=0;
                } else {
                 /*    $stream = FacadesOpenAI::completions()->createStreamed([
                        'model' => 'text-davinci-003',
                        'prompt' => $prompt,
                        'temperature' => (int)$creativity,
                        'max_tokens' => (int)$maximum_length,
                        'n' => (int)$number_of_results
                    ]);
 */
                    $stream = FacadesOpenAI::chat()->createStreamed([
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'You are a helpful assistant.'
                            ],
                            [
                                'role' => 'user',
                                'content' => $prompt,
                            ],
                         ],
                        'temperature' => (int)$creativity,
                        'max_tokens' => (int)$maximum_length,
                    ]);

                    // SMAI text-davinci-003  sync  token usage
                    $send_smai=1;
                    $smai_params_input=array(

                        'model' => 'text-davinci-003',
                        'prompt' => $prompt,
                        'temperature' => (int)$creativity,
                        'max_tokens' => (int)$maximum_length,
                        'n' => (int)$number_of_results

                    );
                     // SMAI text-davinci-003 sync  token usage


                }
            } catch (\Exception $exception) {
                $messageError = 'Error from API call. Please try again. If error persists again please contact system administrator with this message ' . $exception->getMessage();
                echo "data: $messageError";
                echo "\n\n";
                //ob_flush();
                //flush();
                // echo 'data: [DONE]';
                echo "\n\n";
               // ob_flush();
                //flush();
                usleep(50000);
            }

            $total_used_tokens = 0;
            $output = "";
            $responsedText = "";

            foreach ($stream as $response) {
                if ($settings->openai_default_model == 'gpt-3.5-turbo') {
                    if (isset($response['choices'][0]['delta']['content'])) {
                        $message = $response['choices'][0]['delta']['content'];
                        $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                        $output .= $messageFix;
                        $responsedText .= $message;
                        $total_used_tokens += Str::of($messageFix)->wordCount();

                        $string_length = Str::length($messageFix);
                        $needChars = 6000 - $string_length;
                        $random_text = Str::random($needChars);


                        // echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                       // ob_flush();
                       // flush();
                        //usleep(500);
                    }
                } else {
                    if (isset($response->choices[0]->text)) {
                        $message = $response->choices[0]->text;
                        $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                        $output .= $messageFix;
                        $responsedText .= $message;
                        $total_used_tokens += Str::of($messageFix)->wordCount();

                        $string_length = Str::length($messageFix);
                        $needChars = 6000 - $string_length;
                        $random_text = Str::random($needChars);


                        // SMAI text-davinci-003 start sync  token usage
                        if( $send_smai==1)
                         {
                            $smai_params_input['platform'] = 'main_coin';
                            $smai_params_input['gpt_category'] = 'DocText_SmartContentCoIn';

                            $user_id=$user->id;
                            $usage= $total_used_tokens;

                            $data_api=  json_encode($response);
                            //  SMAITokenSyncController::synctoken_digitalasset($user_id,$usage,$data_api,$smai_params_input);

                             

                         }
                            //eof SMAI text-davinci-003 start sync  token usage






                        // echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                       // ob_flush();
                       // flush();
                        //usleep(500);
                    }
                }

                if (connection_aborted()) {
                    break;
                }
            }

            /* Long term streaming
            while ($maximum_length - $total_used_tokens >0){
                try{
                    $stream = FacadesOpenAI::chat()->createStreamed([
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt],
                            ['role' => 'assistant', 'content' => $responsedText],
                            ['role' => 'user', 'content' => 'Please Continue'],
                        ],
                    ]);

                } catch (\Exception $exception){
                    $messageError = 'Error from API call. Please try again. If error persists again please contact system administrator with this message '.$exception->getMessage();
                    echo "data: $messageError";
                    echo "\n\n";
                    ob_flush();
                    flush();
                    echo 'data: [DONE]';
                    echo "\n\n";
                    ob_flush();
                    flush();
                    usleep(50000);
                }


            foreach ($stream as $response){

                    if (isset($response['choices'][0]['delta']['content'])) {
                        $message = $response['choices'][0]['delta']['content'];
                        $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                        $output .= $messageFix;
                        $responsedText .= $message;
                        $total_used_tokens += Str::of($messageFix)->wordCount();

                        $string_length = Str::length($messageFix);
                        $needChars = 6000-$string_length;
                        $random_text = Str::random($needChars);


                        echo 'data: ' . $messageFix .'/**'.$random_text. "\n\n";
                        ob_flush();
                        flush();
                        usleep(500);
                    }

                if (connection_aborted()) {
                    break;
                }
            }

            }
            */

            $message = UserOpenai::whereId($message_id)->first();
            $message->response = $responsedText;
            $message->output = $output;
            $message->hash = Str::random(256);
            $message->credits = $total_used_tokens;
            $message->words = 0;
            $message->save();

            $user = UserMain::where('id',1)->first();
            if ($user->remaining_words != -1) {
                $user->remaining_words -= $total_used_tokens;
                $user->save();
            }

            if ($user->remaining_words < -1) {
                $user->remaining_words = 0;
                $user->save();
            }

            // echo 'data: [DONE]';
            echo "\n\n";
            //ob_flush();
            //flush();
            usleep(50000);
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    public function textOutput2($prompt, $post, $creativity, $maximum_length, $number_of_results, $user,$user_id=NULL)
    {
        if($user_id ==NULL)
        $user_id=1;

        //$user = Auth::user();
        $user = UserMain::where('id',$user_id)->first();
        if ($user->remaining_words <= 0  and $user->remaining_words != -1) {
            $data = array(
                'errors' => ['You have no credits left. Please consider upgrading your plan.'],
            );
            return response()->json($data, 419);
        }
        $entry = new UserOpenai();
        $entry->title = 'New Workbook';
        $entry->slug = str()->random(7) . str($user->fullName())->slug() . '-workbook';
        $entry->user_id = Auth::id();
        $entry->openai_id = $post->id;
        $entry->input = $prompt;
        $entry->response = null;
        $entry->output = null;
        $entry->hash = str()->random(256);
        $entry->credits = 0;
        $entry->words = 0;
        $entry->save();

        $message_id = $entry->id;
        $workbook = $entry;
        $inputPrompt = $prompt;
        //$html = view('panel.user.openai.documents_workbook_textarea', compact('workbook'))->render();
        //return response()->json(compact('message_id', 'html', 'creativity', 'maximum_length', 'number_of_results', 'inputPrompt'));

       
    }

    public function textOutput($prompt, $post, $creativity, $maximum_length, $number_of_results, $user,$user_id=NULL,$post_type=NULL)
    {
        if($user_id ==NULL)
        $user_id=1;
        
        

        //Log::debug('Prompt inside function textOutput '.$prompt);

        $user = UserMain::where('id',$user_id)->first();
        if ($user->remaining_words <= 0  and $user->remaining_words != -1) {
            $data = array(
                'errors' => ['You have no credits left. Please consider upgrading your plan.'],
            );
            return response()->json($data, 419);
        }

      /*   $stream = FacadesOpenAI::completions()->createStreamed([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'temperature' => (int)$creativity,
            'max_tokens' => (int)$maximum_length,
            'n' => (int)$number_of_results
        ]); */

        $stream = FacadesOpenAI::chat()->createStreamed([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
             ],
            'temperature' => (int)$creativity,
            'max_tokens' => (int)$maximum_length,
        ]);

        //Log::debug('Check Stream  ' );
        //Log::info($stream);

        $total_used_tokens = 0;
        $output = "";
        $responsedText = "";

        foreach ($stream as $response) {

            //Log::debug('Response from chatGPT All case  ');

            //Log::info(print_r($response, true));

            if ($this->settings->openai_default_model == 'gpt-3.5-turbo') {
               
                if (isset($response['choices'][0]['delta']['content'])) {

                    
                    $message = $response['choices'][0]['delta']['content'];
                    //Log::debug('Response from chatGPT case Delta '.$message);

                    $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                    $output .= $messageFix;
                    $responsedText .= $message;
                    $total_used_tokens += Str::of($messageFix)->wordCount();

                    $string_length = Str::length($messageFix);
                    $needChars = 6000 - $string_length;
                    $random_text = Str::random($needChars);


                    // echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                    if (ob_get_level() > 0) {ob_flush();}
                    flush();
                    usleep(500);
                }


                if (isset($response->choices[0]->text)) {
                    $message = $response->choices[0]->text;

                    //Log::debug('Response from chatGPT case choices[0]->text '.$message);

                    $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                    $output .= $messageFix;
                    $responsedText .= $message;
                    $total_used_tokens += Str::of($messageFix)->wordCount(); 

                    $string_length = Str::length($messageFix);
                    $needChars = 6000 - $string_length;
                    $random_text = Str::random($needChars);

                    $send_smai=0;
                    // SMAI text-davinci-003 start sync  token usage
                    if( $send_smai==1)
                     {
                        $smai_params_input['platform'] = 'main_coin';
                        $smai_params_input['gpt_category'] = 'DocText_SmartContentCoIn';

                        $user_id=$user->id;
                        $usage= $total_used_tokens;

                        $data_api=  json_encode($response);
                        //  SMAITokenSyncController::synctoken_digitalasset($user_id,$usage,$data_api,$smai_params_input);    

                     }
                        //eof SMAI text-davinci-003 start sync  token usage


                    // echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                    if (ob_get_level() > 0) {ob_flush();}
                    flush();
                    usleep(500);
                }

               





            } else {
                if (isset($response->choices[0]->text)) {
                    $message = $response->choices[0]->text;

                    //Log::debug('Response from chatGPT case choices[0]->text '.$message);

                    $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                    $output .= $messageFix;
                    $responsedText .= $message;
                    $total_used_tokens += Str::of($messageFix)->wordCount(); 

                    $string_length = Str::length($messageFix);
                    $needChars = 6000 - $string_length;
                    $random_text = Str::random($needChars);
                   
                    $send_smai=0;

                    // SMAI text-davinci-003 start sync  token usage
                    if( $send_smai==1)
                     {
                        $smai_params_input['platform'] = 'main_coin';
                        $smai_params_input['gpt_category'] = 'DocText_SmartContentCoIn';

                        $user_id=$user->id;
                        $usage= $total_used_tokens;

                        $data_api=  json_encode($response);
                        //  SMAITokenSyncController::synctoken_digitalasset($user_id,$usage,$data_api,$smai_params_input);    

                     }
                        //eof SMAI text-davinci-003 start sync  token usage


                    // echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                    if (ob_get_level() > 0) {ob_flush();}
                   flush();
                    usleep(500);
                }
            }

            if (connection_aborted()) {
                break;
            }
        }



       
                  
                  
        //Log::debug('Befors save entry response '.$responsedText);
        //Log::debug('Befors save entry output '.$output);



        $entry = new UserOpenai();
        $entry->title = 'New Workbook';
        $entry->slug = str()->random(7) . str($user->fullName())->slug() . '-workbook';
        $entry->user_id = $user->id;
        $entry->openai_id = $post->id;
        $entry->input = $prompt;
        $entry->response = $responsedText;
        $entry->output = $output;
        $entry->hash = str()->random(256);
        $entry->credits = $total_used_tokens;
        $entry->words = 0;
        $entry->save();

        $message_id = $entry->id;
        $workbook = $entry;
        $inputPrompt = $prompt;
        //$html = view('panel.user.openai.documents_workbook_textarea', compact('workbook'))->render();
        

            // SMAI text-davinci-003 start sync  token usage

            $send_smai=0;
            $smai_params_input=array(

                        'model' => 'text-davinci-003',
                        'prompt' => $prompt,
                        'temperature' => (int)$creativity,
                        'max_tokens' => (int)$maximum_length,
                        'n' => (int)$number_of_results,
                        'main_useropenai_message_id' => $message_id,

            );
            if( $send_smai==1)
            {
                $smai_params_input['platform'] = 'main_coin';
                $smai_params_input['gpt_category'] = 'DocText_SmartContentCoIn';

                $user_id=$user->id;
                $usage= 0;

                $data_api=  null;
            //  SMAITokenSyncController::synctoken_digitalasset($user_id,$usage,$data_api,$smai_params_input);    

            }
            //eof SMAI text-davinci-003 start sync  token usage

           if($post_type == 'post_title_generator' || $post_type == 'article_generator' )
           $return_html=$output;
           else
           $return_html=$responsedText;

        $return_array=array(
            'message_id' => $message_id,
            'html' => $return_html,
            'creativity' => $creativity,
            'maximum_length' => $maximum_length,
            'number_of_results' => $number_of_results,
            'inputPrompt' => $inputPrompt,
        );
        return $return_array;

        //return response()->json(compact('message_id', 'html', 'creativity', 'maximum_length', 'number_of_results', 'inputPrompt'));
    }

    //SMAI Synced
    public function codeOutput($prompt, $post, $user,$user_id=NULL)
    {
        if ($this->settings->openai_default_model == 'text-davinci-003') {
            $response = FacadesOpenAI::completions()->create([
                'model' => $this->settings->openai_default_model,
                'prompt' => $prompt,
                'max_tokens' => (int)$this->settings->openai_max_output_length,
            ]);
        } else {
            $response = FacadesOpenAI::chat()->create([
                'model' => $this->settings->openai_default_model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);
        }
        $total_used_tokens = $response->usage->totalTokens;

        $entry = new UserOpenai();
        $entry->title = 'New Workbook';
        $entry->slug = Str::random(7) . Str::slug($user->fullName()) . '-workbook';
        $entry->user_id = $user->id;
        $entry->openai_id = $post->id;
        $entry->input = $prompt;
        $entry->response = json_encode($response->toArray());
        
        if ($this->settings->openai_default_model == 'text-davinci-003') {
            $entry->output = $response['choices'][0]['text'];
            $total_used_tokens =  Str::of($entry->output)->wordCount(); 
        } else {
            $entry->output = $response->choices[0]->message->content;
            $total_used_tokens = Str::of($entry->output)->wordCount(); 
        }
        
        $entry->hash = Str::random(256);
        $entry->credits = $total_used_tokens;
        $entry->words = 0;
        $entry->save();

        $user = UserMain::where('id',1)->first();
        if ($user->remaining_words != -1) {
            $user->remaining_words -= $total_used_tokens;
        }

        if ($user->remaining_words < -1) {
            $user->remaining_words = 0;
        }
        $user->save();

        // SMAI CodeGenerator start sync  token usage
        $smai_params_input = json_encode(array( 
                            
            'model' => $this->settings->openai_default_model,
            'prompt' => $prompt,
            'platform' => 'main_coin',
            'gpt_category' => 'CodeGenerator_SmartContentCoIn',
            
        ));

        $user_id=$user->id;
        $usage= $total_used_tokens;
        
        $data_api=  json_encode($response);
        //  SMAITokenSyncController::synctoken_digitalasset($user_id,$usage,$data_api,$smai_params_input);
        
         
        //eof SMAI CodeGenerator start sync  token usage

        return $this->finalizeOutput($post, $entry);
    }

    //SMAI Synced
    public function imageOutput($prompt, $size, $post, $user, $style, $lighting, $mood, $number_of_images, $image_generator, $negative_prompt,$user_id=NULL,$request=NULL)
    {
        //save generated image datas
        $entries=[];

        if ($user->remaining_images <= 0) {
            $data = array(
                'errors' => ['You have no credits left. Please consider upgrading your plan.'],
            );
            return response()->json($data, 419);
        }

        if ($style != null)
            $prompt .= ' ' . $style . ' style.';
        if ($lighting != null)
            $prompt .= ' ' . $lighting . ' lighting.';
        if ($mood != null)
            $prompt .= ' ' . $mood . ' mood.';

        $image_storage = $this->settings_two->ai_image_storage;

        for ($i = 0; $i < $number_of_images; $i++) {
            if($image_generator != self::STABLEDIFFUSION) {
                //send prompt to openai
                if($prompt == null) return response()->json(["status" => "error", "message" => "You must provide a prompt"]);
                /* $response = FacadesOpenAI::images()->create([
                    'model' => 'image-alpha-001',
                    'prompt' => $prompt,
                    'size' => $size,
                    'response_format' => 'b64_json',
                ]); */
                $response = FacadesOpenAI::images()->create([
                    'prompt' => $prompt,
                    'size' => $size,
                    'response_format' => 'b64_json',
                ]);
               
                $image_url = $response['data'][0]['b64_json'];
                $contents = base64_decode($image_url);
                $nameOfImage = Str::random(12) . '-DALL-E-' . Str::slug($prompt) . '.png';

                //save file on local storage or aws s3
                Storage::disk('topics')->put($nameOfImage, $contents);
               
                //Storage::disk('topics')->put($nameOfImage, file_get_contents($contents));
                $path = 'https://syncapi.smartcontentcrm.com/uploads/topics/' . $nameOfImage;
                //$path = 'uploads/' . $nameOfImage; 

                //SMAI sync
                $data_api=  json_encode($response);
               // $file_size = Storage::size('https://smartcontent.co.in/'.$path);
               

            } else {
                //send prompt to stablediffusion
                $settings = SettingTwo::first();
                $stablediffusionKeys = explode(',', $settings->stable_diffusion_api_key);
                $stablediffusionKey = $stablediffusionKeys[array_rand($stablediffusionKeys)];
                if($prompt == null) 
                    return response()->json(["status" => "error", "message" => "You must provide a prompt"]);
                if ($stablediffusionKey == "") 
                    return response()->json(["status" => "error", "message" => "You must provide a StableDiffusion API Key."]);
                $width = explode('x', $size)[0];
                $height = explode('x', $size)[1];
                $client = new Client([
                    'base_uri' => 'https://api.stability.ai/v1/generation/',
                    'headers' => [
                        'content-type' => 'application/json',
                        'Authorization' => "Bearer ".$stablediffusionKey
                    ]
                ]);
                try {
                    $response = $client->post('stable-diffusion-512-v2-1/text-to-image', [
                        // 'json' => [
                        //     'content-type' => 'application/json',
                        //     'cfg_scale' => 7,
                        //     'clip_guidance_preset' => "FAST_BLUE",
                        //     'width' => $width,
                        //     'height' => $height,
                        //     'sampler' => "K_DPM_2_ANCESTRAL",
                        //     'samples' => "1",
                        //     'steps' => "50",
                        //     'text_prompts' => ['text' => $prompt]
                        // ],
                        'json' => [
                            'cfg_scale' => 7,
                            'clip_guidance_preset' => 'FAST_BLUE',
                            'width' => 512,
                            'height' => 512,
                            'sampler' => 'K_DPM_2_ANCESTRAL',
                            'samples' => 1,
                            'steps' => 50,
                            'text_prompts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ],
                    ]);
                } catch (\Exception $e) {
                    return response()->json(["status" => "error", "message" => $e->getMessage()]);
                }

                
                $body = $response->getBody();
                if ($response->getStatusCode() == 200){
                    $nameOfImage = Str::random(12) . '-' . Str::slug($prompt) . '.png';
                    
                    $contents = base64_decode(json_decode($body)->artifacts[0]->base64);
                }
                else {
                    $message = '';
                    if ($body->status == "error")
                        $message = $body->message;
                    else
                        $message = "Failed, Try Again";
                    return response()->json(["status" => "error", "message" => $message]);
                }
                
                Storage::disk('public')->put($nameOfImage, $contents);
                $path = 'uploads/' . $nameOfImage;

                //SMAI sync
                $data_api=  json_encode($response);
                //$file_size = Storage::size('https://smartcontent.co.in/'.$path);
            }


             //SMAI start sync  token usage
                $file_size=0;
                //$user_id=$user->id;
                if($user_id ==NULL)
                $user_id=1;

                $usage=1;
                $params =  json_encode(array( 
                            'prompt' => $prompt,
                           
                            'size' => $size,
                            'response_format' => 'b64_json',
                            'platform' => 'main_coin',
                            'gpt_category' => 'Images_SmartContentCoIn',
                            'image_generator' => 'DE',
                            'contents' => $path,
                            'nameOfImage' => $nameOfImage,
                            'n' => 1,
                            'file_size' => $file_size,
                        ));
                if($image_generator == "stablediffusion")
                { 
                    $params['model']='stablediffusion';
                    $params['image_generator']='stablediffusion';
                }
    
             $data_api='';
             //  SMAITokenSyncController::synctoken_digitalasset($user_id,$usage,$data_api,$params);

             // $user_id,$usage,$data_image,$image_params
             $chatGPT_catgory='Images_SmartContentCoIn';
             $data_req= json_encode($request);
             $new_update_main_image = new SMAISyncTokenController($data_req, $usage, $chatGPT_catgory, $chat_id=NULL,NULL,NULL,$params,$user_id);
             $return_arr = $new_update_main_image->imageOutput_save_main_coin($user_id, $usage, $data_req, $params,$size=NULL, $post=NULL,  $style=NULL, $lighting=NULL, $mood=NULL, $number_of_images=1, $image_generator='DE', $negative_prompt=NULL,NULL);
 
             Log::debug('Return array from new_update_main_image ');
             Log::info($return_arr);
 
             $path_array =$return_arr['path_array'];
             $image_array =$return_arr['image_array'];

             Log::debug('path_array from new_update_main_image ');
                Log::info($path_array);
                Log::debug('image_array from new_update_main_image ');
                Log::info($image_array);
 
             //save image to BIo OpenAI
             $number_of_images=1;
             $prompt=$prompt;

             $image_array=json_decode(json_encode($image_array), true);
             $main_image_id=$image_array['main_image_id'];
             
             //$image_array['img_width']
             //$image_array['img_height']

             //$image_array=json_encode($image_array);
             //$image_array['size']=$size;

             $new_update_main_image->imageOutput_save_Bio($user_id,$prompt, $number_of_images,$path_array,$image_array,$main_image_id);
            

             //save image to SocialPost OpenAI
             $new_update_main_image->imageOutput_save_SocialPost($user_id,$prompt, $number_of_images,$path_array,$image_array,$main_image_id);

 
             //save image to Design OpenAI
             $new_update_main_image->imageOutput_save_Design($user_id,$prompt, $number_of_images,$path_array,$main_image_id);
 
             //save image to Sync OpenAI
             $new_update_main_image->imageOutput_save_Sync($user_id,$prompt, $number_of_images,$path_array,$main_image_id);
 
             //save image to MobielApp OpenAI
             $new_update_main_image->imageOutput_save_MobileAppV2($user_id,$prompt, $number_of_images,$path_array,$main_image_id);
 
        
        
        }


       return $path_array;
       // return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
    
    }

    //SMAI Synced
    public function audioOutput($file, $post, $user,$user_id=NULL)
    {

        $path = 'upload/audio/';

        $file_name = Str::random(4) . '-' . Str::slug($user->fullName()) . '-audio.' . $file->getClientOriginalExtension();

        //Audio Extension Control
        $imageTypes = ['mp3', 'mp4', 'mpeg', 'mpga', 'm4a', 'wav', 'webm'];
        if (!in_array(Str::lower($file->getClientOriginalExtension()), $imageTypes)) {
            $data = array(
                'errors' => ['Invalid extension, accepted extensions are mp3, mp4, mpeg, mpga, m4a, wav, and webm.'],
            );
            return response()->json($data, 419);
        }

        $file->move($path, $file_name);

        $response = FacadesOpenAI::audio()->transcribe([
            'file' => fopen($path . $file_name, 'r'),
            'model' => 'whisper-1',
            'response_format' => 'verbose_json',
        ]);

        $text  = $response->text;

        $entry = new UserOpenai();
        $entry->title = 'New Workbook';
        $entry->slug = Str::random(7) . Str::slug($user->fullName()) . '-speech-to-text-workbook';
        $entry->user_id = $user->id;
        $entry->openai_id = $post->id;
        $entry->input = $path . $file_name;
        $entry->response = json_encode($response->toArray());
        $entry->output = $text;
        $entry->hash = Str::random(256);
        $entry->credits = countWords($text);
        $entry->words = countWords($text);
        $entry->save();

        if ($user->remaining_words != -1 and $user->remaining_words - $entry->credits < -1) {
            $user->remaining_words = 0;
        }


        if ($user->remaining_words != -1 and $user->remaining_words - $entry->credits > 0) {
            $user->remaining_words -= $entry->credits;
        }

        if ($user->remaining_words < -1) {
            $user->remaining_words = 0;
        }

        $user->save();

        //Workbook add-on
        $workbook = $entry;

        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
        $openai = OpenAIGenerator::where('id', $post->id)->first();
       // $html2 = view('panel.user.openai.generator_components.generator_sidebar_table', compact('userOpenai', 'openai'))->render();
        
         // SMAI whisper-1 start sync  token usage
         $smai_params_input = json_encode(array( 
                            
            'model' => 'whisper-1',
            'prompt' => $path . $file_name,
            'platform' => 'main_coin',
            'gpt_category' => 'Whisper_SmartContentCoIn',
            
        ));

        $user_id=$user->id;
        $usage= countWords($text);
        
        $data_api=  json_encode($response);
        //  SMAITokenSyncController::synctoken_digitalasset($user_id,$usage,$data_api,$smai_params_input);
        
         
        //eof SMAI whisper-1 start sync  token usage
        
        
        
        return response()->json(compact('html2'));
    }

    public function finalizeOutput($post, $entry)
    {
        //Workbook add-on
        $workbook = $entry;

        //$html = view('panel.user.openai.documents_workbook_textarea', compact('workbook'))->render();
        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
        $openai = OpenAIGenerator::where('id', $post->id)->first();
       // $html2 = view('panel.user.openai.generator_components.generator_sidebar_table', compact('userOpenai', 'openai'))->render();
        return response()->json(compact('html', 'html2'));
    }

    public function lowGenerateSave(Request $request)
    {
        $response = $request->response;
        $total_user_tokens = countWords($response);
        $entry = UserOpenai::where('id', $request->message_id)->first();

        $entry->credits = $total_user_tokens;
        $entry->words = $total_user_tokens;
        $entry->response = $response;
        $entry->output = $response;
        $entry->save();


        $user = UserMain::where('id',1)->first();

        if ($user->remaining_words != -1) {
            $user->remaining_words -= $total_user_tokens;
        }

        if ($user->remaining_words < -1) {
            $user->remaining_words = 0;
        }
        $user->save();

          // SMAI text-davinci-003 start sync  token usage

          $send_smai=0;
          $smai_params_input=array(

              'model' => 'text-davinci-003',
             /*  'prompt' => $prompt, */

                );
                if( $send_smai==1)
                {
                    $smai_params_input['platform'] = 'main_coin';
                    $smai_params_input['gpt_category'] = 'DocText_SmartContentCoIn';
                    $smai_params_input['response'] = $response;

                    $user_id=$user->id;
                    $usage= $total_user_tokens;
                    $main_message_id=$entry->id;
                    //$message_id = $entry->id;

                    $data_api=  json_encode($request);
                    // SMAITokenSyncController::synctoken_from_maincoin_update($user_id,$usage,$data_api,$smai_params_input,$main_message_id);

                     

                }
                    //eof SMAI text-davinci-003 start sync  token usage




    }

    public function stream(Request $request)
    {
        $openaiKey = Settings::first()?->openai_api_secret;
        $openai_model = Settings::first()?->openai_default_model;

        $client = OpenAI::factory()
            ->withApiKey($openaiKey)
            // ->withHttpClient(new \GuzzleHttp\Client())
            ->make();

        session_start();
        header("Content-type: text/event-stream");
        header("Cache-Control: no-cache");
        ob_end_flush();
        $result = $client->chat()->createStreamed([
            'model' => $openai_model,
            'messages' => [[
                'role' => 'user',
                'content' => $request->get('message'),
            ]],
            'stream' => true,
        ]);

        foreach ($result as $response) {
            echo "event: data\n";
            echo "data: " . json_encode(['message' => $response->choices[0]->delta->content]) . "\n\n";
            flush();
        }

        echo "event: stop\n";
        echo "data: stopped\n\n";
    }

    public function chatStream(Request $request)
    {
        $openaiKey = Settings::first()?->openai_api_secret;
        $openai_model = Settings::first()?->openai_default_model;

        $client = OpenAI::factory()
            ->withApiKey($openaiKey)
            ->withHttpClient(new \GuzzleHttp\Client())
            ->make();

        session_start();
        header("Content-type: text/event-stream");
        header("Cache-Control: no-cache");
        ob_end_flush();


        //Add previous chat history to the prompt
        $prompt = $request->get('message');
        $categoryId = $request->get('category');
        $list = UserOpenaiChat::where('user_id', Auth::id())->where('openai_chat_category_id', $categoryId)->orderBy('updated_at', 'desc');
        
        $list = $list->get();
        $chat = $list->first();
        $category = $chat->category;
        $prefix = "";
        if($category->prompt_prefix != null)
            $prefix = "$category->prompt_prefix you will now play a character and respond as that character (You will never break character). Your name is $category->human_name but do not introduce by yourself as well as greetings.";

        $messages = [
            array (
                "role" => "assistant",
                "content" => $prefix
            )
        ];
        
        $lastThreeMessage = null;
        if ($chat != null) {
            $lastThreeMessageQuery = $chat->messages()->whereNot('input', null)->orderBy('created_at', 'desc')->take(2);
            $lastThreeMessage = $lastThreeMessageQuery->get()->reverse();
        }

        foreach($lastThreeMessage as $entry) {
            array_push($messages, array (
                "role" => "user",
                "content" => $entry->input
            ));
            array_push($messages, array (
                "role" => "assistant",
                "content" => $entry->output
            ));
        }
        array_push($messages, array (
            "role" => "user",
            "content" => $prompt
        ));

        //send request to openai
        $result = $client->chat()->createStreamed([
            'model' => $openai_model,
            'messages' => $messages,
            'stream' => true,
        ]);

        foreach ($result as $response) {
            echo "event: data\n";
            echo "data: " . json_encode(['message' => $response->choices[0]->delta->content]) . "\n\n";
            flush();
        }

        echo "event: stop\n";
        echo "data: stopped\n\n";
    }

    public function lazyLoadImage(Request $request)
    {
        $offset = $request->get('offset', 0);
        // var_dump($offset)
        $post_type = $request->get('post_type');
        $post = OpenAIGenerator::where('slug', $post_type)->first();

        $limit = 5;
        $images = UserOpenai::where('user_id', Auth::id())->where('openai_id', $post->id)->orderBy('created_at', 'desc')->skip($offset)->take($limit)->get();
        // $images = Image::skip($offset)->take($limit)->get();

        return response()->json([
            'images' => $images,
            'hasMore' => $images->count() === $limit,
        ]);
    }
}