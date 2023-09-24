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
use App\Models\UserOpenaiChatBio;
use App\Models\UserOpenaiChatMessageBio;
use App\Models\UserOpenaiChatSyncNodeJS;
use App\Models\UserOpenaiChatMessageSyncNodeJS;

use App\Models\OpenaiGeneratorChatCategory;

use App\Models\UserBioOpenai;
use App\Models\UserBio;
use App\Models\SPTeam;
use App\Models\ImagesBio;
use App\Models\Files_SP;

use App\Models\UserSyncNodeJS;
use App\Models\UserSyncNodeJSOpenai;

use App\Models\UserMobile;

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
use function PHPUnit\Framework\lessThanOrEqual;

use App\Http\Controllers\APIs\SMAIUpdateProfileController;
//use File;



/* for update Token and chatGTP usage including Log history of chat GPT data */

class SMAISyncTokenController extends Controller
{
    protected $client;
    protected $settings;
    protected $postContent;
    protected $total_used_tokens;
    protected $GPTModel;
    protected $chat_id;
    const STABLEDIFFUSION = 'stablediffusion';
    const STORAGE_S3 = 's3';
    const STORAGE_LOCAL = 'public';

    protected $response_bk;
    public $chat_role;

    public function __construct($response = NULL, $usage = NULL, $chatGPT_catgory = NULL, $chat_id = NULL)
    {
        //Settings
        $this->settings = Settings::first();
        $this->settings_two = SettingTwo::first();


        if (isset($response)) {
           
            if($response!=NULL)
            {
            $this->response_bk=$response;
            }

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


            if (isset($json_array['usage'])) {
                if (isset($json_array['usage']['total_tokens'])) {
                    $this->total_used_tokens = $json_array['usage']['total_tokens'];
                    Log::debug('Found Total_token : ' . $this->total_used_tokens);
                } else {
                    $this->total_used_tokens = 0;
                }

            }
            if (isset($usage)) {
                $this->total_used_tokens = $usage;
                Log::debug('Found Total_token from Main usage : ' . $this->total_used_tokens);
            }


            //$response['choices'][0]['delta']['content']


            if ($chat_id == NULL || Str::length($chat_id) < 5) {
                $this->chat_id = "chat_";
                $this->chat_id .= strval(time());
                $ran = random_int(100, 999);
                $this->chat_id .= $ran;

            } else {
                $this->chat_id = $chat_id;
            }

            Log::debug('Debug $this_chat_id '. $this->chat_id);


        }


    }

    

    public function get_size($file_path)
    {
        return Storage::disk('s3')->size($file_path);
    }

    public function ids(){
        return uniqid();
    }

    //Working can not find the exactly Table that specific the remaining_words and images
    public function SMAI_UpdateGPT_MainMarketing($user_id, $usage, $response, $params,$from,$main_message_id=NULL)
    {

        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (isset($user_id)) {
            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_MainMarketing from SMAIsyncController : ' . $user_id);

            if(is_array($params))
                $params_json1 = $params;
            else
                $params_json1 = json_decode($params, true);

            if(isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory=NULL;

            $response_bk=$response;
            $response = json_decode($response, true);

            //save chatGPT Chat data to DB
            if (Str::contains($chatGPT_catgory, 'Chat_'))
            {
                //add Sync GPT chat version here

                $total_used_tokens = $usage;
                $chat_id = $this->chat_id;

                $chat_new_ins = UserOpenaiChatMainMarketing::updateOrCreate(
                    ['chat_id' => $chat_id],
                    ['user_id' => $user_id, 'openai_chat_category_id' => 1,]
                );


                //$user_openai_chat_id =UserOpenaiChat::where('chat_id',$chat_id);
                $user_openai_chat_id = DB::connection('main_db')->table('user_openai_chat')->where('chat_id', $chat_id)->first();

                if (isset($user_openai_chat_id->id)) {
                    $user_openai_chat_id_ins = $user_openai_chat_id->id;
                    $message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id, 'chat_id' => $chat_id, 'user_openai_chat_id' => $user_openai_chat_id_ins,]);
                } else {
                    $message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id, 'chat_id' => $chat_id,]);
                }
                $message_id = $message_new_ins->id;


                $time = time();
                $time = intval($time);


                if (isset($user_openai_chat_id->id)) {
                    $user_openai_chat_id_ins = $user_openai_chat_id->id;
                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id, 'user_openai_chat_id_ins' => $user_openai_chat_id_ins,'updated_at' => $time]);

                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id' => $chat_id,
                        'user_openai_chat_id' => $user_openai_chat_id_ins,
                        'conversation_list_id' => $user_openai_chat_id_ins,
                        'updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('main_db')->table('conversation_details')->insertGetId($data_message);

                } else {

                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id,'updated_at' => $time ]);
                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id' => $chat_id,

                        'updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('main_db')->table('conversation_details')->insertGetId($data_message);
                }

                $message_id = $message_new_ins;

                if (isset($response['choices'][0]['delta']['content']))
                    $message_response = $response['choices'][0]['delta']['content'];
                if (isset($response['choices'][0]['message']['content']))
                    $message_response = $response['choices'][0]['message']['content'];

                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $time = strval(time());

                $message = UserOpenaiChatMessageMainMarketing::whereId($message_id)->first();
                $chat = UserOpenaiChatMainMarketing::where('chat_id', $chat_id)->first();
                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }
                $message->output = $output;
                $message->sender = "assistant";
                $message->hash = Str::random(256);

                //save token to chat message and if sum all chat message token = chat_id token
                $message->credits = $total_used_tokens;

                $message->words = 0;
                $message->updated_at = $time;
                $message->save();

                //$user = UserMain::where('id',$user_id);
                $user = DB::connection('main_db')->table('users')->where('id', $user_id)->first();

                $user_email=$user->email;
                $old_remaining_words = $user->remaining_words;

                $new_remaining_words = $old_remaining_words - $total_used_tokens;


                if ($new_remaining_words < 0) {
                    $new_remaining_words = 0;
                }

                $remaining_words_arr = array(
                    'remaining_words' => $new_remaining_words,
                );

                /* $user_update = DB::connection('main_db')->table('users')
                    ->where('id', $user_id)
                    ->update($remaining_words_arr); */

                if(isset($user_update))
                Log::debug('Update#1 of Chat text remaining at MainMarketing success by + add $total_used_tokens to old remaining_words in users table in Main ');

                //$user->save();

                //save token to chat ID
                $chat->total_credits += $total_used_tokens;
                $chat->save();

                $chat_openai_id=$chat->id;
                $save_user_request_chat=array(

                    'chat_id' =>$chat_openai_id,
                    'response' => $responsedText ,
                );


                //Define CHat Role Universal
                $n_prompt = count($params_json1["prompt"]);
                $this->chat_role= $params_json1["prompt"][$n_prompt]["role"];
             
                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt' . $n_prompt);
                $x = intval($n_prompt);

                $description = Arr::last($params_json1["prompt"]);
                $description = implode(" ", $description);

                $save_to_where="MainMarketing";
                $save_user_request_chat["input"]=$description;
                $save_user_q=NEW SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat,$user_id,$save_to_where,$user_email,$from,$this->chat_role);

            } else {

                //save text Doc in others case that not Chat
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


                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

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


                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";


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


                if (is_array($params))
                    $params_json = $params;
                else
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

                if($params_json1["model"] =='whisper-1')
                {
                $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                }
                else
                {
                $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }

                if($params_json1["model"] =='whisper-1')
               {
                $prompt = $description;
                $output  = $response['text'];
               }
                
                $response_arr=json_decode($response_bk,true);

                $entry->user_id = $user_id;
                $entry->openai_id = $post->id;
                $entry->input = $prompt;
                $entry->response = serialize(json_encode($response_arr));
                $entry->output = $output;
                $entry->hash = str()->random(256);
                $entry->credits = 0;
                $entry->words = 0;
                $entry->main_user_openai_id = $main_message_id;
                $entry->save();

                $message_id = $entry->id;

                Log::debug('Message_ID of MainMarketing '.$message_id);


                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = UserOpenai::whereId($message_id)->first();
                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $UserOpenai_saved = $message->save();

                if (!$UserOpenai_saved) {
                    Log::debug('Save OpenAI Log Error ');
                } else {
                    Log::debug('Save UserOpenai Log Success ');
                }

                //Update remaining  to users section
            if (isset($this->total_used_tokens) && $this->total_used_tokens > 0)
            $total_used_tokens = $this->total_used_tokens;

        //Update new remaining Tokens to user
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

        if ($user_update > 0)
            Log::debug('Update remaining at MainMarketing success by + add $total_used_tokens to old remaining_words in users table in Main ');

        //echo 'data: [DONE]';
        //echo "\n\n";

                //EOF case none Chat
            }
            //EOF case Doc text or Chat Text


            


        } else {
            //echo 'data: [Update Failed user not found]';
        }


    }

    //Done
    public function SMAI_UpdateGPT_MainCoIn($user_id, $usage, $response, $params,$from,$main_message_id=NULL)
    {

        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (isset($user_id)) {
            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_MainCoIn from SMAIsyncController : ' . $user_id);

            if(is_array($params))
                $params_json1 = $params;
            else
                $params_json1 = json_decode($params, true);

            if(isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory=NULL;
            
            $response_bk=$response;
            $response = json_decode($response, true);
            if (Str::contains($chatGPT_catgory, 'Chat_')) {
                //add Sync GPT chat version here

                $total_used_tokens = $usage;
                $chat_id = $this->chat_id;

                $chat_new_ins = UserOpenaiChat::updateOrCreate(
                    ['chat_id' => $chat_id],
                    ['user_id' => $user_id, 'openai_chat_category_id' => 1,]
                );

                /* $chat_new_ins = DB::connection('main_db')->table('user_openai_chat')->updateOrCreate(
                     ['chat_id' => $chat_id],
                     ['user_id' => $user_id, 'openai_chat_category_id' => 1,]
                 );*/

                //$user_openai_chat_id =UserOpenaiChat::where('chat_id',$chat_id);
                $user_openai_chat_id = DB::connection('main_db')->table('user_openai_chat')->where('chat_id', $chat_id)->first();

                $time = time();
                $time = intval($time);
                if (isset($user_openai_chat_id->id)) {
                    $user_openai_chat_id_ins = $user_openai_chat_id->id;
                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id, 'user_openai_chat_id_ins' => $user_openai_chat_id_ins,'updated_at' => $time]);

                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id' => $chat_id,
                        'user_openai_chat_id' => $user_openai_chat_id_ins,
                        //updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('main_db')->table('user_openai_chat_messages')->insertGetId($data_message);

                } else {

                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id,'updated_at' => $time ]);
                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id' => $chat_id,
                        //updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('main_db')->table('user_openai_chat_messages')->insertGetId($data_message);
                }

                $message_id = $message_new_ins;

                //$chat_new_ins = UserOpenaiChat::create(['user_id' => $user_id,'openai_chat_category_id' => 1,'chat_id' => $chat_id]);


                if (isset($response['choices'][0]['delta']['content']))
                    $message_response = $response['choices'][0]['delta']['content'];
                if (isset($response['choices'][0]['message']['content']))
                    $message_response = $response['choices'][0]['message']['content'];

                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $message = UserOpenaiChatMessage::whereId($message_id)->first();
                $chat = UserOpenaiChat::where('chat_id', $chat_id)->first();
                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $message->save();

                //$user = UserMain::where('id',$user_id);
                $user1 = DB::connection('main_db')->table('users')->where('id', $user_id)->first();
                //$user = \DB::connection('main_db')->table('users')->where('id', $user_id)->get();

                $user_email=$user1->email;
                Log::debug('FOund user main Email ' . $user1->email);
                $old_remaining_words = $user1->remaining_words;
                $new_remaining_words = $old_remaining_words - $total_used_tokens;


                if ($new_remaining_words < 0) {
                    $new_remaining_words = 0;
                }

                $remaining_words_arr = array(
                    'remaining_words' => $new_remaining_words,
                );

 

                $chat->total_credits += $total_used_tokens;
                $chat->save();

                $chat_openai_id=$chat->id;
                $save_user_request_chat=array(

                    'chat_id' =>$chat_openai_id,
                    'response' => $responsedText ,
                );

               
                $n_prompt = count($params_json1["prompt"]);

               if (isset($response['choices'][0]['message']['role']))
               $this->chat_role= $response['choices'][0]['message']['role'];
               else
               $this->chat_role= $params_json1["prompt"][$n_prompt]["role"];
               
                 //Define CHat Role Universal
                 
                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt ' . $n_prompt);
                
                $role_of_previous_chat = $params_json1["prompt"][$n_prompt]["role"];
                Log::debug('Which Role is : '.$this->chat_role);
               

                $description = $params_json1["prompt"][$n_prompt]["content"];

                
                $save_to_where="MainCoIn";
                $save_user_request_chat["input"]=$description;
                $save_user_q=NEW SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat,$user_id,$save_to_where,$user_email,$from,$this->chat_role);




            } else {


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


                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

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


                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";


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


                if (is_array($params))
                    $params_json = $params;
                else
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

                if($params_json1["model"] =='whisper-1')
                {
                $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                }
                else
                {
                $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }

                if($params_json1["model"] =='whisper-1')
               {
                $prompt = $description;
                $output  = $response['text'];
               }

                $response_arr=json_decode($response_bk,true);
                
                $entry->user_id = $user_id;
                $entry->openai_id = $post->id;
                $entry->input = $prompt;
                $entry->response = serialize(json_encode($response_arr));
                $entry->output = $output;
                $entry->hash = str()->random(256);
                $entry->credits = 0;
                $entry->words = 0;
                $entry->save();


                $message_id = $entry->id;
                Log::debug('Message_ID of MainCoIn '.$message_id);

                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = UserOpenai::whereId($message_id)->first();
                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $UserOpenai_saved = $message->save();

                if (!$UserOpenai_saved) {
                    Log::debug('Save OpenAI Log Error ');
                } else {
                    Log::debug('Save UserOpenai Log Success ');
                }
                
                if($message_id == $entry->id)
                 return $message_id;



                 //Update remaining  to users section
            if (isset($this->total_used_tokens) && $this->total_used_tokens > 0)
            $total_used_tokens = $this->total_used_tokens;

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

        if ($user_update > 0)
            Log::debug('Update remaining at MainCoIn by + add $total_used_tokens to old remaining_words in users table in users Main success');

        //echo 'data: [DONE]';
        //echo "\n\n";


            }

           


        } else {
            //echo 'data: [Update Failed user not found]';
        }


    }


    //Done
    public function SMAI_UpdateGPT_SocialPost($user_id, $usage, $response, $params,$from,$main_message_id=NULL)
    {

        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (isset($user_id)) {
            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_SocialPost from SMAIsyncController : ' . $user_id);

            if(is_array($params))
                $params_json1 = $params;
            else
                $params_json1 = json_decode($params, true);

            if(isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory=NULL;

            $response_bk=$response;
            $response = json_decode($response, true);
            if (Str::contains($chatGPT_catgory, 'Chat_')) {
                //add Sync GPT chat version here

                $total_used_tokens = $usage;
                $chat_id = $this->chat_id;

                $chat_new_ins = UserOpenaiChatSocialPost::updateOrCreate(
                    ['chat_id' => $chat_id],
                    ['user_id' => $user_id, 'openai_chat_category_id' => 1,]
                );
                /*$chat_new_ins = DB::connection('main_db')->table('sp_user_openai_chat')->updateOrCreate(
                    ['chat_id' => $chat_id],
                    ['user_id' => $user_id, 'openai_chat_category_id' => 1,]
                );*/

                //$user_openai_chat_id =UserOpenaiChat::where('chat_id',$chat_id);
                $user_openai_chat_id = DB::connection('main_db')->table('sp_user_openai_chat')->where('chat_id', $chat_id)->first();

                $time = time();
                //$time = intval($time);
                if (isset($user_openai_chat_id->id)) {
                    $user_openai_chat_id_ins = $user_openai_chat_id->id;
                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id, 'user_openai_chat_id_ins' => $user_openai_chat_id_ins,'updated_at' => $time]);

                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id' => $chat_id,
                        'user_openai_chat_id' => $user_openai_chat_id_ins,
                        //updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('main_db')->table('sp_user_openai_chat_messages')->insertGetId($data_message);

                } else {

                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id,'updated_at' => $time ]);
                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id' => $chat_id,
                        //updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('main_db')->table('sp_user_openai_chat_messages')->insertGetId($data_message);
                }

                $message_id = $message_new_ins;

                if (isset($response['choices'][0]['delta']['content']))
                    $message_response = $response['choices'][0]['delta']['content'];
                if (isset($response['choices'][0]['message']['content']))
                    $message_response = $response['choices'][0]['message']['content'];

                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $message = UserOpenaiChatMessageSocialPost::whereId($message_id)->first();
                $chat = UserOpenaiChatSocialPost::where('chat_id', $chat_id)->first();
                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $message->save();

                //$user = UserSP::where('id',$user_id);
                $user = DB::connection('main_db')->table('sp_users')->where('id', $user_id)->first();

                $user_email=$user->email;
                $old_remaining_words = $user->remaining_words;

                $new_remaining_words = $old_remaining_words - $total_used_tokens;


                if ($new_remaining_words < 0) {
                    $new_remaining_words = 0;
                }

                $remaining_words_arr = array(
                    'remaining_words' => $new_remaining_words,
                );

                /* $user_update = DB::connection('main_db')->table('sp_users')
                    ->where('id', $user_id)
                    ->update($remaining_words_arr); */

                //$user->save();

                $chat->total_credits += $total_used_tokens;
                $chat->save();

                $chat_openai_id=$chat->id;
                $save_user_request_chat=array(

                    'chat_id' =>$chat_openai_id,
                    'response' => $responsedText ,
                );
                //Define CHat Role Universal
                $n_prompt = count($params_json1["prompt"]);
                

                if (isset($response['choices'][0]['message']['role']))
                $this->chat_role= $response['choices'][0]['message']['role'];
                else
                $this->chat_role= $params_json1["prompt"][$n_prompt]["role"];
                
                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }


                Log::debug('Count n_prompt' . $n_prompt);
                /* Log::info($params_json1["prompt"][0]["content"]);
                 Log::info($params_json1["prompt"][1]["content"]);
                 Log::info($params_json1["prompt"][2]["content"]);*/
                $x = intval($n_prompt);

                $description = Arr::last($params_json1["prompt"]);
                $description = implode(" ", $description);

                Log::debug('Desc after convert to string' . $description);

                $save_to_where="SocialPost";
                $save_user_request_chat["input"]=$description;
                $save_user_q=NEW SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat,$user_id,$save_to_where,$user_email,$from,$this->chat_role);


            } else {
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


                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

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


                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

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

                if (is_array($params))
                    $params_json = $params;
                else
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

                if($params_json1["model"] =='whisper-1')
                {
                $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                }
                else
                {
                $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }

                if($params_json1["model"] =='whisper-1')
               {
                $prompt = $description;
                $output  = $response['text'];
               }

                $response_arr=json_decode($response_bk,true);

                $entry->user_id = $user_id;
                $entry->openai_id = $post->id;
                $entry->input = $prompt;
                $entry->response = serialize(json_encode($response_arr));
                $entry->output = $output;
                $entry->hash = str()->random(256);
                $entry->credits = 0;
                $entry->words = 0;
                $entry->main_user_openai_id = $main_message_id;
                $entry->save();

                $message_id = $entry->id;
                Log::debug('Message_ID of SocialPost '.$message_id);

                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = SP_UserOpenai::whereId($message_id)->first();
                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;


                $UserOpenai_saved = $message->save();

                if (!$UserOpenai_saved) {
                    Log::debug('Save OpenAI Socialpost Log Error ');
                } else {
                    Log::debug('Save SP_UserOpenai Log Success ');
                }

                //Update remaining  to users section
            if (isset($this->total_used_tokens) && $this->total_used_tokens > 0)
            $total_used_tokens = $this->total_used_tokens;

        Log::debug('before update TOken numbers remaining_words');
        // Save Users of Digital_Asset
        //Update new remaining Tokens
        $user = \DB::connection('main_db')->table('sp_users')->where('id', $user_id)->get();
        Log::debug('User email test : ' . $user[0]->email);

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
        if ($user_update > 0)
            Log::debug('Update remaining at Main Socialpost by + add $total_used_tokens to old remaining_words in table sp_users in success');

        //echo 'data: [DONE]';
        //echo "\n\n";



            }

            Log::debug('before Skip Caption _Socialpost');
            if (Str::contains($chatGPT_catgory, 'Chat_') == false) {
                //add to SP_Captions for show in socialpost caption list
                Log::debug('before insert Caption _Socialpost');
                $caption_table = "sp_captions";
                $caption_database = "main_db";
                $this->SMAI_Ins_Eloq_openAI_Caption_Socialpost($description, $responsedText, $user_id, $message_id, $caption_database, $caption_table);
           
            
            }


            

        } else {
            //echo 'data: [Update Failed user not found]';
        }


    }


    //Done
    public function SMAI_UpdateGPT_DigitalAsset($user_id, $usage, $response, $params,$from,$main_message_id=NULL)
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

            if(is_array($params))
                $params_json1 = $params;
            else
                $params_json1 = json_decode($params, true);

            if(isset($params_json1['gpt_category']))
            $chatGPT_catgory = $params_json1['gpt_category'];
            else
            $chatGPT_catgory=NULL;

            $response_bk=$response;
            $response = json_decode($response, true);
            if (Str::contains($chatGPT_catgory, 'Chat_')) {
                //add Sync GPT chat version here

                $total_used_tokens = $usage;
                $chat_id = $this->chat_id;

                Log::debug(' Chat ID before create Eloquent UserOpenaiChatDesign' . $chat_id);

                $chat_new_ins = UserOpenaiChatDesign::updateOrCreate(
                    ['chat_id' => $chat_id],
                    ['user_id' => $user_id, 'openai_chat_category_id' => 1,]
                );

                //$user_openai_chat_id =UserOpenaiChat::where('chat_id',$chat_id);
                $user_openai_chat_id = DB::connection('digitalasset_db')->table('user_openai_chat')->where('chat_id', $chat_id)->first();

                $time = time();
                //$time = intval($time);
                if (isset($user_openai_chat_id->id)) {
                    $user_openai_chat_id_ins = $user_openai_chat_id->id;
                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id, 'user_openai_chat_id_ins' => $user_openai_chat_id_ins,'updated_at' => $time]);

                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id' => $chat_id,
                        'user_openai_chat_id' => $user_openai_chat_id_ins,
                        //updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('digitalasset_db')->table('user_openai_chat_messages')->insertGetId($data_message);

                } else {

                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id,'updated_at' => $time ]);
                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id' => $chat_id,
                        //updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('digitalasset_db')->table('user_openai_chat_messages')->insertGetId($data_message);
                }

                $message_id = $message_new_ins;

                if (isset($response['choices'][0]['delta']['content']))
                    $message_response = $response['choices'][0]['delta']['content'];
                if (isset($response['choices'][0]['message']['content']))
                    $message_response = $response['choices'][0]['message']['content'];


                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $message = UserOpenaiChatMessageDesign::whereId($message_id)->first();
                $chat = UserOpenaiChatDesign::where('chat_id', $chat_id)->first();
                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $message->save();

                //$user = UserDesign::where('id',$user_id);
                $user = DB::connection('digitalasset_db')->table('users')->where('id', $user_id)->first();

                $user_email=$user->email;
                Log::debug('Debug UderDesign from Eloqunt ');
                //Log::info($user);

                $old_remaining_words = $user->remaining_words;

                $new_remaining_words = $old_remaining_words - $total_used_tokens;


                if ($new_remaining_words < 0) {
                    $new_remaining_words = 0;
                }

                $remaining_words_arr = array(
                    'remaining_words' => $new_remaining_words,
                );

                /* $user_update = DB::connection('digitalasset_db')->table('users')
                    ->where('id', $user_id)
                    ->update($remaining_words_arr); */

                //$user->save();

                $chat->total_credits += $total_used_tokens;
                $chat->save();

                $chat_openai_id=$chat->id;
                $save_user_request_chat=array(

                    'chat_id' =>$chat_openai_id,
                    'response' => $responsedText ,
                );

                //Define CHat Role Universal
                $n_prompt = count($params_json1["prompt"]);

               if (isset($response['choices'][0]['message']['role']))
               $this->chat_role= $response['choices'][0]['message']['role'];
               else
               $this->chat_role= $params_json1["prompt"][$n_prompt]["role"];

               
                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt' . $n_prompt);
                $x = intval($n_prompt);

                $description = Arr::last($params_json1["prompt"]);
                $description = implode(" ", $description);

                $save_to_where="Design";
                $save_user_request_chat["input"]=$description;
                $save_user_q=NEW SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat,$user_id,$save_to_where,$user_email,$from,$this->chat_role);


            } else {

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
                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

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

                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

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

                if (is_array($params))
                    $params_json = $params;
                else
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

                if($params_json1["model"] =='whisper-1')
                {

                $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                }
                else
                {
                $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }
                //$response = json_encode($response);

               Log::debug(' REsponse String');
               Log::info($response_bk);
               if($params_json1["model"] =='whisper-1')
               {
                $prompt = $description;
                $output  = $response['text'];
               }

               $response_arr=json_decode($response_bk,true);
               


                $entry->user_id = $user_id;
                $entry->openai_id = $post->id;
                $entry->input = $prompt;
                $entry->response = serialize(json_encode($response_arr));
                $entry->output = $output;
                $entry->hash = str()->random(256);
                $entry->credits = 0;
                $entry->words = 0;
                $entry->main_user_openai_id = $main_message_id;
                $entry->save();

                $message_id = $entry->id;
                Log::debug('Message_ID of DigitalAsset Design '.$message_id);

                Log::info(print_r("Inserted new openai to ID " . $message_id, true));

                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = DigitalAsset_UserOpenai::whereId($message_id)->first();

                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }

                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $UserOpenai_saved = $message->save();

                if (!$UserOpenai_saved) {
                    Log::debug('Save OpenAI Design Log Error ');
                } else {
                    Log::debug('Save DigitalAsset Log Success ');
                }

                //Update remaining  to users section
                if (isset($this->total_used_tokens) && $this->total_used_tokens > 0)
                $total_used_tokens = $this->total_used_tokens;

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

                if ($user_update > 0)
                Log::debug('Update remaining at Design by + add $total_used_tokens to old remaining_words in users table in Design success');

                //echo 'data: [DONE]';
                //echo "\n\n";


            }

            


        } else {
            //echo 'data: [Update Failed user not found]';
        }


    }

//Done
    public function SMAI_UpdateGPT_MobileApp($user_id, $usage, $response, $params,$from,$main_message_id=NULL)
    {
        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (isset($user_id)) {

            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_MobileApp from SMAIsyncController : ' . $user_id);

            if(is_array($params))
                $params_json1 = $params;
            else
            $params_json1 = json_decode($params, true);


            if(isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory=NULL;

            $response_bk=  $response;
            $response = json_decode($response, true);
            if (Str::contains($chatGPT_catgory, 'Chat_')) {
                //add Sync GPT chat version here

                $total_used_tokens = $usage;
                $chat_id = $this->chat_id;


                $chat_new_ins = UserOpenaiChatMobile::updateOrCreate(
                    ['chat_id' => $chat_id],
                    ['user_id' => $user_id, 'openai_chat_category_id' => 1,]
                );

                //$user_openai_chat_id =UserOpenaiChat::where('chat_id',$chat_id);
                $user_openai_chat_id = DB::connection('mobileapp_db')->table('willdev_user_chat')->where('chat_id', $chat_id)->first();

                $time = time();
                //$time = intval($time);
                if (isset($user_openai_chat_id->id)) {
                    $user_openai_chat_id_ins = $user_openai_chat_id->id;
                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id, 'user_openai_chat_id_ins' => $user_openai_chat_id_ins,'updated_at' => $time]);

                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id' => $chat_id,
                        'user_openai_chat_id' => $user_openai_chat_id_ins,
                        //updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('mobileapp_db')->table('user_openai_chat_messages')->insertGetId($data_message);

                } else {

                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id,'updated_at' => $time ]);
                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id' => $chat_id,
                        //updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('mobileapp_db')->table('user_openai_chat_messages')->insertGetId($data_message);
                }

                $message_id = $message_new_ins;


                if (isset($response['choices'][0]['delta']['content']))
                    $message_response = $response['choices'][0]['delta']['content'];
                if (isset($response['choices'][0]['message']['content']))
                    $message_response = $response['choices'][0]['message']['content'];

                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $message = UserOpenaiChatMessageMobile::whereId($message_id)->first();
                $chat = UserOpenaiChatMobile::where('chat_id', $chat_id)->first();
                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $message->save();

                $user = DB::connection('mobileapp_db')->table('users')->where('id', $user_id)->first();
                //$user = UserMain::where('id',$user_id);

                $user_email=$user->email;
                $old_remaining_words = $user->remaining_words;

                $new_remaining_words = $old_remaining_words - $total_used_tokens;


                if ($new_remaining_words < 0) {
                    $new_remaining_words = 0;
                }
                $remaining_words_arr = array(
                    'remaining_words' => $new_remaining_words,
                );


                /* $user_update = DB::connection('mobileapp_db')->table('users')
                    ->where('id', $user_id)
                    ->update($remaining_words_arr); */

                //$user->save();


                $chat->total_credits += $total_used_tokens;
                $chat->save();

                $chat_openai_id=$chat->id;
                $save_user_request_chat=array(

                    'chat_id' =>$chat_openai_id,
                    'response' => $responsedText ,
                );

                //Define CHat Role Universal
                $n_prompt = count($params_json1["prompt"]);

               if (isset($response['choices'][0]['message']['role']))
               $this->chat_role= $response['choices'][0]['message']['role'];
               else
               $this->chat_role= $params_json1["prompt"][$n_prompt]["role"];

               
                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt' . $n_prompt);
                $x = intval($n_prompt);

                $description = Arr::last($params_json1["prompt"]);
                $description = implode(" ", $description);

                $save_to_where="MobileAppV2";
                $save_user_request_chat["input"]=$description;
                $save_user_q=NEW SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat,$user_id,$save_to_where,$user_email,$from,$this->chat_role);

            } else {


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


                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

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


                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

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

                if (is_array($params))
                    $params_json = $params;
                else
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

                if($params_json1["model"] =='whisper-1')
                {

                $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                }
                else
                {
                $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }

                if($params_json1["model"] =='whisper-1')
               {
                $prompt = $description;
                $output  = $response['text'];
               }
                
                
                $response_arr=json_decode($response_bk,true);
                $entry->user_id = $user_id;
                $entry->openai_id = $post->id;
                $entry->input = $prompt;
                $entry->response = serialize(json_encode($response_arr));
                $entry->output = $output;
                $entry->hash = str()->random(256);
                $entry->credits = 0;
                $entry->words = 0;
                $entry->main_user_openai_id = $main_message_id;
                $entry->save();

                $message_id = $entry->id;
                Log::debug('Message_ID of MobileAppV2 '.$message_id);

                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = Mobile_UserOpenai::whereId($message_id)->first();
                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $UserOpenai_saved = $message->save();

                if (!$UserOpenai_saved) {
                    Log::debug('Save OpenAI Mobile Log Error ');
                } else {
                    Log::debug('Save Mobile Log Success ');
                }
                //Update remaining  to users section

            if (isset($this->total_used_tokens) && $this->total_used_tokens > 0)
            $total_used_tokens = $this->total_used_tokens;

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
        if ($user_update > 0)
            Log::debug('Update remaining at MobileApp by + add $total_used_tokens to old remaining_words in users table in Mobileapp success');

        //echo 'data: [DONE]';
        //echo "\n\n";

            }


            


        } else {
            //echo 'data: [Update Failed user not found]';
        }


    }
    public function SMAI_UpdateGPT_Bio($user_id, $usage, $response, $params,$from,$main_message_id=NULL)
    {
        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (isset($user_id)) {

            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_Bio from SMAIsyncController : ' . $user_id);

            if(is_array($params))
                $params_json1 = $params;
            else
            $params_json1 = json_decode($params, true);


            if(isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory=NULL;

            $response_bk=  $response;
            $response = json_decode($response, true);
            if (Str::contains($chatGPT_catgory, 'Chat_')) {
                //add Sync GPT chat version here

                $total_used_tokens = $usage;
                $chat_id = $this->chat_id;


                Log::debug('Debug before Insert $this_chat_id'.$this->chat_id);

                Log::debug('THis local Chat ID '.$chat_id);

                $chat_new_ins = UserOpenaiChatBio::updateOrCreate(
                    ['chat_id_mobile' => $this->chat_id],
                    ['user_id' => $user_id, 'openai_chat_category_id' => 1,]
                );

                Log::debug('!!!!!!!!!!!!!!!!!! important new chats record in Bio !!!!!!!!!!!!!!!!!');
                Log::info($chat_new_ins);

                //$user_openai_chat_id =UserOpenaiChat::where('chat_id',$chat_id);
                $user_openai_chat_id = DB::connection('bio_db')->table('chats')->where('chat_id_mobile', $chat_id)->first();

                $time = time();
                //$time = intval($time);
                if (isset($user_openai_chat_id->chat_id)) {
                    $user_openai_chat_id_ins = $user_openai_chat_id->chat_id;
                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id, 'user_openai_chat_id_ins' => $user_openai_chat_id_ins,'updated_at' => $time]);

                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id_mobile' => $chat_id,
                        'user_openai_chat_id' => $user_openai_chat_id_ins,
                        //updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('bio_db')->table('chats_messages')->insertGetId($data_message,'chat_message_id');

                } else {

                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id,'updated_at' => $time ]);
                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id_mobile' => $chat_id,
                        //updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('bio_db')->table('chats_messages')->insertGetId($data_message,'chat_message_id');
                }

                $message_id = $message_new_ins;


                if (isset($response['choices'][0]['delta']['content']))
                    $message_response = $response['choices'][0]['delta']['content'];
                if (isset($response['choices'][0]['message']['content']))
                    $message_response = $response['choices'][0]['message']['content'];

                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $message = UserOpenaiChatMessageBio::where('chat_message_id',$message_id)->first();
                $chat = UserOpenaiChatBio::where('chat_id_mobile', $chat_id)->first();
                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }
               
                
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $message->save();

                $user = DB::connection('bio_db')->table('users')->where('user_id', $user_id)->first();
                //$user = UserMain::where('id',$user_id);

                $user_email=$user->email;
                $old_remaining_words = $user->remaining_words;

                $new_remaining_words = $old_remaining_words - $total_used_tokens;


                if ($new_remaining_words < 0) {
                    $new_remaining_words = 0;
                }
                $remaining_words_arr = array(
                    'remaining_words' => $new_remaining_words,
                );


                /* $user_update = DB::connection('mobileapp_db')->table('users')
                    ->where('id', $user_id)
                    ->update($remaining_words_arr); */

                //$user->save();

                //Define CHat Role Universal
                $n_prompt = count($params_json1["prompt"]);

               if (isset($response['choices'][0]['message']['role']))
               $this->chat_role= $response['choices'][0]['message']['role'];
               else
               $this->chat_role= $params_json1["prompt"][$n_prompt]["role"];
                
                
                if($n_prompt-1 == 0)
                {
                    $n_prompt-=1;
                    $name_chat= $params_json1["prompt"][$n_prompt]["content"];
                    $chat->name = $name_chat;
                }

                $chat->chat_assistant_id =1;
                $chat->settings = '[]';
                $chat->used_tokens += $total_used_tokens;
                $chat->total_credits += $total_used_tokens;
                $chat->save();

                $chat_openai_id=$chat->chat_id;
                $save_user_request_chat=array(

                    'chat_id_mobile' =>$chat_openai_id,
                    'response' => $responsedText ,
                );

                

                $n_prompt = count($params_json1["prompt"]);
                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt' . $n_prompt);
                $x = intval($n_prompt);

                $description = Arr::last($params_json1["prompt"]);
                $description = implode(" ", $description);

                $save_to_where="Bio";
                $save_user_request_chat["input"]=$description;
                $save_user_q=NEW SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat,$user_id,$save_to_where,$user_email,$from,$this->chat_role);

            } else {


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


                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

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


                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

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

                if (is_array($params))
                    $params_json = $params;
                else
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
                $user = \DB::connection('bio_db')->table('user')->where('id', $user_id)->get();
                //$users = DB::connection('second_db')->table('users')->get();


                $post = OpenAIGenerator::where('slug', $post_type)->first();
                $entry = new UserBioOpenai();
                $entry->title = 'New Workbook';

                if($params_json1["model"] =='whisper-1')
                {

                $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                }
                else
                {
                $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }

                if($params_json1["model"] =='whisper-1')
               {
                $prompt = $description;
                $output  = $response['text'];
               }
                
                
                $response_arr=json_decode($response_bk,true);
                $entry->user_id = $user_id;
                $entry->openai_id = $post->id;
                $entry->input = $prompt;
                $entry->response = serialize(json_encode($response_arr));
                $entry->output = $output;
                $entry->hash = str()->random(256);
                $entry->credits = 0;
                $entry->words = 0;
                $entry->main_user_openai_id = $main_message_id;
                $entry->save();

                $message_id = $entry->id;
                Log::debug('Message_ID of MobileAppV2 '.$message_id);

                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = UserBioOpenai::whereId($message_id)->first();
                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $UserOpenai_saved = $message->save();

                if (!$UserOpenai_saved) {
                    Log::debug('Save OpenAI Mobile Log Error ');
                } else {
                    Log::debug('Save Mobile Log Success ');
                }
                //Update remaining  to users section

            if (isset($this->total_used_tokens) && $this->total_used_tokens > 0)
            $total_used_tokens = $this->total_used_tokens;

        //Update new remaining Tokens
        $user = \DB::connection('bio_db')->table('users')->where('id', $user_id)->get();
        if ($user[0]->remaining_words != -1) {
            $user[0]->remaining_words -= $total_used_tokens;
            $new_remaining_words = $user[0]->remaining_words - $total_used_tokens;
            // $user[0]->save();
            $user_update = DB::connection('bio_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));

        }

        if ($user[0]->remaining_words < -1) {
            $user[0]->remaining_words = 0;
            // $user[0]->save();
            $new_remaining_words = 0;
            $user_update = DB::connection('bio_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));


        }
        if ($user_update > 0)
            Log::debug('Update remaining at MobileApp by + add $total_used_tokens to old remaining_words in users table in Mobileapp success');

        //echo 'data: [DONE]';
        //echo "\n\n";

            }


            


        } else {
            //echo 'data: [Update Failed user not found]';
        }


    }

    public function SMAI_UpdateGPT_SyncNodeJs($user_id, $usage, $response, $params,$from,$main_message_id=NULL)
    {
        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (isset($user_id)) {

            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_SyncNodeJS from SMAIsyncController : ' . $user_id);

            if(is_array($params))
                $params_json1 = $params;
            else
            $params_json1 = json_decode($params, true);


            if(isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory=NULL;

            $response_bk=  $response;
            $response = json_decode($response, true);
            if (Str::contains($chatGPT_catgory, 'Chat_')) {
                //add Sync GPT chat version here

                $total_used_tokens = $usage;
                $chat_id = $this->chat_id;


                $chat_new_ins = UserOpenaiChatSyncNodeJS::updateOrCreate(
                    ['chat_id' => $chat_id],
                    ['user_id' => $user_id, 'openai_chat_category_id' => 1,]
                );

                //$user_openai_chat_id =UserOpenaiChat::where('chat_id',$chat_id);
                $user_openai_chat_id = DB::connection('sync_db')->table('user_openai_chat')->where('chat_id', $chat_id)->first();

                $time = time();
                //$time = intval($time);
                if (isset($user_openai_chat_id->id)) {
                    $user_openai_chat_id_ins = $user_openai_chat_id->id;
                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id, 'user_openai_chat_id_ins' => $user_openai_chat_id_ins,'updated_at' => $time]);

                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id' => $chat_id,
                        'user_openai_chat_id' => $user_openai_chat_id_ins,
                        //updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('sync_db')->table('stt')->insertGetId($data_message);

                } else {

                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id,'updated_at' => $time ]);
                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id' => $chat_id,
                        //updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('sync_db')->table('stt')->insertGetId($data_message);
                }

                $message_id = $message_new_ins;


                if (isset($response['choices'][0]['delta']['content']))
                    $message_response = $response['choices'][0]['delta']['content'];
                if (isset($response['choices'][0]['message']['content']))
                    $message_response = $response['choices'][0]['message']['content'];

                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $message = UserOpenaiChatMessageSyncNodeJS::whereId($message_id)->first();
                $chat = UserOpenaiChatSyncNodeJS::where('chat_id', $chat_id)->first();
                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $message->save();

                $user = DB::connection('sync_db')->table('user')->where('id', $user_id)->first();
                //$user = UserMain::where('id',$user_id);

                $user_email=$user->email;
                $old_remaining_words = $user->remaining_words;

                $new_remaining_words = $old_remaining_words - $total_used_tokens;


                if ($new_remaining_words < 0) {
                    $new_remaining_words = 0;
                }
                $remaining_words_arr = array(
                    'remaining_words' => $new_remaining_words,
                );


                /* $user_update = DB::connection('mobileapp_db')->table('users')
                    ->where('id', $user_id)
                    ->update($remaining_words_arr); */

                //$user->save();


                $chat->total_credits += $total_used_tokens;
                $chat->save();

                $chat_openai_id=$chat->id;
                $save_user_request_chat=array(

                    'chat_id' =>$chat_openai_id,
                    'response' => $responsedText ,
                );

                //Define CHat Role Universal
                $n_prompt = count($params_json1["prompt"]);

               if (isset($response['choices'][0]['message']['role']))
               $this->chat_role= $response['choices'][0]['message']['role'];
               else
               $this->chat_role= $params_json1["prompt"][$n_prompt]["role"];

               
                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt' . $n_prompt);
                $x = intval($n_prompt);

                $description = Arr::last($params_json1["prompt"]);
                $description = implode(" ", $description);

                $save_to_where="SyncNodeJS";
                $save_user_request_chat["input"]=$description;
                $save_user_q=NEW SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat,$user_id,$save_to_where,$user_email,$from,$this->chat_role);

            } else {


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


                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

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


                        //echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";

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

                if (is_array($params))
                    $params_json = $params;
                else
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
                $user = \DB::connection('sync_db')->table('users')->where('id', $user_id)->get();
                //$users = DB::connection('second_db')->table('users')->get();


                $post = OpenAIGenerator::where('slug', $post_type)->first();
                $entry = new UserSyncNodeJSOpenai();
                $entry->title = 'New Workbook';

                if($params_json1["model"] =='whisper-1')
                {

                $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                }
                else
                {
                $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }

                if($params_json1["model"] =='whisper-1')
               {
                $prompt = $description;
                $output  = $response['text'];
               }
                
                
                $response_arr=json_decode($response_bk,true);
                $entry->user_id = $user_id;
                $entry->openai_id = $post->id;
                $entry->input = $prompt;
                $entry->response = serialize(json_encode($response_arr));
                $entry->output = $output;
                $entry->hash = str()->random(256);
                $entry->credits = 0;
                $entry->words = 0;
                $entry->main_user_openai_id = $main_message_id;
                $entry->save();

                $message_id = $entry->id;
                Log::debug('Message_ID of MobileAppV2 '.$message_id);

                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = UserSyncNodeJSOpenai::whereId($message_id)->first();
                if($params_json1["model"] =='whisper-1')
                {
                    $message->response = serialize(json_encode($response_arr));
                    
                }
                else{
                $message->response = $responsedText;
                
                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $total_used_tokens;
                $message->words = 0;
                $UserOpenai_saved = $message->save();

                if (!$UserOpenai_saved) {
                    Log::debug('Save OpenAI Mobile Log Error ');
                } else {
                    Log::debug('Save Mobile Log Success ');
                }
                //Update remaining  to users section

            if (isset($this->total_used_tokens) && $this->total_used_tokens > 0)
            $total_used_tokens = $this->total_used_tokens;

        //Update new remaining Tokens
        $user = \DB::connection('sync_db')->table('users')->where('id', $user_id)->get();
        if ($user[0]->remaining_words != -1) {
            $user[0]->remaining_words -= $total_used_tokens;
            $new_remaining_words = $user[0]->remaining_words - $total_used_tokens;
            // $user[0]->save();
            $user_update = DB::connection('sync_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));

        }

        if ($user[0]->remaining_words < -1) {
            $user[0]->remaining_words = 0;
            // $user[0]->save();
            $new_remaining_words = 0;
            $user_update = DB::connection('sync_db')->update('update users set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));


        }
        if ($user_update > 0)
            Log::debug('Update remaining at MobileApp by + add $total_used_tokens to old remaining_words in users table in Mobileapp success');

        //echo 'data: [DONE]';
        //echo "\n\n";

            }


            


        } else {
            //echo 'data: [Update Failed user not found]';
        }


    }


//Done
    public function SMAI_Check_DigitalAsset_UserColumn($user_id, $key, $database)
    {

        /*  Log::debug('User ID  SMAI_Check_DigitalAsset_UserPlans from SMAIsyncController : '.$user_id);
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

            //echo $token_total;
            return $token_total;

        } else {
            return false;
        }


    }




//Woring
//Universal SMAI fnc
    public function SMAI_Update_TableColumn($arr_ids, $database, $table, $data_array)
    {


        $array_of_ids = $arr_ids;
        $table_update = DB::connection($database)->table($table)->whereIn('id', $array_of_ids)->update(array($data_array));


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

    //Working
    public function SMAI_Ins_Eloq_openAI_content_TB($user_id, $database, $table)
    {

        //define Users
        $user = \DB::connection($database)->table('users')->where('id', $user_id)->get();
        //$users = DB::connection('second_db')->table('users')->get();

        $prompt = '';
        $post_type = 'paragraph_generator';
        $post = OpenAIGenerator::where('slug', $post_type)->first();
        $entry = new Mobile_UserOpenai();
        $entry->title = 'New Workbook';

        if($params_json1["model"] =='whisper-1')
                {
                $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                }
                else
                {
                $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }

        if($params_json1["model"] =='whisper-1')
                {
                    $prompt = $description;
                 $output  = $response['text'];
                }        
          
        $response_arr=json_decode($response_bk,true);       
        $entry->user_id = $user_id;
        $entry->openai_id = $post->id;
        $entry->input = $prompt;
        $entry->response = serialize(json_encode($response_arr));
        $entry->output = $output;
        $entry->hash = str()->random(256);
        $entry->credits = 0;
        $entry->words = 0;
        $entry->main_user_openai_id = $main_message_id;
        $entry->save();

        $message_id = $entry->id;
        Log::debug('Message_ID of working '.$message_id);


    }

    public function SMAI_QryAll_Eloq_openAI_content_TB($user_id, $database, $table)
    {


    }

    public function SMAI_QryFilter_Eloq_openAI_content_TB($user_id, $database, $table)
    {


    }

    //Done
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

    public function update_token_centralize($user_id,$email,$token_array)
    {



    }


    public function imageOutput_save_main_coin($user_id,$usage,$response,$params, $size=NULL, $post=NULL,  $style=NULL, $lighting=NULL, $mood=NULL, $number_of_images=1, $image_generator='DE', $negative_prompt=NULL)
    {
        $image_arr=array(
            'style' => $style,
             'artist' => 'Leonardo da Vinci',
             'lighting' => $lighting,
             'mood' => $mood,
        );

        

        $path_array=array();
        $image_storage = "s3";
        Log::debug('Usage in imageOutput_save '.$usage);
        Log::debug('user_id in imageOutput_save '.$user_id);
        Log::debug('response in imageOutput_save '.$response);
        Log::info($response);
        Log::debug('params in imageOutput_save ');
        Log::info($params);
        
        //$params_json = json_decode($params, true);
        if(is_array($params))
        $params_json =$params;
        else
        $params_json = json_decode($params, true);

        $prompt=$params_json['prompt'];
        $prompt = preg_replace('/[^A-Za-z0-9 ]/', '', $prompt);
        $size=$params_json['size'];
        $number_of_images=$params_json['n'];
        $chatGPT_catgory=$params_json['gpt_category'];

        $image_arr['size']=$size;
        $file_ImageSize=$params_json['file_size'];
        

        $user=UserMain::where('id',$user_id)->first();
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

        $this->settings_two  =  SettingTwo::first();
        $image_storage = $this->settings_two->ai_image_storage;

        Log::debug('Current Storage setting'.$image_storage);




        for ($i = 0; $i < $number_of_images; $i++) {

            //use this for openAI Images
            if($image_generator != self::STABLEDIFFUSION) {

                if( isset($params_json['nameOfImage']))
                {

                     $contents= $params_json['contents'];
                     $nameOfImage= $params_json['nameOfImage'];
                }
                else{
                    
                //send prompt to openai
                if($prompt == null) return response()->json(["status" => "error", "message" => "You must provide a prompt"]);
                
                $response=json_decode($response,true);
                if($chatGPT_catgory=='Images_Design')
                $image_url = $response['data'][0]['url'];
                else
                $image_url = $response['data'][0]['b64_json'];

               //$contents = base64_decode($image_url);
                $contents=$image_url;
                $nameOfImage = Str::random(12) . '-DALL-E-' . Str::slug($prompt) . '.png';

                }

                //save file on local storage or aws s3
                
               
            
            } else {

               
                if( isset($params_json['nameOfImage']))
                {

                     $contents= $params_json['contents'];
                     $nameOfImage= $params_json['nameOfImage'];
                }
                else{
                //send prompt to stablediffusion
                $settings = SettingTwo::first();
                /* $width = explode('x', $size)[0];
                $height = explode('x', $size)[1]; */

                //SMAI response data start
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

            }
                
                
            }


            Storage::disk('topics')->put($nameOfImage, file_get_contents($contents));
            $path = 'https://smartstock.social/uploads/topics/' . $nameOfImage;
            $path_s3 = 'uploads/topics/' . $nameOfImage;
            $uploadedFile = new File($path_s3);
            $aws_path = Storage::disk('s3')->put('', $uploadedFile);
            unlink($path_s3);
            $path = Storage::disk('s3')->url($aws_path);
            if($path)
            {
                    Log::debug('success path of upload');
                    Log::info($path);
            }


            // Save Users of MainCoIN
            $user = UserMain::where('id', $user_id)->first();
            //$users = DB::connection('second_db')->table('users')->get();



            
            $post_type='ai_image_generator';
            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new UserOpenai();
            $entry->title = 'New Image';
            $entry->slug = Str::random(7) . Str::slug($user->name) . '-workbsook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;
            $entry->input = $prompt;
            $entry->response = $image_generator == "stablediffusion" ? "SD" : "DE";
            $entry->output = $image_storage == "s3" ? $path : '/' . $path;
            $entry->hash = Str::random(256);
            $entry->credits = 1;
            $entry->words = 0;
            $entry->storage = UserOpenai::STORAGE_AWS ;
            $entry->file_size=$file_ImageSize;
            $entry->save();

            //push each generated image to an array
            array_push($entries, $entry);

            if ($user->remaining_images - 1 == -1) {
                $user->remaining_images = 0;
                $user->save();
                $userOpenai = UserOpenai::where('user_id', $user_id)->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
                $openai = OpenAIGenerator::where('id', $post->id)->first();
                return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
            }

            if ($user->remaining_images == 1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images != -1 and $user->remaining_images != 1 and $user->remaining_images != 0) {
                $user->remaining_images -= 1;
                $user->save();
            }

            if ($user->remaining_images < -1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images == 0) {
                //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
            }

            array_push($path_array,$path);
        }

         $image_arr['file_size']=$file_ImageSize;
         $return_arr=array(
            'path_array' => $path_array ,
            'image_array' => $image_arr,

         );

          return $return_arr;

       // return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
    }


    public function imageOutput_save_SocialPost_self($user_id,$size,$path,$img_width,$img_height,$image_arr,$prompt)
    {

        //update openai_usage_tokens
        //update_team_data("openai_usage_tokens", get_team_data("openai_usage_tokens", 0) + $usage);
  
        $team_data=SPTeam::where('owner',$user_id)->first();
        $team_id=$team_data->id;

        $tmp_file = "";
        $file_path=$path;
        $folder=0;

        $fileSize = $image_arr['file_size'];

        $data_image=array(
            "ids" => $this->ids(),
            "team_id" => $team_id,
            "is_folder" => 0,
            "pid" => $folder,
            "name" => str_replace( "https://smartcontent-ai-image.s3.amazonaws.com/", "", $file_path),
            "file" => $file_path,
            "type" => "image/jpeg",
            "extension" => "jpg",
            "detect" => "image",
            "size" => $fileSize,
            "is_image" => 1,
            "width" => (int)$img_width,
            "height" => (int)$img_height,
            "created" => time(),
        );

        $image_sp_new_ins = Files_SP::create($data_image);
        Log::debug('Insert new image to self_SocialPost result');
        Log::info($image_sp_new_ins);

    
    }
 
    public function imageOutput_save_Bio_self($user_id,$size,$path,$img_width,$img_height,$image_arr,$prompt)
    {


        $user_bio=UserBio::where('user_id',$user_id)->first();
        $plan_settings=$user_bio->plan_settings;
        $plan_settings_json=json_decode($plan_settings,true);
        //api from plan_setting
        $images_api=$plan_settings_json['images_api'];

        $settings_arr=array(
            "variants" => 1,
        );
        $settings=json_encode($settings_arr);
        $project_id=NULL;
        $name=str_replace( "https://smartcontent-ai-image.s3.amazonaws.com/", "", $path);
        /* Prepare the statement and execute query */
        $data_image = array(
            'user_id' => $user_id,
            'project_id' => $project_id,
            'name' => $name,
            'input' => $prompt,
            'image' => $path,
            'style' => $image_arr['style'],
            'artist' => $image_arr['artist'],
            'lighting' => $image_arr['lighting'],
            'mood' => $image_arr['mood'],
            'size' => $size,
            'settings' => $settings,
            'api' => $images_api,
            'api_response_time' => 9999,
            'datetime' => date("Y-m-d H:i:s"),
        );


        $image_bio_new_ins = new ImagesBio([
            'user_id' => $user_id,
            'project_id' => $project_id,
            'name' => $name,
            'input' => $prompt,
            'image' => $path,
            'style' => $image_arr['style'],
            'artist' => $image_arr['artist'],
            'lighting' => $image_arr['lighting'],
            'mood' => $image_arr['mood'],
            'size' => $size,
            'settings' => $settings,
            'api' => $images_api,
            'api_response_time' => 9999,
            'datetime' => date("Y-m-d H:i:s"),
        ]);
        
        $image_bio_new_ins->save(); //returns true

       // $image_bio_new_ins = ImagesBio::create($data_image);
        Log::debug('Insert new image to self_Bio result');
        Log::info($image_bio_new_ins);
    }

    public function imageOutput_save_Bio($user_id,$prompt, $number_of_images,$path_array,$image_array)
    {


        $image_generator='DE';
        $entries=[];
        $user=UserBio::where('user_id',$user_id)->first();

        for ($i = 0; $i < count($path_array); $i++) {
        
            $post_type='ai_image_generator';
            $image_storage="s3";
            $path=$path_array[$i];

            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new UserBioOpenai();
            $entry->title = 'New Image';
            $entry->slug = Str::random(7) . Str::slug($user->name) . '-workbsook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;
            $entry->input = $prompt;
            $entry->response = $image_generator == "stablediffusion" ? "SD" : "DE";
            $entry->output = $image_storage == "s3" ? $path : '/' . $path;
            $entry->hash = Str::random(256);
            $entry->credits = 1;
            $entry->words = 0;
            $entry->storage = UserOpenai::STORAGE_AWS ;
            $entry->save();

            //push each generated image to an array
            array_push($entries, $entry);

            if ($user->remaining_images - 1 == -1) {
                $user->remaining_images = 0;
                $user->save();
                $userOpenai = UserBioOpenai::where('user_id', $user_id)->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
                $openai = OpenAIGenerator::where('id', $post->id)->first();
                return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
            }

            if ($user->remaining_images == 1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images != -1 and $user->remaining_images != 1 and $user->remaining_images != 0) {
                $user->remaining_images -= 1;
                $user->save();
            }

            if ($user->remaining_images < -1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images == 0) {
                //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
            }

            $size=$image_array['size'];
            $size_arr=explode("x",$size);
            $img_width=$size_arr[0];
            $img_height=$size_arr[1];
            $this->imageOutput_save_Bio_self($user_id,$size,$path,$img_width,$img_height,$image_array,$prompt);
        
        }
        //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);

       Log::debug('imageOutput_save_Bio');
       Log::debug(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);

    }

    public function imageOutput_save_SocialPost($user_id,$prompt, $number_of_images,$path_array,$image_array)
    {
        $image_generator='DE';
        $entries=[];
        $user=UserSP::where('id',$user_id)->first();

        for ($i = 0; $i < count($path_array); $i++) {
        
            $post_type='ai_image_generator';
            $image_storage="s3";
            $path=$path_array[$i];

            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new SP_UserOpenai();
            $entry->title = 'New Image';
            $entry->slug = Str::random(7) . Str::slug($user->name) . '-workbsook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;
            $entry->input = $prompt;
            $entry->response = $image_generator == "stablediffusion" ? "SD" : "DE";
            $entry->output = $image_storage == "s3" ? $path : '/' . $path;
            $entry->hash = Str::random(256);
            $entry->credits = 1;
            $entry->words = 0;
            $entry->storage = UserOpenai::STORAGE_AWS ;
            $entry->save();

            //push each generated image to an array
            array_push($entries, $entry);

            if ($user->remaining_images - 1 == -1) {
                $user->remaining_images = 0;
                $user->save();
                $userOpenai = SP_UserOpenai::where('user_id', $user_id)->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
                $openai = OpenAIGenerator::where('id', $post->id)->first();
                return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
            }

            if ($user->remaining_images == 1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images != -1 and $user->remaining_images != 1 and $user->remaining_images != 0) {
                $user->remaining_images -= 1;
                $user->save();
            }

            if ($user->remaining_images < -1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images == 0) {
                //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
            }

            $size=$image_array['size'];
            $size_arr=explode("x",$size);
            $img_width=$size_arr[0];
            $img_height=$size_arr[1];
            $this->imageOutput_save_SocialPost_self($user_id,$size,$path,$img_width,$img_height,$image_array,$prompt);
        }
        //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);

       Log::debug('imageOutput_save_SocialPost ');
       Log::debug(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);


    }

    public function imageOutput_save_Design($user_id,$prompt, $number_of_images,$path_array)
    {

        $image_generator='DE';
        $entries=[];
        $user=UserDesign::where('id',$user_id)->first();

        for ($i = 0; $i < count($path_array); $i++) {
        
            $post_type='ai_image_generator';
            $image_storage="s3";
            $path=$path_array[$i];

            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new DigitalAsset_UserOpenai();
            $entry->title = 'New Image';
            $entry->slug = Str::random(7) . Str::slug($user->name) . '-workbsook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;
            $entry->input = $prompt;
            $entry->response = $image_generator == "stablediffusion" ? "SD" : "DE";
            $entry->output = $image_storage == "s3" ? $path : '/' . $path;
            $entry->hash = Str::random(256);
            $entry->credits = 1;
            $entry->words = 0;
            $entry->storage = UserOpenai::STORAGE_AWS ;
            $entry->save();

            //push each generated image to an array
            array_push($entries, $entry);

            if ($user->remaining_images - 1 == -1) {
                $user->remaining_images = 0;
                $user->save();
                $userOpenai = DigitalAsset_UserOpenai::where('user_id', $user_id)->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
                $openai = OpenAIGenerator::where('id', $post->id)->first();
                return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
            }

            if ($user->remaining_images == 1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images != -1 and $user->remaining_images != 1 and $user->remaining_images != 0) {
                $user->remaining_images -= 1;
                $user->save();
            }

            if ($user->remaining_images < -1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images == 0) {
                //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
            }
        }
        //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
        Log::debug('imageOutput_save_Design ');
        Log::debug(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
 


    }


    public function imageOutput_save_Sync($user_id,$prompt, $number_of_images,$path_array)
    {

        $image_generator='DE';
        $entries=[];
        $user=UserSyncNodeJS::where('id',$user_id)->first();

        for ($i = 0; $i < count($path_array); $i++) {
        
            $post_type='ai_image_generator';
            $image_storage="s3";
            $path=$path_array[$i];

            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new UserSyncNodeJSOpenai();
            $entry->title = 'New Image';
            $entry->slug = Str::random(7) . Str::slug($user->name) . '-workbsook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;
            $entry->input = $prompt;
            $entry->response = $image_generator == "stablediffusion" ? "SD" : "DE";
            $entry->output = $image_storage == "s3" ? $path : '/' . $path;
            $entry->hash = Str::random(256);
            $entry->credits = 1;
            $entry->words = 0;
            $entry->storage = UserOpenai::STORAGE_AWS ;
            $entry->save();

            //push each generated image to an array
            array_push($entries, $entry);

            if ($user->remaining_images - 1 == -1) {
                $user->remaining_images = 0;
                $user->save();
                $userOpenai = UserSyncNodeJSOpenai::where('user_id', $user_id)->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
                $openai = OpenAIGenerator::where('id', $post->id)->first();
                return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
            }

            if ($user->remaining_images == 1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images != -1 and $user->remaining_images != 1 and $user->remaining_images != 0) {
                $user->remaining_images -= 1;
                $user->save();
            }

            if ($user->remaining_images < -1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images == 0) {
                //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
            }
        }
        //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);

        Log::debug('imageOutput_save_Sync ');
        Log::debug(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);



    }


    public function imageOutput_save_MobileAppV2($user_id,$prompt, $number_of_images,$path_array)
    {

        $image_generator='DE';
        $entries=[];
        $user=UserMobile::where('id',$user_id)->first();

        for ($i = 0; $i < count($path_array); $i++) {
        
            $post_type='ai_image_generator';
            $image_storage="s3";
            $path=$path_array[$i];

            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new Mobile_UserOpenai();
            $entry->title = 'New Image';
            $entry->slug = Str::random(7) . Str::slug($user->name) . '-workbsook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;
            $entry->input = $prompt;
            $entry->response = $image_generator == "stablediffusion" ? "SD" : "DE";
            $entry->output = $image_storage == "s3" ? $path : '/' . $path;
            $entry->hash = Str::random(256);
            $entry->credits = 1;
            $entry->words = 0;
            $entry->storage = UserOpenai::STORAGE_AWS ;
            $entry->save();

            //push each generated image to an array
            array_push($entries, $entry);

            if ($user->remaining_images - 1 == -1) {
                $user->remaining_images = 0;
                $user->save();
                $userOpenai = Mobile_UserOpenai::where('user_id', $user_id)->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
                $openai = OpenAIGenerator::where('id', $post->id)->first();
                return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
            }

            if ($user->remaining_images == 1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images != -1 and $user->remaining_images != 1 and $user->remaining_images != 0) {
                $user->remaining_images -= 1;
                $user->save();
            }

            if ($user->remaining_images < -1) {
                $user->remaining_images = 0;
                $user->save();
            }

            if ($user->remaining_images == 0) {
                //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
            }
        }
        //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);


        Log::debug('imageOutput_save_MobileApp ');
        Log::debug(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);



    }

    public function lowGenerateSaveAll($usage,$response,$main_message_id)
    {
        //$response = $request->response;
        $total_user_tokens = $usage;

        Log::debug('Main Message ID in lowGenerateSaveAll'.$main_message_id);


        for($i=0;$i<3;$i++)
        {
            if($i==0)
            $entry = DigitalAsset_UserOpenai::where('main_user_openai_id', $main_message_id)->first();
            
            if($i==1)
            $entry = SP_UserOpenai::where('main_user_openai_id', $main_message_id)->first();

            if($i==2)
            $entry = Mobile_UserOpenai::where('main_user_openai_id', $main_message_id)->first();

            if($i==3)
            $entry = UserBioOpenai::where('main_user_openai_id',$main_message_id)->first();

            if($i==4)
            $entry = UserSyncNodeJSOpenai::where('main_user_openai_id', $main_message_id)->first();
            
            Log::debug('Debug User Open AI ');
            Log::info($entry);

            if(isset($entry))
            {
            $entry->credits = $total_user_tokens;
            $entry->words = $total_user_tokens;
            $entry->response = $response;
            $entry->output = $response;
            $entry->save();
            }
        }

       //back to Update Tokens remaining_words in APIsController

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


        $user = Auth::user();

        if ($user->remaining_words != -1) {
            $user->remaining_words -= $total_user_tokens;
        }

        if ($user->remaining_words < -1) {
            $user->remaining_words = 0;
        }
        $user->save();
    }







}
