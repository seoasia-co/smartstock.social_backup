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
use App\Models\Setting;
use App\Models\Topic;
use App\Models\TopicCategory;
use App\Models\Webmail;
use App\Models\WebmasterSection;
use App\Models\WebmasterSetting;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Mail;


class SMAIsyncController extends Controller
{


    public function SMAI_UpdateGPT_MainCoIn($user_id,$usage,$data)
{


    if(isset($user_id))
    {     
 
 
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


    public function SMAI_UpdateGPT_SocialPost()
{

            /*  $suggestion = post("suggestion");
        $max_lenght = (int)post("max_lenght");
        $hashtags = (int)post("hashtags"); */
     
 /*        $limit_tokens =  permission("openai_limit_tokens");
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

        if(isset($user_id))
        {     
     
     
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

public static  function SMAI_UpdateGPT_DigitalAsset($user_id,$usage,$data)
{
   // Update Token and usage


        if(isset($user_id))
   {     


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

public static function SMAI_UpdateGPT_MobileApp($user_id,$usage,$data)
{


      if(isset($user_id))
   {     


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

public function SMAI_Update_Main_UserPacakge()
{



}

public function SMAI_Update_Mobile_UserPacakge()
{



}

public function SMAI_Update_DigitalAsset_UserPacakge()
{



}

public function SMAI_Update_SocialPost_UserPacakge()
{



}


}