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

use App\Models\SP_UserCaption;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\File;
//use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI;
use OpenAI\Laravel\Facades\OpenAI as FacadesOpenAI;

use Log;
use DB;


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


    public  function SMAI_UpdateGPT_MainCoIn($user_id,$usage,$response,$params)
{

    $settings = $this->settings;
    $settings_two = $this->settings_two;

    if(isset($user_id))
    {     
        $responsedText="";
        $output="";
        $total_used_tokens=0;
 
        Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_MainCoIn from SMAIsyncController : '.$user_id);
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
                 ob_flush();
                 flush();
                 usleep(500);
             }
             else{
                if(isset($response))
                   {
                            $json_array = json_decode($response, true);
                         if(isset($json_array['choices']))  
                         { 
                            $choices = $json_array['choices'];
                            $postContent = $choices[0]["text"];
                            //Log::debug('Response in else 1st case $postContent2 : '.$json_array['choices'][0]['text']);
                            $postContent=$json_array['choices'][0]['text'];
                            if( Str::length($postContent) > 0 )
                            {
                                $message=trim($postContent);
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
                 ob_flush();
                 flush();
                 //usleep(500);
             }
             else{
                if(isset($response))
                   {
                            $json_array = json_decode($response, true);
                         if(isset($json_array['choices']))  
                         { 
                            $choices = $json_array['choices'];
                            $postContent = $choices[0]["text"];
                            //Log::debug('Response in else 1st case $postContent2 : '.$json_array['choices'][0]['text']);
                            $postContent=$json_array['choices'][0]['text'];
                            if( Str::length($postContent) > 0 )
                            {
                                $message=trim($postContent);
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
         }
 
         $params_json = json_decode($params,true);
        $keywords='';
        $description=$params_json["prompt"];
        $creativity=1;
        $number_of_results=1;
        $tone_of_voice=0;
        $maximum_length=2000;
        $language="en";
        $post_type = 'paragraph_generator';
        $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
            Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
            ";

       
         // Save Users of Digital_Asset
         $user = \DB::connection('main_db')->table('users')->where('id',$user_id)->get();
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
         $message->save();


         
         //Update new remaining Tokens
        $user = \DB::connection('main_db')->table('users')->where('id',$user_id)->get();
        if ($user[0]->remaining_words != -1) {
            $user[0]->remaining_words -= $total_used_tokens;
            $new_remaining_words= $user[0]->remaining_words - $total_used_tokens;
           // $user[0]->save();
           $user_update= DB::connection('main_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words,$user_id));
        }

        if ($user[0]->remaining_words < -1) {
            $user[0]->remaining_words = 0;
           // $user[0]->save();
           $new_remaining_words=0;
           $user_update=DB::connection('main_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words,$user_id));
        }
 
         echo 'data: [DONE]';
         echo "\n\n";
 
 
     }
     else{
         echo 'data: [Update Failed user not found]';
     }
      
 
 
 
 }


    public  function SMAI_UpdateGPT_SocialPost($user_id,$usage,$response,$params)
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
            $responsedText="";
            $output="";
            $total_used_tokens=0;
     
            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_SocialPost from SMAIsyncController : '.$user_id);
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
                     ob_flush();
                     flush();
                     usleep(500);
                 }
                 else{
                    if(isset($response))
                    {
                             $json_array = json_decode($response, true);
                          if(isset($json_array['choices']))  
                          { 
                             $choices = $json_array['choices'];
                             $postContent = $choices[0]["text"];
                             //Log::debug('Response in else 1st case $postContent2 : '.$json_array['choices'][0]['text']);
                             $postContent=$json_array['choices'][0]['text'];
                             if( Str::length($postContent) > 0 )
                             {
                                 $message=trim($postContent);
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
                     ob_flush();
                     flush();
                     //usleep(500);
                 }
                 else{

                   if(isset($response))
                   {
                            $json_array = json_decode($response, true);
                         if(isset($json_array['choices']))  
                         { 
                            $choices = $json_array['choices'];
                            $postContent = $choices[0]["text"];
                            //Log::debug('Response in else 1st case $postContent2 : '.$json_array['choices'][0]['text']);
                            $postContent=$json_array['choices'][0]['text'];
                            if( Str::length($postContent) > 0 )
                            {
                                $message=trim($postContent);
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
             }
     
             $params_json = json_decode($params,true);
        $keywords='';
        $description=$params_json["prompt"];
        $creativity=1;
        $number_of_results=1;
        $tone_of_voice=0;
        $maximum_length=2000;
        $language="en";
        $post_type = 'paragraph_generator';
        $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
            Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
            ";
// Save Users of SocialPOst
$user = \DB::connection('main_db')->table('users')->where('id',$user_id)->get();
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
             $message->save();

             //add to SP_Captions for show in socialpost caption list
        $caption_table="sp_captions";
        $caption_database="main_db";
        $this->SMAI_Ins_Eloq_openAI_Caption_Socialpost($description,$responsedText,$user_id,$message_id,$caption_database,$caption_table);

     
            
             // Save Users of Digital_Asset
             //Update new remaining Tokens
        $user = \DB::connection('main_db')->table('users')->where('id',$user_id)->get();
        if ($user[0]->remaining_words != -1) {
            $user[0]->remaining_words -= $total_used_tokens;
            $new_remaining_words= $user[0]->remaining_words - $total_used_tokens;
           // $user[0]->save();
           $user_update= DB::connection('main_db')->update('update sp_users set remaining_words = ? where id = ?', array($new_remaining_words,$user_id));
        }

        if ($user[0]->remaining_words < -1) {
            $user[0]->remaining_words = 0;
           // $user[0]->save();
           $new_remaining_words=0;
           $user_update=DB::connection('main_db')->update('update sp_users set remaining_words = ? where id = ?', array($new_remaining_words,$user_id));
        }
     
             echo 'data: [DONE]';
             echo "\n\n";
     
     
         }
         else{
             echo 'data: [Update Failed user not found]';
         }
          
     
     
     
     }

public  function SMAI_UpdateGPT_DigitalAsset($user_id,$usage,$response,$params)
{
   // Update Token and usage 
   $settings = $this->settings;
   $settings_two = $this->settings_two;



        if(isset($user_id))
   {    
    
    $responsedText="";
    $output="";
    $total_used_tokens=0;

    Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_DigitalAsset from SMAIsyncController : '.$user_id);

    //$response=json_decode($response,true);

    

    Log::info(print_r($response, true));

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

                Log::debug(' response_choices SMAI_UpdateGPT_DigitalAsset from SMAIsyncController : '.info(print_r($response['choices'], true)));
                echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                ob_flush();
                flush();
                usleep(500);
            }
            else{
                if(isset($response))
                {
                         $json_array = json_decode($response, true);
                      if(isset($json_array['choices']))  
                      { 
                         $choices = $json_array['choices'];
                         $postContent = $choices[0]["text"];
                         //Log::debug('Response in else 1st case $postContent2 : '.$json_array['choices'][0]['text']);
                         $postContent=$json_array['choices'][0]['text'];
                         if( Str::length($postContent) > 0 )
                         {
                             $message=trim($postContent);
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

                Log::debug(' response_choices in else SMAI_UpdateGPT_DigitalAsset from SMAIsyncController : '.info(print_r($response['choices'], true)));

                echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                ob_flush();
                flush();
                //usleep(500);
            }

            else{

                if(isset($response))
                   {
                            $json_array = json_decode($response, true);
                         if(isset($json_array['choices']))  
                         { 
                            $choices = $json_array['choices'];
                            $postContent = $choices[0]["text"];
                            //Log::debug('Response in else 1st case $postContent2 : '.$json_array['choices'][0]['text']);
                            $postContent=$json_array['choices'][0]['text'];
                            if( Str::length($postContent) > 0 )
                            {
                                $message=trim($postContent);
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
        }

        //Log::info(print_r($response, true));
        
        Log::debug('$params SMAI_UpdateGPT_DigitalAsset from SMAIsyncController : '.info(print_r($params, true)));
        
        //Log::debug('$params smaisync_tokens from APIsController : '.info(json_encode($params)));
        Log::info(print_r($params, true));

        $params_json = json_decode($params,true);
        $keywords='';
        $description=$params_json["prompt"];
        $creativity=1;
        $number_of_results=1;
        $tone_of_voice=0;
        $maximum_length=2000;
        $language="en";
        $post_type = 'paragraph_generator';
        $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
            Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
            ";

          // Save Users of Digital_Asset
        $user = \DB::connection('digitalasset_db')->table('users')->where('id',$user_id)->get();
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

         Log::info(print_r("Inserted new openai to ID ".$message_id, true));

        // Create UserOpenai Models belong to OpenAIGenerator Models
        $message = DigitalAsset_UserOpenai::whereId($message_id)->first();
        $message->response = $responsedText;
        $message->output = $output;
        $message->hash = Str::random(256);
        $message->credits = $total_used_tokens;
        $message->words = 0;
        $message->save();

      //Update new remaining Tokens
        $user = \DB::connection('digitalasset_db')->table('users')->where('id',$user_id)->get();
        if ($user[0]->remaining_words != -1) {
            $user[0]->remaining_words -= $total_used_tokens;
            $new_remaining_words= $user[0]->remaining_words - $total_used_tokens;
           // $user[0]->save();
           $user_update= DB::connection('digitalasset_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words,$user_id));
        }

        if ($user[0]->remaining_words < -1) {
            $user[0]->remaining_words = 0;
           // $user[0]->save();
           $new_remaining_words=0;
           $user_update=DB::connection('digitalasset_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words,$user_id));
        }

        echo 'data: [DONE]';
        echo "\n\n";


    }
    else{
        echo 'data: [Update Failed user not found]';
    }
     



}

public function SMAI_UpdateGPT_MobileApp($user_id,$usage,$response,$params)
{
    $settings = $this->settings;
    $settings_two = $this->settings_two;

      if(isset($user_id))
   {     

    $responsedText="";
    $output="";
    $total_used_tokens=0;

    Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_MobileApp from SMAIsyncController : '.$user_id);
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
                ob_flush();
                flush();
                usleep(500);
            }
            else{
                if(isset($response))
                   {
                            $json_array = json_decode($response, true);
                         if(isset($json_array['choices']))  
                         { 
                            $choices = $json_array['choices'];
                            $postContent = $choices[0]["text"];
                            //Log::debug('Response in else 1st case $postContent2 : '.$json_array['choices'][0]['text']);
                            $postContent=$json_array['choices'][0]['text'];
                            if( Str::length($postContent) > 0 )
                            {
                                $message=trim($postContent);
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
                ob_flush();
                flush();
                //usleep(500);
            }
            else{
                if(isset($response))
                   {
                            $json_array = json_decode($response, true);
                         if(isset($json_array['choices']))  
                         { 
                            $choices = $json_array['choices'];
                            $postContent = $choices[0]["text"];
                            //Log::debug('Response in else 1st case $postContent2 : '.$json_array['choices'][0]['text']);
                            $postContent=$json_array['choices'][0]['text'];
                            if( Str::length($postContent) > 0 )
                            {
                                $message=trim($postContent);
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
        }

        $params_json = json_decode($params,true);
        $keywords='';
        $description=$params_json["prompt"];
        $creativity=1;
        $number_of_results=1;
        $tone_of_voice=0;
        $maximum_length=2000;
        $language="en";
        $post_type = 'paragraph_generator';
        $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
            Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
            ";

        
        // Save Users of Mobile App
        $user = \DB::connection('mobileapp_db')->table('users')->where('id',$user_id)->get();
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
        $message->save();

        

        //Update new remaining Tokens
        $user = \DB::connection('mobileapp_db')->table('users')->where('id',$user_id)->get();
        if ($user[0]->remaining_words != -1) {
            $user[0]->remaining_words -= $total_used_tokens;
            $new_remaining_words= $user[0]->remaining_words - $total_used_tokens;
           // $user[0]->save();
           $user_update= DB::connection('mobileapp_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words,$user_id));
        }

        if ($user[0]->remaining_words < -1) {
            $user[0]->remaining_words = 0;
           // $user[0]->save();
           $new_remaining_words=0;
           $user_update=DB::connection('mobileapp_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words,$user_id));
        }

        echo 'data: [DONE]';
        echo "\n\n";


    }
    else{
        echo 'data: [Update Failed user not found]';
    }
     



}

public  function SMAI_Update_Main_UserPlans()
{



}

public  function SMAI_Update_Mobile_UserPlans()
{



}

public  function SMAI_Update_DigitalAsset_UserPlans()
{



}

public  function SMAI_Update_SocialPost_UserPlans()
{



}

public  function SMAI_Check_DigitalAsset_UserColumn($user_id,$key,$database)
{

    /*   Log::debug('User ID  SMAI_Check_DigitalAsset_UserPlans from SMAIsyncController : '.$user_id);
        Log::debug('Key SMAI_Check_DigitalAsset_UserPlans from SMAIsyncController : '.$key);
        Log::debug('Database SMAI_Check_DigitalAsset_UserPlans from SMAIsyncController : '.$database);

    */

    //$database=strval($database);
    if($user_id>0)
    {
    $user = DB::connection($database)->table('users')->where('id', $user_id)->first();
    
    //$user = DB::connection('digitalasset_db')->table('users')->where('id', $user_id)->first();
    
    //$column=strval($key);

    $token_total=$user->remaining_words;
    Log::debug('Remaining Word from DB SMAI_Check_DigitalAsset_UserPlans from SMAIsyncController : '.$token_total);

    echo $token_total;
    return $token_total;

    }
    else{
        return false;
    }


}

    public  function SMAI_Check_SocialPost_UserPlans()
    {



    }

    public  function SMAI_Update_UserColumn()
{




}

public  function SMAI_Update_TableColumn($arr_ids,$database,$table,$data)
{


    $array_of_ids=$arr_ids;
    $table_update = DB::connection($database)->table($table)->whereIn('id', $array_of_ids)->update(array('votes' => 1));



}

public function SMAI_Ins_TableColumn($database,$table,$data_arr)
{
    $createMultipleUsers = [
        [
            'name'=>'Admin',
            'email'=>'admin@techvblogs.com', 
            'password' => bcrypt('TechvBlogs@123')],

        [
            'name'=>'Guest',
            'email'=>'guest@techvblogs.com', 
            'password' => bcrypt('Guest@456')],

        [
            'name'=>'Account',
            'email'=>'account@techvblogs.com', 
            'password' => bcrypt('Account@789')]
        ];

        $data_arr=[];
        $data_arr=$createMultipleUsers;

       // User::insert($createMultipleUsers); // Eloquent

       $table_ins = DB::connection($database)->table($table)->insert($data_arr); 
        // Query Builder

   }

   public function SMAI_Ins_Eloq_openAI_content_TB($user_id,$database,$table)
   {

     //define Users
    $user = \DB::connection($database)->table('users')->where('id',$user_id)->get();
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
    // 



   }

   public function SMAI_QryAll_Eloq_openAI_content_TB($user_id,$database,$table)
   {


   }

   public function SMAI_QryFilter_Eloq_openAI_content_TB($user_id,$database,$table)
   {


   }

   public function SMAI_Ins_Eloq_openAI_Caption_Socialpost($title,$content,$user_id,$openai_id,$database,$table)
   {

    //switch on

    //define Users
    $user = \DB::connection($database)->table('sp_team')->where('owner',$user_id)->get();
    //$users = DB::connection('second_db')->table('users')->get();
    //$post = OpenAIGenerator::where('slug', $post_type)->first();

    $team_id=$user[0]->id;
    if(isset($openai_id) && $openai_id>0)
    $parentid=$openai_id;
    else
    $parentid='';

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

   public  function SMAI_Check_Universal_UserPlans($database,$platform)
    {

        //1.where's the Plan of each $platform
        //2.when the Plan was or will be changed


    }

    public  function SMAI_Update_Universal_UserPlans($database,$platform,$plan_id)
    {

        //1.where's the Plan of each $platform
        //2.when the Plan was or will be changed



    }



}