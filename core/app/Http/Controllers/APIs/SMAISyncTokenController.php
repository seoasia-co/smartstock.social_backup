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

use App\Models\UserBioOpenaiTemplate;

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
use App\Http\Controllers\MainController;
use App\Models\PostSmartSocial;
use App\Models\Media_files;

//use File;
/* for update Token and chatGTP usage including Log history of chat GPT data */

class SMAISyncTokenController extends Controller
{


    /* Main Type of API from platforms GPT Category
    DocText_
    Images_ 
    ------------------------------
    Platforms

    main_coin
    MainCoIn

    SocialPost
    socilpost

    MobileApp
    MobileV2
    mobile

    Sync
    SyncNodeJS
    sync

    Design
    DigitalAsset
    design


    
    */

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
    public $chat_name;
    public $chat_main_id;
    public $chatGPT_catgory;
    public $response_text;
    public $platform;
    public $main_template_id;
    public $main_openai_id;
    public $main_chat_category;
    public $image_url;
    public $nameOfImage;
    public $image_origin_id;
    public $image_generator;
    //public $token_usge;

    public function __construct($response = NULL, $usage = NULL, $chatGPT_catgory = NULL, $chat_id = NULL,$chat_main_id =NULL,$chat_name=NULL,$params=NULL,$user_id=NULL,$response_text=NULL,$main_useropenai_message_id=NULL)
    {
        Log::debug('Debug params in start SMAISyncTokenController constructor ');
        Log::info($params);

        //Settings
        $this->settings = Settings::first();
        $this->settings_two = SettingTwo::first();

        //$category = OpenaiGeneratorChatCategory::where('id', $request->category_id)->firstOrFail();

        if (is_array($params))
        $params_json1 = $params;
        else
        $params_json1 = json_decode($params, true);

        Log::debug('Debug params in SMAISyncTokenController constructor ');
        Log::info($params);

        if (isset($params_json1['openai_chat_category_id']))
            $this->main_chat_category = $params_json1['openai_chat_category_id'];
        else
            $this->main_chat_category = 1;

        if(isset($params_json1['chat_main_id']))
        Log::debug(' Test Main ID of Chat '.$params_json1['chat_main_id']);
        
        if(isset($params_json1['chat_main_id']))
        $this->chat_main_id=$params_json1['chat_main_id'];

        if(isset($params_json1['main_template_id']))
        {
            Log::debug(' Main Template ID Docs '.$params_json1['main_template_id']);
            
            if($params_json1['main_template_id']>0)
            $this->main_template_id=$params_json1['main_template_id'];

        }

        if(isset($params_json1['image_origin_id']))
        {  
            //if source image ID was set
            $this->image_origin_id=$params_json1['image_origin_id'];

        }



        if(isset($params_json1['image_generator']))
        {
        
        $this->image_generator=$params_json1['image_generator'];
        }



        if(isset($params_json1['main_useropenai_message_id']))
        {
            Log::debug(' Main UserOpenAI Docs ID Docs '.$params_json1['main_useropenai_message_id']);
            $this->main_openai_id=$params_json1['main_useropenai_message_id'];

            if($this->main_template_id <= 0 || $this->main_template_id==NULL )
            {
                $find_main_template=UserOpenai::where('id', $params_json1['main_useropenai_message_id'])->first();
                $this->main_template_id=$find_main_template->openai_id;
                
            }

        }

        if(isset($params_json1['platform']))
        {
        $from=$params_json1['platform'];
        $this->platform=$params_json1['platform'];
        }

        if (isset($params_json1->gpt_category))
        $this->chatGPT_catgory =$params_json1->gpt_category;
        else if (isset($params_json1['gpt_category']))
        $this->chatGPT_catgory = $params_json1['gpt_category'];
        else
        $this->chatGPT_catgory = NULL;


        Log::debug(' Check response_text from APIs in Contructor '.$response_text);
        if (isset($response)) {

            
            if ($response != NULL) 
            $this->response_bk = $response;
            else
            $this->response_bk=NULL;

            if(isset($response_text) && $response_text!=NULL)
            {
                Log::debug('Main ID main_user_openai_id ' .$main_useropenai_message_id);

            $this->response_text = $response_text;
            $sp_check_fix=SP_UserOpenai::where('main_user_openai_id',$main_useropenai_message_id)->first();
            
            if(isset($sp_check_fix->id))
            {
              $parent_caption_id= $sp_check_fix->id;
              $check_fix_caption=SP_UserCaption::where('parent_id',$parent_caption_id)->first();
            }
            
                if(isset($check_fix_caption->wait_for_fix))
                {
                      if($check_fix_caption->wait_for_fix==1)
                      {

                        $check_fix_caption->content=$this->response_text;
                        $check_fix_caption->save();

                        $sucess_id_fix_caption=$check_fix_caption->id;
                        if($sucess_id_fix_caption>0)
                        {
                            $check_fix_caption->wait_for_fix=0;
                            $check_fix_caption->save();

                        }


                      }
                }

            }
            else 
            {
            $this->response_text = NULL;
            }

            Log::debug(' Check response text in Contructor '.$this->response_text);
            if(isset($this->response_text['model']))
            $this->GPTModel=$this->response_text['model'];

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


                //Log::debug(' !!!!!!!!!!!!!! Check Post COntent response text in Contructor '.$this->postContent);


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


            if( Str::contains($this->chatGPT_catgory, 'chat_')==true || Str::contains($this->chatGPT_catgory, 'Chat_')==true  )   
            { 

            //create chat_id only in Initiate
            //define where's chat come from

            Log::debug('Debug $this_chat_id Global ' . $this->chat_id);
            Log::debug('Debug $chat_id Local ' . $chat_id);

            if($chat_id!=NULL)
            $this->chat_id = $chat_id;

            else if($from=='bio' && $this->chat_id==NULL)
            $this->chat_id=$this->find_chat_id($from);

            else if($from=='main_coin' && $this->chat_id==NULL)
            $this->chat_id=$this->find_chat_id($from);

            else if($from=='MobileAppV2' && $this->chat_id==NULL)
            $this->chat_id=$this->find_chat_id($from);

            else 
            $this->chat_id=NULL;



            //Log::debug('Debug $this_chat_id ' . $this->chat_id);

            
            if ($chat_main_id == NULL || $chat_main_id  < 0) {
            
                $this->chat_main_id=NULL;
            }
            else{

                $this->chat_main_id=$chat_main_id ;
            }

             if(isset($this->chat_main_id))
             Log::debug('!!!!!! Debug $chat_main_id !!!!!' . $this->chat_main_id);
             
             if($this->chat_main_id==NULL)
             {
                if(isset($params_json1['chat_main_id']) && $params_json1['chat_main_id']>0)
                $this->chat_main_id=$params_json1['chat_main_id'];
                else
                $this->chat_main_id=$this->create_new_chat_main_id($from,$user_id,$this->chat_id);
             }

        }


        }

        if($chat_name != NULL)
        $this->chat_name = $chat_name;
        else
        $this->chat_name =NULL;

        /* if (is_array($params))
                $params_json1 = $params;
            else
                $params_json1 = json_decode($params, true); */


        if(isset($params_json1['prompt']))
        $prompt=$params_json1['prompt'];
        else
        $prompt=NULL;


     if( Str::contains($this->chatGPT_catgory, 'chat_')==true || Str::contains($this->chatGPT_catgory, 'Chat_')==true  )   
    {
        //check chat_id again
        if( strlen($this->chat_id) >2 ) 
        {
           Log::debug('!!!!!! Debug $this->chat_id  again!!!!!' . $this->chat_id);
        }
        else
        {
       
           Log::debug('!!!!!! Debug $this->chat_id  again!!!!! case No cat ID' . $this->chat_id);
                   if($params_json1['platform']=='bio')
                   {

                       Log::debug('!!!!!! Debug fine Bio old ChatID !!!!!' );
               // $chat_id_find=UserOpenaiChatBio::where('chat_id',$this->chat_main_id)->first();
                   
                   $chat_id_find=DB::connection('bio_db')->table('chats')->where('chat_id',$this->chat_main_id)->first();
                   $new_chat_id=$chat_id_find->chat_id_mobile;
                   $this->chat_id = $new_chat_id;

                           if($this->chat_id==NULL)
                           {
                                   $chat_mobile_id= "chat_";
                                   $chat_mobile_id.= strval(time());
                                   $ran = rand(100, 999);
                                   $chat_mobile_id.= $ran;

                                   $new_chat_id=array(
                                       'chat_id_mobile' => $chat_mobile_id,
                                   );

                                $chatData = DB::connection('bio_db')->table('chats')
                                ->where('chat_id',$this->chat_main_id)
                                ->update($new_chat_id);

                                 //$chat_id_find->update($new_chat_id);
                                   

                                   $this->chat_id =$chat_mobile_id;



                           }
                   }
                   else
                   {
                   $chat_id_find=UserOpenaiChat::where('id',$this->chat_main_id)->first();
                   $new_chat_id=$chat_id_find->chat_id;
                   $this->chat_id = $new_chat_id;
               
                   }

           // $chat_id_find=DB::connection('main_db')->table('user_openai_chat')->wherewhere('id',$this->chat_main_id)->first();

        

             Log::debug('!!!!!! Debug $this->chat_id Define  again!!!!!' . $this->chat_id);

        }

    }



        if($this->chat_name !=NULL && $prompt=='SKIP' )
        {
            //send first chatname to all platforms
            // check and update chat_id before send
            Log::debug('!!!!!! Debug $chat_main_id !!!!!' . $this->chat_main_id);

            if($params_json1['platform']=='main_coin')
            $main_db_chat=UserOpenaiChat::where('id',$this->chat_main_id)->first();

            if($params_json1['platform']=='bio')
            $main_db_chat=UserOpenaiChatBio::where('chat_id',$this->chat_main_id)->first();

           
            if($main_db_chat->chat_id==NULL || $main_db_chat->chat_id <1 )
            {
                $main_db_chat->chat_id=$this->chat_id;
                $main_db_chat->save();


            }


            
            $new_chat_ins_all = $this->new_chat_all_platforms($this->chat_name,$this->chat_id,$user_id,$this->chat_main_id);
        }


    }


    public function get_size($file_path)
    {
        return Storage::disk('s3')->size($file_path);
    }

    public function ids()
    {
        return uniqid();
    }

    //Working can not find the exactly Table that specific the remaining_words and images
    public function SMAI_UpdateGPT_MainMarketing($user_id, $usage, $response, $params, $from, $main_message_id = NULL)
    {

        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (isset($user_id)) {
            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_MainMarketing from SMAIsyncController : ' . $user_id);

            if (is_array($params))
                $params_json1 = $params;
            else
                $params_json1 = json_decode($params, true);

            if (isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory = NULL;

            $response_bk = $response;
            $response = json_decode($response, true);

            //save chatGPT Chat data to DB
            if (Str::contains($chatGPT_catgory, 'Chat_')) {
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
               
                if($chat_id != NULL && $chat_id !='')
                $chat = UserOpenaiChatMainMarketing::where('chat_id', $chat_id)->first();
                else
                $chat = UserOpenaiChatMainMarketing::where('user_openai_chat_id', $this->chat_main_id)->first();
               
                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }
                $message->output = $output;
                $message->sender = "assistant";
                $message->hash = Str::random(256);

                //save token to chat message and if sum all chat message token = chat_id token
                $message->credits = $this->total_used_tokens;

                $message->words = 0;
                $message->updated_at = $time;
                $message->save();

                //$user = UserMain::where('id',$user_id);
                $user = DB::connection('main_db')->table('users')->where('id', $user_id)->first();

                $user_email = $user->email;
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

                if (isset($user_update))
                    Log::debug('Update#1 of Chat text remaining at MainMarketing success by + add $total_used_tokens to old remaining_words in users table in Main ');

                //$user->save();

                //save token to chat ID
                $chat->total_credits += $total_used_tokens;
                $chat->openai_chat_category_id=$this->main_chat_category;
                $chat->save();

                $chat_openai_id = $chat->id;
                $save_user_request_chat = array(

                    'chat_id' => $chat_openai_id,
                    'response' => $responsedText,
                );


                //Define CHat Role Universal
                if (is_array($params_json1["prompt"])!=FALSE)
                {
                    $n_prompt = count($params_json1["prompt"]);

                    if (isset($response['choices'][0]['message']['role']))
                        $this->chat_role = $response['choices'][0]['message']['role'];
                    else
                        $this->chat_role = $params_json1["prompt"][$n_prompt]["role"];
    
    
                    if ($n_prompt > 0) {
                        $n_prompt -= 1;
                    }
    
                    Log::debug('Count n_prompt' . $n_prompt);
                    $x = intval($n_prompt);
    
                    $description = Arr::last($params_json1["prompt"]);
                    $description = implode(" ", $description);

            }
            else{

                $n_prompt = 2;

                $this->chat_role = $params_json1['messages'][1]['role'];

                //Define CHat Role Universal

                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt ' . $n_prompt);
                $role_of_previous_chat = $params_json1['messages'][0]['role'];
                Log::debug('Which Role is : ' . $this->chat_role);
                $description = $params_json1['messages'][0]['content'];


            }

            if(!isset($save_user_request_chat["chat_id"]))
                $save_user_request_chat["chat_id"]=$this->chat_main_id;


                $save_user_request_chat["chat_id_mobile"]=$this->chat_id;
                $save_to_where = "MainMarketing";
                $save_user_request_chat["input"] = $description;
                $save_user_q = new SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat, $user_id, $save_to_where, $user_email, $from, $this->chat_role);

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

                } 

             else if (Str::contains($this->GPTModel,'gpt-4-')) {
                    if (isset($response['choices'][0]['message']['content'])) {
                        $message = $response['choices'][0]['message']['content'];
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
             
                else {
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

                if ($params_json1["model"] == 'whisper-1') {
                    $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                } else {
                    $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }

                if ($params_json1["model"] == 'whisper-1') {
                    $prompt = $description;
                    $output = $response['text'];
                }

                $response_arr = json_decode($response_bk, true);

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
               
                //for socialpost caption
                $responsedText_backup =$entry->response;

                $message_id = $entry->id;

                Log::debug('Message_ID of MainMarketing ' . $message_id);


                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = UserOpenai::whereId($message_id)->first();
                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $this->total_used_tokens;
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
    public function SMAI_UpdateGPT_MainCoIn($user_id, $usage, $response, $params, $from, $main_message_id = NULL)
    {
        if (is_array($params))
                $params_json1 = $params;
            else
                $params_json1 = json_decode($params, true);

            if (isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory = NULL;
        
        if($params_json1['prompt']!='SKIP')
        {

        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (isset($user_id)) {
            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_MainCoIn from SMAIsyncController : ' . $user_id);

           /*  if (is_array($params))
                $params_json1 = $params;
            else
                $params_json1 = json_decode($params, true);

            if (isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory = NULL; */

            $response_bk = $response;
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
                
                else if (isset($response['choices'][0]['message']['content']))
                    $message_response = $response['choices'][0]['message']['content'];

                else if(isset($params_json1['messages'][1]['content']))
                $message_response=$params_json1['messages'][1]['content'];
                
                else if(isset($params_json1['response']))
                $message_response=$params_json1['response'];
                 
                else
                $message_response=NULL;
              
                /*  if($params_json1['prompt']=='SKIP')
                {

                    $new_chat_ins_all = $this->new_chat_all_platforms($this->chat_name,$this->chat_id,$user_id,$this->chat_main_id); 
                    $message_response=$params_json1['chat_name'];

                } 
                   */

                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $message = UserOpenaiChatMessage::whereId($message_id)->first();
                
                if($chat_id != NULL && $chat_id !='')
                $chat = UserOpenaiChat::where('chat_id', $this->chat_id)->first();
                else
                $chat = UserOpenaiChat::where('id', $this->chat_main_id)->first();



                
                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $this->total_used_tokens;
                $message->words = 0;
                $message->save();

                //$user = UserMain::where('id',$user_id);
                $user1 = DB::connection('main_db')->table('users')->where('id', $user_id)->first();
                //$user = \DB::connection('main_db')->table('users')->where('id', $user_id)->get();

                $user_email = $user1->email;
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
                $chat->openai_chat_category_id=$this->main_chat_category;
                $chat->save();

                $chat_openai_id = $chat->id;
                $save_user_request_chat = array(

                    'chat_id' => $chat_openai_id,
                    'response' => $responsedText,
                );


               if (is_array($params_json1["prompt"])!=FALSE)
                {
                $n_prompt = count($params_json1["prompt"]);

                if (isset($response['choices'][0]['message']['role']))
                    $this->chat_role = $response['choices'][0]['message']['role'];
                else
                    $this->chat_role = $params_json1["prompt"][$n_prompt]["role"];

                //Define CHat Role Universal

                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt ' . $n_prompt);
                $role_of_previous_chat = $params_json1["prompt"][$n_prompt]["role"];
                Log::debug('Which Role is : ' . $this->chat_role);
                $description = $params_json1["prompt"][$n_prompt]["content"];

            }
            else{

                $n_prompt = 2;

               
                

                $this->chat_role = $params_json1['messages'][1]['role'];
                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt ' . $n_prompt);
                $role_of_previous_chat = $params_json1['messages'][0]['role'];
                Log::debug('Which Role is : ' . $this->chat_role);
                $description = $params_json1['messages'][0]['content'];

                
                //Define CHat Role Universal

                


            }


                if(!isset($save_user_request_chat["chat_id"]))
                $save_user_request_chat["chat_id"]=$this->chat_main_id;

                $save_user_request_chat["chat_id_mobile"]=$this->chat_id;
                $save_to_where = "MainCoIn";
                $save_user_request_chat["input"] = $description;
                $save_user_q = new SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat, $user_id, $save_to_where, $user_email, $from, $this->chat_role);


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

                }
                else if (Str::contains($this->GPTModel,'gpt-4-')) {
                    if (isset($response['choices'][0]['message']['content'])) {
                        $message = $response['choices'][0]['message']['content'];
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
                else {
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

                if ($params_json1["model"] == 'whisper-1') {
                    $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                } else {
                    $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }

                if ($params_json1["model"] == 'whisper-1') {
                    $prompt = $description;
                    $output = $response['text'];
                }

                $response_arr = json_decode($response_bk, true);

                $entry->user_id = $user_id;
                $entry->openai_id = $post->id;
                $entry->input = $prompt;
                $entry->response = serialize(json_encode($response_arr));
                $entry->output = $output;
                $entry->hash = str()->random(256);
                $entry->credits = 0;
                $entry->words = 0;
                $entry->save();

                //for socialpost caption
                $responsedText_backup =$entry->response;

                $message_id = $entry->id;
               
                Log::debug('Message_ID of MainCoIn ' . $message_id);
                if($this->chat_id==NULL)

                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = UserOpenai::whereId($message_id)->first();
                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $this->total_used_tokens;
                $message->words = 0;
                $UserOpenai_saved = $message->save();

                if (!$UserOpenai_saved) {
                    Log::debug('Save OpenAI Log Error ');
                } else {
                    Log::debug('Save UserOpenai Log Success ');
                }

                if ($message_id == $entry->id)
                {
                    $return_message_array=array(
                        'message_id'=>$message_id,
                        'total_used_tokens'=>$this->total_used_tokens,
                    );
                  
                    return $return_message_array;

                }


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


    }


    //Done
    public function SMAI_UpdateGPT_SocialPost($user_id, $usage, $response, $params, $from, $main_message_id = NULL)
    {

        if($from!=NULL)
        $this->platform=$from;

        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (is_array($params))
        $params_json1 = $params;
    else
        $params_json1 = json_decode($params, true);

    if (isset($params_json1['gpt_category']))
        $chatGPT_catgory = $params_json1['gpt_category'];
    else
        $chatGPT_catgory = NULL;

if($params_json1['prompt']!='SKIP')
{

        if (isset($user_id)) {
            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_SocialPost from SMAIsyncController : ' . $user_id);

            if (is_array($params))
                $params_json1 = $params;
            else
                $params_json1 = json_decode($params, true);

            if (isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory = NULL;

            $response_bk = $response;
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

                    if(isset($params_json1['messages'][1]['content']))
                    $message_response=$params_json1['messages'][1]['content'];
                    
                    if(isset($params_json1['response']))
                    $message_response=$params_json1['response'];

                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $message = UserOpenaiChatMessageSocialPost::whereId($message_id)->first();

                if($chat_id != NULL && $chat_id != '')
                $chat = UserOpenaiChatSocialPost::where('chat_id', $chat_id)->first();
                else
                $chat = UserOpenaiChatSocialPost::where('user_openai_chat_id', $this->chat_main_id)->first();

                
                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $this->total_used_tokens;
                $message->words = 0;
                $message->save();

                //$user = UserSP::where('id',$user_id);
                $user = DB::connection('main_db')->table('sp_users')->where('id', $user_id)->first();

                $user_email = $user->email;
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
                $chat->openai_chat_category_id=$this->main_chat_category;
                $chat->save();

                $chat_openai_id = $chat->id;
                $save_user_request_chat = array(

                    'chat_id' => $chat_openai_id,
                    'response' => $responsedText,
                );
                //Define CHat Role Universal
                if (is_array($params_json1["prompt"])!=FALSE)
                {
                    $n_prompt = count($params_json1["prompt"]);

                    if (isset($response['choices'][0]['message']['role']))
                        $this->chat_role = $response['choices'][0]['message']['role'];
                    else
                        $this->chat_role = $params_json1["prompt"][$n_prompt]["role"];
    
    
                    if ($n_prompt > 0) {
                        $n_prompt -= 1;
                    }
    
                    Log::debug('Count n_prompt' . $n_prompt);
                    $x = intval($n_prompt);
    
                    $description = Arr::last($params_json1["prompt"]);
                    $description = implode(" ", $description);

            }
            else{

                $n_prompt = 2;

                $this->chat_role = $params_json1['messages'][1]['role'];

                //Define CHat Role Universal

                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt ' . $n_prompt);
                $role_of_previous_chat = $params_json1['messages'][0]['role'];
                Log::debug('Which Role is : ' . $this->chat_role);
                $description = $params_json1['messages'][0]['content'];


            }


                //Log::debug('Desc after convert to string' . $description);

                if(!isset($save_user_request_chat["chat_id"]))
                $save_user_request_chat["chat_id"]=$this->chat_main_id;

                $save_user_request_chat["chat_id_mobile"]=$this->chat_id;
                $save_to_where = "SocialPost";
                $save_user_request_chat["input"] = $description;
                $save_user_q = new SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat, $user_id, $save_to_where, $user_email, $from, $this->chat_role);


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
                } 
                else if (Str::contains($this->GPTModel,'gpt-4-')) {
                    if (isset($response['choices'][0]['message']['content'])) {
                        $message = $response['choices'][0]['message']['content'];
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
                else {
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

                if ($params_json1["model"] == 'whisper-1') {
                    $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                } else {
                    $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }

                if ($params_json1["model"] == 'whisper-1') {
                    $prompt = $description;
                    $output = $response['text'];
                }

                $response_arr = json_decode($response_bk, true);

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

                

                //Log::debug('$response_arr OpenAIUserSP of SocialPost ' . $response_arr);
                //Log::debug('Entry OpenAIUserSP of SocialPost ' . $entry->response);
               // Log::debug(' Gobal $this->response_bk OpenAIUserSP of SocialPost ' . $this->response_bk);
                
                //for socialpost caption
                $responsedText_backup =$entry->response;
                Log::debug('responsedText_backup of SocialPost ' .$responsedText_backup);

                $message_id = $entry->id;
                Log::debug('Message_ID of SocialPost ' . $message_id);

               

                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = SP_UserOpenai::whereId($message_id)->first();
                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $this->total_used_tokens;
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

                //Log::debug('before update TOken numbers remaining_words');
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

            //Log::debug('before Skip Caption _Socialpost');
            if (Str::contains($chatGPT_catgory, 'Chat_') == false) {

                if(Str::length($this->response_text) >2 )
                $caption_save=$this->response_text;
                else if(Str::length($responsedText) >2 && $responsedText!= NULL)
                $caption_save=$responsedText_backup;
                else
                $caption_save=$responsedText;


                //add to SP_Captions for show in socialpost caption list
                //Log::debug('before insert Caption _Socialpost caption_text '.$caption_save);
                $caption_table = "sp_captions";
                $caption_database = "main_db";
                $this->SMAI_Ins_Eloq_openAI_Caption_Socialpost($description, $caption_save, $user_id, $message_id, $caption_database, $caption_table);


            }


        } else {
            //echo 'data: [Update Failed user not found]';
        }

}


    }


    //Done
    public function SMAI_UpdateGPT_DigitalAsset($user_id, $usage, $response, $params, $from, $main_message_id = NULL)
    {
        // Update Token and usage
        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (is_array($params))
        $params_json1 = $params;
    else
        $params_json1 = json_decode($params, true);

    if (isset($params_json1['gpt_category']))
        $chatGPT_catgory = $params_json1['gpt_category'];
    else
        $chatGPT_catgory = NULL;

if($params_json1['prompt']!='SKIP')
{


        if (isset($user_id)) {

            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_DigitalAsset from SMAIsyncController : ' . $user_id);

            //$response=json_decode($response,true);

            Log::info(print_r($response, true));

            if (is_array($params))
                $params_json1 = $params;
            else
                $params_json1 = json_decode($params, true);

            if (isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory = NULL;

            $response_bk = $response;
            $response = json_decode($response, true);
            if (Str::contains($chatGPT_catgory, 'Chat_')) {
                //add Sync GPT chat version here

                $total_used_tokens = $usage;
                $chat_id = $this->chat_id;

                Log::debug(' Chat ID before create Eloquent UserOpenaiChatDesign' . $chat_id);
                
                if( $chat_id==NULL) 
                {
                $chat_id_find=UserOpenaiChat::where('id',$this->chat_main_id);

                $chat_id=$chat_id_find->chat_id;
                $this->chat_id =$chat_id_find->chat_id;

                }
                

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

                    if(isset($params_json1['messages'][1]['content']))
                    $message_response=$params_json1['messages'][1]['content'];
                    
                    if(isset($params_json1['response']))
                    $message_response=$params_json1['response'];

                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $message = UserOpenaiChatMessageDesign::whereId($message_id)->first();
                
                if($chat_id != NULL && $chat_id != '')
                $chat = UserOpenaiChatDesign::where('chat_id', $this->chat_id)->first();
                else
                $chat = UserOpenaiChatDesign::where('user_openai_chat_id', $this->chat_main_id)->first();

                
                
                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $this->total_used_tokens;
                $message->words = 0;
                $message->save();

                //$user = UserDesign::where('id',$user_id);
                $user = DB::connection('digitalasset_db')->table('users')->where('id', $user_id)->first();

                if(isset($user->email))
                $user_email = $user->email;
            
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
                $chat->openai_chat_category_id=$this->main_chat_category;
                $chat->save();

                $chat_openai_id = $chat->id;
                $save_user_request_chat = array(

                    'chat_id' => $chat_openai_id,
                    'response' => $responsedText,
                );

                //Define CHat Role Universal
                if (is_array($params_json1["prompt"])!=FALSE)
                {
                    $n_prompt = count($params_json1["prompt"]);

                    if (isset($response['choices'][0]['message']['role']))
                        $this->chat_role = $response['choices'][0]['message']['role'];
                    else
                        $this->chat_role = $params_json1["prompt"][$n_prompt]["role"];
    
    
                    if ($n_prompt > 0) {
                        $n_prompt -= 1;
                    }
    
                    Log::debug('Count n_prompt' . $n_prompt);
                    $x = intval($n_prompt);
    
                    $description = Arr::last($params_json1["prompt"]);
                    $description = implode(" ", $description);

            }
            else{

                $n_prompt = 2;

                $this->chat_role = $params_json1['messages'][1]['role'];

                //Define CHat Role Universal

                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt ' . $n_prompt);
                $role_of_previous_chat = $params_json1['messages'][0]['role'];
                Log::debug('Which Role is : ' . $this->chat_role);
                $description = $params_json1['messages'][0]['content'];


            }

            if(!isset($save_user_request_chat["chat_id"]))
            $save_user_request_chat["chat_id"]=$this->chat_main_id;


            $save_user_request_chat["chat_id_mobile"]=$this->chat_id;
                $save_to_where = "Design";
                $save_user_request_chat["input"] = $description;
                $save_user_q = new SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat, $user_id, $save_to_where, $user_email, $from, $this->chat_role);


            } else {

                if ($settings->openai_default_model == 'gpt-3.5-turbo') {
                    //Log::debug('Response in gpt-3.5-turbo SMAI_UpdateGPT_DigitalAsset from SMAIsyncController ');
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


                } 
                else if (Str::contains($this->GPTModel,'gpt-4-')) {
                    if (isset($response['choices'][0]['message']['content'])) {
                        $message = $response['choices'][0]['message']['content'];
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
                else {
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

                if ($params_json1["model"] == 'whisper-1') {

                    $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                } else {
                    $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }
                //$response = json_encode($response);

                Log::debug(' REsponse String');
                Log::info($response_bk);
                if ($params_json1["model"] == 'whisper-1') {
                    $prompt = $description;
                    $output = $response['text'];
                }

                $response_arr = json_decode($response_bk, true);


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

                //for socialpost caption
                $responsedText_backup =$entry->response;

                $message_id = $entry->id;
                Log::debug('Message_ID of DigitalAsset Design ' . $message_id);

                //Log::info(print_r("Inserted new openai to ID " . $message_id, true));

                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = DigitalAsset_UserOpenai::whereId($message_id)->first();

                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }

                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $this->total_used_tokens;
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


    }

//Done
    public function SMAI_UpdateGPT_MobileApp($user_id, $usage, $response, $params, $from, $main_message_id = NULL)
    {
        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (is_array($params))
        $params_json1 = $params;
    else
        $params_json1 = json_decode($params, true);

    if (isset($params_json1['gpt_category']))
        $chatGPT_catgory = $params_json1['gpt_category'];
    else
        $chatGPT_catgory = NULL;

if($params_json1['prompt']!='SKIP')
{

        if (isset($user_id)) {

            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_MobileApp from SMAIsyncController : ' . $user_id);

            if (is_array($params))
                $params_json1 = $params;
            else
                $params_json1 = json_decode($params, true);


            if (isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory = NULL;

            $response_bk = $response;
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

                    if(isset($params_json1['messages'][1]['content']))
                    $message_response=$params_json1['messages'][1]['content'];
                    
                    if(isset($params_json1['response']))
                    $message_response=$params_json1['response'];


                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $message = UserOpenaiChatMessageMobile::whereId($message_id)->first();
               
                if($chat_id != NULL && $chat_id != '')
                $chat = UserOpenaiChatMobile::where('chat_id', $chat_id)->first();
                else
                $chat = UserOpenaiChatMobile::where('id', $this->chat_main_id)->first();
                
                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $this->total_used_tokens;
                $message->words = 0;
                $message->save();

                $user = DB::connection('mobileapp_db')->table('users')->where('id', $user_id)->first();
                //$user = UserMain::where('id',$user_id);

                $user_email = $user->email;
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
                $chat->openai_chat_category_id=$this->main_chat_category;
                $chat->save();

                $chat_openai_id = $chat->id;
                $save_user_request_chat = array(

                    'chat_id' => $chat_openai_id,
                    'response' => $responsedText,
                );

                //Define CHat Role Universal
                if (is_array($params_json1["prompt"])!=FALSE)
                {
                    $n_prompt = count($params_json1["prompt"]);

                    if (isset($response['choices'][0]['message']['role']))
                        $this->chat_role = $response['choices'][0]['message']['role'];
                    else
                        $this->chat_role = $params_json1["prompt"][$n_prompt]["role"];
    
    
                    if ($n_prompt > 0) {
                        $n_prompt -= 1;
                    }
    
                    Log::debug('Count n_prompt' . $n_prompt);
                    $x = intval($n_prompt);
    
                    $description = Arr::last($params_json1["prompt"]);
                    $description = implode(" ", $description);

            }
            else{

                $n_prompt = 2;

                $this->chat_role = $params_json1['messages'][1]['role'];

                //Define CHat Role Universal

                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt ' . $n_prompt);
                $role_of_previous_chat = $params_json1['messages'][0]['role'];
                Log::debug('Which Role is : ' . $this->chat_role);
                $description = $params_json1['messages'][0]['content'];


            }

            if(!isset($save_user_request_chat["chat_id"]))
                $save_user_request_chat["chat_id"]=$this->chat_main_id;


            $save_user_request_chat["chat_id_mobile"]=$this->chat_id;
                $save_to_where = "MobileAppV2";
                $save_user_request_chat["input"] = $description;
                $save_user_q = new SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat, $user_id, $save_to_where, $user_email, $from, $this->chat_role);

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
                }
                else if (Str::contains($this->GPTModel,'gpt-4-')) {
                    if (isset($response['choices'][0]['message']['content'])) {
                        $message = $response['choices'][0]['message']['content'];
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
                else {
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

                if ($params_json1["model"] == 'whisper-1') {

                    $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                } else {
                    $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }

                if ($params_json1["model"] == 'whisper-1') {
                    $prompt = $description;
                    $output = $response['text'];
                }


                $response_arr = json_decode($response_bk, true);
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

                //for socialpost caption
                $responsedText_backup =$entry->response;

                $message_id = $entry->id;
                Log::debug('Message_ID of MobileAppV2 ' . $message_id);

                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = Mobile_UserOpenai::whereId($message_id)->first();
                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $this->total_used_tokens;
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

    }

    public function SMAI_UpdateGPT_Bio($user_id, $usage, $response, $params, $from, $main_message_id = NULL)
    {
        $settings = $this->settings;
        $settings_two = $this->settings_two;

        if (is_array($params))
        $params_json1 = $params;
    else
        $params_json1 = json_decode($params, true);

    if (isset($params_json1['gpt_category']))
        $chatGPT_catgory = $params_json1['gpt_category'];
    else
        $chatGPT_catgory = NULL;

if($params_json1['prompt']!='SKIP')
{

        if (isset($user_id)) {

            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;


            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_Bio from SMAIsyncController : ' . $user_id);
            Log::debug('With response ');
            Log::info($response);

            if (is_array($params))
                $params_json1 = $params;
            else
                $params_json1 = json_decode($params, true);


            if (isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory = NULL;

            $response_bk = $response;
            $response = json_decode($response, true);
            if (Str::contains($chatGPT_catgory, 'Chat_')) {
                //add Sync GPT chat version here

                $total_used_tokens = $usage;
                $chat_id = $this->chat_id;


                Log::debug('Debug before Insert $this_chat_id' . $this->chat_id);

                Log::debug('THis local Chat ID ' . $chat_id);

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
                    $message_new_ins = DB::connection('bio_db')->table('chats_messages')->insertGetId($data_message, 'chat_message_id');

                } else {

                    //$message_new_ins = UserOpenaiChatMessageMainMarketing::create(['user_id' => $user_id,'chat_id' => $chat_id,'updated_at' => $time ]);
                    $data_message = array(
                        'user_id' => $user_id,
                        'chat_id_mobile' => $chat_id,
                        //updated_at' => $time,
                    );
                    $message_new_ins = DB::connection('bio_db')->table('chats_messages')->insertGetId($data_message, 'chat_message_id');
                }

                $message_id = $message_new_ins;


                if (isset($response['choices'][0]['delta']['content']))
                    $message_response = $response['choices'][0]['delta']['content'];
                if (isset($response['choices'][0]['message']['content']))
                    $message_response = $response['choices'][0]['message']['content'];

                    if(isset($params_json1['messages'][1]['content']))
                    $message_response=$params_json1['messages'][1]['content'];
                    
                    if(isset($params_json1['response']))
                    $message_response=$params_json1['response'];


                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $message = UserOpenaiChatMessageBio::where('chat_message_id', $message_id)->first();
                
                if($chat_id != NULL && $chat_id != '')
                $chat = UserOpenaiChatBio::where('chat_id_mobile', $chat_id)->first();
                else
                $chat = UserOpenaiChatBio::where('user_openai_chat_id', $this->chat_main_id)->first();

                
                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }


                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $this->total_used_tokens;
                $message->words = 0;
                $message->save();

                $user = DB::connection('bio_db')->table('users')->where('user_id', $user_id)->first();
                //$user = UserMain::where('id',$user_id);

                $user_email = $user->email;
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
                

                if (is_array($params_json1["prompt"])!=FALSE)
                {
                    $n_prompt = count($params_json1["prompt"]);

                if (isset($response['choices'][0]['message']['role']))
                    $this->chat_role = $response['choices'][0]['message']['role'];
                else
                    $this->chat_role = $params_json1["prompt"][$n_prompt]["role"];


                if ($n_prompt - 1 == 0) {
                    $n_prompt -= 1;

                    $name_chat = $params_json1["prompt"][$n_prompt]["content"];
                    $role_chat = $params_json1["prompt"][$n_prompt]["role"];

                    if($chat->name==NULL && $role_chat=='user' )
                    $chat->name = $name_chat;
                }

            }
            else{

                $n_prompt = 2;

                $this->chat_role = $params_json1['messages'][1]['role'];

                //Define CHat Role Universal

                if ($n_prompt > 0) {
                    $n_prompt -= 1;

                    $name_chat = $params_json1["messages"][$n_prompt]["content"];
                    $role_chat = $params_json1["messages"][$n_prompt]["role"];

                    if($chat->name==NULL && $role_chat=='user' )
                    $chat->name = $name_chat;
                }

               


            }



                $chat->chat_assistant_id = 1;
                $chat->settings = '[]';
                $chat->used_tokens += $total_used_tokens;
                $chat->total_credits += $total_used_tokens;
                $chat->openai_chat_category_id=$this->main_chat_category;
                $chat->save();

                $chat_openai_id = $chat->chat_id;
                $save_user_request_chat = array(

                    'chat_id_mobile' => $chat_openai_id,
                    'response' => $responsedText,
                );


                if (is_array($params_json1["prompt"])!=FALSE)
                {
                    $n_prompt = count($params_json1["prompt"]);

                    if (isset($response['choices'][0]['message']['role']))
                        $this->chat_role = $response['choices'][0]['message']['role'];
                    else
                        $this->chat_role = $params_json1["prompt"][$n_prompt]["role"];
    
    
                    if ($n_prompt > 0) {
                        $n_prompt -= 1;
                    }
    
                    Log::debug('Count n_prompt' . $n_prompt);
                    $x = intval($n_prompt);
    
                    $description = Arr::last($params_json1["prompt"]);
                    $description = implode(" ", $description);

            }
            else{

                $n_prompt = 2;

                $this->chat_role = $params_json1['messages'][1]['role'];

                //Define CHat Role Universal

                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt ' . $n_prompt);
                $role_of_previous_chat = $params_json1['messages'][0]['role'];
                Log::debug('Which Role is : ' . $this->chat_role);
                $description = $params_json1['messages'][0]['content'];


            }

            if(!isset($save_user_request_chat["chat_id"]))
                $save_user_request_chat["chat_id"]=$this->chat_main_id;


            $save_user_request_chat["chat_id_mobile"]=$this->chat_id;
                $save_to_where = "Bio";
                $save_user_request_chat["input"] = $description;
                $save_user_q = new SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat, $user_id, $save_to_where, $user_email, $from, $this->chat_role);

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
                else if (Str::contains($this->GPTModel,'gpt-4-')) {
                    if (isset($response['choices'][0]['message']['content'])) {
                        $message = $response['choices'][0]['message']['content'];
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
                else {
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


                // Save Users of Bio 
                $user = \DB::connection('bio_db')->table('users')->where('user_id', $user_id)->get();
                //$users = DB::connection('second_db')->table('users')->get();


                $post = OpenAIGenerator::where('slug', $post_type)->first();
                
            //wait for fix bug to merge Template AI Docs
                $entry = new UserBioOpenai();

                Log::debug('Check if create AI Bio Doc success'.$entry->document_id);

                $entry->title = 'New Workbook';
                

                if ($params_json1["model"] == 'whisper-1') {

                    $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                } else {
                    $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                }

                if ($params_json1["model"] == 'whisper-1') {
                    $prompt = $description;
                    $output = $response['text'];
                }


                $response_arr = json_decode($response_bk, true);
                $entry->user_id = $user_id;
                $entry->openai_id = $post->id;
                $entry->input = $prompt;
                $entry->response = serialize(json_encode($response_arr));
                $entry->output = $output;
                $entry->hash = str()->random(256);
                $entry->credits = 0;
                $entry->words = 0;
                $entry->main_user_openai_id = $main_message_id;
                $entry->name = $entry->title;

                if($this->chatGPT_catgory=='Text_Design' || $this->chatGPT_catgory=='DocText_SocialPost')
                {
                    $entry->template_id = 11;
                    $entry->type = 11;
                    $entry->template_category_id=2;
                    $entry->content=$entry->output;

                }



                $entry->save();

                //for socialpost caption
                if($this->chatGPT_catgory=='Images_Design' || $this->platform=='design')
                $responsedText_backup =$entry->$output;
                else
                $responsedText_backup =$entry->response;

                if(isset($entry->id))
                $message_id = $entry->id;
                else
                $message_id =$entry->document_id ;

                Log::debug('Message_ID of Bio ' . $message_id);

                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = UserBioOpenai::where('document_id',$message_id)->first();
                
               // new update gpt-4-0613
               if(isset($message->response))
               {
                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }
               }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $this->total_used_tokens;
                //bug fix already fix
                $message->words = $this->total_used_tokens;
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
                $user = \DB::connection('bio_db')->table('users')->where('user_id', $user_id)->get();
                if ($user[0]->remaining_words != -1) {
                    $user[0]->remaining_words -= $total_used_tokens;
                    $new_remaining_words = $user[0]->remaining_words - $total_used_tokens;
                    // $user[0]->save();
                    $user_update = DB::connection('bio_db')->update('update users set remaining_words = ? where user_id = ?', array($new_remaining_words, $user_id));

                }

                if ($user[0]->remaining_words < -1) {
                    $user[0]->remaining_words = 0;
                    // $user[0]->save();
                    $new_remaining_words = 0;
                    $user_update = DB::connection('bio_db')->update('update users set remaining_words = ? where user_id = ?', array($new_remaining_words, $user_id));


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
    }

    public function SMAI_UpdateGPT_SyncNodeJs($user_id, $usage, $response, $params, $from, $main_message_id = NULL)
    {
        $settings = $this->settings;
        $settings_two = $this->settings_two;
        if (is_array($params))
        $params_json1 = $params;
    else
        $params_json1 = json_decode($params, true);

    if (isset($params_json1['gpt_category']))
        $chatGPT_catgory = $params_json1['gpt_category'];
    else
        $chatGPT_catgory = NULL;

if($params_json1['prompt']!='SKIP')
{


        if (isset($user_id)) {

            $responsedText = "";
            $output = "";
            $total_used_tokens = 0;

            Log::debug('User ID log in smaisync_tokens SMAI_UpdateGPT_SyncNodeJS from SMAIsyncController : ' . $user_id);

            if (is_array($params))
                $params_json1 = $params;
            else
                $params_json1 = json_decode($params, true);


            if (isset($params_json1['gpt_category']))
                $chatGPT_catgory = $params_json1['gpt_category'];
            else
                $chatGPT_catgory = NULL;

            $response_bk = $response;
            $response = json_decode($response, true);

            Log::debug('Debug after jSOn dEcode respone');
            if (Str::contains($chatGPT_catgory, 'Chat_')) {
                //add Sync GPT chat version here

                $total_used_tokens = $usage;

                if(isset($this->chat_id))
                {
                $chat_id = $this->chat_id;
                }

                Log::debug('Debug after this _ chat_id');


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

                Log::debug('Debug after message_id');
                if (isset($response['choices'][0]['delta']['content']))
                    $message_response = $response['choices'][0]['delta']['content'];
                if (isset($response['choices'][0]['message']['content']))
                    $message_response = $response['choices'][0]['message']['content'];

                    if(isset($params_json1['messages'][1]['content']))
                    $message_response=$params_json1['messages'][1]['content'];
                    
                    if(isset($params_json1['response']))
                    $message_response=$params_json1['response'];

                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message_response);
                $output .= $messageFix;
                $responsedText .= $message_response;

                $message = UserOpenaiChatMessageSyncNodeJS::whereId($message_id)->first();
                
                if($chat_id != NULL && $chat_id !='')
                $chat = UserOpenaiChatSyncNodeJS::where('chat_id', $chat_id)->first();
                else
                $chat = UserOpenaiChatSyncNodeJS::where('user_openai_chat_id', $this->chat_main_id)->first();

                
                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $this->total_used_tokens;
                $message->words = 0;
                $message->save();

                $user = DB::connection('sync_db')->table('user')->where('id', $user_id)->first();
                //$user = UserMain::where('id',$user_id);

                $user_email = $user->email;
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
                $chat->openai_chat_category_id=$this->main_chat_category;
                $chat->save();

                $chat_openai_id = $chat->id;
                $save_user_request_chat = array(

                    'chat_id' => $chat_openai_id,
                    'response' => $responsedText,
                );

                //Define CHat Role Universal
               
                if (is_array($params_json1["prompt"])!=FALSE)
                {
                    $n_prompt = count($params_json1["prompt"]);

                    if (isset($response['choices'][0]['message']['role']))
                        $this->chat_role = $response['choices'][0]['message']['role'];
                    else
                        $this->chat_role = $params_json1["prompt"][$n_prompt]["role"];
    
    
                    if ($n_prompt > 0) {
                        $n_prompt -= 1;
                    }
    
                    Log::debug('Count n_prompt' . $n_prompt);
                    $x = intval($n_prompt);
    
                    $description = Arr::last($params_json1["prompt"]);
                    $description = implode(" ", $description);

            }
            else{

                $n_prompt = 2;

                 if(isset($params_json1['messages'][1]['role']))
                 $this->chat_role = $params_json1['messages'][1]['role'];

                //Define CHat Role Universal

                if ($n_prompt > 0) {
                    $n_prompt -= 1;
                }

                Log::debug('Count n_prompt ' . $n_prompt);
                $role_of_previous_chat = $params_json1['messages'][0]['role'];
                Log::debug('Which Role is : ' . $this->chat_role);
                $description = $params_json1['messages'][0]['content'];


            }

            if(!isset($save_user_request_chat["chat_id"]))
                $save_user_request_chat["chat_id"]=$this->chat_main_id;


            $save_user_request_chat["chat_id_mobile"]=$this->chat_id;
                $save_to_where = "SyncNodeJS";
                $save_user_request_chat["input"] = $description;
                $save_user_q = new SMAIUpdateProfileController();
                $save_user_q->lowChatSave($save_user_request_chat, $user_id, $save_to_where, $user_email, $from, $this->chat_role);

            } else {


                if ($settings->openai_default_model == 'gpt-3.5-turbo') {
                    Log::debug('Debug after gpt-3.5-turbo');
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

                            Log::debug('Debug after response');
                            if (isset($this->postContent) && Str::length($this->postContent) > 0) {
                                
                                Log::debug('Debug before trim postContent');
                                $message = trim($this->postContent);
                                $messageFix = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
                                $output .= $messageFix;
                                $responsedText .= $message;
                                $total_used_tokens += Helper::countWords($messageFix);

                                $string_length = Str::length($messageFix);
                                $needChars = 6000 - $string_length;
                                $random_text = Str::random($needChars);
                                Log::debug('Debug finished trim postContent');

                            }

                        }
                    }
                } 
                else if (isset($this->GPTModel) && Str::contains($this->GPTModel,'gpt-4-')) {
                    Log::debug('Debug after gpt-4-');
                    if (isset($response['choices'][0]['message']['content'])) {
                        $message = $response['choices'][0]['message']['content'];
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
                else {
                    if (isset($response->choices[0]->text)) {
                        Log::debug('Debug after response_choices0_text none GPT version');
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

                Log::debug('Debug after NodeJs params_json');
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
                $user = \DB::connection('sync_db')->table('user')->where('id', $user_id)->get();
                //$users = DB::connection('second_db')->table('users')->get();


                $post = OpenAIGenerator::where('slug', $post_type)->first();
                $entry = new UserSyncNodeJSOpenai();
                $entry->title = 'New Workbook';

                Log::debug('Check if create AI Doc success');

                if(isset($params_json1["model"]))
                {
                    if ($params_json1["model"] == 'whisper-1') {

                        $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
                    } else {
                        $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
                    }
                    

                    if ($params_json1["model"] == 'whisper-1') {
                        $prompt = $description;
                        $output = $response['text'];
                    }
                }


                Log::debug('Debug before response_arr');
                $response_arr = json_decode($response_bk, true);
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

                //for socialpost caption
                $responsedText_backup =$entry->response;

                $message_id = $entry->id;
                Log::debug('Message_ID of MobileAppV2 ' . $message_id);

                // Create UserOpenai Models belong to OpenAIGenerator Models
                $message = UserSyncNodeJSOpenai::whereId($message_id)->first();
                if ($params_json1["model"] == 'whisper-1') {
                    $message->response = serialize(json_encode($response_arr));

                } else {
                    $message->response = $responsedText;

                }
                $message->output = $output;
                $message->hash = Str::random(256);
                $message->credits = $this->total_used_tokens;
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
                $user = \DB::connection('sync_db')->table('user')->where('id', $user_id)->get();
                if ($user[0]->remaining_words != -1) {
                    $user[0]->remaining_words -= $total_used_tokens;
                    $new_remaining_words = $user[0]->remaining_words - $total_used_tokens;
                    // $user[0]->save();
                    $user_update = DB::connection('sync_db')->update('update user set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));

                }

                if ($user[0]->remaining_words < -1) {
                    $user[0]->remaining_words = 0;
                    // $user[0]->save();
                    $new_remaining_words = 0;
                    $user_update = DB::connection('sync_db')->update('update user set remaining_words = ? where id = ?', array($new_remaining_words, $user_id));


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


    }

    //working send to /social
    public function SMAI_UpdateGPT_Social($user_id, $main_openai_id,$type=NULL)
    {

        //get post detail from main_openai
        $main_coin_openai = UserOpenai::where('id', $main_openai_id)->first();
       
        if($type=='image')
        {
            $description=$main_coin_openai->input;

   
        }
        else
        {
            $description=$main_coin_openai->output;
        }

        //add new post to social timeline
        $post = new PostSmartSocial();
        $post->user_id = $user_id;
        $post->publisher = 'post';
        $post->publisher_id = $user_id;
        $post->post_type = "general";
        $post->privacy = "private";
        $post->tagged_user_ids = json_encode(array());
        $post->description =$description;
        $post->status = 'active';
        $post->user_reacts = json_encode(array());
        $post->shared_user = json_encode(array());
        $time = time();
        $post->created_at = $time;
        $post->updated_at = $time;
        $post->smai_log_from = $this->platform;
        $post->smai_log_type = $this->chatGPT_catgory;
        $post->main_user_openai_id = $main_openai_id;
        $post->origin_user_openai_id = $main_openai_id;
        $post->save();
        $done = $post->id; // get the ID

        if($type=='image')
        {
            $file_name=$main_coin_openai->output;
            $media_file_data = array('user_id' => $user_id, 'post_id' => $done, 'file_name' => $file_name, 'file_type' => 'image', 'privacy' => 'private');
            $media_file_data['created_at'] = time();
            $media_file_data['updated_at'] = $media_file_data['created_at'];
            Media_files::create($media_file_data);
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

        if ($params_json1["model"] == 'whisper-1') {
            $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
        } else {
            $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
        }

        if ($params_json1["model"] == 'whisper-1') {
            $prompt = $description;
            $output = $response['text'];
        }

        $response_arr = json_decode($response_bk, true);
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

        //for socialpost caption
        $responsedText_backup =$entry->response;

        $message_id = $entry->id;
        Log::debug('Message_ID of working ' . $message_id);


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
        
       

        $team_id = $user[0]->id;
        if (isset($openai_id) && $openai_id > 0)
            $parentid = $openai_id;
        else
            $parentid = '';

        $parent_openai_data = SP_UserOpenai::where('id', $parentid)->first();
        $response_from_parent= $parent_openai_data->response;

        $isUTF8 = preg_match('//u', $content);

        if(Str::length($content)<2 || $content==NULL || $isUTF8==false) 
        $content=$response_from_parent;

        if(Str::contains($this->platform,'social') || Str::contains($this->platform,'design'))
        $content=$response_from_parent;



        if(Str::length($content)<2 || $content==NULL )
        $wait_fix=1;
        else
        $wait_fix=0;

        
       
        

        $entry = new SP_UserCaption();

        $entry->wait_for_fix = $wait_fix;
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

    public function update_token_centralize($user_id, $email, $token_array)
    {

    }


    public function imageOutput_save_main_coin($user_id, $usage, $response, $params, $size = NULL, $post = NULL, $style = NULL, $lighting = NULL, $mood = NULL, $number_of_images = 1, $image_generator = 'DE', $negative_prompt = NULL,$main_image_id=NULL)
    {
        $image_arr = array(
            'style' => $style,
            'artist' => 'Leonardo da Vinci',
            'lighting' => $lighting,
            'mood' => $mood,
        );

        $path_array = array();
        $image_storage = "s3";
        Log::debug('Usage in imageOutput_save ' . $usage);
        Log::debug('user_id in imageOutput_save ' . $user_id);
        Log::debug('response in imageOutput_save ' . $response);
        Log::info($response);

        if(isset($response))
        {
            //case if defind imgae_url from $data_image
            $image_url_decode=json_decode($response,true);
            if(isset($image_url_decode['image_url']))
            {
            $this->image_url=$image_url_decode['image_url'];
            Log::debug(' Debug reponse url '.$this->image_url);
            Log::info($this->image_url);
            }
        }

        Log::debug('params in imageOutput_save ');
        Log::info($params);

        //$params_json = json_decode($params, true);
        if (is_array($params))
            $params_json = $params;
        else
            $params_json = json_decode($params, true);

        $prompt = $params_json['prompt'];
        $prompt = preg_replace('/[^A-Za-z0-9 ]/', '', $prompt);
        $size = $params_json['size'];
        $number_of_images = $params_json['n'];
        $chatGPT_catgory = $params_json['gpt_category'];

        $image_arr['size'] = $size;
        $file_ImageSize = $params_json['file_size'];


        $user = UserMain::where('id', $user_id)->first();
        //save generated image datas
        $entries = [];

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

        $this->settings_two = SettingTwo::first();
        $image_storage = $this->settings_two->ai_image_storage;

        Log::debug('Current Storage setting' . $image_storage);


        for ($i = 0; $i < $number_of_images; $i++) {

            //use this for openAI Images
            if ($image_generator != self::STABLEDIFFUSION) {

                if (isset($params_json['nameOfImage'])) {

                    if($chatGPT_catgory == 'Images_Bio')
                    {
                        //Log::info($response);
                        //$image_url = $this->image_url;
                        $contents = $this->image_url;
                    }
                    
                    else
                    {
                        $contents = $params_json['contents'];
                    }

                    $nameOfImage = $params_json['nameOfImage'];

                } else {

                    //send prompt to openai
                    if ($prompt == null) return response()->json(["status" => "error", "message" => "You must provide a prompt"]);

                    if(is_array($response)==false)
                    {
                         $response = json_decode($response, true);
                    
                    }

                    if ($chatGPT_catgory == 'Images_Design' || $chatGPT_catgory == 'Images_SocialPost' )
                    {
                        $image_url = $response['data'][0]['url'];
                        $contents = $image_url;
                    }
                    else if($chatGPT_catgory == 'Images_Bio')
                    {
                        
                        /* $image_url = $response['b64_json'];
                        $contents = base64_decode($image_url); */
                        $contents = $this->image_url;
                    }
                    else
                    {
                        $image_url = $response['data'][0]['b64_json'];
                        $contents = $image_url;
                    }

                    
                    
                    $nameOfImage = Str::random(12) . '-DALL-E-' . Str::slug($prompt) . '.png';

                }

                //save file on local storage or aws s3


            } else {


                if (isset($params_json['nameOfImage'])) {

                    if($chatGPT_catgory == 'Images_Bio')
                    {
                      
                       /*  $image_url = $response['b64_json'];
                        $contents = base64_decode($image_url); */
                        $contents = $this->image_url;
                    }
                    
                    else
                    {
                    $contents = $params_json['contents'];
                    }
                    
                    
                    $nameOfImage = $params_json['nameOfImage'];

                } else {
                    //send prompt to stablediffusion
                    $settings = SettingTwo::first();
                    /* $width = explode('x', $size)[0];
                    $height = explode('x', $size)[1]; */

                    //SMAI response data start
                    $body = $response->getBody();
                    if ($response->getStatusCode() == 200) {

                        $nameOfImage = Str::random(12) . '-' . Str::slug($prompt) . '.png';

                        $contents = base64_decode(json_decode($body)->artifacts[0]->base64);
                    } else {
                        $message = '';
                        if ($body->status == "error")
                            $message = $body->message;
                        else
                            $message = "Failed, Try Again";
                        return response()->json(["status" => "error", "message" => $message]);
                    }

                }


            }

            Log::debug('Image name '.$nameOfImage);
            Log::debug(' Check Content before get ');
            //Log::info($contents);
            Storage::disk('topics')->put($nameOfImage, file_get_contents($contents));
            $path = 'https://smartstock.social/uploads/topics/' . $nameOfImage;
            $path_s3 = 'uploads/topics/' . $nameOfImage;
            $uploadedFile = new File($path_s3);
            $aws_path = Storage::disk('s3')->put('', $uploadedFile);
            unlink($path_s3);
            $path = Storage::disk('s3')->url($aws_path);
            if ($path) {
                Log::debug('success path of upload');
                Log::info($path);
            }


            // Save Users of MainCoIN
            $user = UserMain::where('id', $user_id)->first();
            //$users = DB::connection('second_db')->table('users')->get();


            $post_type = 'ai_image_generator';
            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new UserOpenai();
            $entry->title = 'New Image';
            $entry->slug = Str::random(7) . Str::slug($user->name) . '-workbsook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;

            if(is_array($prompt)==true)
            $entry->input = json_encode($prompt);
            else
            $entry->input = $prompt;
        
            $entry->response = $image_generator == "stablediffusion" ? "SD" : "DE";
            $entry->output = $image_storage == "s3" ? $path : '/' . $path;
            $entry->hash = Str::random(256);
            $entry->credits = 1;
            $entry->words = 0;
            $entry->storage = UserOpenai::STORAGE_AWS;
            $entry->file_size = $file_ImageSize;
            $entry->origin_user_openai_id =$this->image_origin_id;
            $entry->save();

            $image_arr['main_image_id'] =$entry->id;
            $this->main_openai_id=$entry->id;
            Log::debug('Image ID '.$entry->id);



              if(Str::contains($this->chatGPT_catgory,'_SmartContentCoIn')==true || Str::contains($this->chatGPT_catgory,'_main_coin') ==true )
              {
                
              }
              else{

                      //Bio
                      if(Str::contains($this->chatGPT_catgory,'_SmartBio')==true || Str::contains($this->chatGPT_catgory,'_Bio')==true )
                      {

                          $image_origin_update=ImagesBio::where('image_id',$this->image_origin_id)->first();
                      }

                      //SocialPost
                      if(Str::contains($this->chatGPT_catgory,'_SocialPost')==true || Str::contains($this->chatGPT_catgory,'_socialpost')==true || Str::contains($this->platform,'socialpost')==true )
                      {

                        $post_type = 'ai_image_generator';
                        $post = OpenAIGenerator::where('slug', $post_type)->first();
                        $entrys = new SP_UserOpenai();
                        $entrys->title = 'New Image';
                        $entrys->slug = $entry->slug;
                        $entrys->user_id = $user_id;
                        $entrys->openai_id = $post->id;
                        $entrys->input = $prompt;
                        $entrys->response = $image_generator == "stablediffusion" ? "SD" : "DE";
                        $entrys->output = $image_storage == "s3" ? $path : '/' . $path;
                        $entrys->hash = Str::random(256);
                        $entrys->credits = 1;
                        $entrys->words = 0;
                        $entrys->storage = UserOpenai::STORAGE_AWS;

                        $entrys->main_user_openai_id =$entrys->id;
                        $entrys->save();

                        $this->image_origin_id=$entrys->id;

                        $entry->origin_user_openai_id =$this->image_origin_id;
                        $entry->save();
                          
                        $image_origin_update=SP_UserOpenai ::where('id',$this->image_origin_id)->first();
                      
                      
                        }

                     /*  //BIo
                      if(Str::contains($this->chatGPT_catgory,'_SmartBio')==true || Str::contains($this->chatGPT_catgory,'_Bio')==true )
                      {
                          $image_origin_update=UserBioOpenai::where('id',$this->image_origin_id)->first();
                      } */

                      //Sync wait for update real image table
                      if(Str::contains($this->chatGPT_catgory,'_SyncNodeJS')==true || Str::contains($this->chatGPT_catgory,'_sync')==true )
                      {
                          $image_origin_update=UserSyncNodeJSOpenai::where('id',$this->image_origin_id)->first();
                      }

                      //MobileV2 wait for update real image table
                      if(Str::contains($this->chatGPT_catgory,'_mobile')==true || Str::contains($this->chatGPT_catgory,'_MobileApp')==true )
                      {
                          $image_origin_update=Mobile_UserOpenai::where('id',$this->image_origin_id)->first();
                      }

                      //Design Digital_asset
                      if(Str::contains($this->chatGPT_catgory,'_design')==true || Str::contains($this->chatGPT_catgory,'_Design')==true || Str::contains($this->platform,'design')==true)
                      {
                          $image_origin_update=DigitalAsset_UserOpenai::where('id',$this->image_origin_id)->first();
                      
                          $post_type = 'ai_image_generator';
                          $post = OpenAIGenerator::where('slug', $post_type)->first();
                          $entrys = new DigitalAsset_UserOpenai();
                          $entrys->title = 'New Image';
                          $entrys->slug = $entry->slug;
                          $entrys->user_id = $user_id;
                          $entrys->openai_id = $post->id;
                          $entrys->input = $prompt;
                          $entrys->response = $image_generator == "stablediffusion" ? "SD" : "DE";
                          $entrys->output = $image_storage == "s3" ? $path : '/' . $path;
                          $entrys->hash = Str::random(256);
                          $entrys->credits = 1;
                          $entrys->words = 0;
                          $entrys->storage = UserOpenai::STORAGE_AWS;
  
                          $entrys->main_user_openai_id =$entrys->id;
                          $entrys->save();
  
                          $this->image_origin_id=$entrys->id;
  
                          $entry->origin_user_openai_id =$this->image_origin_id;
                          $entry->save();
                            
                          $image_origin_update=DigitalAsset_UserOpenai::where('id',$this->image_origin_id)->first();
                        
                        }

                      if(isset($image_origin_update->main_user_openai_id))
                      {
                        $image_origin_update->main_user_openai_id = $entry->id;
                     
                        $image_origin_update->save();
                      }
                      else
                      {

                             /* if(Str::contains($this->chatGPT_catgory,'_design')==true || Str::contains($this->chatGPT_catgory,'_Design')==true || Str::contains($this->platform,'design')==true)
                                {
                                    UserOpenai::query()
                                        ->where('id','>', $entry->id)
                                        ->each(function ($oldRecord) {
                                            $newRecord = NEW DigitalAsset_UserOpenai();
                                            $newRecord = $oldRecord->replicate();
                                            //$newRecord->setTable('inactive_users');
                                            $newRecord->save();
                                            $oldRecord->delete();
                                        });

                                } */
                      }
                     


                      

                     

              }

            //push each generated image to an array
            array_push($entries, $entry);

            if ($user->remaining_images - 1 == -1) {
                $user->remaining_images = 0;
                $user->save();
                $userOpenai = UserOpenai::where('user_id', $user_id)->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
                $openai = OpenAIGenerator::where('id', $post->id)->first();
                //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
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

            array_push($path_array, $path);
        }

        $image_arr['file_size'] = $file_ImageSize;
        $return_arr = array(
            'path_array' => $path_array,
            'image_array' => $image_arr,

        );

        return $return_arr;

        // return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);
    }


    public function imageOutput_save_SocialPost_self($user_id, $size, $path, $img_width, $img_height, $image_arr, $prompt,$main_image_id=NULL)
    {

        //update openai_usage_tokens
        //update_team_data("openai_usage_tokens", get_team_data("openai_usage_tokens", 0) + $usage);

        $team_data = SPTeam::where('owner', $user_id)->first();
        $team_id = $team_data->id;

        $tmp_file = "";
        $file_path = $path;
        $folder = 0;

        $fileSize = $image_arr['file_size'];

        $data_image = array(
            "ids" => $this->ids(),
            "team_id" => $team_id,
            "is_folder" => 0,
            "pid" => $folder,
            "name" => str_replace("https://smartcontent-ai-image.s3.amazonaws.com/", "", $file_path),
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

    public function imageOutput_save_Bio_self($user_id, $size, $path, $img_width, $img_height, $image_arr, $prompt,$main_image_id=NULL)
    {


        $user_bio = UserBio::where('user_id', $user_id)->first();
        $plan_settings = $user_bio->plan_settings;
        $plan_settings_json = json_decode($plan_settings, true);
        //api from plan_setting
        $images_api = $plan_settings_json['images_api'];

        $settings_arr = array(
            "variants" => 1,
        );
        $settings = json_encode($settings_arr);
        $project_id = NULL;
        $name = str_replace("https://smartcontent-ai-image.s3.amazonaws.com/", "", $path);
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
            'input' => json_encode($prompt),
            'image' => strval($path),
            'style' => $image_arr['style'],
            'artist' => $image_arr['artist'],
            'lighting' => $image_arr['lighting'],
            'mood' => $image_arr['mood'],
            'size' => $size,
            'settings' => $settings,
            'api' => $images_api,
            'api_response_time' => 9999,
            'datetime' => date("Y-m-d H:i:s"),
            'main_user_openai_id' => $main_image_id,
        ]);

        $image_bio_new_ins->save(); //returns true
        $images_bio_new_ins_id=$image_bio_new_ins->id;

        // $image_bio_new_ins = ImagesBio::create($data_image);
        Log::debug('Insert new image to self_Bio result');
        Log::info($image_bio_new_ins);

        //Check if double inserted
        if(Str::contains($this->chatGPT_catgory,'_SmartBio')==true || Str::contains($this->chatGPT_catgory,'_Bio')==true )
        {
           $main_user_origin=UserOpenai::where('id',$main_image_id)->first();
           $main_user_origin_id= $main_user_origin->origin_user_openai_id;
           $images_bio_origin=ImagesBio::where('image_id',$main_user_origin_id)->first();
           $images_bio_origin->image = $image_bio_new_ins->image ;
           //update original
           $images_bio_origin->save();
           //del the new 
           $image_bio_new_ins->delete();

        }



    }

    public function imageOutput_save_Bio($user_id, $prompt, $number_of_images, $path_array, $image_array,$main_image_id=NULL)
    {

        if(isset($this->image_generator))
        $image_generator = $this->image_generator;
        else
        $image_generator = 'DE';

        $entries = [];
        $user = UserBio::where('user_id', $user_id)->first();

        for ($i = 0; $i < count($path_array); $i++) {

            $post_type = 'ai_image_generator';
            $image_storage = "s3";
            $path = $path_array[$i];
            $post = OpenAIGenerator::where('slug', $post_type)->first();
           
            // change to update not insert
           /*  $entry = new ImagesBio();
            $entry->name = 'New Image';
            $entry->slug = Str::random(7) . Str::slug($user->name) . '-workbsook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;
            $entry->input = $prompt;
            $entry->response = $image_generator == "stablediffusion" ? "SD" : "DE";
            $entry->output = $image_storage == "s3" ? $path : '/' . $path;
            $entry->hash = Str::random(256);
            $entry->credits = 1;
            $entry->words = 0;
            $entry->storage = UserOpenai::STORAGE_AWS;
            $entry->main_user_openai_id =$main_image_id;
            $entry->save(); */

            //push each generated image to an array
           

            Log::debug('Image array in  imageOutput_save_Bio');
            Log::info($image_array);
            $size = $image_array['size'];
            $size_arr = explode("x", $size);
            $img_width = $size_arr[0];
            $img_height = $size_arr[1];
           
            // change this 2 method to Update data not insert incase from Bio
            $this->imageOutput_save_Bio_self($user_id, $size, $path, $img_width, $img_height, $image_array, $prompt,$main_image_id);
            
        }
        //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);

        Log::debug('imageOutput_save_Bio');
        Log::debug(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);

    }

    public function imageOutput_save_SocialPost($user_id, $prompt, $number_of_images, $path_array, $image_array,$main_image_id=NULL)
    {
        
        if(isset($this->image_generator))
        $image_generator = $this->image_generator;
        else
        $image_generator = 'DE';


        $entries = [];
        $user = UserSP::where('id', $user_id)->first();

        for ($i = 0; $i < count($path_array); $i++) {

            $post_type = 'ai_image_generator';
            $image_storage = "s3";
            $path = $path_array[$i];

            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new SP_UserOpenai();
            $entry->title = 'New Image';
            $entry->slug = Str::random(7) . Str::slug($user->name) . '-workbsook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;

            if(is_array($prompt)==true)
            $entry->input = json_encode($prompt);
            else
            $entry->input = $prompt;


            $entry->response = $image_generator == "stablediffusion" ? "SD" : "DE";
            $entry->output = $image_storage == "s3" ? $path : '/' . $path;
            $entry->hash = Str::random(256);
            $entry->credits = 1;
            $entry->words = 0;
            $entry->storage = UserOpenai::STORAGE_AWS;
            $entry->main_user_openai_id =$main_image_id;
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

            $size = $image_array['size'];
            $size_arr = explode("x", $size);
            $img_width = $size_arr[0];
            $img_height = $size_arr[1];
            $this->imageOutput_save_SocialPost_self($user_id, $size, $path, $img_width, $img_height, $image_array, $prompt);
        }
        //return response()->json(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);

        Log::debug('imageOutput_save_SocialPost ');
        Log::debug(["status" => "success", "images" => $entries, "image_storage" => $image_storage]);


    }

    public function imageOutput_save_Design($user_id, $prompt, $number_of_images, $path_array,$main_image_id=NULL)
    {

         Log::debug('imageOutput_save_Design  User ID '.$user_id);
        if(isset($this->image_generator))
        $image_generator = $this->image_generator;
        else
        $image_generator = 'DE';
        
        
        $entries = [];

        if($user_id==1)
        $user_check_id=20;
        else
        $user_check_id=$user_id;

        $user = UserDesign::where('id', $user_check_id)->first();

        for ($i = 0; $i < count($path_array); $i++) {

            $post_type = 'ai_image_generator';
            $image_storage = "s3";
            $path = $path_array[$i];

            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new DigitalAsset_UserOpenai();
            $entry->title = 'New Image';
            $entry->slug = Str::random(7) . Str::slug($user->name) . '-workbsook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;

            if(is_array($prompt)==true)
            $entry->input = json_encode($prompt);
            else
            $entry->input = $prompt;

            $entry->response = $image_generator == "stablediffusion" ? "SD" : "DE";
            $entry->output = $image_storage == "s3" ? $path : '/' . $path;
            $entry->hash = Str::random(256);
            $entry->credits = 1;
            $entry->words = 0;
            $entry->storage = UserOpenai::STORAGE_AWS;
            $entry->main_user_openai_id =$main_image_id;
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


    public function imageOutput_save_Sync($user_id, $prompt, $number_of_images, $path_array,$main_image_id=NULL)
    {

        if(isset($this->image_generator))
        $image_generator = $this->image_generator;
        else
        $image_generator = 'DE';


        $entries = [];
        $user = UserSyncNodeJS::where('id', $user_id)->first();

        for ($i = 0; $i < count($path_array); $i++) {

            $post_type = 'ai_image_generator';
            $image_storage = "s3";
            $path = $path_array[$i];

            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new UserSyncNodeJSOpenai();
            $entry->title = 'New Image';
            $entry->slug = Str::random(7) . Str::slug($user->name) . '-workbsook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;

            if(is_array($prompt)==true)
            $entry->input = json_encode($prompt);
            else
            $entry->input = $prompt;

            $entry->response = $image_generator == "stablediffusion" ? "SD" : "DE";
            $entry->output = $image_storage == "s3" ? $path : '/' . $path;
            $entry->hash = Str::random(256);
            $entry->credits = 1;
            $entry->words = 0;
            $entry->storage = UserOpenai::STORAGE_AWS;
            $entry->main_user_openai_id =$main_image_id;
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


    public function imageOutput_save_MobileAppV2($user_id, $prompt, $number_of_images, $path_array,$main_image_id=NULL)
    {

        if(isset($this->image_generator))
        $image_generator = $this->image_generator;
        else
        $image_generator = 'DE';


        $entries = [];
        $user = UserMobile::where('id', $user_id)->first();

        for ($i = 0; $i < count($path_array); $i++) {

            $post_type = 'ai_image_generator';
            $image_storage = "s3";
            $path = $path_array[$i];

            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new Mobile_UserOpenai();
            $entry->title = 'New Image';
            $entry->slug = Str::random(7) . Str::slug($user->name) . '-workbsook';
            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;

             if(is_array($prompt)==true)
            $entry->input = json_encode($prompt);
            else
            $entry->input = $prompt;

            $entry->response = $image_generator == "stablediffusion" ? "SD" : "DE";
            $entry->output = $image_storage == "s3" ? $path : '/' . $path;
            $entry->hash = Str::random(256);
            $entry->credits = 1;
            $entry->words = 0;
            $entry->storage = UserOpenai::STORAGE_AWS;
            $entry->main_user_openai_id =$main_image_id;
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

    public function lowGenerateSaveAll($usage, $response, $main_message_id)
    {
        //$response = $request->response;
        $total_user_tokens = $usage;

        Log::debug('Main Message or DocTextOpenAi AI ID in lowGenerateSaveAll' . $main_message_id);
        Log::debug('Main response in lowGenerateSaveAll' . $response);
        

        for ($i = 0; $i <= 4; $i++) {
            if ($i == 0)
                $entry = DigitalAsset_UserOpenai::where('main_user_openai_id', $main_message_id)->first();

            if ($i == 1)
                $entry = SP_UserOpenai::where('main_user_openai_id', $main_message_id)->first();

            if ($i == 2)
                $entry = Mobile_UserOpenai::where('main_user_openai_id', $main_message_id)->first();

            if ($i == 3)
                $entry = UserBioOpenai::where('main_user_openai_id', $main_message_id)->first();

            if ($i == 4)
                $entry = UserSyncNodeJSOpenai::where('main_user_openai_id', $main_message_id)->first();

            Log::debug('Debug User Open AI ');
            Log::info($entry);

            if($i == 3)
            {
                if($response==NULL || Str::length($response) <2 || Str::contains($response,'null'))
                    {
                        Log::debug('Case Output NULL or Empty ');

                        //$response_from_openai=DB::connection('main_db')->table('user_openai')->where('id',$this->main_openai_id)->first();
                        $response_from_openai=SP_UserOpenai::where('id',$main_message_id)->first();
                        $response=$response_from_openai->response;
                        $output=$response_from_openai->output;

                        //Log::debug('Debug response from Main '. $response_from_openai->response);
                        //Log::debug('Debug output from Main '. $response_from_openai->output); 


                    }

            } 

            if (isset($entry)) {

                if(isset($output))
                {
                  $entry->output = $output;
                 
                }
                else
                {
                  $entry->output = $response;

                }

                if($i == 3)
                $entry->content  = $response;

                Log::debug('Found Total_token from Main usage in Lower SaveAll :'.$this->total_used_tokens);

                $entry->credits = $this->total_used_tokens;
                $entry->words = $this->total_used_tokens;
                $entry->response = $response;
                
                $entry->save();

                //Fix Social timeline

                            if($main_message_id != NULL) {
                 

                                $entry_timeline = DB::connection('social_db')->table('posts')->where('main_user_openai_id', $main_message_id)->first();
                            
                                if($entry_timeline->description == NULL) {
                                    DB::connection('social_db')->table('posts')
                                        ->where('main_user_openai_id', $main_message_id)
                                        ->update(['description' => $entry->output]);
                                }
                            }
                        
                   
                        

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

    //if $params->'model' => 'SKIP', 'prompt' => 'SKIP', then  $this->new_chat_all_platforms()
    // and if !isset($chat->total_credits)  then $this->new_chat_all_platforms()
    //$this->chat_id  and $this->chat_name  should be completed
    public function new_chat_all_platforms($chat_name,$chat_id,$user_id,$main_chat_id=NULL)
    {

        Log::debug('ChatID case Bio start '.$chat_id);

        if($chat_name != NULL && $chat_id != NULL )

        {
        
        //Main
        /* if($this->check_exsist_chat('main_db','user_openai_chat',$chat_name,$chat_id) < 1)
        $chat_new_ins_main = UserOpenaiChat::Create(
            ['chat_id' => $chat_id],
            ['user_id' => $user_id,
            'title' => $chat_name,
            'openai_chat_category_id' => 1,]
        ); */

        
    //Main

    if($this->check_exsist_chat('main_db','user_openai_chat',$chat_name,$chat_id) < 1)
    {
        $data = [

            "chat_id" => $chat_id,
            "openai_chat_category_id" => 1,
            "user_id" => $user_id,
            "title" => $chat_name,
            
        ];

        $chat_new_ins_main_add_id =  DB::connection('main_db')->table('user_openai_chat')->insertGetId($data);
        
       

        if(isset($chat_new_ins_main_add_id ))
        Log::debug('Sucess add new chat to UserOpenaiChatMain CoIN '.$chat_new_ins_main_add_id );

    }
    if(isset($chat_new_ins_main_add_id ))
    $chat_ins_main=$chat_new_ins_main_add_id ;
    else
    $chat_ins_main=$main_chat_id;

      
        //Main.marketing
         if($this->check_exsist_chat('main_db','conversation_list',$chat_name,$chat_id) < 1)
        $chat_new_ins_mar = UserOpenaiChatMainMarketing::Create(
            ['chat_id' => $chat_id],
            ['user_id' => $user_id,
            'title' => $chat_name,
            'user_openai_chat_id' =>$chat_ins_main,
            'openai_chat_category_id' => 1,]
        );

        if(isset($chat_new_ins_mar->id))
        Log::debug('Sucess add new chat to UserOpenaiChatMainMarketing '.$chat_new_ins_mar->id);


        //Design
        if($this->check_exsist_chat('digitalasset_db','user_openai_chat',$chat_name,$chat_id) < 1)
        $chat_new_ins_design = UserOpenaiChatDesign::Create(
            ['chat_id' => $chat_id],
            ['user_id' => $user_id,
            'title' => $chat_name,
            'user_openai_chat_id' =>$chat_ins_main,
            'openai_chat_category_id' => 1,]
        );


        if(isset($chat_new_ins_design->id))
        Log::debug('Sucess add new chat to UserOpenaiChatDesign '.$chat_new_ins_design->id);


        //SocialPost
        if($this->check_exsist_chat('main_db','sp_user_openai_chat',$chat_name,$chat_id) < 1)
        $chat_new_ins_social = UserOpenaiChatSocialPost::Create(
            ['chat_id' => $chat_id],
            ['user_id' => $user_id,
            'title' => $chat_name,
            'user_openai_chat_id' =>$chat_ins_main,
            'openai_chat_category_id' => 1,]
        );


        if(isset($chat_new_ins_social->id))
        Log::debug('Sucess add new chat to UserOpenaiChatSocialPost '.$chat_new_ins_social->id);



        //MobileAppV2
        if($this->check_exsist_chat('mobileapp_db','user_openai_chat',$chat_name,$chat_id) < 1)
        $chat_new_ins_mobile = UserOpenaiChatMobile::Create(
            ['chat_id' => $chat_id],
            ['user_id' => $user_id,
            'title' => $chat_name,
            'user_openai_chat_id' =>$chat_ins_main,
            'openai_chat_category_id' => 1,]
        );


        if(isset($chat_new_ins_mobile->id))
        Log::debug('Sucess add new chat to UserOpenaiChatMessageMobile '.$chat_new_ins_mobile->id);




        //SyncNodeJS
        if($this->check_exsist_chat('sync_db','user_openai_chat',$chat_name,$chat_id) < 1)
        $chat_new_ins_sync = UserOpenaiChatSyncNodeJS::Create(
            ['chat_id' => $chat_id],
            ['user_id' => $user_id,
            'title' => $chat_name,
            'user_openai_chat_id' =>$chat_ins_main,
            'openai_chat_category_id' => 1,]
        );


        if(isset($chat_new_ins_sync->id))
        Log::debug('Sucess add new chat to UserOpenaiChatSyncNodeJS '.$chat_new_ins_sync->id);





        //Bio
        if($this->check_exsist_chat('bio_db','chats',$chat_name,$chat_id) < 1)
       {
       
        $data = [

            "chat_id_mobile" => $chat_id,
            "openai_chat_category_id" => 1,
            "user_id" => $user_id,

            "title" => $chat_name,
            "name" => $chat_name,
            "chat_assistant_id" => 1,
            "settings" => '[]',
            
        ];

        $chat_new_ins_bio_add_id =  DB::connection('bio_db')->table('chats')->insertGetId($data);
 

    }
        if(isset($chat_new_ins_bio_add_id))
        Log::debug('Sucess add new chat to UserOpenaiChatBio '.$chat_new_ins_bio_add_id);


        return $chat_ins_main;
    }
    else
    {
        return 0;
    }

    }

    public function check_exsist_chat($db,$table,$chat_name,$chat_id)
    {
        if($chat_id!=NULL)
       {

        if($db=='bio_db')
        $check_chat = DB::connection($db)->table($table)->where('chat_id_mobile',$chat_id)->where('name',$chat_name)->get();
        else
        $check_chat = DB::connection($db)->table($table)->where('chat_id',$chat_id)->where('title',$chat_name)->get();



        $found_chat = $check_chat->count();
        return $found_chat;
       }
       else
       {
        return 0;
       }

    }

    public function find_chat_id($from)
    {



                if($from=='bio')
                   {

                   Log::debug('!!!!!! Debug fine Bio old ChatID in Function !!!!!' );
                   // $chat_id_find=UserOpenaiChatBio::where('chat_id',$this->chat_main_id)->first();
                     $db='bio_db';
                     $table='chats';
                     $column='chat_id';

                   }

                   if($from=='main_coin')
                   {

                   Log::debug('!!!!!! Debug fine MainCoIN old ChatID in Function !!!!!' );
                   // $chat_id_find=UserOpenaiChatBio::where('chat_id',$this->chat_main_id)->first();
                     $db='main_db';
                     $table='user_openai_chat';
                     $column='id';

                   }

                   if($from=='MobileAppV2')
                   {

                   Log::debug('!!!!!! Debug fine MobileAppV2 ChatID in Function !!!!!' );
                   // $chat_id_find=UserOpenaiChatMobile::where('chat_id',$this->chat_main_id)->first();
                     $db='mobile_db';
                     $table='willdev_user_chat';
                     $column='id';

                   }

                   $chat_id_find=DB::connection($db)->table($table)->where($column,$this->chat_main_id)->first();
                 
                  
                   if($from=='bio')
                   $new_chat_id=$chat_id_find->chat_id_mobile;
                   else
                   $new_chat_id=$chat_id_find->chat_id;


                   $this->chat_id = $new_chat_id;

                           if($this->chat_id==NULL)
                           {
                                   $chat_mobile_id= "chat_";
                                   $chat_mobile_id.= strval(time());
                                   $ran = rand(100, 999);
                                   $chat_mobile_id.= $ran;

                                   $new_chat_id=array(
                                       'chat_id_mobile' => $chat_mobile_id,
                                   );

                                $chatData = DB::connection('bio_db')->table('chats')
                                ->where('chat_id',$this->chat_main_id)
                                ->update($new_chat_id);

                                 //$chat_id_find->update($new_chat_id);
                                   

                                   $this->chat_id =$chat_mobile_id;



                           }

                           return  $this->chat_id;
                   


         }


         public function fix_user_openai_chat_ID($db,$table,$chat_id)

         {

            if($db=='bio_db')
            $column='chat_id_mobile';
            else
            $column='chat_id';


            $chat_main_id_find=DB::connection($db)->table($table)->where($column,$chat_id)->first();
                 
                  
            if(isset($chat_main_id_find->id) || isset($chat_main_id_find->chat_id))
            {
                return $chat_main_id_find->id;

            }
            else
            {
                return 0;
            }


         }


         
         public function Save_Bio_Documents($params, $output,$response,$user_id,$usage,$main_message_id)
         {

            if (is_array($params))
            $params_json1 = $params;
            else
            $params_json1 = json_decode($params, true);



            // Save Users of Bio
             $user = \DB::connection('bio_db')->table('users')->where('user_id', $user_id)->get();
            //$users = DB::connection('second_db')->table('users')->get();

            $post_type = 'paragraph_generator';
            //linkedin-advertisement-PCiTr3
            $post = OpenAIGenerator::where('slug', $post_type)->first();
            $entry = new UserBioOpenai();

            Log::debug('Check if create AI Bio Doc success'.$entry->document_id);

            $entry->title = 'New Workbook';

            if ($params_json1["model"] == 'whisper-1') {

                $entry->slug = Str::random(7) . Str::slug($user[0]->name) . '-speech-to-text-workbook';
            } else {
                $entry->slug = str()->random(7) . str($user[0]->name)->slug() . '-workbook';
            }


            if($main_message_id==NULL)
            $main_message_id=$params_json1['main_useropenai_message_id'];

            if($this->main_openai_id>0)
            $main_message_id=$this->main_openai_id;

         

            //Bio section
        
            /* Settings of request */
                $settings = json_encode([
                    'language' => 'English',
                    'variants' => 1,
                    'max_words_per_variant' => 255900,
                    'creativity_level' => 'optimal',
                    'creativity_level_custom' => null,
                ]);

            /* Prepare the statement and execute query */
           /*  $document_id = db()->insert('documents', [
                'user_id' => $this->user->user_id,
                'project_id' => $_POST['project_id'],
                'template_category_id' => $templates[$_POST['type']]->template_category_id,
                'template_id' => $_POST['type'],
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'input' => $input,
                'content' => $content,
                'words' => $words,
                'model' => $this->user->plan_settings->documents_model,
                'api_response_time' => $api_response_time,
                'settings' => $settings,
                'datetime' => \Altum\Date::$date,
            ]); */

          

            //$template_id   $openai_id
            if($this->main_template_id != NULL)
            {
                Log::debug('Debug still found Main Template ID in Bio save Docs '. $this->main_template_id);
                $template_ins=UserBioOpenaiTemplate::where('openai_id',$this->main_template_id)->first();
                Log::debug('Debug after Qry Main Template ID in Bio save Docs '. $template_ins->template_id);
                               
            }
            else
            {
                $find_main_template_id=UserOpenai::where('id',$main_message_id)->first();
                $this->main_template_id=$find_main_template_id->openai_id;
                $template_ins=UserBioOpenaiTemplate::where('openai_id',$this->main_template_id)->first();

            }

            Log::debug('Debug Output '.$output);
            Log::debug('Debug Response '.$response);

            if($output==NULL || Str::length($output) <2 || Str::contains($response,'null'))
            {
                Log::debug('Case Output NULL or Empty ');

                //$response_from_openai=DB::connection('main_db')->table('user_openai')->where('id',$this->main_openai_id)->first();
                $response_from_openai=UserOpenai::where('id',$this->main_openai_id)->first();

                Log::info($response_from_openai);

                $response=$response_from_openai->response;
                $output=$response_from_openai->output;

                
                $prompt=json_encode([

                    "name"=> $response_from_openai->title,
                    "description" => $response_from_openai->input,
                   ]);


                /* Log::debug('Debug response from Main '. $response_from_openai->response);
                Log::debug('Debug output from Main '. $response_from_openai->output); */


            }
            
            $entry->template_id = $template_ins->template_id;
            $entry->template_category_id  = $template_ins->template_category_id;
            $entry->name  = $entry->title;
            $entry->type  = $template_ins->template_id;
            $entry->content  = $response;
            $entry->settings  = $settings;


            $entry->user_id = $user_id;
            $entry->openai_id = $post->id;
            $entry->input = $prompt;
           // $entry->response = serialize(json_encode($response_arr));
            $entry->response =$response;
            $entry->output = $output;
            $entry->hash = str()->random(256);
            $entry->credits = $usage;
            $entry->words = $usage;
            $entry->main_user_openai_id = $main_message_id;
            $entry->save();



         }


         public function create_new_chat_main_id($from,$user_id,$chat_id)
         {
            if($from=='bio')
            {
                $db='bio_db';
                $table='chats';
                $column='chat_id_mobile';
            }
            else if($from=='main_coin')
            {
                $db='main_db';
                $table='user_openai_chat';
                $column='id';
            }
            else if($from=='MobileAppV2')
            {
                $db='mobile_db';
                $table='willdev_user_chat';
                $column='id';
            }
            else if($from=='SyncNodeJS')
            {
                $db='sync_db';
                $table='user_openai_chat';
                $column='id';
            }
            else if($from=='Design')
            {
                $db='digitalasset_db';
                $table='user_openai_chat';
                $column='id';
            }
            else if($from=='SocialPost')
            {
                $db='main_db';
                $table='sp_user_openai_chat';
                $column='id';
            }
            else if($from=='Bio')
            {
                $db='bio_db';
                $table='chats';
                $column='chat_id_mobile';
            }
            else
            {
                $db='main_db';
                $table='user_openai_chat';
                $column='chat_id';
            }

            if($from=='MobileAppV2')
            {
              
               $category = OpenaiGeneratorChatCategory::where('id', 1)->firstOrFail();
               $chat = new UserOpenaiChatMobile();
               $chat->user_id = $user_id;
               $chat->openai_chat_category_id = $category->id;
               $chat->title = $category->name . ' Chat';
               $chat->total_credits = 0;
               $chat->total_words = 0;
               $chat->chat_id = $chat_id;
               $chat->save();
       
               $openai_chat_id=$chat->id;

               //update user_openai_chat_id in user_openai_chat_message_mobile
               $chat_messages=UserOpenaiChatMessageMobile::where('chat_id',$chat_id)->get();
               foreach($chat_messages as $chat_message)
               {
                   $chat_message->user_openai_chat_id=$openai_chat_id;
                   $chat_message->save();
               }



            }
        


             return $openai_chat_id;




         }
      

    


}
