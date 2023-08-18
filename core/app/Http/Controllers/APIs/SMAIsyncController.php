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

use App\Models\UserOpenai;
use App\Models\UserOpenaiChat;

use App\Models\SP_UserOpenai;
use App\Models\DigitalAsset_UserOpenai;
use App\Models\Mobile_UserOpenai;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI;
use OpenAI\Laravel\Facades\OpenAI as FacadesOpenAI;




class SMAIsyncController extends Controller
{
    protected $client;
    protected $settings;
    const STABLEDIFFUSION = 'stablediffusion';
    const STORAGE_S3 = 's3';
    const STORAGE_LOCAL = 'public';

    public function __construct()
    {
        //Settings
        $this->settings = Settings::first();
        $this->settings_two = SettingTwo::first();
    }


    public static  function SMAI_UpdateGPT_MainCoIn($user_id,$usage,$response,$params)
{

    $settings = $this->settings;
    $settings_two = $this->settings_two;

    if(isset($user_id))
    {     
 
        Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_MainCoIn from SMAIsyncController : '.$user_id);
         if ($settings->openai_default_model == 'gpt-3.5-turbo') {
             if (isset($response['choices'][0]['delta']['content'])) {
                 $message = $response['choices'][0]['delta']['content'];
                 $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                 $output .= $messageFix;
                 $responsedText .= $message;
                 $total_used_tokens += countWords($messageFix);
 
                 $string_length = Str::length($messageFix);
                 $needChars = 6000 - $string_length;
                 $random_text = Str::random($needChars);
 
 
                 echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                 ob_flush();
                 flush();
                 usleep(500);
             }
         } else {
             if (isset($response->choices[0]->text)) {
                 $message = $response->choices[0]->text;
                 $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                 $output .= $messageFix;
                 $responsedText .= $message;
                 $total_used_tokens += countWords($messageFix);
 
                 $string_length = Str::length($messageFix);
                 $needChars = 6000 - $string_length;
                 $random_text = Str::random($needChars);
 
 
                 echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                 ob_flush();
                 flush();
                 //usleep(500);
             }
         }
 
         $params_json = json_decode($params);
         $keywords='';
         $description=$params_json->prompt;
        $creativity=1;
        $number_of_results=1;
        $tone_of_voice=0;
        $maximum_length=2000;
        $post_type = 'paragraph_generator';
        $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
            Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
            ";

         
         $post = OpenAIGenerator::where('slug', $post_type)->first();
         $entry = new UserOpenai();
         $entry->title = 'New Workbook';
         $entry->slug = str()->random(7) . str($user->fullName())->slug() . '-workbook';
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
         $message->save();





 
         //$user = Auth::user();
         // Save Users of Digital_Asset
         $user = \DB::connection('digitalasset_db')->table('users')->select('id',$user_id)->get();
         //$users = DB::connection('second_db')->table('users')->get();
         
         if ($user->remaining_words != -1) {
             $user->remaining_words -= $total_used_tokens;
             $user->save();
         }
 
         if ($user->remaining_words < -1) {
             $user->remaining_words = 0;
             $user->save();
         }
 
         echo 'data: [DONE]';
         echo "\n\n";
 
 
     }
     else{
         echo 'data: [Update Failed user not found]';
     }
      
 
 
 
 }


    public static  function SMAI_UpdateGPT_SocialPost($user_id,$usage,$response,$params)
{

            /*  $suggestion = post("suggestion");
        $max_lenght = (int)post("max_lenght");
        $hashtags = (int)post("hashtags"); 
     
       $limit_tokens =  permission("openai_limit_tokens");
        log_message('debug', 'Limit Token from SMAITokenSyncController : '.$limit_tokens); 
        $usage_tokens =  get_team_data("openai_usage_tokens", 0);
        log_message('debug', 'Limit Usage Token from SMAITokenSyncController : '.$usage_tokens); 


        if($usage_tokens >= $limit_tokens){
            ms([
                "status" => "error",
                "message" => sprintf( __("You've used the reaching of the limit of %s OpenAI tokens"), $limit_tokens)
            ]);
        }
         update_team_data("openai_usage_tokens",  get_team_data("openai_usage_tokens", 0) + $usage);

        ms([
            "status" => "success",
            "message" => "Success",
            "data" => trim($text)
        ]); */

        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if(isset($user_id))
        {     
     
            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_SocialPost from SMAIsyncController : '.$user_id);
             if ($settings->openai_default_model == 'gpt-3.5-turbo') {
                 if (isset($response['choices'][0]['delta']['content'])) {
                     $message = $response['choices'][0]['delta']['content'];
                     $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                     $output .= $messageFix;
                     $responsedText .= $message;
                     $total_used_tokens += countWords($messageFix);
     
                     $string_length = Str::length($messageFix);
                     $needChars = 6000 - $string_length;
                     $random_text = Str::random($needChars);
     
     
                     echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                     ob_flush();
                     flush();
                     usleep(500);
                 }
             } else {
                 if (isset($response->choices[0]->text)) {
                     $message = $response->choices[0]->text;
                     $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                     $output .= $messageFix;
                     $responsedText .= $message;
                     $total_used_tokens += countWords($messageFix);
     
                     $string_length = Str::length($messageFix);
                     $needChars = 6000 - $string_length;
                     $random_text = Str::random($needChars);
     
     
                     echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                     ob_flush();
                     flush();
                     //usleep(500);
                 }
             }
     
             $params_json = json_decode($params);
             $keywords='';
             $description=$params_json->prompt;
        $creativity=1;
        $number_of_results=1;
        $tone_of_voice=0;
        $maximum_length=2000;
        $post_type = 'paragraph_generator';
        $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
            Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
            ";

         
             $post = OpenAIGenerator::where('slug', $post_type)->first();
             $entry = new UserOpenai();
             $entry->title = 'New Workbook';
             $entry->slug = str()->random(7) . str($user->fullName())->slug() . '-workbook';
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
             $message->save();
     
             //$user = Auth::user();
             // Save Users of Digital_Asset
             $user = \DB::connection('digitalasset_db')->table('users')->select('id',$user_id)->get();
             //$users = DB::connection('second_db')->table('users')->get();
             
             if ($user->remaining_words != -1) {
                 $user->remaining_words -= $total_used_tokens;
                 $user->save();
             }
     
             if ($user->remaining_words < -1) {
                 $user->remaining_words = 0;
                 $user->save();
             }
     
             echo 'data: [DONE]';
             echo "\n\n";
     
     
         }
         else{
             echo 'data: [Update Failed user not found]';
         }
          
     
     
     
     }

public static  function SMAI_UpdateGPT_DigitalAsset($user_id,$usage,$response,$params)
{
   // Update Token and usage 
   $settings = $this->settings;
   $settings_two = $this->settings_two;



        if(isset($user_id))
   {     
    Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_DigitalAsset from SMAIsyncController : '.$user_id);

        if ($settings->openai_default_model == 'gpt-3.5-turbo') {
            if (isset($response['choices'][0]['delta']['content'])) {
                $message = $response['choices'][0]['delta']['content'];
                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                $output .= $messageFix;
                $responsedText .= $message;
                $total_used_tokens += countWords($messageFix);

                $string_length = Str::length($messageFix);
                $needChars = 6000 - $string_length;
                $random_text = Str::random($needChars);


                echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                ob_flush();
                flush();
                usleep(500);
            }
        } else {
            if (isset($response->choices[0]->text)) {
                $message = $response->choices[0]->text;
                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                $output .= $messageFix;
                $responsedText .= $message;
                $total_used_tokens += countWords($messageFix);

                $string_length = Str::length($messageFix);
                $needChars = 6000 - $string_length;
                $random_text = Str::random($needChars);


                echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                ob_flush();
                flush();
                //usleep(500);
            }
        }

        $params_json = json_decode($params);
        $keywords='';
        $description=$params_json->prompt;
        $creativity=1;
        $number_of_results=1;
        $tone_of_voice=0;
        $maximum_length=2000;
        $post_type = 'paragraph_generator';
        $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
            Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
            ";

         
         $post = OpenAIGenerator::where('slug', $post_type)->first();
         $entry = new UserOpenai();
         $entry->title = 'New Workbook';
         $entry->slug = str()->random(7) . str($user->fullName())->slug() . '-workbook';
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
        $message = DigitalAsset_UserOpenai::whereId($message_id)->first();
        $message->response = $responsedText;
        $message->output = $output;
        $message->hash = Str::random(256);
        $message->credits = $total_used_tokens;
        $message->words = 0;
        $message->save();

        //$user = Auth::user();
        // Save Users of Digital_Asset
        $user = \DB::connection('digitalasset_db')->table('users')->select('id',$user_id)->get();
        //$users = DB::connection('second_db')->table('users')->get();
        
        if ($user->remaining_words != -1) {
            $user->remaining_words -= $total_used_tokens;
            $user->save();
        }

        if ($user->remaining_words < -1) {
            $user->remaining_words = 0;
            $user->save();
        }

        echo 'data: [DONE]';
        echo "\n\n";


    }
    else{
        echo 'data: [Update Failed user not found]';
    }
     



}

public static function SMAI_UpdateGPT_MobileApp($user_id,$usage,$response,$params)
{
    $settings = $this->settings;
    $settings_two = $this->settings_two;

      if(isset($user_id))
   {     

    Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_MobileApp from SMAIsyncController : '.$user_id);
        if ($settings->openai_default_model == 'gpt-3.5-turbo') {
            if (isset($response['choices'][0]['delta']['content'])) {
                $message = $response['choices'][0]['delta']['content'];
                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                $output .= $messageFix;
                $responsedText .= $message;
                $total_used_tokens += countWords($messageFix);

                $string_length = Str::length($messageFix);
                $needChars = 6000 - $string_length;
                $random_text = Str::random($needChars);


                echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                ob_flush();
                flush();
                usleep(500);
            }
        } else {
            if (isset($response->choices[0]->text)) {
                $message = $response->choices[0]->text;
                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                $output .= $messageFix;
                $responsedText .= $message;
                $total_used_tokens += countWords($messageFix);

                $string_length = Str::length($messageFix);
                $needChars = 6000 - $string_length;
                $random_text = Str::random($needChars);


                echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                ob_flush();
                flush();
                //usleep(500);
            }
        }

        $params_json = json_decode($params);
        $keywords='';
        $description=$params_json->prompt;
        $creativity=1;
        $number_of_results=1;
        $tone_of_voice=0;
        $maximum_length=2000;
        $post_type = 'paragraph_generator';
        $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
            Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
            ";

         $post = OpenAIGenerator::where('slug', $post_type)->first();
         $entry = new UserOpenai();
         $entry->title = 'New Workbook';
         $entry->slug = str()->random(7) . str($user->fullName())->slug() . '-workbook';
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
        $message->save();

        //$user = Auth::user();
        // Save Users of Digital_Asset
        $user = \DB::connection('digitalasset_db')->table('users')->select('id',$user_id)->get();
        //$users = DB::connection('second_db')->table('users')->get();
        
        if ($user->remaining_words != -1) {
            $user->remaining_words -= $total_used_tokens;
            $user->save();
        }

        if ($user->remaining_words < -1) {
            $user->remaining_words = 0;
            $user->save();
        }

        echo 'data: [DONE]';
        echo "\n\n";


    }
    else{
        echo 'data: [Update Failed user not found]';
    }
     



}

public static  function SMAI_Update_Main_UserPlans()
{



}

public static  function SMAI_Update_Mobile_UserPlans()
{



}

public static  function SMAI_Update_DigitalAsset_UserPlans()
{



}

public static  function SMAI_Update_SocialPost_UserPlans()
{



}


}