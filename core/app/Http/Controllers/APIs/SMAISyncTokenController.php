<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Mail\NotificationEmail;
use App\Models\AttachFile;
use App\Models\Banner;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Language;
use App\Models\Map;
use App\Models\Menu;
use App\Models\Photo;
use App\Models\Section;

use App\Models\Topic;
use App\Models\TopicCategory;
use App\Models\Webmail;
use App\Models\WebmasterSection;
use App\Models\WebmasterSetting;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Mail;

use App\Models\Settings;
use App\Models\SettingTwo;

use App\Models\OpenAIGenerator;
use GuzzleHttp\Client;


use App\Models\SP_UserOpenai;
use App\Models\DigitalAsset_UserOpenai;
use App\Models\Mobile_UserOpenai;
use App\Models\UserOpenai;

use App\Models\UserDesign;
use App\Models\UserMain;
use App\Models\UserSP;


use App\Models\SP_UserCaption;


use App\Models\UserOpenaiChat;
use App\Models\UserOpenaiChatMessage;
use App\Models\UserOpenaiChatDesign;
use App\Models\UserOpenaiChatMessageDesign;
use App\Models\UserOpenaiChatMainMarketing;
use App\Models\UserOpenaiChatMessageMainMarketing;
use App\Models\UserOpenaiChatMobile;
use App\Models\UserOpenaiChatMessageMobile;
use App\Models\UserOpenaiChatSocialPost;
use App\Models\UserOpenaiChatMessageSocialPost;

use App\Models\OpenaiGeneratorChatCategory;



use Illuminate\Support\Facades\Auth;
use Illuminate\Http\File;

//use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI;
use OpenAI\Laravel\Facades\OpenAI as FacadesOpenAI;

use Log;
use DB;
use Illuminate\Support\Arr;

/* for update Token and chatGTP usage including Log history of chat GPT data */

class SMAISyncTokenController extends Controller
{
    protected $client;
    protected $settings;
    protected $postContent;
    protected $total_used_tokens;
    protected $GPTModel;
    const STABLEDIFFUSION = 'stablediffusion';
    const STORAGE_S3 = 's3';
    const STORAGE_LOCAL = 'public';

    public function __construct($response = NULL,$usage = NULL, $chatGPT_catgory = NULL)
    {
        //Settings
        $this->settings = Settings::first();
        $this->settings_two = SettingTwo::first();


        


        if (isset($response)) {
            $json_array = json_decode($response, true);
            Log::info($json_array);

            if (isset($json_array['model']))
                $this->GPTModel = $json_array['model'];
            /* 'model' => 'gpt-4-0613' */

            if (isset($json_array['choices']))
                $choices = $json_array['choices'];

            if (isset($choices[0]["text"]))
                $this->postContent = $choices[0]["text"];

            if (isset($choices["text"]))
                $this->postContent = $choices["text"];


            //case send from Smart Bio ||   model = gpt-4-0613
            if (!isset($this->postContent)) {
                if (isset($choices[0]["message"]["content"]))
                    $this->postContent = $choices[0]["message"]["content"];

                if (isset($choices["message"]["content"]))
                    $this->postContent = $choices["message"]["content"];


            }


            if (isset($json_array['usage']))
            {
                if(isset($json_array['usage']['total_tokens']))
                {
                $this->total_used_tokens = $json_array['usage']['total_tokens'];
                Log::debug('Found Total_token : '.$this->total_used_tokens);
                }
                else{
                    $this->total_used_tokens=0;
                }

            }
            if (isset($usage))
            {
                $this->total_used_tokens = $usage;
                Log::debug('Found Total_token from Main usage : '.$this->total_used_tokens);
            }


            //$response['choices'][0]['delta']['content']





        }


    }
    public function SMAI_UpdateGPT_MainMarketing($user_id, $usage, $response, $params)
    {

        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (isset($user_id)) {
            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_MainMarketing from SMAIsyncController : ' . $user_id);
     
      //$params_json1 = json_decode($params, true);
      $params_json1 = $params;
      $chatGPT_catgory=$params_json1['gpt_category'];
      $response= json_decode($response, true);
       if( Str::contains($chatGPT_catgory, 'Chat_'))
         {
            //add Sync GPT chat version here

                $total_used_tokens=$usage;
                $chat_id = $params_json1['chat_id'];

                $chat_new_ins = UserOpenaiChatMainMarketing::updateOrCreate(
                    ['chat_id' => $chat_id],
                    ['user_id' => $user_id,'openai_chat_category_id' => 1,]
                );

                //$user_openai_chat_id =UserOpenaiChat::where('chat_id',$chat_id);
                $user_openai_chat_id =DB::connection('main_db')->table('conversation_list')->where('chat_id', $chat_id)->first();
                
         
                $time = time();
                $time = intval($time);



                if(isset($user_openai_chat_id->id))
                {
                $user_openai_chat_id_ins=$user_openai_chat_id->id ;
                //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id, 'user_openai_chat_id_ins' => $user_openai_chat_id_ins,'updated_at' => $time]);    
                
                $data_message=array(
                    'user_id' => $user_id,
                    'chat_id' => $chat_id, 
                    'user_openai_chat_id' => $user_openai_chat_id_ins,
                    'conversation_list_id' => $user_openai_chat_id_ins,
                    'updated_at' => $time,
                );
                $message_new_ins=DB::connection('main_db')->table('conversation_details')->insertGetId($data_message);
            
            }
                else
                {

                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id,'updated_at' => $time ]);
                    $data_message=array(
                        'user_id' => $user_id,
                        'chat_id' => $chat_id, 
                    
                        'updated_at' => $time,
                    );
                     $message_new_ins=DB::connection('main_db')->table('conversation_details')->insertGetId($data_message);
                }

                $message_id = $message_new_ins;

                if(isset($response['choices'][0]['delta']['content']))
                $message_response = $response['choices'][0]['delta']['content'];
                if(isset($response['choices'][0]['message']['content']))
                $message_response = $response['choices'][0]['message']['content'];

                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $time=strval(time());

                $message = UserOpenaiChatMessageMainMarketing::whereId($message_id)->first();
                $chat = UserOpenaiChatMainMarketing::where('chat_id',$chat_id)->first();
                $message->response = $responsedText;
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $message->updated_at=$time;
                $message->save();

                //$user = UserMain::where('id',$user_id);
                $user = DB::connection('main_db')->table('users')->where('id', $user_id)->first();


                $old_remaining_words=$user->remaining_words;

                $new_remaining_words = $old_remaining_words - $total_used_tokens;


                if ($new_remaining_words < 0) {
                   $new_remaining_words = 0;
                }

                $remaining_words_arr= array(
                    'remaining_words' => $new_remaining_words,
                  );

                $user_update = DB::connection('main_db')->table('users')
                ->where('id', $user_id)
                ->update($remaining_words_arr);

                //$user->save();

                $chat->total_credits += $total_used_tokens;
                $chat->save();

                $n_prompt=count($params_json1["prompt"]);
                      if($n_prompt>0)
                      {
                        $n_prompt-=1;
                      }
                      
                      Log::debug('Count n_prompt'.$n_prompt);
                      $x=intval($n_prompt);
                      
                      $description= Arr::last($params_json1["prompt"]);
                      $description= implode(" ",$description);
                      
         }
        else    
        { 
            if ($settings->openai_default_model == 'gpt-3.5-turbo') {
                if (isset($response['choices'][0]['delta']['content'])) {
                    $message = $response['choices'][0]['delta']['content'];
                    $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                    $output .= $messageFix;
                    $responsedText .= $message;
                    $total_used_tokens += Helper::countWords($messageFix);

                    $string_length = Str::length($messageFix);
                    $needChars = 6000 - $string_length;
                    $random_text = Str::random($needChars);


                    echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

                } else {
                    if (isset($response)) {

                        if (Str::length($this->postContent) > 0) {
                            $message = trim($this->postContent);
                            $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                            $output .= $messageFix;
                            $responsedText .= $message;
                            $total_used_tokens += Helper::countWords($messageFix);

                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);
                        }
                    }

                }

            } else {
                if (isset($response->choices[0]->text)) {
                    $message = $response->choices[0]->text;
                    $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                    $output .= $messageFix;
                    $responsedText .= $message;
                    $total_used_tokens += Helper::countWords($messageFix);

                    $string_length = Str::length($messageFix);
                    $needChars = 6000 - $string_length;
                    $random_text = Str::random($needChars);


                    echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";


                } else {

                    if (isset($response)) {

                        if (Str::length($this->postContent) > 0) {
                            $message = trim($this->postContent);
                            $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                            $output .= $messageFix;
                            $responsedText .= $message;
                            $total_used_tokens += Helper::countWords($messageFix);

                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);
                        }

                    }

                }

            }


            $params_json = json_decode($params, true);
            $keywords = '';
            $description = $params_json["prompt"];
            $creativity = 1;
            $number_of_results = 1;
            $tone_of_voice = 0;
            $maximum_length = 2000;
            $language = "en";
            $post_type = 'paragraph_generator';
            $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
            Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
            ";


            // Save Users of Digital_Asset
            $user = \DB::connection('main_db')->table('users')->where('id', $user_id)->get();
            //$users = DB::connection('second_db')->table('users')->get();



            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new UserOpenai();
            $entry->title = 'New Workbook';
            $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;
            $entry->input = $prompt;
            $entry->response = null;
            $entry->output = null;
            $entry->hash = str()->random(256);
            $entry->credits = 0;
            $entry->words = 0;
            $entry->save();

            $message_id = $entry->id;


            // Create UserOpenai Models belong to OpenAIGenerator Models
            $message = UserOpenai::whereId($message_id)->first();
            $message->response = $responsedText;
            $message->output = $output;
            $message->hash = Str::random(256);
            $message->credits = $total_used_tokens;
            $message->words = 0;
            $UserOpenai_saved = $message->save();

            if(!$UserOpenai_saved){
                Log::debug('Save OpenAI Log Error ');
            }
            else{
                Log::debug('Save UserOpenai Log Success ');
            }

        }




        if(isset($this->total_used_tokens) && $this->total_used_tokens > 0 )
        $total_used_tokens=$this->total_used_tokens;

            //Update new remaining Tokens
            $user = \DB::connection('main_db')->table('users')->where('id', $user_id)->get();
            if ($user[0]->remaining_words != -1) {
                $user[0]->remaining_words -= $total_used_tokens;
                $new_remaining_words = $user[0]->remaining_words - $total_used_tokens;
                // $user[0]->save();
                $user_update = DB::connection('main_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));
            }

            if ($user[0]->remaining_words < -1) {
                $user[0]->remaining_words = 0;
                // $user[0]->save();
                $new_remaining_words = 0;
                $user_update = DB::connection('main_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));
            }

            if($user_update > 0)
            Log::debug('Update remaining at Main1 success');

            echo 'data: [DONE]';
            echo "\n\n";


        } else {
            echo 'data: [Update Failed user not found]';
        }


    }

    //Done
    public function SMAI_UpdateGPT_MainCoIn($user_id, $usage, $response, $params)
    {

        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (isset($user_id)) {
            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_MainCoIn from SMAIsyncController : ' . $user_id);
            
            //$params_json1 = json_decode($params, true);
            $params_json1 = $params;
      $chatGPT_catgory=$params_json1['gpt_category'];
      $response= json_decode($response, true);
       if( Str::contains($chatGPT_catgory, 'Chat_'))
         {
            //add Sync GPT chat version here

                $total_used_tokens=$usage;
                $chat_id = $params_json1['chat_id'];

                $chat_new_ins = UserOpenaiChat::updateOrCreate(
                    ['chat_id' => $chat_id],
                    ['user_id' => $user_id,'openai_chat_category_id' => 1,]
                );

                //$user_openai_chat_id =UserOpenaiChat::where('chat_id',$chat_id);
                $user_openai_chat_id =DB::connection('main_db')->table('user_openai_chat')->where('chat_id', $chat_id)->first();
                
                if(isset($user_openai_chat_id->id))
                {
                $user_openai_chat_id_ins=$user_openai_chat_id->id ;
                $message_new_ins = UserOpenaiChatMessage::create(['user_id' => $user_id,'chat_id' => $chat_id, 'user_openai_chat_id' => $user_openai_chat_id_ins,]);    
               }
                else
                {
                $message_new_ins = UserOpenaiChatMessage::create(['user_id' => $user_id,'chat_id' => $chat_id, ]);
                }

                
                $message_id = $message_new_ins->id;

                //$chat_new_ins = UserOpenaiChat::create(['user_id' => $user_id,'openai_chat_category_id' => 1,'chat_id' => $chat_id]);
      
               

                if(isset($response['choices'][0]['delta']['content']))
                      $message_response = $response['choices'][0]['delta']['content'];
                      if(isset($response['choices'][0]['message']['content']))
                      $message_response = $response['choices'][0]['message']['content'];

                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $message = UserOpenaiChatMessage::whereId($message_id)->first();
                $chat = UserOpenaiChat::where('chat_id',$chat_id)->first();
                $message->response = $responsedText;
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $message->save();

                //$user = UserMain::where('id',$user_id);
                $user1 = DB::connection('main_db')->table('users')->where('id', $user_id)->first();
                //$user = \DB::connection('main_db')->table('users')->where('id', $user_id)->get();

                Log::debug('FOund user main Email '.$user1->email);
                $old_remaining_words=$user1->remaining_words;

                      $new_remaining_words = $old_remaining_words - $total_used_tokens;
      
      
                      if ($new_remaining_words < 0) {
                         $new_remaining_words = 0;
                      }

                      $remaining_words_arr= array(
                        'remaining_words' => $new_remaining_words,
                      );

                      $user_update = DB::connection('main_db')->table('users')
                      ->where('id', $user_id)
                      ->update($remaining_words_arr);

                      //$user->save();

                $chat->total_credits += $total_used_tokens;
                $chat->save();

                $n_prompt=count($params_json1["prompt"]);
                      if($n_prompt>0)
                      {
                        $n_prompt-=1;
                      }
                      
                      Log::debug('Count n_prompt'.$n_prompt);
                      $description = $params_json1["prompt"][$n_prompt]["content"];


         }
        else    
        {    
            
            
            if ($settings->openai_default_model == 'gpt-3.5-turbo') {
                if (isset($response['choices'][0]['delta']['content'])) {
                    $message = $response['choices'][0]['delta']['content'];
                    $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                    $output .= $messageFix;
                    $responsedText .= $message;
                    $total_used_tokens += Helper::countWords($messageFix);

                    $string_length = Str::length($messageFix);
                    $needChars = 6000 - $string_length;
                    $random_text = Str::random($needChars);


                    echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

                } else {
                    if (isset($response)) {

                        if (Str::length($this->postContent) > 0) {
                            $message = trim($this->postContent);
                            $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                            $output .= $messageFix;
                            $responsedText .= $message;
                            $total_used_tokens += Helper::countWords($messageFix);

                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);
                        }
                    }

                }

            } else {
                if (isset($response->choices[0]->text)) {
                    $message = $response->choices[0]->text;
                    $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                    $output .= $messageFix;
                    $responsedText .= $message;
                    $total_used_tokens += Helper::countWords($messageFix);

                    $string_length = Str::length($messageFix);
                    $needChars = 6000 - $string_length;
                    $random_text = Str::random($needChars);


                    echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";


                } else {

                    if (isset($response)) {

                        if (Str::length($this->postContent) > 0) {
                            $message = trim($this->postContent);
                            $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                            $output .= $messageFix;
                            $responsedText .= $message;
                            $total_used_tokens += Helper::countWords($messageFix);

                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);
                        }

                    }

                }

            }


            $params_json = json_decode($params, true);
            $keywords = '';
            $description = $params_json["prompt"];
            $creativity = 1;
            $number_of_results = 1;
            $tone_of_voice = 0;
            $maximum_length = 2000;
            $language = "en";
            $post_type = 'paragraph_generator';
            $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
            Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
            ";


            // Save Users of Digital_Asset
            $user = \DB::connection('main_db')->table('users')->where('id', $user_id)->get();
            //$users = DB::connection('second_db')->table('users')->get();

            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new UserOpenai();
            $entry->title = 'New Workbook';
            $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;
            $entry->input = $prompt;
            $entry->response = null;
            $entry->output = null;
            $entry->hash = str()->random(256);
            $entry->credits = 0;
            $entry->words = 0;
            $entry->save();

            $message_id = $entry->id;


            // Create UserOpenai Models belong to OpenAIGenerator Models
            $message = UserOpenai::whereId($message_id)->first();
            $message->response = $responsedText;
            $message->output = $output;
            $message->hash = Str::random(256);
            $message->credits = $total_used_tokens;
            $message->words = 0;
            $UserOpenai_saved = $message->save();

            if(!$UserOpenai_saved){
                Log::debug('Save OpenAI Log Error ');
            }
            else{
                Log::debug('Save UserOpenai Log Success ');
            }


        }     

        if(isset($this->total_used_tokens) && $this->total_used_tokens > 0 )
        $total_used_tokens=$this->total_used_tokens;

            //Update new remaining Tokens
            $user = \DB::connection('main_db')->table('users')->where('id', $user_id)->get();
            if ($user[0]->remaining_words != -1) {
                $user[0]->remaining_words -= $total_used_tokens;
                $new_remaining_words = $user[0]->remaining_words - $total_used_tokens;
                // $user[0]->save();
                $user_update = DB::connection('main_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));
            }

            if ($user[0]->remaining_words < -1) {
                $user[0]->remaining_words = 0;
                // $user[0]->save();
                $new_remaining_words = 0;
                $user_update = DB::connection('main_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));
            }

            if($user_update > 0)
            Log::debug('Update remaining at Main1 success');

            echo 'data: [DONE]';
            echo "\n\n";


        } else {
            echo 'data: [Update Failed user not found]';
        }


    }


    //Done
    public function SMAI_UpdateGPT_SocialPost($user_id, $usage, $response, $params)
    {

        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (isset($user_id)) {
            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_SocialPost from SMAIsyncController : ' . $user_id);
            
            //$params_json1 = json_decode($params, true);
            $params_json1 = $params;
            $chatGPT_catgory=$params_json1['gpt_category'];
            $response= json_decode($response, true);
             if( Str::contains($chatGPT_catgory, 'Chat_'))
               {
                  //add Sync GPT chat version here
      
                      $total_used_tokens=$usage;
                      $chat_id = $params_json1['chat_id'];

                    $chat_new_ins = UserOpenaiChat::updateOrCreate(
                        ['chat_id' => $chat_id],
                        ['user_id' => $user_id,'openai_chat_category_id' => 1,]
                    );
    
                    //$user_openai_chat_id =UserOpenaiChat::where('chat_id',$chat_id);
                    $user_openai_chat_id =DB::connection('main_db')->table('sp_user_openai_chat')->where('chat_id', $chat_id)->first();
                    
                    if(isset($user_openai_chat_id->id))
                    {
                    $user_openai_chat_id_ins=$user_openai_chat_id->id ;
                    $message_new_ins = UserOpenaiChatMessageSocialPost::create(['user_id' => $user_id,'chat_id' => $chat_id, 'user_openai_chat_id' => $user_openai_chat_id_ins,]);    
                   }
                    else
                    {
                    $message_new_ins = UserOpenaiChatMessageSocialPost::create(['user_id' => $user_id,'chat_id' => $chat_id, ]);
                    }

                    $message_id = $message_new_ins->id;



                      if(isset($response['choices'][0]['delta']['content']))
                      $message_response = $response['choices'][0]['delta']['content'];
                      if(isset($response['choices'][0]['message']['content']))
                      $message_response = $response['choices'][0]['message']['content'];

                      $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                      $output .= $messageFix;
                      $responsedText .= $message_response;
      
                      $message = UserOpenaiChatMessageSocialPost::whereId($message_id)->first();
                      $chat = UserOpenaiChatSocialPost::where('chat_id',$chat_id)->first();
                      $message->response = $responsedText;
                      $message->output = $output;
                      $message->hash = Str::random(256);
                      $message->credits = $total_used_tokens;
                      $message->words = 0;
                      $message->save();
      
                      //$user = UserSP::where('id',$user_id);
                      $user = DB::connection('main_db')->table('sp_users')->where('id', $user_id)->first();
      
      
                      $old_remaining_words=$user->remaining_words;

                      $new_remaining_words = $old_remaining_words - $total_used_tokens;
      
      
                      if ($new_remaining_words < 0) {
                         $new_remaining_words = 0;
                      }

                      $remaining_words_arr= array(
                        'remaining_words' => $new_remaining_words,
                      );

                      $user_update = DB::connection('main_db')->table('sp_users')
                      ->where('id', $user_id)
                      ->update($remaining_words_arr);

                      //$user->save();
      
                      $chat->total_credits += $total_used_tokens;
                      $chat->save();

                      $n_prompt=count($params_json1["prompt"]);
                      if($n_prompt>0)
                      {
                        $n_prompt-=1;
                      }

                      
                      
                      Log::debug('Count n_prompt'.$n_prompt);
                      Log::info($params_json1["prompt"][0]["content"]);
                      Log::info($params_json1["prompt"][1]["content"]);
                      Log::info($params_json1["prompt"][2]["content"]);
                      $x=intval($n_prompt);
                    
                      $description= Arr::last($params_json1["prompt"]);
                      $description= implode(" ",$description);

                      Log::debug('Desc after convert to string'.$description);
      
      

               }
              else    
              { 
                if ($settings->openai_default_model == 'gpt-3.5-turbo') {
                            if (isset($response['choices'][0]['delta']['content'])) {
                                $message = $response['choices'][0]['delta']['content'];
                                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                                $output .= $messageFix;
                                $responsedText .= $message;
                                $total_used_tokens += Helper::countWords($messageFix);

                                $string_length = Str::length($messageFix);
                                $needChars = 6000 - $string_length;
                                $random_text = Str::random($needChars);


                                echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

                            } else {
                                if (isset($response)) {

                                    if (Str::length($this->postContent) > 0) {
                                        $message = trim($this->postContent);
                                        $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                                        $output .= $messageFix;
                                        $responsedText .= $message;
                                        $total_used_tokens += Helper::countWords($messageFix);

                                        $string_length = Str::length($messageFix);
                                        $needChars = 6000 - $string_length;
                                        $random_text = Str::random($needChars);
                                    }

                                }
                            }
            } else {
                            if (isset($response->choices[0]->text)) {
                                $message = $response->choices[0]->text;
                                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                                $output .= $messageFix;
                                $responsedText .= $message;
                                $total_used_tokens += Helper::countWords($messageFix);

                                $string_length = Str::length($messageFix);
                                $needChars = 6000 - $string_length;
                                $random_text = Str::random($needChars);


                                echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

                            } else {

                                if (isset($response)) {

                                    if (Str::length($this->postContent) > 0) {
                                        $message = trim($this->postContent);
                                        $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                                        $output .= $messageFix;
                                        $responsedText .= $message;
                                        $total_used_tokens += Helper::countWords($messageFix);

                                        $string_length = Str::length($messageFix);
                                        $needChars = 6000 - $string_length;
                                        $random_text = Str::random($needChars);
                                    }

                                }


                            }
            }

            $params_json = json_decode($params, true);
            $keywords = '';
            $description = $params_json["prompt"];
            $creativity = 1;
            $number_of_results = 1;
            $tone_of_voice = 0;
            $maximum_length = 2000;
            $language = "en";
            $post_type = 'paragraph_generator';
            $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
            Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
            ";
                // Save Users of SocialPOst
                            $user = \DB::connection('main_db')->table('users')->where('id', $user_id)->get();
                //$users = DB::connection('second_db')->table('users')->get();

                            $post = OpenAIGenerator::where('slug', $post_type)->first();
                            $entry = new SP_UserOpenai();
                            $entry->title = 'New Workbook';
                            $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                            $entry->user_id = $user_id;
                            $entry->openai_id = $post->id;
                            $entry->input = $prompt;
                            $entry->response = null;
                            $entry->output = null;
                            $entry->hash = str()->random(256);
                            $entry->credits = 0;
                            $entry->words = 0;
                            $entry->save();

                            $message_id = $entry->id;
                            // Create UserOpenai Models belong to OpenAIGenerator Models
                            $message = SP_UserOpenai::whereId($message_id)->first();
                            $message->response = $responsedText;
                            $message->output = $output;
                            $message->hash = Str::random(256);
                            $message->credits = $total_used_tokens;
                            $message->words = 0;
                            

                            $UserOpenai_saved = $message->save();

                            if(!$UserOpenai_saved){
                                Log::debug('Save OpenAI Socialpost Log Error ');
                            }
                            else{
                                Log::debug('Save SP_UserOpenai Log Success ');
                            }


        }

        Log::debug('before Skip Caption _Socialpost'); 
          if( Str::contains($chatGPT_catgory, 'Chat_') == false)
          {
            //add to SP_Captions for show in socialpost caption list
            Log::debug('before insert Caption _Socialpost'); 
            $caption_table = "sp_captions";
            $caption_database = "main_db";
            $this->SMAI_Ins_Eloq_openAI_Caption_Socialpost($description, $responsedText, $user_id, $message_id, $caption_database, $caption_table);
          }

  
            if(isset($this->total_used_tokens) && $this->total_used_tokens > 0 )
            $total_used_tokens=$this->total_used_tokens;

            Log::debug('before update TOken numbers remaining_words'); 
            // Save Users of Digital_Asset
            //Update new remaining Tokens
            $user = \DB::connection('main_db')->table('sp_users')->where('id', $user_id)->get();
            Log::debug('User email test : '.$user[0]->email);

            if ($user[0]->remaining_words != -1) {
                $user[0]->remaining_words -= $total_used_tokens;
                $new_remaining_words = $user[0]->remaining_words - $total_used_tokens;
                // $user[0]->save();
                $user_update = DB::connection('main_db')->update('update sp_users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));
            }

            if ($user[0]->remaining_words < -1) {
                $user[0]->remaining_words = 0;
                // $user[0]->save();
                $new_remaining_words = 0;
                $user_update = DB::connection('main_db')->update('update sp_users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));
            }
            if($user_update > 0)
            Log::debug('Update remaining at Main Socialpost success');

            echo 'data: [DONE]';
            echo "\n\n";


        } else {
            echo 'data: [Update Failed user not found]';
        }


    }


    //Done
    public function SMAI_UpdateGPT_DigitalAsset($user_id, $usage, $response, $params)
    {
        // Update Token and usage
        $settings = $this->settings;
        $settings_two = $this->settings_two;


        if (isset($user_id)) {

            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_DigitalAsset from SMAIsyncController : ' . $user_id);

            //$response=json_decode($response,true);

            Log::info(print_r($response, true));

            //$params_json1 = json_decode($params, true);
            $params_json1 = $params;
            $chatGPT_catgory=$params_json1['gpt_category'];
            $response= json_decode($response, true);
             if( Str::contains($chatGPT_catgory, 'Chat_'))
               {
                  //add Sync GPT chat version here
      
                      $total_used_tokens=$usage;
                      $chat_id = $params_json1['chat_id'];

                      Log::debug(' Chat ID before create Eloquent UserOpenaiChatDesign'.$chat_id);

                     $chat_new_ins = UserOpenaiChat::updateOrCreate(
                        ['chat_id' => $chat_id],
                        ['user_id' => $user_id,'openai_chat_category_id' => 1,]
                    );
    
                    //$user_openai_chat_id =UserOpenaiChat::where('chat_id',$chat_id);
                    $user_openai_chat_id =DB::connection('digitalasset_db')->table('user_openai_chat')->where('chat_id', $chat_id)->first();
                    
                    if(isset($user_openai_chat_id->id))
                    {
                    $user_openai_chat_id_ins=$user_openai_chat_id->id ;
                    $message_new_ins = UserOpenaiChatMessageDesign::create(['user_id' => $user_id,'chat_id' => $chat_id, 'user_openai_chat_id' => $user_openai_chat_id_ins,]);    
                   }
                    else
                    {
                    $message_new_ins = UserOpenaiChatMessageDesign::create(['user_id' => $user_id,'chat_id' => $chat_id, ]);
                    }
                    $message_id = $message_new_ins->id;

                      if(isset($response['choices'][0]['delta']['content']))
                      $message_response = $response['choices'][0]['delta']['content'];
                      if(isset($response['choices'][0]['message']['content']))
                      $message_response = $response['choices'][0]['message']['content'];



                      $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                      $output .= $messageFix;
                      $responsedText .= $message_response;
      
                      $message = UserOpenaiChatMessageDesign::whereId($message_id)->first();
                      $chat = UserOpenaiChatDesign::where('chat_id',$chat_id)->first();
                      $message->response = $responsedText;
                      $message->output = $output;
                      $message->hash = Str::random(256);
                      $message->credits = $total_used_tokens;
                      $message->words = 0;
                      $message->save();
      
                      //$user = UserDesign::where('id',$user_id);
                      $user = DB::connection('digitalasset_db')->table('users')->where('id', $user_id)->first();

                      Log::debug('Debug UderDesign from Eloqunt ');
                      //Log::info($user);
      
                      $old_remaining_words=$user->remaining_words;

                      $new_remaining_words = $old_remaining_words - $total_used_tokens;
      
      
                      if ($new_remaining_words < 0) {
                         $new_remaining_words = 0;
                      }

                      $remaining_words_arr= array(
                        'remaining_words' => $new_remaining_words,
                      );

                      $user_update = DB::connection('digitalasset_db')->table('users')
                      ->where('id', $user_id)
                      ->update($remaining_words_arr);

                      //$user->save();
      
                      $chat->total_credits += $total_used_tokens;
                      $chat->save();

                      $n_prompt=count($params_json1["prompt"]);
                      if($n_prompt>0)
                      {
                        $n_prompt-=1;
                      }
                      
                      Log::debug('Count n_prompt'.$n_prompt);
                      $x=intval($n_prompt);
                    
                      $description= Arr::last($params_json1["prompt"]);
                      $description= implode(" ",$description);

      
               }
              else    
              { 

            if ($settings->openai_default_model == 'gpt-3.5-turbo') {
                Log::debug('Response in gpt-3.5-turbo SMAI_UpdateGPT_DigitalAsset from SMAIsyncController ');
                if (isset($response['choices'][0]['delta']['content'])) {
                    $message = $response['choices'][0]['delta']['content'];
                    $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                    $output .= $messageFix;
                    $responsedText .= $message;
                    $total_used_tokens += Helper::countWords($messageFix);

                    $string_length = Str::length($messageFix);
                    $needChars = 6000 - $string_length;
                    $random_text = Str::random($needChars);

                    Log::debug(' response_choices SMAI_UpdateGPT_DigitalAsset from SMAIsyncController : ' . info(print_r($response['choices'], true)));
                    echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

                } else {

                    if (isset($response)) {


                        if (Str::length($this->postContent) > 0) {
                            $message = trim($this->postContent);
                            $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                            $output .= $messageFix;
                            $responsedText .= $message;
                            $total_used_tokens += Helper::countWords($messageFix);

                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);
                        }
                    }
                }


            } else {
                if (isset($response->choices[0]->text)) {
                    $message = $response->choices[0]->text;
                    $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                    $output .= $messageFix;
                    $responsedText .= $message;
                    $total_used_tokens += Helper::countWords($messageFix);

                    $string_length = Str::length($messageFix);
                    $needChars = 6000 - $string_length;
                    $random_text = Str::random($needChars);

                    Log::debug(' response_choices in else SMAI_UpdateGPT_DigitalAsset from SMAIsyncController : ' . info(print_r($response['choices'], true)));

                    echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

                } else {

                    if (isset($response)) {

                        if (Str::length($this->postContent) > 0) {
                            $message = trim($this->postContent);
                            $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                            $output .= $messageFix;
                            $responsedText .= $message;
                            $total_used_tokens += Helper::countWords($messageFix);

                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);
                        }

                    }
                }
            }

            //Log::info(print_r($response, true));

            Log::debug('$params SMAI_UpdateGPT_DigitalAsset from SMAIsyncController : ' . info(print_r($params, true)));

            //Log::debug('$params smaisync_tokens from APIsController : '.info(json_encode($params)));
            Log::info(print_r($params, true));

            $params_json = json_decode($params, true);
            $keywords = '';
            $description = $params_json["prompt"];
            $creativity = 1;
            $number_of_results = 1;
            $tone_of_voice = 0;
            $maximum_length = 2000;
            $language = "en";
            $post_type = 'paragraph_generator';
            $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
            Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
            ";

            // Save Users of Digital_Asset
            $user = \DB::connection('digitalasset_db')->table('users')->where('id', $user_id)->get();
            //$users = DB::connection('second_db')->table('users')->get();

            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new DigitalAsset_UserOpenai();
            $entry->title = 'New Workbook';
            $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;
            $entry->input = $prompt;
            $entry->response = null;
            $entry->output = null;
            $entry->hash = str()->random(256);
            $entry->credits = 0;
            $entry->words = 0;
            $entry->save();

            $message_id = $entry->id;

            Log::info(print_r("Inserted new openai to ID " . $message_id, true));

            // Create UserOpenai Models belong to OpenAIGenerator Models
            $message = DigitalAsset_UserOpenai::whereId($message_id)->first();
            $message->response = $responsedText;
            $message->output = $output;
            $message->hash = Str::random(256);
            $message->credits = $total_used_tokens;
            $message->words = 0;
            $UserOpenai_saved = $message->save();

            if(!$UserOpenai_saved){
                Log::debug('Save OpenAI Design Log Error ');
            }
            else{
                Log::debug('Save DigitalAsset Log Success ');
            }

        }

            if(isset($this->total_used_tokens) && $this->total_used_tokens > 0 )
            $total_used_tokens=$this->total_used_tokens;

            //Update new remaining Tokens
            $user = \DB::connection('digitalasset_db')->table('users')->where('id', $user_id)->get();
            if ($user[0]->remaining_words != -1) {
                $user[0]->remaining_words -= $total_used_tokens;
                $new_remaining_words = $user[0]->remaining_words - $total_used_tokens;
                // $user[0]->save();
                $user_update = DB::connection('digitalasset_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));
            }

            if ($user[0]->remaining_words < -1) {
                $user[0]->remaining_words = 0;
                // $user[0]->save();
                $new_remaining_words = 0;
                $user_update = DB::connection('digitalasset_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));
            }

            if($user_update > 0)
            Log::debug('Update remaining at Design success');

            echo 'data: [DONE]';
            echo "\n\n";


        } else {
            echo 'data: [Update Failed user not found]';
        }


    }

//Done
    public function SMAI_UpdateGPT_MobileApp($user_id, $usage, $response, $params)
    {
        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (isset($user_id)) {

            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_MobileApp from SMAIsyncController : ' . $user_id);
            
            //$params_json1 = json_decode($params, true);
            $params_json1 = $params;
            $chatGPT_catgory=$params_json1['gpt_category'];
            $response= json_decode($response, true);
             if( Str::contains($chatGPT_catgory, 'Chat_'))
               {
                  //add Sync GPT chat version here
      
                      $total_used_tokens=$usage;
                      $chat_id = $params_json1['chat_id'];


                    $chat_new_ins = UserOpenaiChatMobile::updateOrCreate(
                        ['chat_id' => $chat_id],
                        ['user_id' => $user_id,'openai_chat_category_id' => 1,]
                    );
    
                    //$user_openai_chat_id =UserOpenaiChat::where('chat_id',$chat_id);
                    $user_openai_chat_id =DB::connection('mobileapp_db')->table('willdev_user_chat')->where('chat_id', $chat_id)->first();
                    
                    if(isset($user_openai_chat_id->id))
                    {
                    $user_openai_chat_id_ins=$user_openai_chat_id->id ;
                    $message_new_ins = UserOpenaiChatMessageMobile::create(['user_id' => $user_id,'chat_id' => $chat_id, 'user_openai_chat_id' => $user_openai_chat_id_ins,]);    
                   }
                    else
                    {
                    $message_new_ins = UserOpenaiChatMessageMobile::create(['user_id' => $user_id,'chat_id' => $chat_id, ]);
                    }
                    $message_id = $message_new_ins->id;


                      if(isset($response['choices'][0]['delta']['content']))
                      $message_response = $response['choices'][0]['delta']['content'];
                      if(isset($response['choices'][0]['message']['content']))
                      $message_response = $response['choices'][0]['message']['content'];

                      $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                      $output .= $messageFix;
                      $responsedText .= $message_response;
      
                      $message = UserOpenaiChatMessageMobile::whereId($message_id)->first();
                      $chat = UserOpenaiChatMainMobile::where('chat_id',$chat_id)->first();
                      $message->response = $responsedText;
                      $message->output = $output;
                      $message->hash = Str::random(256);
                      $message->credits = $total_used_tokens;
                      $message->words = 0;
                      $message->save();
      
                      $user = DB::connection('mobileapp_db')->table('users')->where('id', $user_id)->first();
                      //$user = UserMain::where('id',$user_id);
      
      
                      $old_remaining_words=$user->remaining_words;

                      $new_remaining_words = $old_remaining_words - $total_used_tokens;
      
      
                      if ($new_remaining_words < 0) {
                         $new_remaining_words = 0;
                      }
                      $remaining_words_arr= array(
                        'remaining_words' => $new_remaining_words,
                      );


                      $user_update = DB::connection('mobileapp_db')->table('users')
                      ->where('id', $user_id)
                      ->update($remaining_words_arr);

                      //$user->save();
                  
      
                      $chat->total_credits += $total_used_tokens;
                      $chat->save();

                      $n_prompt=count($params_json1["prompt"]);
                      if($n_prompt>0)
                      {
                        $n_prompt-=1;
                      }
                      
                      Log::debug('Count n_prompt'.$n_prompt);
                      $x=intval($n_prompt);
                      
                      $description= Arr::last($params_json1["prompt"]);
                      $description= implode(" ",$description);
      
               }
              else    
              {   
            
            
            
            
            if ($settings->openai_default_model == 'gpt-3.5-turbo') {
                if (isset($response['choices'][0]['delta']['content'])) {
                    $message = $response['choices'][0]['delta']['content'];
                    $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                    $output .= $messageFix;
                    $responsedText .= $message;
                    $total_used_tokens += Helper::countWords($messageFix);

                    $string_length = Str::length($messageFix);
                    $needChars = 6000 - $string_length;
                    $random_text = Str::random($needChars);


                    echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

                } else {
                    if (isset($response)) {

                        if (Str::length($this->postContent) > 0) {
                            $message = trim($this->postContent);
                            $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                            $output .= $messageFix;
                            $responsedText .= $message;
                            $total_used_tokens += Helper::countWords($messageFix);

                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);
                        }

                    }
                }
            } else {
                if (isset($response->choices[0]->text)) {
                    $message = $response->choices[0]->text;
                    $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                    $output .= $messageFix;
                    $responsedText .= $message;
                    $total_used_tokens += Helper::countWords($messageFix);

                    $string_length = Str::length($messageFix);
                    $needChars = 6000 - $string_length;
                    $random_text = Str::random($needChars);


                    echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

                } else {
                    if (isset($response)) {
                        if (Str::length($this->postContent) > 0) {
                            $message = trim($this->postContent);
                            $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                            $output .= $messageFix;
                            $responsedText .= $message;
                            $total_used_tokens += Helper::countWords($messageFix);

                            $string_length = Str::length($messageFix);
                            $needChars = 6000 - $string_length;
                            $random_text = Str::random($needChars);
                        }

                    }
                }
            }

            $params_json = json_decode($params, true);
            $keywords = '';
            $description = $params_json["prompt"];
            $creativity = 1;
            $number_of_results = 1;
            $tone_of_voice = 0;
            $maximum_length = 2000;
            $language = "en";
            $post_type = 'paragraph_generator';
            $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
            Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
            ";


            // Save Users of Mobile App
            $user = \DB::connection('mobileapp_db')->table('users')->where('id', $user_id)->get();
            //$users = DB::connection('second_db')->table('users')->get();


            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new Mobile_UserOpenai();
            $entry->title = 'New Workbook';
            $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;
            $entry->input = $prompt;
            $entry->response = null;
            $entry->output = null;
            $entry->hash = str()->random(256);
            $entry->credits = 0;
            $entry->words = 0;
            $entry->save();

            $message_id = $entry->id;
            // Create UserOpenai Models belong to OpenAIGenerator Models
            $message = Mobile_UserOpenai::whereId($message_id)->first();
            $message->response = $responsedText;
            $message->output = $output;
            $message->hash = Str::random(256);
            $message->credits = $total_used_tokens;
            $message->words = 0;
            $UserOpenai_saved = $message->save();

            if(!$UserOpenai_saved){
                Log::debug('Save OpenAI Mobile Log Error ');
            }
            else{
                Log::debug('Save Mobile Log Success ');
            }

        }

            if(isset($this->total_used_tokens) && $this->total_used_tokens > 0 )
            $total_used_tokens=$this->total_used_tokens;

            //Update new remaining Tokens
            $user = \DB::connection('mobileapp_db')->table('users')->where('id', $user_id)->get();
            if ($user[0]->remaining_words != -1) {
                $user[0]->remaining_words -= $total_used_tokens;
                $new_remaining_words = $user[0]->remaining_words - $total_used_tokens;
                // $user[0]->save();
                $user_update = DB::connection('mobileapp_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));

            }

            if ($user[0]->remaining_words < -1) {
                $user[0]->remaining_words = 0;
                // $user[0]->save();
                $new_remaining_words = 0;
                $user_update = DB::connection('mobileapp_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));


            }
            if($user_update > 0)
            Log::debug('Update remaining at MobileApp success');

            echo 'data: [DONE]';
            echo "\n\n";


        } else {
            echo 'data: [Update Failed user not found]';
        }

    

    


    }


//Done
    public function SMAI_Check_DigitalAsset_UserColumn($user_id, $key, $database)
    {

        /*   Log::debug('User ID  SMAI_Check_DigitalAsset_UserPlans from SMAIsyncController : '.$user_id);
            Log::debug('Key SMAI_Check_DigitalAsset_UserPlans from SMAIsyncController : '.$key);
            Log::debug('Database SMAI_Check_DigitalAsset_UserPlans from SMAIsyncController : '.$database);

        */

        //$database=strval($database);
        if ($user_id > 0) {
            $user = DB::connection($database)->table('users')->where('id', $user_id)->first();

            //$user = DB::connection('digitalasset_db')->table('users')->where('id', $user_id)->first();

            //$column=strval($key);

            $token_total = $user->remaining_words;
            Log::debug('Remaining Word from DB SMAI_Check_DigitalAsset_UserPlans from SMAIsyncController : ' . $token_total);

            echo $token_total;
            return $token_total;

        } else {
            return false;
        }


    }




//Woring
//Universal SMAI fnc
    public function SMAI_Update_TableColumn($arr_ids, $database, $table, $data)
    {


        $array_of_ids = $arr_ids;
        $table_update = DB::connection($database)->table($table)->whereIn('id', $array_of_ids)->update(array('votes' => 1));


    }


//Woring
//Universal SMAI fnc
    public function SMAI_Ins_TableColumn($database, $table, $data_arr)
    {
        $createMultipleUsers = [
            [
                'name' => 'Admin',
                'email' => 'admin@techvblogs.com',
                'password' => bcrypt('TechvBlogs@123')],

            [
                'name' => 'Guest',
                'email' => 'guest@techvblogs.com',
                'password' => bcrypt('Guest@456')],

            [
                'name' => 'Account',
                'email' => 'account@techvblogs.com',
                'password' => bcrypt('Account@789')]
        ];

        $data_arr = [];
        $data_arr = $createMultipleUsers;

        // User::insert($createMultipleUsers); // Eloquent

        $table_ins = DB::connection($database)->table($table)->insert($data_arr);
        // Query Builder

    }

    public function SMAI_Ins_Eloq_openAI_content_TB($user_id, $database, $table)
    {

        //define Users
        $user = \DB::connection($database)->table('users')->where('id', $user_id)->get();
        //$users = DB::connection('second_db')->table('users')->get();

        $prompt='';
        $post_type='paragraph_generator';
        $post = OpenAIGenerator::where('slug', $post_type)->first();
        $entry = new Mobile_UserOpenai();
        $entry->title = 'New Workbook';
        $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
        $entry->user_id = $user_id;
        $entry->openai_id = $post->id;
        $entry->input = $prompt;
        $entry->response = null;
        $entry->output = null;
        $entry->hash = str()->random(256);
        $entry->credits = 0;
        $entry->words = 0;
        $entry->save();

        $message_id = $entry->id;
        //


    }

    public function SMAI_QryAll_Eloq_openAI_content_TB($user_id, $database, $table)
    {


    }

    public function SMAI_QryFilter_Eloq_openAI_content_TB($user_id, $database, $table)
    {


    }

    public function SMAI_Ins_Eloq_openAI_Caption_Socialpost($title, $content, $user_id, $openai_id, $database, $table)
    {

        //switch on

        //define Users
        $user = \DB::connection($database)->table('sp_team')->where('owner', $user_id)->get();
        //$users = DB::connection('second_db')->table('users')->get();
        //$post = OpenAIGenerator::where('slug', $post_type)->first();

        $team_id = $user[0]->id;
        if (isset($openai_id) && $openai_id > 0)
            $parentid = $openai_id;
        else
            $parentid = '';

        $entry = new SP_UserCaption();

        $entry->user_id = $user_id;
        $entry->team_id = $team_id;
        $entry->parent_id = $parentid;
        $entry->title = $title;
        $entry->content = $content;
        $entry->ids = str()->random(13);
        $entry->changed = time();
        $entry->created = time();
        $entry->save();


    }


}
