<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Jobs\SendConfirmationEmail;


use App\Providers\RouteServiceProvider;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\SettingTwo;


use Log;
use Session;
use Cookie;
use Carbon\Carbon;
use stdClass;

use App\Models\SubscriptionMain;
use App\Models\SubscriptionMobile;
use App\Models\OpenAIGenerator;
use GuzzleHttp\Client;

use App\Models\UserOpenai;
use App\Models\UserOpenaiChat;

use App\Models\SP_UserOpenai;
use App\Models\DigitalAsset_UserOpenai;
use App\Models\Mobile_UserOpenai;

use App\Models\SP_UserCaption;

use App\Models\Settings;

use App\Models\PlanMobile;
use App\Models\Plan;

use App\Models\SettingBio;

use App\Models\UserSP;
use App\Models\UserSEO;
use App\Models\UserCourse;
use App\Models\UserDesign;
use App\Models\UserLiveShop;
use App\Models\UserMain;
use App\Models\UserBioBlog;
use App\Models\UserBio;
use App\Models\UserSyncNodeJS;
use App\Models\UserMobile;
use App\Models\PlanBio;
use App\Models\SPTeam;
use App\Models\TokenLogs;
use App\Models\UserCRM;


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

use App\Models\UserDesignSubscriptions;
use App\Models\OpenaiGeneratorChatCategory;
use App\Models\UserBioOpenaiTemplate;
use App\Models\OpenaiGeneratorFilter;
use App\Models\SubscriptionBio;


use App\Models\Team_Members_Bio;
use App\Models\Team_Members_SocialPost;
use App\Models\Team_Members_Main;


use Illuminate\Support\Arr;
use App\Http\Controllers\Auth\SMAISessionAuthController;
use Storage;


class SMAIUpdateProfileController extends Controller
{


    //protected $request;
    protected $hash_password;
    protected $skip_update_pss = 0;
    protected $upFromWhere = NULL;
    protected $upByWhom = NULL;
    protected $plus_new_images_token;
    protected $plus_new_words_token;
    protected $bio_template_id;
    public $freetrial_plan_images;
    public $freetrial_plan_words;
    protected $clear_token;

    // request as an attribute of the controllers


    public function __construct($request_update = NULL, $user_id = NULL, $user_email = NULL, $whatup = NULL, $upFromWhere = NULL, $upByWhom = NULL)
    {


        $this->plus_new_images_token = 0;
        $this->plus_new_words_token = 0;
        $this->upFromWhere = $upFromWhere;
        $this->upByWhom = $upByWhom;

        //CHECK MAIN PLAN is still active
        $user_main = UserMain::where('id', $user_id)->first();

        if ($user_main) {
            Log::debug('Dubug checking current Main User PLan' . $user_main->plan);

            Log::debug('Debug upFromWhere in construct ' . $this->upFromWhere);
            Log::debug('Debug upByWhom in construct ' . $this->upByWhom);
        }

        //freetrial images and words
        $plan_main = Plan::where('id', 8)->first();
        $this->freetrial_plan_images = $plan_main->total_images;
        $this->freetrial_plan_words = $plan_main->total_words;


        Log::debug(" Start constructor of Class update Profile with these Params : ");

        Log::info($request_update);
        Log::info($user_id);
        Log::info($whatup);
        Log::info($upFromWhere);

        $main_user = UserMain::where('id', $user_id)->first();

        if (isset($main_user->remaining_words))
            $remaining_words_at_start = $main_user->remaining_words;
        else
            $remaining_words_at_start = 0;

        Log::debug('Debug remaining_words_at_start ' . $remaining_words_at_start);

        if (isset($request_update->data))
            Log::info($request_update->data);

        if (isset($request_update->data['name'])) {
            Log::debug(' Name for data : ');
            Log::info($request_update->data['name']);
        }


        if (isset($request_update->data['password'])) {
            $password = $request_update->data['password'];
        }

        if (isset($request_update->data['password'])) {
            if ($password != null && $password != NULL) {
                $password = $request_update->data['password'];
                $this->hash_password = Hash::make($request_update->data['password']);
            } else {
                $this->skip_update_pss = 1;
            }
        } else {
            $this->skip_update_pss = 1;
        }


        $userdata = [];

        $user_main = UserMain::where('id', $user_id)->first();

        if ($user_main)
            Log::debug('Dubug checking current Main User PLan LINE 182 ' . $user_main->plan);

        // for check if column existing
        // $user_column_on= $this->checkColumnExist($column,$table,$db);
        /* $request_update = json_decode($request_update,true);
        Log::info($request_update);
        $request = json_decode($request_update['data'],true ); */

        /* $request = json_encode($request_update->data);
        $request = json_decode($request,true); */

        if (isset($request_update->data))
            $request = $request_update->data;

        Log::debug('Debug request Decode Json');

        if (isset($request))
            Log::info($request);

        $user_main = UserMain::where('id', $user_id)->first();
        if ($user_main)
            Log::debug('Dubug checking current Main User PLan LINE 202 ' . $user_main->plan);


        if (isset($whatup) && $whatup != NULL) {

            //incase of update AI Document Template from Admin
            if (in_array("ai_template", $whatup)) {

                if (in_array("ai_docs", $whatup)) {
                    if (is_array($request) == true)
                        $request_json = $request;
                    else
                        $request_json = json_decode($request, true);


                    if (isset($request_json['template_id'])) {

                        $this->bio_template_id = $request_json['template_id'];
                    }

                    Log::debug('Case Update AI Docs Template ID ' . $this->bio_template_id);
                    Log::debug('Case Update AI Docs Template from ' . $this->upFromWhere);

                    // if added this protected $primaryKey = 'template_id';  in Models then can use id not template_id
                    //$bio_ai_docs_text= UserBioOpenaiTemplate::where('id',$this->bio_template_id);
                    $bio_ai_docs_text = DB::connection('bio_db')->table('templates')->where('template_id', $this->bio_template_id)->first();


                    if ($bio_ai_docs_text->template_id > 0) {
                        //insert thise Template from Bio to MainCoin,MobileV2 and Mobile old

                        //value from params
                        // $request_json['template_category_id'];
                        // $request_json['prompt'];
                        // $request_json['settings'];
                        // $request_json['order'];
                        // $request_json['is_enabled'] ;
                        // $request_json['last_datetime'];


                        //1. str_replace &#34;{name}&#34; and {description}
                        //2.
                        $description_openai = $request_json['prompt'];

                        if (Str::contains($description_openai, '&#34;{name}&#34;'))
                            $description_openai = str_replace('&#34;{name}&#34;', '', $description_openai);

                        if (Str::contains($description_openai, '{description}'))
                            $description_openai = str_replace('{description}', '', $description_openai);


                        $description_openai = trim($description_openai);
                        if (Str::contains($description_openai, 'with the following description:'))
                            $description_openai = str_replace('with the following description:', '', $description_openai);

                        $description_openai .= '.';


                        if (Str::length($description_openai) < 250)
                            $type_openai_input = 'text';
                        else
                            $type_openai_input = 'textarea';


                        //find svg online image match to $request_json['icon']
                        //for example
                        /* fa fa-align-left ==  <svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"><path d="M160 666v-60h389v60H160Zm0-120v-60h640v60H160Z"/></svg> */

                        $filters_openai_input = 'blog';

                        if (Str::contains($filters_openai_input, 'blog'))
                            $color_openai = '#A3D6C2';
                        else
                            $color_openai = '#A3D6C2';

                        if ($request_json['icon'] == 'fa fa-align-left')
                            $image_icon = '<svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"><path d="M160 666v-60h389v60H160Zm0-120v-60h640v60H160Z"/></svg>';
                        else
                            $image_icon = '<svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"><path d="M160 666v-60h389v60H160Zm0-120v-60h640v60H160Z"/></svg>';


                        if ($bio_ai_docs_text->openai_id == NULL) {


                            $ins_main_ai_docs_text_arr = array(

                                'title' => $request_json['name'],
                                'description' => $description_openai,
                                'image' => $image_icon,
                                'color' => $color_openai,
                                'prompt' => NULL,
                                'filters' => 'blog',
                                'premium' => 0,
                                'input_name' => $request_json['name'] . ' description',
                                'input_description' => 'describe your ' . $request_json['name'],
                                'input_type' => $type_openai_input,
                                'bio_template_id' => $this->bio_template_id,
                                'template_id' => NULL,


                            );

                            //case add new
                            // $request_en=json_encode($ins_main_ai_docs_text_arr);
                            $openai_ins_id = $this->openAICustomAddOrUpdateSave($ins_main_ai_docs_text_arr);

                            $bio_ai_docs_model = UserBioOpenaiTemplate::where('template_id', $this->bio_template_id)->first();
                            $bio_ai_docs_model->openai_id = $openai_ins_id;
                            $bio_ai_docs_model->save();


                        } else {
                            //Maybe update id to sync all table
                            //incase of $bio_ai_docs_text->openai_id is not NULL then Update it
                            //สิ่งนี้คือ สิ่งสำคัญ ที่ทำให้  $prompt ของบริษัทไหน แพล็ตฟอร์มไหน โดดเด่นและหลากหลายกว่า
                            //และดึงดูด users ได้มากกว่า

                            $ins_main_ai_docs_text_arr = array(

                                'title' => $request_json['name'],
                                'description' => $description_openai,
                                'image' => $image_icon,
                                'color' => $color_openai,
                                'prompt' => NULL,
                                'filters' => 'blog',
                                'premium' => 0,
                                'input_name' => $request_json['name'] . ' description',
                                'input_description' => 'describe your ' . $request_json['name'],
                                'input_type' => $type_openai_input,
                                'bio_template_id' => $this->bio_template_id,
                                'template_id' => $bio_ai_docs_text->openai_id,


                            );
                            //case send to update existing
                            //$request_en=json_encode($ins_main_ai_docs_text_arr);
                            $this->openAICustomAddOrUpdateSave($ins_main_ai_docs_text_arr);

                        }
                        $user_main = UserMain::where('id', $user_id)->first();
                        if ($user_main)
                            Log::debug('Dubug checking current Main User PLan Line 348 ' . $user_main->plan);

                        Log::debug('Debug prompt from DB ');
                        Log::info($bio_ai_docs_text->prompt);
                        Log::debug('Debug name from DB ');
                        Log::info($bio_ai_docs_text->name);

                    }


                }

            }

            if (in_array("profile", $whatup)) {

                //basic_profile universal
                if (isset($request['password']) && $this->skip_update_pss != 1)
                    $userdata['password'] = $this->hash_password;


                //basic_profile main co in
                if (isset($request['surname']))
                    $userdata['surname'] = $request['surname'];

                //basic_profile universal
                if (isset($request['name'])) {
                    Log::debug('FOund name : ' . $request['name']);
                    Log::info($request['name']);
                    $userdata['name'] = $request['name'];
                }

                //basic_profile universal
                if (isset($request['email']))
                    $userdata['email'] = $request['email'];


                //basic_profile socialpost mobile bio
                if (isset($request['username']))
                    $userdata['username'] = $request['username'];

                //basic_profile phone all socialpost mobile bio
                if (isset($request['phone']))
                    $userdata['phone'] = $request['phone'];

            }

            if (in_array("plan", $whatup)) {


                //plan main_coin
                if (isset($request['remaining_words_plus']))
                    $userdata['remaining_words_plus'] = $request['remaining_words_plus'];

                if (isset($request['remaining_images_plus']))
                    $userdata['remaining_images_plus'] = $request['remaining_images_plus'];

                //plan universal
                if (isset($request['plan']))
                    $userdata['plan'] = $request['plan'];

                //plan main_marketing=package_id  /  main_coin=plan
                if (isset($request['package_id']))
                    $userdata['package_id'] = $request['package_id'];

                //extra_profile bio
                if (isset($request['plan_id']))
                    $userdata['plan_id'] = $request['plan_id'];


                //extra_profile bio
                if (isset($request['plan_settings']))
                    $userdata['plan_settings'] = $request['plan_settings'];


                //plan universal
                if (isset($request['remaining_words']))
                    $userdata['remaining_words'] = $request['remaining_words'];

                //plan universal
                if (isset($request['remaining_images']))
                    $userdata['remaining_images'] = $request['remaining_images'];

                //plan mobile_new
                if (isset($request['words_left']))
                    $userdata['words_left'] = $request['words_left'];


                //plan mobile_new
                if (isset($request['image_left']))
                    $userdata['image_left'] = $request['image_left'];


                //plan mobile_old
                if (isset($request['available_words']))
                    $userdata['available_words'] = $request['available_words'];

                //plan mobile_old
                if (isset($request['available_images']))
                    $userdata['available_images'] = $request['available_images'];

                //plan mobile_old
                if (isset($request['total_words']))
                    $userdata['total_words'] = $request['total_words'];

                //plan mobile_old
                if (isset($request['total_images']))
                    $userdata['total_images'] = $request['total_images'];


                //plan universal
                if (isset($request['expiration_date']))
                    $userdata['expiration_date'] = $request['expiration_date'];

                //plan mobile_old
                if (isset($request['plan_expire_date']))
                    $userdata['plan_expire_date'] = $request['plan_expire_date'];

                //plan main
                if (isset($request['expired_date']))
                    $userdata['expired_date'] = $request['expired_date'];

                //extra_profile bio
                if (isset($request['plan_expiration_date']))
                    $userdata['plan_expiration_date'] = $request['plan_expiration_date'];


            }

            if (in_array("extra_profile", $whatup)) /*  if($whatup=="extra_profile") */ {

                //extra profile main
                if (isset($request['under_which_affiliate_user']))
                    $userdata['under_which_affiliate_user'] = $request['under_which_affiliate_user'];

                //extra profile main
                if (isset($request['last_login_at']))
                    $userdata['last_login_at'] = $request['last_login_at'];


                //extra profile main
                if (isset($request['last_login_ip']))
                    $userdata['last_login_ip'] = $request['last_login_ip'];

                //extra_profile socialpost
                if (isset($request['ids']))
                    $userdata['ids'] = $request['remaining_images'];

                //extra_profile socialpost
                if (isset($request['is_admin']))
                    $userdata['is_admin'] = $request['is_admin'];

                //extra_profile socialpost
                if (isset($request['role']))
                    $userdata['role'] = $request['role'];

                //extra_profile socialpost
                if (isset($request['fullname']))
                    $userdata['fullname'] = $request['fullname'];

                //extra_profile socialpost
                if (isset($request['timezone']))
                    $userdata['timezone'] = $request['timezone'];

                //extra_profile socialpost
                if (isset($request['language']))
                    $userdata['language'] = $request['language'];

                //extra_profile socialpost
                if (isset($request['login_type']))
                    $userdata['login_type'] = $request['login_type'];

                //extra_profile socialpost
                if (isset($request['avatar']))
                    $userdata['avatar'] = $request['avatar'];


                //extra_profile socialpost
                if (isset($request['last_login']))
                    $userdata['last_login'] = $request['last_login'];

                //extra_profile main ,socialpost ,Design
                if (isset($request['status']))
                    $userdata['status'] = $request['status'];


                //extra_profile socialpost
                if (isset($request['changed']))
                    $userdata['changed'] = $request['changed'];


                //extra_profile mobile_new=User,   mobile_old=user,super admin / main_marketing=Member,admin
                if (isset($request['user_type']))
                    $userdata['user_type'] = $request['user_type'];

                //extra_profile mobile_old
                if (isset($request['created_by']))
                    $userdata['created_by'] = $request['created_by'];

                //extra_profile Design
                if (isset($request['phone_no']))
                    $userdata['phone_no'] = $request['phone_no'];


                //extra_profile Design
                if (isset($request['profile_pic']))
                    $userdata['profile_pic'] = $request['profile_pic'];

                //extra_profile mobile_new
                if (isset($request['image']))
                    $userdata['image'] = $request['image'];

                //extra_profile bio
                if (isset($request['timezone']))
                    $userdata['timezone'] = $request['timezone'];

                //extra_profile bio
                if (isset($request['is_newsletter_subscribed']))
                    $userdata['is_newsletter_subscribed'] = $request['is_newsletter_subscribed'];


            }
            if (in_array("login", $whatup) || in_array("express_upgrade_plan", $whatup)) {

                if (isset($request_update['data']['raw_password']))
                    $raw_password = $request_update['data']['raw_password'];

                if (isset($request_update['data'][0]['raw_password']))
                    $raw_password = $request_update['data'][0]['raw_password'];
                else
                    $raw_password = '';

                //create session login
                $user_bio_id = $user_id;
                $user_email = $request_update['email'];

                $session_php = $request_update['session_php'];
                $universal_user = Auth::loginUsingId($user_id);
                $login_session_bio = new SMAISessionAuthController();

                //add user to users table if not exist
                $start_add_user = 1;
                $login_session_bio->freetrial_user_api($request_update, $user_id, $raw_password);
                if ($user_id > 1 && $start_add_user == 1) {

                    if ($this->check_old_user_id('main_db', 'sp_users', $user_id) < 1)
                        $login_session_bio->freetrial_socialpost($request_update, $user_id, $raw_password);

                    /* if($this->check_old_user_id('mobileapp_db', 'users', $user_id))
                    $login_session_bio->freetrial_mobileApp($request_update, $user_id, $raw_password);
                     */

                    if ($this->check_old_user_id('main_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_main_co_in($request_update, $user_id, $raw_password);

                    /* if($this->check_old_user_id('main_db', 'users', $user_id))
                    $login_session_bio->freetrial_main_marketing($request_update, $user_id, $raw_password);
                     */

                    if ($this->check_old_user_id('digitalasset_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_design($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('mobileapp_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_mobileAppV2($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('bio_blog_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_bio_blog($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('sync_db', 'user', $user_id) < 1)
                        $login_session_bio->freetrial_sync_node($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('crm_db', 'tblleads', $user_id) < 1)
                        $login_session_bio->freetrial_crm($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('bio_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_bio($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('course_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_course($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('liveshop_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_liveshop($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('seo_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_seo($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('punbot_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_punbot($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('social_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_social($request_update, $user_id, $raw_password);
                }

                $login_session_bio->db_session_create($user_id, $session_php);

                //Subscription CHECK
                $current_active_subscription_main = SubscriptionMain::where('user_id', $user_id)->where("bio_token_sync", 0)->where('stripe_status', 'active')->orWhere('stripe_status', 'trialing')->first();

                $check_upFromMain = 0;

                if ($current_active_subscription_main->id > 0)
                    $check_upFromMain = 1;


                //recheck user Plan
                //$this->up_plan_bio($userdata, $user_id, $user_email);
                $new_checkuserplan = new SMAISyncPlanController();

                //Sync PLan that Buy from Bio ..... Stop to Buy from Bio
                //Fixing No No  this case mean Log in from Bio not only Buy from Bio
                //But Whythis case Fix Main only so Do not use it
                //if($this->upFromWhere=='bio')
                if ($this->upFromWhere == 'bio_main_cancel_this_to_use_main_coin_only') {
                    $main_user_at_middle = UserMain::where('id', $user_id)->first();

                    $current_bio_plan_array = $new_checkuserplan->SMAI_Check_Universal_UserPlans($user_id, 'bio_db', 'Bio');
                    $current_bio_plan_id = $current_bio_plan_array['plan_id'];
                    $current_bio_plan_expire = $current_bio_plan_array['expire'];
                    $current_main_plan = $current_bio_plan_array['main_plan_id'];

                    $Bio_Plan_from_DB = PlanBio::where('plan_id', $current_bio_plan_id)->first();
                    $BIo_Plan_from_DB_package_type = $Bio_Plan_from_DB->package_type;
                    $main_plan_should_be = $Bio_Plan_from_DB->main_plan_id;

                    Log::debug('Debug Main Plan from Setting ' . $main_plan_should_be);
                    Log::debug('Debug Main Plan from DB ' . $current_main_plan);
                    Log::debug('Debug Bio Plan TYpe from DB ' . $BIo_Plan_from_DB_package_type);

                    $have_to_fix_main_plan = 0;
                    if ($main_plan_should_be == $current_main_plan) {
                        Log::debug('Main Plan ID is same from DB and Setting');
                        //then check main Plan expired date
                        $main_cur_plan_from_main = UserMain::where('id', $user_id)->first();
                        $user_plan_expire_from_main = $main_cur_plan_from_main->expired_date;
                        Log::debug('Current Time of Expired in Main ' . $user_plan_expire_from_main);
                        Log::debug('Time of Expired  in Bio ' . $current_bio_plan_expire);
                        $current_bio_plan_expire_date = $current_bio_plan_expire ? Carbon::parse($current_bio_plan_expire)->toDateString() : null;
                        //$current_bio_plan_expire_date = Carbon::parse($current_bio_plan_expire)->toDateString();
                        $user_plan_expire_from_main_date = $user_plan_expire_from_main ? Carbon::parse($user_plan_expire_from_main)->toDateString() : null;
                        //$user_plan_expire_from_main_date = Carbon::parse($user_plan_expire_from_main)->toDateString();


                        if ($user_plan_expire_from_main == NULL || $current_bio_plan_expire_date != $user_plan_expire_from_main_date) {
                            Log::debug('Time of Expired is not right in Main ' . $user_plan_expire_from_main);
                            Log::debug('Time of Expired  in Bio ' . $current_bio_plan_expire);
                            Log::debug('Time of Expired Date in Bio ' . $current_bio_plan_expire_date);
                            Log::debug('Time of Expired Date in Main ' . $user_plan_expire_from_main_date);
                            Log::debug('Check Pla type from Bio ' . $BIo_Plan_from_DB_package_type);
                            $have_to_fix_main_plan = 1;
                        }


                    }


                    if (($current_main_plan != $main_plan_should_be || $have_to_fix_main_plan > 0) && $BIo_Plan_from_DB_package_type == 'bundle') {

                        if ($current_main_plan != $main_plan_should_be)
                            $have_to_fix_main_plan = 1;

                        //recheck if Main Plan is freeplan then update plan to main_plan_should_be
                        if ($main_plan_should_be == 0 || $main_plan_should_be == '0' || $main_plan_should_be == 8 || $main_plan_should_be == '8' || $have_to_fix_main_plan > 0) {

                            Log::debug('Pass to Update new Main plan to ' . $main_plan_should_be);

                            if ($have_to_fix_main_plan > 0) {
                                Log::debug('Pass to Update new Main plan to ' . $main_plan_should_be . ' because of Expired Date');
                            }

                            //plan main_coin
                            /* if (isset($request['remaining_words_plus']))
                                $userdata['remaining_words_plus'] = $request['remaining_words_plus']; */

                            /* if (isset($request['remaining_images_plus']))
                                    $userdata['remaining_images_plus'] = $request['remaining_images_plus']; */

                            //plan universal
                            /* if (isset($request['plan']))
                                    $userdata['plan'] = $request['plan']; */
                            $userdata['plan'] = $current_bio_plan_id;

                            //plan main_marketing=package_id  /  main_coin=plan
                            //if (isset($request['package_id']))
                            $userdata['package_id'] = 0;

                            //extra_profile bio
                            /* if (isset($request['plan_id']))
                                    $userdata['plan_id'] = $request['plan_id']; */


                            //extra_profile bio
                            /* if (isset($request['plan_settings']))
                                    $userdata['plan_settings'] = $request['plan_settings']; */

                            $user_bio_for_update = UserBio::where('user_id', $user_id)->first();
                            $bio_remaining_words = $user_bio_for_update->remaining_words;
                            $bio_remaining_images = $user_bio_for_update->remaining_images;

                            //plan universal
                            //if (isset($request['remaining_words']))
                            $userdata['remaining_words'] = $bio_remaining_words;

                            //plan universal
                            // if (isset($request['remaining_images']))
                            $userdata['remaining_images'] = $bio_remaining_images;

                            //plan mobile_new
                            //if (isset($request['words_left']))
                            $userdata['words_left'] = $bio_remaining_words;


                            //plan mobile_new
                            //if (isset($request['image_left']))
                            $userdata['image_left'] = $bio_remaining_images;


                            //plan mobile_old
                            //if (isset($request['available_words']))
                            $userdata['available_words'] = $bio_remaining_words;

                            //plan mobile_old
                            //if (isset($request['available_images']))
                            $userdata['available_images'] = $bio_remaining_images;

                            /* //plan mobile_old
                                if (isset($request['total_words']))
                                    $userdata['total_words'] = $request['total_words'];

                                //plan mobile_old
                                if (isset($request['total_images']))
                                    $userdata['total_images'] = $request['total_images']; */


                            //plan universal
                            //if (isset($request['expiration_date']))
                            $userdata['expiration_date'] = $current_bio_plan_expire;

                            //plan mobile_old
                            //if (isset($request['plan_expire_date']))
                            $userdata['plan_expire_date'] = $current_bio_plan_expire;

                            //plan main
                            //if (isset($request['expired_date']))
                            $userdata['expired_date'] = $current_bio_plan_expire;

                            //extra_profile bio
                            /* if (isset($request['plan_expiration_date']))
                                    $userdata['plan_expiration_date'] = $current_bio_plan_expire; */

                            // fixing move to Active Plan from Main only
                            //$this->up_plan_bio($userdata, $user_id, $user_email, 'fix_main_plan');
                        }


                    }

                    //Fixing if wabt to use this case as login from Bio it should has case
                    // $have_to_fix_bio_plan = 1;

                }

                //case 2
                //!important Sync PLan that Buy from MainCOIn
                //Updated 7 March 2024 Checked Both login from Bio and Main are Trigger update
                //Both Login From Bio and Main can Detect Plan From Main
                if ($check_upFromMain > 0 || $this->upFromWhere == 'main_coin' || Str::contains(strtolower($this->upFromWhere), 'main_coin') || Str::contains(strtolower($this->upFromWhere), 'maincoin')) {
                    //fixed bug case plan id in Main is not 0 or 8 but Subscription not found
                    //fixed added Subscription status is active or trialing

                    //fixing bugging case
                    $main_subscription = SubscriptionMain::where('user_id', $user_id)->where('stripe_status', 'active')->orWhere('stripe_status', 'trialing')->first();
                    if (isset($main_subscription->id)) {
                        if ($main_subscription->id > 0 && $main_subscription->id != 8) {
                            //check Subscription But How about in case Team Plan
                            if ($main_subscription->stripe_status != 'active' && $main_subscription->stripe_status != 'trialing') {
                                Log::debug('Debug Case Plan ID in Main is not 0 or 8 but Subscription not found');
                                $main_users = UserMain::where('id', $user_id)->first();
                                $main_users->plan = 8;
                                $main_users->token_downgraded = 0;
                                $main_users->save();
                            }
                        }

                    }


                    $current_main_plan_array = $new_checkuserplan->SMAI_Check_Universal_UserPlans($user_id, 'main_db', 'main_coin');

                    $current_main_plan_id = $current_main_plan_array['plan_id'];
                    $current_main_plan_expire = $current_main_plan_array['expire'];
                    $current_bio_plan_id = $current_main_plan_array['bio_plan_id'];

                    $Main_Plan_from_DB = Plan::where('id', $current_main_plan_id)->first();
                    $Main_Plan_from_DB_package_type = $Main_Plan_from_DB->package_type;
                    $Bio_plan_should_be = $Main_Plan_from_DB->bio_id;

                    Log::debug('Debug Bio Plan from Plan Table Setting ' . $Bio_plan_should_be);
                    Log::debug('Debug Bio Plan from users DB ' . $current_bio_plan_id);


                    $have_to_fix_bio_plan = 0;
                    if ($Bio_plan_should_be == $current_main_plan_id) {
                        Log::debug('Bio Plan ID is same from DB and Setting');
                        //then check main Plan expired date
                        $bio_cur_plan_from_main = UserBio::where('user_id', $user_id)->first();
                        $user_plan_expire_from_bio = $bio_cur_plan_from_main->plan_expiration_date;
                        Log::debug('Current Time of Expired in BIo ' . $user_plan_expire_from_bio);
                        Log::debug('Time of Expired  in Main ' . $current_main_plan_expire);

                        $current_main_plan_expire_date = $current_main_plan_expire ? Carbon::parse($current_main_plan_expire)->toDateString() : null;
                        $user_plan_expire_from_bio_date = $user_plan_expire_from_bio ? Carbon::parse($user_plan_expire_from_bio)->toDateString() : null;


                        if ($user_plan_expire_from_bio == NULL || $current_main_plan_expire_date != $user_plan_expire_from_bio_date) {
                            Log::debug('Time of Expired is not right in Bio ' . $user_plan_expire_from_bio);
                            Log::debug('Time of Expired  in Main ' . $current_main_plan_expire);
                            Log::debug('Time of Expired Date in Main ' . $current_main_plan_expire_date);
                            Log::debug('Time of Expired Date in Bio ' . $user_plan_expire_from_bio_date);
                            Log::debug('Check Pla type from Main ' . $Main_Plan_from_DB_package_type);
                            $have_to_fix_bio_plan = 1;
                        }


                    }

                    Log::debug('Debug Main Plan from DB TYpe ' . $Main_Plan_from_DB_package_type);
                    if (($current_bio_plan_id != $Bio_plan_should_be || $have_to_fix_bio_plan > 0) && $Main_Plan_from_DB_package_type == 'bundle') {
                        Log::debug('Debug Case Bio Plan have to be fix to ' . $Bio_plan_should_be);
                        $have_to_fix_bio_plan = 2;

                        //recheck if Main Plan is freeplan then update plan to main_plan_should_be
                        if ($Bio_plan_should_be == 0 || $Bio_plan_should_be == '0' || $Bio_plan_should_be == 'free' || $have_to_fix_bio_plan > 0) {

                            Log::debug('Pass to Update new BIo plan to ' . $Bio_plan_should_be);

                            if ($have_to_fix_bio_plan == 1) {
                                Log::debug('Pass to Update new bio plan to ' . $Bio_plan_should_be . ' because of Expired Date ');
                            }

                            if ($have_to_fix_bio_plan == 2)
                                Log::debug('Pass to Update new bio plan to ' . $Bio_plan_should_be . ' because of Plan ID ');


                            //plan main_coin
                            /* if (isset($request['remaining_words_plus']))
                        $userdata['remaining_words_plus'] = $request['remaining_words_plus']; */

                            /* if (isset($request['remaining_images_plus']))
                            $userdata['remaining_images_plus'] = $request['remaining_images_plus']; */

                            //plan universal
                            /* if (isset($request['plan']))
                            $userdata['plan'] = $request['plan']; */
                            $userdata['plan'] = $current_main_plan_id;

                            //plan main_marketing=package_id  /  main_coin=plan
                            //if (isset($request['package_id']))
                            $userdata['package_id'] = 0;

                            //extra_profile bio
                            /* if (isset($request['plan_id']))
                            $userdata['plan_id'] = $request['plan_id']; */


                            //extra_profile bio
                            /* if (isset($request['plan_settings']))
                            $userdata['plan_settings'] = $request['plan_settings']; */

                            $user_main_for_update = UserMain::where('id', $user_id)->first();
                            $main_remaining_words = $user_main_for_update->remaining_words;
                            $main_remaining_images = $user_main_for_update->remaining_images;

                            //plan universal
                            //if (isset($request['remaining_words']))
                            $userdata['remaining_words'] = $main_remaining_words;

                            //plan universal
                            // if (isset($request['remaining_images']))
                            $userdata['remaining_images'] = $main_remaining_images;

                            //plan mobile_new
                            //if (isset($request['words_left']))
                            $userdata['words_left'] = $main_remaining_words;


                            //plan mobile_new
                            //if (isset($request['image_left']))
                            $userdata['image_left'] = $main_remaining_images;


                            //plan mobile_old
                            //if (isset($request['available_words']))
                            $userdata['available_words'] = $main_remaining_words;

                            //plan mobile_old
                            //if (isset($request['available_images']))
                            $userdata['available_images'] = $main_remaining_images;

                            /* //plan mobile_old
                        if (isset($request['total_words']))
                            $userdata['total_words'] = $request['total_words'];

                        //plan mobile_old
                        if (isset($request['total_images']))
                            $userdata['total_images'] = $request['total_images']; */


                            //plan universal
                            //if (isset($request['expiration_date']))
                            $userdata['expiration_date'] = $current_main_plan_expire;

                            //plan mobile_old
                            //if (isset($request['plan_expire_date']))
                            $userdata['plan_expire_date'] = $current_main_plan_expire;

                            //plan main
                            //if (isset($request['expired_date']))
                            $userdata['expired_date'] = $current_main_plan_expire;

                            //extra_profile bio
                            /* if (isset($request['plan_expiration_date']))
                            $userdata['plan_expiration_date'] = $current_bio_plan_expire; */


                            //fixing fixing
                            $this->up_plan_main_coin($userdata, $user_id, $user_email, 'fix_bio_plan');
                        }


                    }


                } //end !important case of sync plan from main coin or bio
                else {
                    Log::error('!!!!!Platform not Found then Exit!!!!!!!!!!!!');
                    exit();
                }


                $main_user_from_realtime = UserMain::where('id', $user_id)->first();
                $downgraded_token = $main_user_from_realtime->token_downgraded;
                $upgraded_token = $main_user_from_realtime->token_upgraded;

                if (!isset($current_main_plan))
                    $current_main_plan = $main_user_from_realtime->plan;

                //recheck active subscription from main_coin
                $current_active_subscription = SubscriptionMain::where('user_id', $user_id)->where('stripe_status', 'active')->orWhere('stripe_status', 'trialing')->first();
                $bio_user = UserBio::where('user_id', $user_id)->first();
                $current_bio_plan = $bio_user->plan_id;

                //fixing bug no update Main from Bio
                /* if ($current_active_subscription) {
                            $current_main_plan = $current_active_subscription->plan_id;
                            Log::debug('Debug Main Plan from DB after condition reset Plan and Token '.$current_main_plan);
                        } */


                // case 3 ถ้าMain Plan เป็น 0 หรือ 8 และยังไม่ได้ downgrade ให้ downgrade ทุก platform
                //!important this is the trigger for downgrade Main and It's bundle platform
                Log::debug('Debug Main Plan from DB before condition reset Plan and Token ' . $current_main_plan);
                if (($current_main_plan == 0 || $current_main_plan == 8) && $downgraded_token == 0) {
                    Log::debug('Pass to downgrade Main Plan to 8 or 0');
                    $userdata_main = array(
                        'plan' => 8
                    );
                    $this->up_plan_main_coin($userdata_main, $user_id, $user_email);
                    //bug after above should update $downgraded_token=1

                    //!important Reset Token = 0 if remaining words and images > golden_tokens
                    $main_user_from_realtime = UserMain::where('id', $user_id)->first();
                    $current_main_token = $main_user_from_realtime->remaining_words + $main_user_from_realtime->remaining_images;
                    $current_main_golden_token = $main_user_from_realtime->golden_tokens;

                    if ($current_main_token > $current_main_golden_token) {
                        $reset_token = 0;
                        $old_reamaining_images = $main_user_from_realtime->remaining_images;
                        $old_reamaining_words = $main_user_from_realtime->remaining_words;
                        $token_delete_img = $main_user_from_realtime->remaining_images;

                        Log::debug('Debug Main Images Token from DB before condition current_main_token>current_main_golden_token reset Token ' . $old_reamaining_images);
                        Log::debug('Debug Main Words Token from DB before condition current_main_token>current_main_golden_token reset Token ' . $old_reamaining_words);
                        //record Token Log

                        $main_user_from_realtime->remaining_words = 0;
                        $main_user_from_realtime->remaining_images = 0;

                        if ($main_user_from_realtime->token_downgraded == 0)
                            $main_user_from_realtime->token_downgraded = 1;

                        $main_user_from_realtime->plan = 8;
                        $reset_token = 1;


                        Log::debug('Reset normal Token to 0 success because of downgrade main Plan to 8 or 0');
                        if ($reset_token == 1) {
                            // Save was successful
                            //$SMAI_Update_Profile_Token_obj= new self();
                            Log::debug('User Downgrade!!!!! in Golden condition was saved successfully and send Token Log save');
                            $this->record_token_log('image', 'reset', 'main_coin', $user_id, 'PlanDowngrade_Main', $token_delete_txt = 0);
                            $this->record_token_log('text', 'reset', 'main_coin', $user_id, 'PlanDowngrade_Main', $token_delete_img);
                            if ($main_user_from_realtime->save()) {
                                Log::debug('User Downgrade!!!!! Golden condition was saved successfully after  Token Log save');
                            } else {
                                $updated = DB::connection('main_db')
                                    ->table('users')
                                    ->where('id', $user_id)
                                    ->update([
                                        'remaining_words' => 0,
                                        'remaining_images' => 0
                                    ]);

                                if ($updated > 0) {
                                    // the row was updated
                                    Log::debug('User Downgrade!!!!! Golden condition Token log was saved successfully in back up DB way ');
                                } else {
                                    // the row was not updated
                                    $main_user_from_realtime->remaining_words = 0;
                                    $main_user_from_realtime->remaining_images = 0;
                                    $main_user_from_realtime->save();
                                }
                            }
                        } else {
                            // Save failed
                            Log::debug('User Downgrade!!!!! Golden condition save operation failed after 3 times try');
                        }

                    }


                    //and also if golden_expired_date < current date


                    //golden_tokens_mode should be 1 incase of main plan is 0 or 8 and golden_tokens > 0


                    //golden_freeze_date is for the blue dimond token that will be freeze for 30 days or more for use later when any plan back to normal
                }


                Log::debug('Debug Main Plan from DB after condition reset Plan and Token ' . $current_main_plan);


            }

            //Start Swith case of Profile update
            if (in_array("ai_template", $whatup) && $this->upFromWhere == 'bio') {

                Log::debug('Start What to do in case Bio update Ai Docs Template');

            } else if (in_array("delete", $whatup)) {
                //DEl all users Platform
                $this->del_user_all_platforms($user_id, $user_email, $this->upFromWhere);
            } else if (in_array("BioReset", $whatup)) {
                //DEl all users Platform
                $result_return = $this->reset_user_Bio($user_id, $user_email, $this->upFromWhere);
                return $result_return;
            } else if (in_array("password", $whatup) && $this->skip_update_pss != 1) {
                Log::debug('Update Profile case Password ');

                if ($this->upFromWhere == 'bio' || $this->upFromWhere == 'main_coin') {
                    $userdata['password'] = $request_update->data['password'];
                } else {
                    $userdata['password'] = $this->hash_password;
                }

                //create session login
                $user_bio_id = $user_id;

                if (isset($request_update['session_php']))
                    $session_php = $request_update['session_php'];
                else if (isset($_COOKIE['PHPSESSID']))
                    $session_php = $_COOKIE['PHPSESSID'];
                else
                    $session_php = Session::getId();

                $universal_user = Auth::loginUsingId($user_id);
                $login_session_bio = new SMAISessionAuthController();


                $raw_password = $userdata['password'];
                //add user to users table if not exist
                $start_add_user = 1;
                $login_session_bio->freetrial_user_api($request_update, $user_id, $raw_password);
                if ($user_id > 100 && $start_add_user == 1) {

                    if ($this->check_old_user_id('main_db', 'sp_users', $user_id) < 1)
                        $login_session_bio->freetrial_socialpost($request_update, $user_id, $raw_password);

                    /* if($this->check_old_user_id('mobileapp_db', 'users', $user_id))
                    $login_session_bio->freetrial_mobileApp($request_update, $user_id, $raw_password);
                     */

                    if ($this->check_old_user_id('main_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_main_co_in($request_update, $user_id, $raw_password);

                    /* if($this->check_old_user_id('main_db', 'users', $user_id))
                    $login_session_bio->freetrial_main_marketing($request_update, $user_id, $raw_password);
                     */

                    if ($this->check_old_user_id('digitalasset_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_design($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('mobileapp_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_mobileAppV2($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('bio_blog_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_bio_blog($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('sync_db', 'user', $user_id) < 1)
                        $login_session_bio->freetrial_sync_node($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('crm_db', 'tblleads', $user_id) < 1)
                        $login_session_bio->freetrial_crm($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('bio_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_bio($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('course_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_course($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('liveshop_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_liveshop($request_update, $user_id, $raw_password);

                    if ($this->check_old_user_id('seo_db', 'users', $user_id) < 1)
                        $login_session_bio->freetrial_seo($request_update, $user_id, $raw_password);

                    $login_session_bio->freetrial_punbot($request_update, $user_id, $raw_password);
                }

                $login_session_bio->db_session_create($user_id, $session_php);

                //send to medthod update password to all platforms
                $this->update_password_all($userdata, $user_id, $user_email);


            } else if (in_array("profile", $whatup) && $this->upFromWhere == 'main_coin') {
                Log::debug('Now working in up_profile_main_co_in_by_admin case ');

                $this->up_profile_main_co_in_by_admin($userdata, $user_id, $user_email);


            } else if (in_array("profile", $whatup) && $this->upFromWhere == 'socialpost') {


                $this->up_profile_socialpost($userdata, $user_id, $user_email);


            } else if (in_array("profile", $whatup) && $this->upFromWhere == 'bio') {
                // #ep1

                Log::debug('dEbug User data : ');
                Log::info($userdata);
                Log::info($user_id);
                Log::info($user_email);


                //1. separate  basic profile to update all platforms
                // update all platforms $userdata->name
                //2. specific some extra profile for some platforms
                // update some platforms $userdata->timezone
                // update some platforms $userdata->is_newsletter_subscribed

                /*  $userdata_example= array(
                 'name' => $userdata['name'],
                 'billing' => $_POST['billing'],
                 'timezone' => $userdata['timezone'],
                 'is_newsletter_subscribed' => $userdata['is_newsletter_subscribed'],

                 ); */

                //send to medthod update bio profile to all platforms
                $this->up_profile_bio($userdata, $user_id, $user_email);


            } else if (in_array("plan", $whatup) && $this->upFromWhere == 'bio') {
                // #ep2

                //send to medthod update bio profile to all platforms
                $this->up_plan_bio($userdata, $user_id, $user_email, NULL);
                //plans_token_centralize($user_id, $user_email, $token_array, $usage = NULL, $from = NULL, $old_reamaining_word = NULL, $old_reamaining_image = NULL, $chatGPT_catgory = NULL, $token_update_type = NULL);

                $user_bio = UserBio::where('user_id', $user_id)->first();
                Log::debug("Check update Bio user plain ID LINE 1263 after function up_plan_bio " . $user_bio->plan_id);


            } else if (in_array("plan", $whatup) && (Str::contains($this->upFromWhere, 'MainCoIn') || Str::contains($this->upFromWhere, 'main_coin'))) {
                // #ep3

                //send to medthod update bio profile to all platforms
                $this->up_plan_main_coin($userdata, $user_id, $user_email);


            } else if (in_array("plan", $whatup) && $this->upFromWhere == 'socialpost') {


            } else {


            }

        }

        $user_bio = UserBio::where('user_id', $user_id)->first();

        if ($user_bio)
            Log::debug("Check update Bio user plain ID LINE 1284 after whatups!! " . $user_bio->plan_id);


        // $this->upFromWhere  == 'socialpost_profile  ||  $this->upFromWhere  == 'socialpost_plan


        if (isset($this->upFromWhere) && $this->upFromWhere == 'socialpost_profile') {


            //1. update to profile of SocialPost
            //2. update basic profile to all Platforms
            //3. update extra profile to some Platforms


        }


        $user_bio = UserBio::where('user_id', $user_id)->first();
        if ($user_bio)
            Log::debug("Check update Bio user plain ID LINE 1303 End of contruct SMAIUpdateProfileController " . $user_bio->plan_id);


    }


    public function checkColumnExist($column, $table, $db)
    {
        try {
            $has_table = Schema::connection($db)->hasColumn($table, $column);
            return $has_table;
        } catch (\Exception $e) {
            return false;
        }
    }


    public function ids()
    {
        return uniqid();
    }


    //Done
    public function up_profile_main_co_in_by_admin($userdata, $user_id, $user_email)
    {


        //1. update to profile of SocialPost
        //2. update basic profile to all Platforms
        //3. update extra profile to some Platforms

        /* if ($this->upFromWhere  == 'socialpost')
        {
            $user = UserSP::where('id', '=', $user_id)
                    ->update($userdata);
        } */

        Log::debug("Start update MainCoIn Profile to all Platforms in up_profile_main_coin with this data  :");
        Log::info($userdata);


        //Update phone to all platforms
        if (isset($userdata['phone']))
            $this->update_phone_centralize($user_id, $user_email, $userdata['phone']);

        if (isset($userdata['remaining_words'])) {
            Log::debug('FOund remaining_words Updating by token_centralize');

            $token_array = array(
                'remaining_words' => $userdata['remaining_words'],
                'remaining_images' => $userdata['remaining_images'],

            );
            $this->update_token_centralize($user_id, $user_email, $token_array);


        }


        //Bio , Socialpost, Main
        if (isset($userdata['country'])) {

            // to Main , Socialpost
            $userdata_country = array(
                'country' => $userdata['country'],
            );
            $this->update_column_all($userdata_country, $user_id, $user_email, 'main_db', 'users');

            $this->update_column_all($userdata_country, $user_id, $user_email, 'bio_db', 'users');

        }

        //Done
        //Final update name to all platforms
        if (isset($userdata['name'])) {
            if (isset($userdata['surname']))
                $userdata['name'] .= " " . $userdata['surname'];

            $userdata_name = array(
                'name' => $userdata['name'],
                'email' => $userdata['email'],

            );

            $this->update_column_all($userdata_name, $user_id, $user_email);

        }


    }

    public function db_session_create()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (isset($_COOKIE['PHPSESSID'])) {
            // dd(Auth::user()->id);
            //socialpost subfolder session login create
            $team_id = DB::connection('main_db')->table('sp_team')
                ->select('id', 'ids')
                ->where('owner', '=', Auth::user()->id)
                ->limit(1)
                ->get();
            $user_uid = DB::connection('main_db')->table('sp_users')
                ->select('id', 'ids')
                ->where('id', '=', Auth::user()->id)
                ->limit(1)
                ->get();

            // dd($team_id[0]->ids);
            // dd($user_uid[0]->ids);


            Log::debug('Team socialpost ID : ' . $team_id[0]->ids);
            Log::debug('User socialpost ID : ' . $user_uid[0]->ids);

            Session::put('uid', $team_id[0]->ids);
            Session::put('team_id', $user_uid[0]->ids);
            //set_session(["team_id" => $user_uid[0]->ids]);
            $sessionId = Session::getId();
            Log::debug('Debug Current Session ID : ' . $sessionId);
            // dd($sessionId);
            Log::debug('Debug PHP Session ID : ' . $_COOKIE['PHPSESSID']);

            $current_session_id = DB::table('sessions')
                ->select('pub_id', 'id', 'session_PHPSESSID')
                ->where('id', '=', $sessionId)
                ->orderBy('pub_id', 'desc')
                ->limit(1)
                ->get();


            $current_time = \Carbon\Carbon::now()->timestamp;

            if (count($current_session_id) > 0)
                DB::connection('main_db')->update('update sessions set session_PHPSESSID = ? where id LIKE ? order by pub_id desc', [$_COOKIE['PHPSESSID'], $sessionId]);
            else
                DB::connection('main_db')->insert('insert into sessions (id, session_PHPSESSID, user_id, last_activity, ids, team_id, payload) values (?, ?, ?, ?, ?, ?, ?)', [$sessionId, $_COOKIE['PHPSESSID'], Auth::user()->id, $current_time, $user_uid[0]->ids, $team_id[0]->ids, 'payload']);

            //eof socialpost subfolder session login create
        }


    }


    //working
    public function up_profile_socialpost($userdata, $user_id, $user_email)
    {


        Log::debug("Start update SocialPost Profile to all Platforms in up_profile_socialpost ");


        //1.update name to all platforms
        if (isset($userdata['name'])) {
            $userdata_name = array(
                'name' => $userdata['name'],
            );
            $this->update_column_all($userdata_name, $user_id, $user_email);

        }

        //Language email username avatar


        //Bio , Socialpost, Main
        if (isset($userdata['timezone'])) {

            // to Main , Socialpost
            $userdata_timezone = array(
                'timezone' => $userdata['timezone'],
            );

            //add update timezone to Bio because request not from Bio
            $this->update_column_all($userdata_timezone, $user_id, $user_email, 'bio_db', 'users');

            $this->update_column_all($userdata_timezone, $user_id, $user_email, 'main_db', 'users');

            $this->update_column_all($userdata_timezone, $user_id, $user_email, 'main_db', 'sp_users');

        }


        if (isset($userdata['language'])) {
            // to Main , Socialpost
            //Bio use getDisplayLanguageForLocaleCode($langcode) to convert lnguage , lang
            //CRM default_language
            //SEO
            //Bio blog
            $userdata_language = array(
                'language' => $userdata['language'],
            );
            $lang = $userdata['language'];
            $this->update_language_centralize($user_id, $user_email, $lang);

        }

        if (isset($userdata['avatar'])) {
            // to Main , Socialpost
            //Bio use getDisplayLanguageForLocaleCode($langcode) to convert language , lang
            //CRM default_language
            //SEO
            //Bio blog

            //main users table
            //column profile_pic,photo,	profile_image,avatar
            $userdata_avatar = array(
                'avatar' => $userdata['avatar'],
                'profile_pic' => $userdata['avatar'],
                'photo' => $userdata['avatar'],
                'profile_image' => $userdata['avatar'],
            );
            $this->update_column_all($userdata_avatar, $user_id, $user_email, 'main_db', 'users');

            unset($userdata_avatar['profile_pic']);
            unset($userdata_avatar['photo']);
            unset($userdata_avatar['profile_image']);

            //socialpost  avatar

            //bio_blog  avatar
            $this->update_column_all($userdata_avatar, $user_id, $user_email, 'bio_blog_db', 'users');

            //design profile_pic
            $userdata_avatar['profile_pic'] = $userdata['avatar'];
            unset($userdata_avatar['avatar']);
            $this->update_column_all($userdata_avatar, $user_id, $user_email, 'digitalasset_db', 'users');

        }


    }

    public function up_profile_mobileApp($request, $user_id)
    {
        if (isset($request->password) && $this->skip_update_pss != 1) {
            $privatehash_password = $this->hash_password;
        }
        //TODO Mobile Old app Demo
        $MobileApp_connected = 1;
        if ($MobileApp_connected == 1) {

            $assign_plan = Plan::where('price', 0)->orderBy('id', 'asc')->first();
            $userdata = [
                'id' => $user_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => $this->hash_password,
                'user_type' => 'company',
                'created_by' => 1,
                'plan' => $assign_plan->id,
                'available_words' => $assign_plan->max_words,
                'total_words' => $assign_plan->max_words,
                'available_images' => $assign_plan->max_images,
                'total_images' => $assign_plan->max_images,
                'email_verified_at' => Carbon::now(),
                'plan_expire_date' => Carbon::now()->addDays(14),
            ];

            $user = UserMain::where('id', '=', $user_id)
                ->update($userdata);


        }


        //EOF TODO Mobile Old app Demo
    }


    public function up_profile_main_marketing($request, $user_id)
    {
        if (isset($request->password) && $this->skip_update_pss != 1) {
            $privatehash_password = $this->hash_password;
        }

        $affiliate_user_id = Cookie::get('affiliate_user_id');
        if ($affiliate_user_id === null) $affiliate_user_id = 0;
        //set_agency_config();

        //$affiliate_user_id = 0;
        $package_info = DB::connection('main_db')->table('packages')->where(['is_default' => '1'])->first();
        $validity = isset($package_info->validity) ? $package_info->validity : 0;
        $package_id = isset($package_info->id) ? $package_info->id : 0;
        $to_date = date('Y-m-d');
        $expiry_date = date("Y-m-d", strtotime('+' . $validity . ' day', strtotime($to_date)));
        $curtime = date("Y-m-d H:i:s");
        $userdata = [
            'id' => $user_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $this->hash_password,
            'user_type' => 'Member',
            'package_id' => $package_id,
            'created_at' => $curtime,
            'updated_at' => $curtime,
            'expired_date' => $expiry_date,
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => get_real_ip(),
            'under_which_affiliate_user' => $affiliate_user_id

        ];
        $user = UserMain::where('id', '=', $user_id)
            ->update($userdata);

    }


    public function up_profile_design($request, $main_id)
    {

        Log::debug('before update new Design Email :' . $request->name);
        Log::debug('before insert new Design Name :' . $request->email);
        if (!empty($request->name) && !empty($request->email)) {
            $where = array('email' => ($request->email));
            //$result = $this->Common_DML->get_data( 'users', $where, 'COUNT(*) As total' );
            $result = DB::connection('digitalasset_db')->table('users')->where(['email' => $request->email])->get();
            $total = $result->count();

            if (isset($request->password) && $this->skip_update_pss != 1) {
                $privatehash_password = $this->hash_password;
            }

            if (isset($request->avatar))
                $profile_pic = $request->avatar;
            else if (isset($request->profileImage) && substr($request->profileImage, 0, 4) == "http")
                $profile_pic = $request->profileImage;
            else if (isset($request->image) && substr($request->image, 0, 4) == "http")
                $profile_pic = $request->image;
            else
                $profile_pic = '';

            Log::debug('while Insert new Design Name found user :' . $total);

            $update_id = DB::connection('digitalasset_db')->table('users')->update($array);


        }


    }


    public function up_profile_mobileAppV2($request, $user_id)
    {

        if (isset($request->image))
            $user_avatar = $request->image;
        else
            $user_avatar = '';


        $assign_plan = PlanMobile::where('price', 0)->orderBy('id', 'asc')->first();
        $userdata = [
            'id' => $user_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $this->hash_password,
            'image' => $request->image,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'words_left' => $this->freetrial_plan_words,
            'image_left' => $this->freetrial_plan_images,
            'user_type' => "User",
            'remaining_words' => $this->freetrial_plan_words,
            'remaining_images' => $this->freetrial_plan_images,

        ];

        $user = UserMobile::where('id', '=', $user_id)
            ->update($userdata);

    }


    public function up_profile_mobileAppV2_email($request, $user_id, $user_email)
    {

        //$assign_plan=PlanMobile::where('price',0)->orderBy('id','asc')->first();
        $userdata = [
            'id' => $user_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $this->hash_password,
            'image' => $request->image,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'words_left' => $this->freetrial_plan_words,
            'image_left' => $this->freetrial_plan_images,
            'user_type' => "User",
            'remaining_words' => $this->freetrial_plan_words,
            'remaining_images' => $this->freetrial_plan_images,

        ];

        $user = UserMobile::where('email', '=', $user_email)
            ->update($userdata);

    }

    //Done

    public function up_profile_bio($userdata, $user_id, $user_email)
    {
        $user_main = UserMain::where('id', $user_id)->first();
        if ($user_main)
            Log::debug('Dubug checking current Main User PLan Line 1967 ' . $user_main->plan);

        Log::debug("Start update Bio Profile to all Platforms in up_profile_bio ");


        //1.update name to all platforms
        if (isset($userdata['name'])) {
            $userdata_name = array(
                'name' => $userdata['name'],
            );
            $this->update_column_all($userdata_name, $user_id, $user_email);

        }


        //Bio , Socialpost, Main
        if (isset($userdata['timezone'])) {

            // to Main , Socialpost
            $userdata_timezone = array(
                'timezone' => $userdata['timezone'],
            );
            $this->update_column_all($userdata_timezone, $user_id, $user_email, 'main_db', 'users');

            $this->update_column_all($userdata_timezone, $user_id, $user_email, 'main_db', 'sp_users');

        }


        if (isset($userdata['is_newsletter_subscribed'])) {
            //Only Smart Bio have newsletter subscribe

        }


    }

    public function up_freeplan_bio($userdata, $user_id, $user_email)
    {
        //update every Platforms to free plan id
        // Main.co.in to plan id  8
        //Main.marketing to package_id 1

    }

    //DOne
    //ยึดตามการสมัครใช้งานหรือซื้อแพคเกจหรือการล๊อคอินจาก Bio Platform
    // up_plan_series should devided to 3 major 1.Feature Sync 2.Token Sync 3.TimeSync
    public function up_plan_bio($userdata, $user_id, $user_email, $case = NULL)
    {
        $user_main = UserMain::where('id', $user_id)->first();
        if ($user_main)
            Log::debug('Dubug checking current Main User PLan Line 1749 ' . $user_main->plan);


        Log::debug("Start update Bio Profile to all Platforms in up_plan_bio ");

        //1.update plan to all platforms
        if (isset($userdata['plan'])) {
            $userdata_plan = array(
                'plan' => $userdata['plan'],
            );

            $user_main = UserMain::where('id', $user_id)->first();
            if ($user_main)
                Log::debug('Dubug checking current Main User PLan Line 1761 ' . $user_main->plan);

            // not working because each plan value not the same
            //$this->update_column_all($userdata_name,$user_id,$user_email);


            $each_plan = PlanBio::where('plan_id', $userdata['plan'])->orderBy('plan_id', 'asc')->first();

            $socialpost_plan = $each_plan->socialpost_id;
            $design_plan_id = $each_plan->design_id;
            $userdata_plan['plan'] = $socialpost_plan;
            $package_type_bio = $each_plan->package_type;
            //Update Bio Plan change will effect SocialPost when It's is bundle

            $plan_before = UserMain::where('id', $user_id)->first();
            $plan_before_id = $plan_before->bio_plan;


            Log::debug('Dubug checking current Main User PLan Line 1778 ' . $plan_before->plan);


            $package_type_bio = 'single';


            //fixing big bug is below!!!!!!!!!!!!!!!!!!!
            /* if($plan_before_id==4 && $userdata_plan['plan']==0 && $userdata_plan['plan_id']==0)
            $package_type_bio=='bundle'; */

            /*    if($package_type_bio=='bundle')
            {

                if($socialpost_plan==0)
                $socialpost_plan=1;


            $this->socialpost_permission_update_user($user_id,$socialpost_plan);
            $this->update_column_all($userdata_plan, $user_id, $user_email, 'main_db', 'sp_users');

            } */

            $main_coin_plan = $each_plan->main_plan_id;
            if ($main_coin_plan == 8) {
                $main_marketing_id = 1;
            } else {
                $main_marketing_id = $main_coin_plan;
            }

            $plan_before = UserMain::where('id', $user_id)->first();
            $plan_before_id = $plan_before->main_plan;

            $plan_before_type = Plan::where('id', $plan_before_id)->orderBy('id', 'asc')->first();


            //Downgrade in case of reset to Freetrial Plan
            /* if($plan_before_type->package_type =='bundle' && $userdata_plan['plan']==0 && $userdata_plan['plan_id']==0)
            $package_type_bio=='bundle'; */

            $package_type_bio = 'single';

            if ($package_type_bio == 'bundle') {

                $userdata_plan['plan'] = $main_coin_plan;

                $this->update_column_all($userdata_plan, $user_id, $user_email, 'main_db', 'users');
                $design_plan = $each_plan->design_id;
                $userdata_plan['plan'] = $design_plan;
                $this->update_column_all($userdata_plan, $user_id, $user_email, 'digitalasset_db', 'users');


                $mobile_plan = $each_plan->mobile_id;
                $userdata_plan['plan'] = $mobile_plan;
                $this->update_column_all($userdata_plan, $user_id, $user_email, 'mobileapp_db', 'users');

                $sync_plan = $each_plan->sync_id;
                $userdata_plan['plan'] = $sync_plan;
                $this->update_column_all($userdata_plan, $user_id, $user_email, 'sync_db', 'user');


                // prepare for next update
                $userdata['package_id'] = $main_marketing_id;

            }

        }

        $user_main = UserMain::where('id', $user_id)->first();
        if ($user_main)
            Log::debug('Dubug checking current Main User PLan Line 1847 ' . $user_main->plan);

        //Bio ,Main, Socialpost, Design,Mobile2 Sync
        $user_old_data = UserBio::where('user_id', $user_id)->orderBy('user_id', 'asc')->first();

        if ($user_old_data->plan_settings == NULL) {
            if ($user_old_data->plan_id == 'free')
                $user_bio_plan_id = 0;
            else if ($user_old_data->plan_id == 'team')
                $user_bio_plan_id = 99;
            else
                $user_bio_plan_id = $user_old_data->plan_id;


            $bio_user_setting_from_plan = PlanBio::where('plan_id', $user_bio_plan_id)->first();
            $user_old_data_plan = $bio_user_setting_from_plan->settings;
            $user_old_data->plan_settings = $user_old_data_plan;
            $user_old_data->save();

            $user_main = UserMain::where('id', $user_id)->first();
            if ($user_main)
                Log::debug('Dubug checking current Main User PLan Line 1866' . $user_main->plan);


        } else {
            $user_old_data_plan = $user_old_data->plan_settings;

        }

        if ($this->isJson(trim($user_old_data->plan_settings, '"'))) {
            $user_old_data_plan = json_decode(trim($user_old_data->plan_settings, '"'), true);

        }

        $user_main = UserMain::where('id', $user_id)->first();
        if ($user_main)
            Log::debug('Dubug checking current Main User PLan Line 1882 ' . $user_main->plan);


        Log::debug('Found old data in Bio Plan update words_per_month_limit ' . $user_old_data_plan['words_per_month_limit']);

        Log::debug('Found old data in Bio Plan update images_per_month_limit ' . $user_old_data_plan['images_per_month_limit']);


        //defind remaining_words should come from Main in some case data because Bio has reset it to 0 when change Plan


        $userdata['remaining_words'] = $user_old_data_plan['words_per_month_limit'];
        $userdata['remaining_images'] = $user_old_data_plan['images_per_month_limit'];
        $user_bio_old_data = UserBio::where('user_id', $user_id)->orderBy('user_id', 'asc')->first();

        $user_old_data->remaining_words = $user_old_data_plan['words_per_month_limit'];
        $user_old_data->remaining_images = $user_old_data_plan['images_per_month_limit'];

        $user_main = UserMain::where('id', $user_id)->first();
        if ($user_main)
            Log::debug('Dubug checking current Main User PLan Line 1901 ' . $user_main->plan);

        //fixing bug
        $main_subscription = SubscriptionMain::where('user_id', $user_id)
            ->where(function ($query) {
                $query->where('stripe_status', 'active')
                    ->orWhere('stripe_status', 'trialing');
            })
            ->first();

        Log::info('Main subscription:', ['main_subscription' => $main_subscription]);

        if ($main_subscription) {
            $trial_ends_at = \Carbon\Carbon::parse($main_subscription->trial_ends_at);
            $ends_at = \Carbon\Carbon::parse($main_subscription->ends_at);

            if ($trial_ends_at->greaterThan($ends_at)) {
                $userdata['plan_expiration_date'] = $main_subscription->trial_ends_at;
                Log::debug('Time end ' . $main_subscription->trial_ends_at);
            } else {
                $userdata['plan_expiration_date'] = $main_subscription->ends_at;
                Log::debug('Time end ' . $main_subscription->ends_at);
            }
        } else {
            // handle the case where main_subscription is not found
            // if necessary
        }


        //defind others old mobile old Main
        $userdata['total_words'] = $user_old_data->remaining_words;
        $userdata['total_images'] = $user_old_data->remaining_images;

        $userdata_time = array();
        //$userdata_time['expiration_date'] = $user_bio_old_data->plan_expiration_date;
        $userdata_time['plan_expire_date'] = $userdata['plan_expiration_date'];
        $userdata_time['expired_date'] = $userdata['plan_expiration_date'];

        $userdata['expiration_date'] = $userdata['plan_expiration_date'];
        $userdata['plan_expire_date'] = $userdata['plan_expiration_date'];
        $userdata['expired_date'] = $userdata['plan_expiration_date'];

        //if(isset($userdata_time['expiration_date']))
        $expired = $user_bio_old_data->plan_expiration_date;

        $userdata['available_words'] = $user_old_data->available_words;
        $userdata['available_images'] = $user_old_data->available_images;

        $user_main = UserMain::where('id', $user_id)->first();
        if ($user_main)
            Log::debug('Dubug checking current Main User PLan Line 1924 ' . $user_main->plan);
        if (isset($userdata['remaining_words'])) {

            //because bio has plan call free save in plan_id
            if ($userdata['plan'] == 'free')
                $userdata['plan'] = 0;


            //select  plans table where plan_id = $users Bio plan_id
            $check_main_plan = PlanBio::where('plan_id', $userdata['plan'])->orderBy('plan_id', 'asc')->first();
            $user_main = UserMain::where('id', $user_id)->first();
            if ($user_main)
                Log::debug('Dubug checking current Main User PLan Line 1935 ' . $user_main->plan);

            //this will return plan should be of main_plan_id
            $main_plan_id = $check_main_plan->main_plan_id;

            $user_main = UserMain::where('id', $user_id)->first();
            if ($user_main)
                Log::debug('Dubug checking current Main User PLan Line 1941 ' . $user_main->plan);

            //เนื่องจากเป็นการเรียกอัพเดทจาก Bio ซึ่งในตัวมันเองทำการ บวกToken ไปเรียบร้อยแล้ว
            //ดังนั้นเหลือ Token ที่มาจาก MainCoIn ที่ยังไม่ได้บวก
            $check_plus_remaining = Plan::where('id', $main_plan_id)->orderBy('id', 'asc')->first();


            Log::debug('Found Main Plan Id that reference from Plan Bio and Main Plan ID is ' . $check_plus_remaining->id);
            //backup Plan in user Main as the core value
            $use_update_core_users_plans_backup = 1;

            $sp_cur_plan = $check_main_plan->socialpost_id;
            $design_cur_plan = $check_main_plan->design_id;
            $mobile_cur_plan = $check_main_plan->mobile_id;
            $sync_cur_plan = $check_main_plan->sync_id;

            if ($use_update_core_users_plans_backup == 1) {

                if ($userdata['plan'] == 'free')
                    $userdata['plan'] = 0;

                if ($userdata['plan'] == 'team')
                    $userdata['plan'] = 99;

                $user_main_plans_id = array(
                    'sp_plan' => $sp_cur_plan,
                    'design_plan' => $design_cur_plan,
                    'mobile_plan' => $mobile_cur_plan,
                    'sync_plan' => $sync_cur_plan,
                    'bio_plan' => $userdata['plan'],
                    'plan' => $main_plan_id,


                );
                $user_main = UserMain::where('id', $user_id)->first();
                if ($user_main)
                    Log::debug('Dubug checking current Main User PLan Line 1974 ' . $user_main->plan);

            }

            $user_main = UserMain::where('id', $user_id)->first();
            if ($user_main)
                Log::debug('Dubug checking current Main User PLan Line 2028 After if condition ' . $user_main->plan);


            //Sync Token section
            //Solution use SubscriptionBio,SubscriptionMain check plan_id that bundle only

            $central_remaining = UserDesign::where('id', $user_id)->first();


            //Fixing Move to Main Plan only
            $from_payment = 'SubscriptionMain';
            //$where_payment_bundle_from=SubscriptionMain::where('stripe_status','active')->orWhere('stripe_status', 'trialing')->where('user_id',$user_id)->whereIn('plan_id', [5,7,10,11])->latest()->first();
            $where_payment_bundle_from = SubscriptionMain::where('stripe_status', 'active')->orWhere('stripe_status', 'trialing')->where('user_id', $user_id)->latest()->first();
            //eof Fixing Move to Main Plan only

            $bio_token_synced = $where_payment_bundle_from->bio_token_sync;
            $main_token_synced = $where_payment_bundle_from->main_token_sync;

            if ($main_token_synced == 0) {
                //main in this case is Bio
                $plus_remaining_images = $check_main_plan->total_images;
                $plus_remaining_words = $check_main_plan->total_words;
            } else {
                $plus_remaining_images = 0;
                $plus_remaining_words = 0;

            }

            if ($bio_token_synced == 0) {
                //Bio in this case is main
                $plus_bio_remaining_images = $check_plus_remaining->total_images;
                $plus_bio_remaining_words = $check_plus_remaining->total_words;
                Log::debug(' Case Bio Token Synced =0 and the Images Token plus will be add is' . $plus_bio_remaining_images);
                Log::debug(' Case Bio Token Synced =0 and the DocText Token plus will be add is' . $plus_bio_remaining_words);

                //this is the key that make $plus_bio_remaining_words = 0
                // $case = 'fix_main_plan_from_fresh_upgrade_Bio';


            } else {
                $plus_bio_remaining_images = 0;
                $plus_bio_remaining_words = 0;


            }


            //because this happending in Main so
            //comapre old data from Main with the Central remaining

            if ($user_old_data->remaining_words < $central_remaining->remaining_words + $plus_remaining_words) {
                Log::debug('The Old remaining Main < than Central maybe because subscription RESET Token reason');
                //$plus_remaining_images=0;
                //$plus_remaining_images=0;
                //because It's already reset inside MainCoIn

            } else {

                if ($user_old_data->remaining_words == $central_remaining->remaining_words + $plus_remaining_words)
                    Log::debug('The Old remaining Main = Central maybe because subscription still keep Token reason');
                else
                    Log::debug('The Old remaining Main > Central maybe because  subscription already added Token reason and Maybe Error and this Sync happend outside upgrading in Main or someting Error!!!!!!!!!! ');

            }
            $user_main = UserMain::where('id', $user_id)->first();
            if ($user_main)
                Log::debug('Dubug checking current Main User PLan Line 2116 ' . $user_main->plan);


            /* if($case=='fix_main_plan')
            {
                $plus_remaining_images = 0;
                $plus_remaining_words = 0;
                $plus_bio_remaining_images = 0;
                $plus_bio_remaining_words = 0;
            } */


            //case reset to freetrial no need to add token
            if ($userdata['plan'] == 0) {
                $plus_remaining_images = 0;
                $plus_remaining_words = 0;

            }


            //to centralize Token
            if ($user_old_data->remaining_words < $central_remaining->remaining_words + $plus_remaining_words) {
                $old_reamaining_word = $user_old_data->remaining_words;
                $old_reamaining_image = $user_old_data->remaining_images;
                $plus_remaining_images = 0;
                $plus_remaining_words = 0;
                //because the value from MainCoIn has already added token by package
                //then add only for bio_plus

            } else {
                //$old_reamaining_word = $central_remaining->remaining_words ;
                //$old_reamaining_image = $central_remaining->remaining_images ;

                $old_reamaining_word = $user_old_data->remaining_words;
                $old_reamaining_image = $user_old_data->remaining_images;
            }

            $user_main = UserMain::where('id', $user_id)->first();
            if ($user_main)
                Log::debug('Dubug checking current Main User PLan Line 2161 ' . $user_main->plan);


            Log::debug('Bio Plan Sync Total plus words from Bio before send Centralize' . $plus_remaining_words);
            Log::debug('Bio Plan Sync Total plus words from MainCoIn before send Centralize' . $plus_bio_remaining_words);

            $user_bio = UserBio::where('user_id', $user_id)->first();
            if ($user_bio)
                Log::debug("Check update Bio user plain ID LINE 2170 before upgrading plan in Bio " . $user_bio->plan_id);

            //update new data after plus new Plan
            $userdata['remaining_words'] += $plus_remaining_words + $plus_bio_remaining_words;
            $userdata['remaining_images'] += $plus_remaining_images + $plus_bio_remaining_images;
            $userdata['available_words'] += $plus_remaining_words + $plus_bio_remaining_words;
            $userdata['available_images'] += $plus_remaining_images + $plus_bio_remaining_images;
            $userdata['total_words'] += $plus_remaining_words + $plus_bio_remaining_words;
            $userdata['total_images'] += $plus_remaining_images + $plus_bio_remaining_images;


            if (isset($userdata['remaining_words_plus'])) {
                Log::debug('This process happend while upgrading plan in Bio');
                if ($bio_token_synced == 0 && $main_token_synced)
                    Log::debug('Yessss It is confirmed this Token process while upgrading plan in Bio');

                if ($central_remaining->remaining_words < $user_old_data->remaining_words)
                    Log::debug('Yessss!!!!!! It is Triple confirmed this Token process while upgrading plan in Bio');

                $this->plus_new_images_token = $plus_remaining_images;
                Log::debug('Bio Plan Sync Total plus words from Bio after send Centralize' . $plus_remaining_words);
                $this->plus_new_words_token = $plus_remaining_words;
                Log::debug('Bio Plan Sync Total plus words from MainCoIn after send Centralize' . $plus_bio_remaining_words);

            } else {
                $this->plus_new_images_token = $plus_remaining_images;
                $this->plus_new_words_token = $plus_remaining_words;
            }

            $user_main = UserMain::where('id', $user_id)->first();
            if ($user_main)
                Log::debug('Dubug checking current Main User PLan Line 2196 ' . $user_main->plan);


            $user_bio = UserBio::where('user_id', $user_id)->first();
            if ($user_bio)
                Log::debug("Check update Bio user plain ID LINE 2205  " . $user_bio->plan_id);


            $token_array = array(
                'remaining_words' => $userdata['remaining_words'],
                'remaining_images' => $userdata['remaining_images'],
            );

            // Log::debug('Before send Token Array to Centalize'. $token_array);
            $token_plus_array = array(
                'plus_remaining_images' => $plus_remaining_images,
                'plus_remaining_words' => $plus_remaining_words,
                'plus_bio_remaining_images' => $plus_bio_remaining_images,
                'plus_bio_remaining_words' => $plus_bio_remaining_words,
            );


            Log::debug('Before send Token plus and Token Array to Centalize');
            Log::info($token_array);
            Log::debug(' and Token Plus !!!!!!! ');
            Log::info($token_plus_array);


            //To Bio ,Main, Socialpost, Design,Mobile2 Sync
            $userdata_remaining_words = array(
                'remaining_words' => $userdata['remaining_words'],
                'remaining_images' => $userdata['remaining_images'],
            );

            //


            //move this to centralize Token
            $user_bio = UserBio::where('user_id', $user_id)->first();
            if ($user_bio)
                Log::debug("Check update Bio user plain ID LINE 2187  " . $user_bio->plan_id);

        }

        $user_main = UserMain::where('id', $user_id)->first();
        if ($user_main)
            Log::debug('Dubug checking current Main User PLan Line 2232 ' . $user_main->plan);


        //Separate all of these for each platform


        //plan mobile_old
        //Done
        if (isset($userdata['available_images'])) {

        }

        //plan mobile_old
        //Done
        if (isset($userdata['total_words'])) {

        }

        //plan mobile_old
        //Done
        if (isset($userdata['total_images'])) {

        }

        //plan universal
        //Done
        if (isset($userdata['expiration_date'])) {

        }

        //plan mobile_old
        //Done
        if (isset($userdata['plan_expire_date'])) {

        }

        //plan main
        //Done
        if (isset($userdata['expired_date'])) {

        }


        //plan mobile_old
        //Done
        if (isset($userdata['available_words'])) {

        }

        $user_bio = UserBio::where('user_id', $user_id)->first();
        if ($user_bio)
            Log::debug("Check update Bio user plain ID LINE 2243  " . $user_bio->plan_id);

        //plan mobile_new
        $usersync_old_data = UserMobile::where('id', $user_id)->orderBy('id', 'asc')->first();
        if (isset($userdata['words_left'])) {
            $userdata['words_left'] = $usersync_old_data->words_left;
            $userdata['image_left'] = $usersync_old_data->image_left;
        } else {
            $userdata = array(
                'words_left' => $usersync_old_data->words_left,
                'image_left' => $usersync_old_data->image_left,
            );

        }

        $userdata['words_left'] += $this->plus_new_words_token;
        $userdata['image_left'] += $this->plus_new_images_token;

        if (isset($userdata['words_left'])) {
            //To Bio ,Main, Socialpost, Design,Mobile2 Sync
            $userdata_mobile_plan_array = array(
                'words_left' => $userdata['words_left'],
                'image_left' => $userdata['image_left'],

            );

            // comment to move to  plans_token_centralize();
            //$this->update_column_all($userdata_mobile_plan_array, $user_id, $user_email, 'mobileapp_db', 'users');

        }


        //plan mobile_new
        if (isset($userdata['image_left'])) {

        }

        //plan bio
        //because call from bio Do nothing
        if (isset($userdata['plan_id'])) {

        }


        //plan bio
        //because call from bio Do nothing
        if (isset($userdata['plan_settings'])) {

        }


        //plan bio
        //because change to update from Main_COin all
        if (isset($userdata['plan_expiration_date']))
            $expired = $userdata['plan_expiration_date'];
        else if (isset($userdata['expiration_date']))
            $expired = $userdata['expiration_date'];
        else
            $expired = NULL;


        $user_bio = UserBio::where('user_id', $user_id)->first();
        $user_bio->plan_expiration_date = $expired;
        $user_bio->save();
        Log::debug("Check update Bio user plain Expiration LINE 2341  " . $user_bio->plan_expiration_date);


        Log::debug("Check update Bio user plain ID LINE 2303  " . $user_bio->plan_id);

        if (isset($userdata['remaining_words'])) {
            //defind exactly Token that will be update then update centralize
            $this->plans_token_centralize($user_id, $user_email, $token_array, $usage = 0, $from = 'bio', $old_reamaining_word, $old_reamaining_image, $chatGPT_catgory = "SubscriptionMain", $token_update_type = 'both', $expired, $design_plan_id, $token_plus_array, $from_payment, $case);
        }

        $user_bio = UserBio::where('user_id', $user_id)->first();
        if ($user_bio)
            Log::debug("Check update Bio user plain ID LINE 2311  " . $user_bio->plan_id);

    }

    //Done
    //fixing fixing up_plan_series should devided to 3 major 1.Feature Sync 2.Token Sync 3.TimeSync
    public function up_plan_main_coin($userdata, $user_id, $user_email, $case = NULL)
    {
        $golden_tokens = 0;
        $userdata_plan = array();
        Log::debug("Start update Bio Profile to all Platforms in up_plan_main_coin ");


        //1.update plan to all platforms
        if (isset($userdata['plan']) && ($this->upFromWhere == 'main_coin' || Str::contains($this->upFromWhere, 'MainCoIn_'))) {
            if ($userdata['plan'] == 7 || $userdata['plan'] == 4) {
                $golden_tokens = 1;

            }

        }


        if (isset($userdata['plan'])) {

            Log::debug('Yes userdata_plan has been set');
            $userdata_plan = array(
                'plan' => $userdata['plan'],
            );


            // not working because each plan value not the same
            //$this->update_column_all($userdata_name,$user_id,$user_email);

            if (isset($userdata['plan']))
                $each_plan = Plan::where('id', $userdata['plan'])->orderBy('id', 'asc')->first();
            else
                Log::debug('Error _userdata_plan not set');

            //Update social post

            if (isset($each_plan)) {

                $bio_plan_id = $each_plan->bio_id;
                Log::debug('FOund Bio Plan ' . $bio_plan_id);
                $design_plan_id = $each_plan->design_id;
                Log::debug('Case isset $each_plan from Main  true');
                $socialpost_plan = $each_plan->socialpost_id;
                Log::debug('FOund Social Plan ' . $socialpost_plan);

                $userdata_plan['plan'] = $socialpost_plan;
                Log::debug('success set userdata_plan ' . $userdata_plan['plan']);

                $package_type_main = $each_plan->package_type;

                Log::debug('and Package Type ' . $package_type_main);
            }

            if ($socialpost_plan == 0)
                $socialpost_plan = 1;

            $this->socialpost_permission_update_user($user_id, $socialpost_plan);
            $this->update_column_all($userdata_plan, $user_id, $user_email, 'main_db', 'sp_users');

            Log::debug('Success passed socialpost_permission_update_user');


            $main_coin_plan = $userdata['plan'];
            if ($main_coin_plan == 8 || $main_coin_plan == 0) {
                $main_marketing_id = 1;
            } else {
                $main_marketing_id = $main_coin_plan;
            }


            if ($main_coin_plan === 0 || $main_coin_plan == 8) {
                //Update MainCoIn back to free Plan
                $main_subscription = SubscriptionMain::where('user_id', $user_id)->where('stripe_status', 'trialing')->latest('id')->first();
                if (isset($main_subscription)) {
                    $main_subscription->stripe_status = 'cancelled';
                    $main_subscription->save();
                }
            }


            //call from main_coin need not to update itself
            /* $userdata_plan['plan']=$main_coin_plan;
            $this->update_column_all( $userdata_plan,$user_id,$user_email,'main_db','users');
     */

            if (!isset($bio_plan_id)) {
                //try other ways to get bio plan id
                $bioplan_id_from_main = UserMain::where('id', $user_id)->first();
                $bio_plan = $bioplan_id_from_main->bio_plan;

            } else {

                $bio_plan = $bio_plan_id;
            }

            if ($package_type_main == 'bundle') {


                //updated Nov2023 add stripcslashes
                //$this->bio_plan_settings_update_user($user_id,$bio_plan);
                $result_return = $this->reset_user_Bio($user_id, $user_email, $this->upFromWhere);

                unset($userdata_plan['plan']);
                $userdata_plan['plan_id'] = $bio_plan;
                $this->update_column_all($userdata_plan, $user_id, $user_email, 'bio_db', 'users');
                unset($userdata_plan['plan_id']);
            }

            //mostly it depend on MainCoIn
            $design_plan = $each_plan->design_id;

            $userdata_plan['plan'] = $design_plan;
            $this->update_column_all($userdata_plan, $user_id, $user_email, 'digitalasset_db', 'users');

            //mostly it depend on MainCoIn
            $mobile_plan = $each_plan->mobile_id;
            $userdata_plan['plan'] = $mobile_plan;
            $this->update_column_all($userdata_plan, $user_id, $user_email, 'mobileapp_db', 'users');

            //mostly it depend on MainCoIn
            $sync_plan = $each_plan->sync_id;
            $userdata_plan['plan'] = $sync_plan;
            $this->update_column_all($userdata_plan, $user_id, $user_email, 'sync_db', 'user');


            // prepare for next update
            $userdata['package_id'] = $main_marketing_id;

        }


        //Bio ,Main, Socialpost, Design,Mobile2 Sync
        $user_old_data = UserMain::where('id', $user_id)->orderBy('id', 'asc')->first();


        //defind remaining_words
        //fix when call from plan sync
        $userdata['remaining_words'] = $user_old_data->remaining_words;
        $userdata['remaining_images'] = $user_old_data->remaining_images;
        //$user_bio_old_data=UserBio::where('user_id',$user_id)->orderBy('user_id', 'asc')->first();

        //defind others old mobile old Main
        $userdata['total_words'] = $user_old_data->total_words;
        $userdata['total_images'] = $user_old_data->total_images;

        $userdata['expiration_date'] = $user_old_data->plan_expiration_date;
        $userdata['plan_expire_date'] = $user_old_data->plan_expiration_date;
        $userdata['expired_date'] = $user_old_data->plan_expiration_date;

        $userdata['available_words'] = $user_old_data->available_words;
        $userdata['available_images'] = $user_old_data->available_images;

        if (isset($userdata['remaining_words'])) {


            //ระวัง!!!!!!!!!!! ตรงนี้อาจบวก Token ซ้ำหลายรอบได้
            // add then column name  token_upgraded and token_downgraded to users table
            // set to 0 if no yet added and set to 1 if added when token_downgraded==1 and token_upgraded==1 that mean time to reset
            $check_main_plan = Plan::where('id', $userdata['plan'])->orderBy('id', 'asc')->first();
            $main_plan_id = $check_main_plan->id;
            $correct_bio_plan = $check_main_plan->bio_id;

            if ($main_plan_id == 8 || $main_plan_id == 0) {
                $plan_bio_check_id = 0;
            } else {
                $cur_plan_bio = UserBio::where('user_id', $user_id)->first();

                $plan_bio_check_id = $cur_plan_bio->plan_id;
            }

            $check_plus_remaining = PlanBio::where('plan_id', $plan_bio_check_id)->orderBy('plan_id', 'asc')->first();

            $plus_remaining_images = $check_plus_remaining->total_images;
            $plus_remaining_words = $check_plus_remaining->total_words;


            //ระวัง!!!!!!!!!!! ตรงนี้อาจบวก Token ซ้ำหลายรอบได้


            //check if Tokens from package is Golden Tokens
            if ($golden_tokens == 1) {
                //add tokens to  main Golden Tokens
                //2. should add? golden_freeze_date , golden_expired_date
                $golden_tokens_save = array(

                    'golden_tokens' => $plus_remaining_images + $plus_remaining_words,
                );

                $this->update_column_all($golden_tokens_save, $user_id, $user_email, 'main_db', 'users');
            }


            //backup Plan in user Main as the core value
            $use_update_core_users_plans_backup = 1;

            $sp_cur_plan = $check_main_plan->socialpost_id;
            $design_cur_plan = $check_main_plan->design_id;
            $mobile_cur_plan = $check_main_plan->mobile_id;
            $sync_cur_plan = $check_main_plan->sync_id;

            //remove 'bio_plan' => $userdata['plan'], because it was recheked before this step
            //and $userdata['plan'] is the plan_id of MainCoIn not Bio
            if ($use_update_core_users_plans_backup == 1) {
                if ($package_type_main == 'bundle') {
                    $user_main_plans_id = array(
                        'sp_plan' => $sp_cur_plan,
                        'design_plan' => $design_cur_plan,
                        'mobile_plan' => $mobile_cur_plan,
                        'sync_plan' => $sync_cur_plan,
                        'bio_plan' => $correct_bio_plan,
                    );

                } else {
                    $user_main_plans_id = array(
                        'sp_plan' => $sp_cur_plan,
                        'design_plan' => $design_cur_plan,
                        'mobile_plan' => $mobile_cur_plan,
                        'sync_plan' => $sync_cur_plan,

                    );


                }

                $this->update_column_all($user_main_plans_id, $user_id, $user_email, 'main_db', 'users');

            }

            // if main Plan have extra Token to Plus +
            //Step 1. Check Where is the Bundle payment or transaction subscription come from
            //2. check column main_token_synced, bio_token_synced

            //fixing move to Main Plan only
            $central_remaining = UserDesign::where('id', $user_id)->first();

            //$where_payment_bundle_from=SubscriptionMain::where('stripe_status','active')->orWhere('stripe_status', 'trialing')->where('user_id',$user_id)->whereIn('plan_id', [5,7,10,11])->latest()->first();
            $where_payment_bundle_from = SubscriptionMain::where('stripe_status', 'active')->orWhere('stripe_status', 'trialing')->where('user_id', $user_id)->latest()->first();
            if ($where_payment_bundle_from) {
                $from_payment = 'SubscriptionMain';
                Log::debug('FOund bundle subscription in Main then use it');

            }
            //Stop using Bio
            /*  else{
                $from_payment='SubscriptionBio';
                $where_payment_bundle_from=SubscriptionBio::where('user_id',$user_id)->whereIn('plan_id', [4,4])->latest()->first();

            } */

            $bio_token_synced = $where_payment_bundle_from->bio_token_sync;
            $main_token_synced = $where_payment_bundle_from->main_token_sync;

            //bug now in main_coin is main_token_sync=0 so it should be 1 and update when in mail Paypal or Stripe is success
            //fixing fixing Double check by add Subscription ID to TokenLogs table
            //to record that reamining_words or images already added
            if ($main_token_synced == 0) {
                $plus_remaining_images = $check_main_plan->total_images;
                $plus_remaining_words = $check_main_plan->total_words;
            } else {
                $plus_remaining_images = 0;
                $plus_remaining_words = 0;
            }
            Log::debug('After checked $main_token_synced $plus_remaining_words in Main value is ' . $plus_remaining_words);

            if ($bio_token_synced == 0) {
                $plus_bio_remaining_images = $check_plus_remaining->total_images;
                $plus_bio_remaining_words = $check_plus_remaining->total_words;

                //this is the key that make $plus_bio_remaining_words = 0
                $case = 'fix_main_plan_from_fresh_upgrade_MainCoIn';

            } else {

                $plus_bio_remaining_words = 0;
                $plus_bio_remaining_images = 0;

            }
            Log::debug('After checked $bio_token_synced $plus_bio_remaining_words in Bio value is ' . $plus_bio_remaining_words);


            //because this happending in Main so
            //comapre old data from Main with the Central remaining
            if ($user_old_data->remaining_words < $central_remaining->remaining_words + $plus_remaining_words) {
                Log::debug('The Old remaining Main < than Central maybe because subscription RESET Token reason');
                //$plus_remaining_images=0;
                //$plus_remaining_images=0;
                //because It's already reset inside MainCoIn

            } else {

                if ($user_old_data->remaining_words == $central_remaining->remaining_words + $plus_remaining_words)
                    Log::debug('The Old remaining Main = Central maybe because subscription still keep Token reason');
                else
                    Log::debug('The Old remaining Main > Central maybe because  subscription already added Token reason and Maybe Error and this Sync happend outside upgrading in Main or someting Error!!!!!!!!!! ');
                Log::debug('Inside Fix Bio!!!!!!!!');
            }

            if ($case == 'fix_bio_plan') {
                Log::debug('Case fix_bio_plan');
                $plus_remaining_images = 0;
                $plus_remaining_words = 0;
                $plus_bio_remaining_images = 0;
                $plus_bio_remaining_words = 0;
            }


            //case reset to freetrial no need to add token
            if ($userdata['plan'] == 0) {
                $plus_remaining_images = 0;
                $plus_remaining_words = 0;

            }


            //to centralize Token
            if ($user_old_data->remaining_words < $central_remaining->remaining_words + $plus_remaining_words) {
                $old_reamaining_word = $user_old_data->remaining_words;
                Log::debug('Old remaining words from MainCoIn ' . $old_reamaining_word);
                $old_reamaining_image = $user_old_data->remaining_images;
                Log::debug('Old remaining images from MainCoIn ' . $old_reamaining_image);
                $plus_remaining_images = 0;
                $plus_remaining_words = 0;
                //because the value from MainCoIn has already added token by package
                //then add only for bio_plus

            } else {
                $old_reamaining_word = $central_remaining->remaining_words;
                $old_reamaining_image = $central_remaining->remaining_images;


            }

            //update new data after plus new Plan
            $userdata['remaining_words'] += $plus_remaining_words + $plus_bio_remaining_words;
            $userdata['remaining_images'] += $plus_remaining_images + $plus_bio_remaining_images;
            $userdata['available_words'] += $plus_remaining_words + $plus_bio_remaining_words;
            $userdata['available_images'] += $plus_remaining_images + $plus_bio_remaining_images;
            $userdata['total_words'] += $plus_remaining_words + $plus_bio_remaining_words;
            $userdata['total_images'] += $plus_remaining_images + $plus_bio_remaining_images;


            if (isset($userdata['remaining_words_plus'])) {
                Log::debug('This process happend while upgrading plan in Bio');
                if ($bio_token_synced == 0 && $main_token_synced)
                    Log::debug('Yessss It is confirmed this Token process while upgrading plan in Bio');

                if ($central_remaining->remaining_words < $user_old_data->remaining_words)
                    Log::debug('Yessss!!!!!! It is Triple confirmed this Token process while upgrading plan in Bio');

                $this->plus_new_images_token = $plus_remaining_images;
                $this->plus_new_words_token = $plus_remaining_words;

            } else {
                $this->plus_new_images_token = $plus_remaining_images;
                $this->plus_new_words_token = $plus_remaining_words;
            }

            $token_array = array(
                'remaining_words' => $userdata['remaining_words'],
                'remaining_images' => $userdata['remaining_images'],
            );
            $token_plus_array = array(
                'plus_remaining_images' => $plus_remaining_images,
                'plus_remaining_words' => $plus_remaining_words,
                'plus_bio_remaining_images' => $plus_bio_remaining_images,
                'plus_bio_remaining_words' => $plus_bio_remaining_words,
            );

            Log::debug('Before send Token plus Array to Centralize ' . json_encode($token_array));

            //To Bio ,Main, Socialpost, Design,Mobile2 Sync
            $userdata_remaining_words = array(
                'remaining_words' => $userdata['remaining_words'],
                'remaining_images' => $userdata['remaining_images'],
            );

            Log::debug('Before send  Token reamaining Array to Centalize' . json_encode($token_plus_array));


        }

        //Separate all of these for each platform

        //plan main marketing && mobile old
        if (isset($userdata['package_id'])) {


            //To Main marketing co.in, Mobile old,
            //Main expired_date => $userdata['expiration_date'],

            $userdata_main_plan_array = array(
                'package_id' => $userdata['package_id'],

                'total_words' => $userdata['total_words'],
                'total_images' => $userdata['total_images'],


                'plan_expire_date' => $userdata['plan_expire_date'],
                'expired_date' => $userdata['expired_date'],

                'available_words' => $userdata['available_words'],
                'available_images' => $userdata['available_images'],

            );


            $this->update_column_all($userdata_main_plan_array, $user_id, $user_email, 'main_db', 'users');


            //Socialpost Expired date
            $expire_date_arr = array(
                'expiration_date' => strtotime($userdata['expiration_date']),

            );
            $this->update_column_all($expire_date_arr, $user_id, $user_email, 'main_db', 'sp_users');


            //MobileApp Expired date
            $expire_dateMobile_arr = array(
                'subscription_end_date' => $userdata['expired_date'],

            );
            $this->update_column_all($expire_dateMobile_arr, $user_id, $user_email, 'mobileapp_db', 'users');


            //Sync Expired date planexpire
            $expire_dateSync_arr = array(
                'planexpire' => $userdata['expired_date'],

            );
            $this->update_column_all($expire_dateSync_arr, $user_id, $user_email, 'sync_db', 'user');


        }

        //plan mobile_old
        //Done
        if (isset($userdata['available_images'])) {

        }

        //plan mobile_old
        //Done
        if (isset($userdata['total_words'])) {

        }

        //plan mobile_old
        //Done
        if (isset($userdata['total_images'])) {

        }

        //plan universal
        //Done
        if (isset($userdata['expiration_date'])) {

        }

        //plan mobile_old
        //Done
        if (isset($userdata['plan_expire_date'])) {

        }

        //plan main
        //Done
        if (isset($userdata['expired_date'])) {

        }


        //plan mobile_old
        //Done
        if (isset($userdata['available_words'])) {

        }

        //plan mobile_new
        $usersync_old_data = UserMobile::where('id', $user_id)->orderBy('id', 'asc')->first();

        if (isset($usersync_old_data->words_left)) {
            $userdata['words_left'] = $usersync_old_data->words_left;
            $userdata['image_left'] = $usersync_old_data->image_left;
        } else {
            $userdata['words_left'] = 0;
            $userdata['image_left'] = 0;
            /*  $usersync_old_data->words_left=0;
            $usersync_old_data->image_left=0;
            $usersync_old_data->save(); */


        }

        $userdata['words_left'] += $this->plus_new_words_token;
        $userdata['image_left'] += $this->plus_new_images_token;

        if (isset($userdata['words_left'])) {
            //To Bio ,Main, Socialpost, Design,Mobile2 Sync
            $userdata_mobile_plan_array = array(
                'words_left' => $userdata['words_left'],
                'image_left' => $userdata['image_left'],

            );
            $this->update_column_all($userdata_mobile_plan_array, $user_id, $user_email, 'mobileapp_db', 'users');

        }


        //plan mobile_new
        if (isset($userdata['image_left'])) {

        }

        //plan bio
        //because call from bio Do nothing
        if (isset($userdata['plan_id'])) {

        }


        //plan bio
        //because call from bio Do nothing
        if (isset($userdata['plan_settings'])) {

        }


        //plan bio
        //because call from bio Do nothing
        if (isset($userdata['plan_expiration_date']))
            $expired = $userdata['plan_expiration_date'];
        else if (isset($userdata['expiration_date']))
            $expired = $userdata['expiration_date'];
        else
            $expired = NULL;


        if (isset($userdata['remaining_words'])) {
            //defind exactly Token that will be update then update centralize

            //if all in $token_array sum != 0 then token_centralize
            if (!isset($case))
                $case = NULL;

            $main_user_check = UserMain::where('id', $user_id)->first();
            $remaining_words_check = $main_user_check->remaining_words;
            Log::debug('Before send Token Array to Centalize Remaining_word check ' . $remaining_words_check);

            $this->plans_token_centralize($user_id, $user_email, $token_array, $usage = 0, $from = 'main_coin', $old_reamaining_word, $old_reamaining_image, $chatGPT_catgory = "PlanUpgrade_from_MainCoIn", $token_update_type = 'both', $expired, $design_plan_id, $token_plus_array, $from_payment, $case);

        }


    }


    public function up_profile_crm($request, $user_id, $user_email)
    {


    }

    public function update_password_all($userdata, $user_id, $user_email)
    {
        Log::debug(" Start update password to all Platforms in update all Fnc with this User data ");
        Log::info($userdata);

        //Mobile
        $usermoible = UserMobile::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //Main CoIn  Main.marketing  Mobile old
        $usermaincoin = UserMain::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //SocialPost
        $usersocial = UserSP::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //Design
        $userdesign = UserDesign::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //Bio
        $userbio = UserBio::where('email', '=', $user_email)->where('user_id', '=', $user_id)
            ->update($userdata);

        //BioBlog
        $userbioblog = UserBioBlog::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //Sync
        $usersync = UserSyncNodeJS::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //CRM  Lead need not to be updated

        /*  $usercrm = UserCRM::where('email', '=', $user_email)->where('id', '=', $user_id)
         ->update($userdata); */

        //SEO
        $userseo = UserSEO::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);


        //Course Laravel
        $usercourse = UserCourse::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //Live Shopping that upgrade from Old PunBot

        $userliveshop = UserLiveShop::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);


    }


    public function update_email_all($userdata, $user_id, $user_email)
    {
        Log::debug(" Start update Email to all Platforms in update all Fnc ");

        //Mobile
        $usermoible = UserMobile::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //Main CoIn  Main.marketing  Mobile old
        $usermaincoin = UserMain::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //SocialPost
        $usersocial = UserSP::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //Design
        $userdesign = UserDesign::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //Bio
        $userbio = UserBio::where('email', '=', $user_email)->where('user_id', '=', $user_id)
            ->update($userdata);

        //BioBlog
        $userbioblog = UserBioBlog::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //Sync
        $usersync = UserSyncNodeJS::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //CRM  Lead need not to be updated

        /*  $usercrm = UserCRM::where('email', '=', $user_email)->where('id', '=', $user_id)
         ->update($userdata); */

        //SEO
        $userseo = UserSEO::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);


        //Course Laravel
        $usercourse = UserCourse::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

        //Live Shopping that upgrade from Old PunBot

        $userliveshop = UserLiveShop::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);


    }

    public function update_column_all($userdata, $user_id, $user_email, $db = NULL, $table = NULL)
    {

        if ($db != NULL && $table != NULL) {
            //Database &&  Table
            /* $usermoible = UserMobile::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);  */
            Log::debug(" Start update Universal Column case Not Null db table that exist to all Platforms in update_column_all " . $db . " in Table " . $table);


            if ($db == 'bio_db') {

                if ($user_email != NULL && Str::length($user_email) > 2)
                    $user_data_update = DB::connection($db)->table($table)->where('email', $user_email)->where('user_id', $user_id)->orderBy('user_id', 'asc')->update($userdata);
                else
                    $user_data_update = DB::connection($db)->table($table)->where('user_id', $user_id)->orderBy('user_id', 'asc')->update($userdata);


            } else {

                if ($user_email != NULL && Str::length($user_email) > 2)
                    $user_data_update = DB::connection($db)->table($table)->where('email', $user_email)->where('id', $user_id)->orderBy('id', 'asc')->update($userdata);
                else
                    $user_data_update = DB::connection($db)->table($table)->where('id', $user_id)->orderBy('id', 'asc')->update($userdata);

                return $user_data_update;

            }


        } else {


            Log::debug(" Start update Universal Column case NULL db table that exist to all Platforms in update_column_all ");

            Log::debug(' With these info Data : ');
            Log::info($userdata);
            //Mobile
            $usermoible = UserMobile::where('email', '=', $user_email)->where('id', '=', $user_id)
                ->update($userdata);

            //Main CoIn  Main.marketing  Mobile old

            if (strpos($userdata['name'], " ") !== false) {
                $firstname = $this->get_first_last_name($userdata['name'], 'firstname');
                $lastname = $this->get_first_last_name($userdata['name'], 'lastname');

            } else {
                $firstname = $userdata['name'];
                $lastname = '';
            }

            $userdata['name'] = $firstname;
            $userdata['surname'] = $lastname;
            $usermaincoin = UserMain::where('email', '=', $user_email)->where('id', '=', $user_id)
                ->update($userdata);

            $userdata['name'] = $userdata['name'] . " " . $userdata['surname'];
            if (isset($userdata['surname'])) {
                unset($userdata['surname']);

            }


            //Design
            $userdesign = UserDesign::where('email', '=', $user_email)->where('id', '=', $user_id)
                ->update($userdata);

            //Bio
            $userbio = UserBio::where('email', '=', $user_email)->where('user_id', '=', $user_id)
                ->update($userdata);

            //BioBlog
            $userbioblog = UserBioBlog::where('email', '=', $user_email)->where('id', '=', $user_id)
                ->update($userdata);

            //Sync
            $usersync = UserSyncNodeJS::where('email', '=', $user_email)->where('id', '=', $user_id)
                ->update($userdata);

            //CRM  Lead need not to be updated

            $usercrm = DB::connection('crm_db')->table('tblleads')->where('email', '=', $user_email)->where('id', '=', $user_id)->update($userdata);

            //SEO (first_name,last_name)

            if (isset($userdata['name'])) {

                if (strpos($userdata['name'], " ") !== false) {
                    $firstname = $this->get_first_last_name($userdata['name'], 'firstname');
                    $lastname = $this->get_first_last_name($userdata['name'], 'lastname');

                } else {
                    $firstname = $userdata['name'];
                    $lastname = '';
                }

                $userdata['first_name'] = $firstname;
                $userdata['last_name'] = $lastname;


            }

            $userseo = UserSEO::where('email', '=', $user_email)->where('id', '=', $user_id)
                ->update($userdata);

            //Unset first_name,last_name  after update

            if (isset($userdata['first_name'])) {
                unset($userdata['first_name']);
                unset($userdata['last_name']);
            }


            //Course Laravel
            $usercourse = UserCourse::where('email', '=', $user_email)->where('id', '=', $user_id)
                ->update($userdata);


            //Live Shopping that upgrade from Old PunBot (firstname,lastname)
            if (isset($userdata['name'])) {

                if (strpos($userdata['name'], " ") !== false) {
                    $firstname = $this->get_first_last_name($userdata['name'], 'firstname');
                    $lastname = $this->get_first_last_name($userdata['name'], 'lastname');

                } else {
                    $firstname = $userdata['name'];
                    $lastname = '';
                }

                $userdata['firstname'] = $firstname;
                $userdata['lastname'] = $lastname;


            }


            $userliveshop = UserLiveShop::where('email', '=', $user_email)->where('id', '=', $user_id)
                ->update($userdata);

            //Unset firstname,lastname after update
            if (isset($userdata['firstname'])) {
                unset($userdata['firstname']);
                unset($userdata['lastname']);
            }

            //SocialPost

            if (isset($userdata['name'])) {
                $userdata['fullname'] = $userdata['name'];
                unset($userdata['name']);
            }

            $usersocial = UserSP::where('email', '=', $user_email)->where('id', '=', $user_id)
                ->update($userdata);

            if (isset($userdata['fullname'])) {
                $userdata['name'] = $userdata['fullname'];
                unset($userdata['fullname']);
            }


        }


    }

    public function check_old_user($db, $table, $email)
    {

        $user_old = DB::connection($db)->table($table)->where('email', $email)->orderBy('id', 'asc')->get();

        $found_user = $user_old->count();
        return $found_user;

    }

    public function check_old_user_id($db, $table, $user_id)
    {
        if ($db == 'bio_db')
            $column = 'user_id';
        else
            $column = 'id';


        $user_old = DB::connection($db)->table($table)->where($column, $user_id)->orderBy($column, 'asc')->get();

        $found_user = $user_old->count();
        return $found_user;

    }


    public function get_first_last_name($name, $firstOrlast)
    {
        // Split the full name into first and last name parts
        $name_parts = explode(" ", $name);

        // Assign the first name
        $first_name = $name_parts[0];

        // Assign the last name
        $last_name = $name_parts[1];

        if ($firstOrlast == 'firstname')
            return $first_name;
        else
            return $last_name;
    }

    //Done
    public function update_phone_centralize($user_id, $user_email, $phone)
    {
        Log::debug('Now working in update_phone_centralize ');
        //MainCoIn   phone
        //MainMarketing mobile, phone
        $userdata_phone = array(
            'phone' => $phone,
            'mobile' => $phone,
        );
        $db = "main_db";
        $table = "users";

        $this->update_column_all($userdata_phone, $user_id, $user_email, $db, $table);

        // SocialPost   phone
        unset($userdata_phone['mobile']);
        $db = "main_db";
        $table = "sp_users";
        $this->update_column_all($userdata_phone, $user_id, $user_email, $db, $table);


        // blog Bio.bio   phone
        $db = "bio_blog_db";
        $table = "users";
        $this->update_column_all($userdata_phone, $user_id, $user_email, $db, $table);

        // SEO  phone
        $db = "seo_db";
        $table = "users";
        $this->update_column_all($userdata_phone, $user_id, $user_email, $db, $table);


        // Sync  phone
        $db = "sync_db";
        $table = "user";
        $this->update_column_all($userdata_phone, $user_id, $user_email, $db, $table);

        // Bio  phone
        $db = "bio_db";
        $table = "users";
        $this->update_column_all($userdata_phone, $user_id, $user_email, $db, $table);

        // Design phone_no ,   phone
        $userdata_phone['phone_no'] = $phone;

        $db = "digitalasset_db";
        $table = "users";
        $this->update_column_all($userdata_phone, $user_id, $user_email, $db, $table);
        unset($userdata_phone['phone_no']);

        // Mobile  	mobile_no, phone
        $userdata_phone['mobile_no'] = $phone;
        $db = "mobileapp_db";
        $table = "users";
        $this->update_column_all($userdata_phone, $user_id, $user_email, $db, $table);
        unset($userdata_phone['mobile_no']);


        // CRM phonenumber ,phone
        $userdata_phone['phonenumber'] = $phone;
        $db = "crm_db";
        $table = "tblleads";
        $this->update_column_all($userdata_phone, $user_id, $user_email, $db, $table);
        unset($userdata_phone['phonenumber']);


    }

    //Done
    public function update_token_centralize($user_id, $user_email, $token_array, $usage = NULL, $from = NULL, $old_reamaining_word = NULL, $old_reamaining_image = NULL, $chatGPT_catgory = NULL, $token_update_type = NULL, $token_plus_array = NULL, $case = NULL)
    {


        //MainCoIn default remaining_words,remaining_images
        //MainMarketing use same MainCoIn remaining_words,remaining_images

        /* $token_array= array(
            'remaining_words' => $userdata['remaining_words'],
            'remaining_images' => $userdata['remaining_images'],
        ); */

        /*  $userdata_example=array(
             'phone' => $phone,
             'mobile' => $phone,
         ); */


        $main_user = UserMain::where('id', $user_id)->first();
        $main_goldens_token = $main_user->golden_tokens;

        if (($main_goldens_token > 0) && ($main_goldens_token > $token_array['remaining_images'] + $token_array['remaining_words'])) {
            $main_user->golden_tokens = $token_array['remaining_images'] + $token_array['remaining_words'];
            $main_user->golden_tokens_mode = 1;
            $main_user->save();
            //$token_array['golden_tokens']=$token_array['remaining_images'] + $token_array['remaining_words'];
        }

        //because MainCoIn has already deducted token from MainCoIn by itself
        if ($chatGPT_catgory == 'DocText_SmartContentCoIn') {
            $token_array['remaining_words'] = $token_array['remaining_words'] + $usage;
        }

        //because MainCoIn has already deducted token from MainCoIn by itself
        if ($chatGPT_catgory == 'Images_SmartContentCoIn') {
            $token_array['remaining_images'] = $token_array['remaining_images'] + $usage;
        }

        //because MainCoIn has already deducted token from MainCoIn by itself
        if ($chatGPT_catgory == 'CodeGenerator_SmartContentCoIn') {
            $token_array['remaining_words'] = $token_array['remaining_words'] + $usage;
        }
        //because MainCoIn has already deducted token from MainCoIn by itself
        if ($chatGPT_catgory == 'Whisper_SmartContentCoIn') {
            $token_array['remaining_words'] = $token_array['remaining_words'] + $usage;
        }

        //because MainCoIn has already deducted token from MainCoIn by itself
        if ($chatGPT_catgory == 'VoiceOver_SmartContentCoIn') {
            $token_array['remaining_words'] = $token_array['remaining_words'] + $usage;
        }

        //for Chat from SmartBio use
        /* if($chatGPT_catgory=='Chat_SmartBio')
        {
            if($usage==NULL || !isset($usage))
            {
                $usage_from_main=UserOpenaiChatMessage::where('user_id',$user_id)->where('chat_id',$chatGPT_catgory)->orderBy('id', 'desc')->get();


            }

        } */


        //because MainCoIn has already deducted token from MainCoIn by itself
        if ($chatGPT_catgory == 'Chat_' && $from == 'main_coin') {
            $token_array['remaining_words'] = $token_array['remaining_words'] + $usage;
            //make sure case of 'Chat_' usage =0
            $usage = 0;
        }


        $db = "main_db";
        $table = "users";

        $this->update_column_all($token_array, $user_id, $user_email, $db, $table);

        // SocialPost   openai_usage_tokens in array permissions column
        $db = "main_db";
        $table = "sp_users";
        $this->update_column_all($token_array, $user_id, $user_email, $db, $table);

        $team_data = DB::connection('main_db')->table('sp_team')->where('owner', $user_id)->first();
        $team_id = $team_data->ids;
        $total_token = $token_array['remaining_words'] + $token_array['remaining_images'];
        //$this->update_team_permissions($key, $value, $team_id);
        $this->update_team_permissions('openai_limit_tokens', $total_token, $team_id, $user_id);

        //recheck gin if it subtrack by openai_usage_tokens then .. openai_limit_tokens = openai_usage_tokens + openai_limit_tokens
        //set this $value to 0 if want to reset token usage
        //$this->update_team_permissions('openai_usage_tokens', $total_token,$team_id,$user_id);

        // Sync tts_words_limit,  dalle_limit, gpt_words_limit, $token_array

        $token_array['dalle_limit'] = $token_array['remaining_images'];
        $token_array['gpt_words_limit'] = $token_array['remaining_words'];
        $db = "sync_db";
        $table = "user";
        $this->update_column_all($token_array, $user_id, $user_email, $db, $table);

        unset($token_array['dalle_limit']);
        unset($token_array['gpt_words_limit']);


        //BOF Bio Area Bio plan_settings, aix_images_current_month, aix_words_current_month
        //words_per_month_limit,images_per_month_limit,synthesized_characters_per_month_limit,transcriptions_per_month_limit

        $db = "bio_db";
        $table = "users";
        $chatGPT_catgory_low = strtolower($chatGPT_catgory);

        if (Str::contains($chatGPT_catgory_low, 'speech') || Str::contains($chatGPT_catgory_low, 'voice') || Str::contains($chatGPT_catgory_low, 'code') || Str::contains($chatGPT_catgory_low, 'image') || Str::contains($chatGPT_catgory_low, 'text') || Str::contains($chatGPT_catgory_low, 'chat')) {

            $Biouser = UserBio::where('user_id', $user_id)->first();

            if ($this->isJson(trim($Biouser->plan_settings, '"'))) {
                $bio_plan_settings = json_decode(trim($Biouser->plan_settings, '"'), true);
                //$plan_settings = $Biouser->plan_settings;
            } else {
                //Handle the situation when plan_settings is not a valid JSON
                //For example you can set $old_transcriptions_per_month_limit to default
                $bio_plan_settings = json_encode($Biouser->plan_settings);


            }


            if (Str::contains($chatGPT_catgory_low, 'image')) {
                $Biouser->aix_images_current_month += $usage;
                $Biouser->save();

            } else {

                $Biouser->aix_words_current_month += $usage;
                //aix_words_current_month=synthesized_+transcriptions+code+chat+other doc text

                if (Str::contains($chatGPT_catgory_low, 'voice')) {

                    // Check if 'synthesized_characters_per_month_limit' exists and get value
                    if (isset($bio_plan_settings['synthesized_characters_per_month_limit'])) {
                        $synthesized_limit = $bio_plan_settings['synthesized_characters_per_month_limit'];
                    } else {
                        // Fallback if 'synthesized_characters_per_month_limit' does not exist
                        $synthesized_limit = 0;
                    }


                    if ($Biouser->synthesized_characters_per_month_limit == NULL) {
                        $Biouser->synthesized_characters_per_month_limit = $synthesized_limit;
                        $Biouser->synthesized_characters_per_month_limit -= $usage;

                    } else {
                        $Biouser->synthesized_characters_per_month_limit -= $usage;
                    }

                }

                if (Str::contains($chatGPT_catgory_low, 'speech')) {

                    if ($Biouser->transcriptions_characters_per_month_limit == NULL) {
                        $Biouser->transcriptions_characters_per_month_limit = 0;
                        $Biouser->transcriptions_characters_per_month_limit -= $usage;
                    } else {
                        $Biouser->transcriptions_characters_per_month_limit -= $usage;

                    }


                }


                $Biouser->save();

            }


            $Biouser->remaining_images = $token_array['remaining_images'];
            $Biouser->remaining_words = $token_array['remaining_words'];
            $Biouser->save();


        }


        //Change to Model style update as above
        //$this->update_column_all($token_array, $user_id, $user_email, $db, $table);


        //aix_words_current_month
        //aix_images_current_month
        $this->update_bio_users_plan_settings('words_per_month_limit', $token_array['remaining_words'], $user_id);
        $this->update_bio_users_plan_settings('images_per_month_limit', $token_array['remaining_images'], $user_id);
        //EOF Bio Area


        // Design default remaining_words
        $db = "digitalasset_db";
        $table = "users";
        $this->update_column_all($token_array, $user_id, $user_email, $db, $table);


        // Mobile  	words_left, image_left
        $token_array['words_left'] = $token_array['remaining_words'];
        $token_array['image_left'] = $token_array['remaining_images'];
        $db = "mobileapp_db";
        $table = "users";
        $this->update_column_all($token_array, $user_id, $user_email, $db, $table);
        unset($token_array['words_left']);
        unset($token_array['image_left']);


        //Social network of SmartContentAI in Main.marketing default remaining_words
        $db = "social_db";
        $table = "users";
        $social_token = $this->update_column_all($token_array, $user_id, $user_email, $db, $table);

        // CRM default remaining_words

        $db = "crm_db";
        $table = "tblleads";

        Log::debug('Check params before update : ');
        Log::info($token_array);
        Log::info($user_id);
        Log::info($user_email);
        Log::info($db);
        Log::info($table);

        $sync_token = $this->update_column_all($token_array, $user_id, $user_email, $db, $table);

        Log::debug('After Sync CRM Token Updated? : ' . $sync_token);

        $token_before_text = $token_array['remaining_words'];
        $token_before_image = $token_array['remaining_images'];


        if ($token_update_type == 'text') {
            $token_before_text = $token_array['remaining_words'] + $usage;
        } else if ($token_update_type == 'image') {
            $token_before_image = $token_array['remaining_images'] + $usage;
        } else {


            if ($token_update_type == 'both') {
                $token_before_text = $old_reamaining_word;
                $token_before_image = $old_reamaining_image;

                if ($chatGPT_catgory == NULL)
                    $chatGPT_catgory = "AdminManualUpdate";


            }


        }

        $token_before = $token_before_text + $token_before_image;
        $token_after = $token_array['remaining_words'] + $token_array['remaining_images'];
        $openai_record = NULL;


        if ($sync_token >= 0) {


            if ($token_plus_array != NULL) {
                $amount_main = $token_plus_array['plus_remaining_images'] + $token_plus_array['plus_remaining_words'];

                $token_after_1 = $token_before + $amount_main;


                //if($chatGPT_catgory==)


                $log_data_plus = array(

                    'user_openai_id' => NULL,
                    'user_openai_chat_id' => NULL,
                    'amount' => $token_plus_array['plus_remaining_images'] + $token_plus_array['plus_remaining_words'],
                    'platform' => $from,
                    'token_before' => $token_before,
                    'token_after' => $token_after_1,
                    'user_id' => $user_id,
                    'type' => 'PlanUpgrade_Main',
                    'token_text_before' => $token_before_text,
                    'token_text_after' => $token_array['remaining_words'] - $token_plus_array['plus_bio_remaining_words'],
                    'token_image_before' => $token_before_image,
                    'token_image_after' => $token_array['remaining_images'] - $token_plus_array['plus_bio_remaining_images'],


                );


                $log_token_plus = TokenLogs::create($log_data_plus);
                $log_token_plus_id = $log_token_plus->id;


                $amount_bio = $token_plus_array['plus_bio_remaining_images'] + $token_plus_array['plus_bio_remaining_words'];
                $log_data_plus2 = array(

                    'user_openai_id' => NULL,
                    'user_openai_chat_id' => NULL,
                    'amount' => $token_plus_array['plus_bio_remaining_images'] + $token_plus_array['plus_bio_remaining_words'],
                    'platform' => $from,
                    'token_before' => $token_before + $amount_main,
                    'token_after' => $token_after,
                    'user_id' => $user_id,
                    'type' => 'PlanUpgrade_Bio',
                    'token_text_before' => $token_before_text + $token_plus_array['plus_remaining_words'],
                    'token_text_after' => $token_array['remaining_words'],
                    'token_image_before' => $token_before_image + $token_plus_array['plus_remaining_images'],
                    'token_image_after' => $token_array['remaining_images'],


                );


                $log_token_plus2 = TokenLogs::create($log_data_plus2);
                $log_token_plus_id2 = $log_token_plus2->id;
            } else {


                if ($this->upFromWhere == 'main_coin' && $this->upByWhom == 'admin') {
                    Log::debug('Debug upByWhom in Log Token ' . $this->upByWhom);
                    $chatGPT_catgory = 'AdminManualUpdate';
                    $from = 'main_coin';

                    //find usage from last token log
                    if ($usage == NULL) {
                        $token_log = TokenLogs::where('user_id', $user_id)->orderBy('id', 'desc')->first();

                        if (isset($token_log->token_after))
                            $token_log_before = $token_log->token_after;
                        else
                            $token_log_before = 0;

                        $usage = $token_after - $token_log_before;

                        if ($usage < 0)
                            $usage = abs($usage);
                    }
                }

                if ($chatGPT_catgory == 'Images_SocialPost') {
                    $token_log = TokenLogs::where('user_id', $user_id)->orderBy('id', 'desc')->first();
                    $token_log_before = $token_log->token_after;
                    $token_image_before = $token_log->token_image_after;
                    $token_all_diff = $token_after - $token_log_before;
                    $usage = $token_all_diff;

                    if ($usage < 0)
                        $usage = abs($usage);

                    $token_before_image = $token_before_image + $usage;
                    $token_before = $token_before + $usage;


                }

                if ($chatGPT_catgory == 'Images_Design') {
                    $token_log = TokenLogs::where('user_id', $user_id)->orderBy('id', 'desc')->first();
                    $token_log_before = $token_log->token_after;
                    $token_image_before = $token_log->token_image_after;
                    $token_all_diff = $token_after - $token_log_before;
                    $usage = $token_all_diff;

                    if ($usage < 0)
                        $usage = abs($usage);

                    $token_before_image = $token_before_image + $usage;
                    $token_before = $token_before + $usage;

                }

                /* if($chatGPT_catgory =='DocText_SmartContentCoIn_ArticleGen_Wizard')
                {

                    $tokens_log=TokenLogs::where('user_id',$user_id)->orderBy('id', 'desc')->first();
                    $tokens_log_before=$tokens_log->token_after;

                    $tokens_text_before=$tokens_log->token_text_after;
                    $tokens_all_diff=$token_after-$tokens_log_before;

                    $tokens_before_image =$tokens_log->token_image_before;
                    $tokens_after_image =$tokens_log->token_image_after;

                    //need to del $usage of article final wizard
                    $usage_keyword_title_outline=$tokens_all_diff-$usage;

                    if($usage_keyword_title_outline<0)
                    $usage_keyword_title_outline=abs($usage_keyword_title_outline);


                    //$token_before_text= $token_before_text+$usage_keyword_title_outline;

                    $log_keyword_title_outline_data = array(

                        'user_openai_id' => NULL,
                        'user_openai_chat_id' => NULL,
                        'amount' => $usage_keyword_title_outline,
                        'platform' => $from,
                        'token_before' => $tokens_log_before,
                        'token_after' => $tokens_log_before+$usage_keyword_title_outline,
                        'user_id' => $user_id,
                        'type' => $chatGPT_catgory,
                        'token_text_before' => $token_before_text,
                        'token_text_after' => $tokens_text_before+$usage_keyword_title_outline,
                        'token_image_before' => $tokens_before_image,
                        'token_image_after' =>  $tokens_after_image,


                    );

                    $log_token_keyword_title_outline = TokenLogs::create( $log_keyword_title_outline_data);
                    $log_token_keyword_title_outline_id=$log_token_keyword_title_outline->id;


                    $token_before=$tokens_log_before+$usage_keyword_title_outline;
                    $token_after=$token_before+$usage;
                    $token_before_text=$tokens_text_before+$usage_keyword_title_outline;
                    $token_after_text=$token_before_text+$usage;

                    if($token_array['remaining_words']!=$token_after_text)
                    {
                        Log::debug('Debug !!!!!!!!!!!!!! token_array[remaining_words] in Log Token not equal to it should be '. $token_array['remaining_words']);
                        Log::debug('Debug but token_after_text in Log Token is '. $token_after_text);
                        $token_array['remaining_words']=$token_after_text;
                    }
                    else
                    {
                        Log::debug('Debug token_array[remaining_words] in Log Token is equal to it should be '. $token_array['remaining_words']);
                        Log::debug('Debug and token_after_text in Log Token is '. $token_after_text);
                    }


                } */


                $log_data = array(

                    'user_openai_id' => NULL,
                    'user_openai_chat_id' => NULL,
                    'amount' => $usage,
                    'platform' => $from,
                    'token_before' => $token_before,
                    'token_after' => $token_after,
                    'user_id' => $user_id,
                    'type' => $chatGPT_catgory,
                    'token_text_before' => $token_before_text,
                    'token_text_after' => $token_array['remaining_words'],
                    'token_image_before' => $token_before_image,
                    'token_image_after' => $token_array['remaining_images'],


                );

                $log_token = TokenLogs::create($log_data);
                $log_token_id = $log_token->id;
                //return $log_token->id;


            }

            if (!isset($log_token_id))
                $log_token_id = NULL;

            if (!isset($log_token_plus_id))
                $log_token_plus_id = NULL;

            if (!isset($log_token_plus_id2))
                $log_token_plus_id2 = NULL;

            $return_log_array = array(
                'log_token_id' => $log_token_id,
                'log_token_plus_id' => $log_token_plus_id,
                'log_token_plus_id2' => $log_token_plus_id2,
            );
            return $return_log_array;


        }


    }

    public function plans_token_centralize($user_id, $user_email, $token_array, $usage = NULL, $from = NULL, $old_reamaining_word = NULL, $old_reamaining_image = NULL, $chatGPT_catgory = NULL, $token_update_type = NULL, $expired = NULL, $design_plan_id, $token_plus_array, $from_payment = NULL, $case = NULL)
    {

        //check Token changed after upgrade/downgrade Plan
        //1. when change plan still not yet insert token_logs
        // 1.1 Check How Many tokens must be add ?
        // 1.2 that token in 1.1 from where?  and which Plan ID
        // 2. then call $this->update_token_centralize($user_id, $user_email, $token_array, $usage = NULL, $from = NULL, $old_reamaining_word = NULL, $old_reamaining_image = NULL, $chatGPT_catgory = NULL, $token_update_type = NULL)


        //add checked if updated token success


        //fixing fixing  add case = Team  package update
        $return_token_update = $this->update_token_centralize($user_id, $user_email, $token_array, $usage, $from, $old_reamaining_word, $old_reamaining_image, $chatGPT_catgory, $token_update_type, $token_plus_array, $case);

        /*  $return_log_array=array(
            'log_token_id'=>$log_token_id,
            'log_token_plus_id'=>$log_token_plus_id,
            'log_token_plus_id2'=>$log_token_plus_id2,
        ); */

        if ($return_token_update['log_token_id'] != NULL || $return_token_update['log_token_plus_id'] != NULL || $return_token_update['log_token_plus_id2'] != NULL) {


            if ($from_payment == 'SubscriptionMain') {

                //$where_payment_bundle_from=SubscriptionMain::where('stripe_status','active')->orWhere('stripe_status', 'trialing')->where('user_id',$user_id)->whereIn('plan_id', [5,7,10,11])->latest()->first();
                $where_payment_bundle_from = SubscriptionMain::where('stripe_status', 'active')->orWhere('stripe_status', 'trialing')->where('user_id', $user_id)->latest()->first();
                if ($where_payment_bundle_from->bio_token_sync == NULL || $where_payment_bundle_from->bio_token_sync < 1) {
                    $where_payment_bundle_from->bio_token_sync = 1;
                    $where_payment_bundle_from->main_token_sync = 1;


                    $Mainuser = UserMain::where('id', $user_id)->first();
                    $Mainuser->token_upgraded = 1;

                    //add reset 1st time Bio Plan here
                    //for example images_per_month_limit
                    //words_per_month_limit
                    //synthesized_characters_per_month_limit
                    //transcriptions_per_month_limit
                    // pull from BioPlan->settings
                    $user_main_plan_id = $Mainuser->plan;

                    $user_bio_plan = PlanBio::where('main_plan_id', $user_main_plan_id)->first();
                    $user_bio_plan_id = $user_bio_plan->plan_id;

                    $start_bio_images_per_month_limit = $this->get_bio_plan_settings('images_per_month_limit', $user_bio_plan_id);
                    Log::debug('Start Bio Images Per Month Limit : ' . $start_bio_images_per_month_limit);
                    $start_bio_words_per_month_limit = $this->get_bio_plan_settings('words_per_month_limit', $user_bio_plan_id);
                    Log::debug('Start Bio Words Per Month Limit : ' . $start_bio_words_per_month_limit);
                    $start_bio_synthesized_characters_per_month_limit = $this->get_bio_plan_settings('synthesized_characters_per_month_limit', $user_bio_plan_id);
                    Log::debug('Start Bio Synthesized Characters Per Month Limit : ' . $start_bio_synthesized_characters_per_month_limit);
                    $start_bio_transcriptions_per_month_limit = $this->get_bio_plan_settings('transcriptions_per_month_limit', $user_bio_plan_id);
                    Log::debug('Start Bio Transcriptions Per Month Limit : ' . $start_bio_transcriptions_per_month_limit);

                    $stat_bio_chats_per_month_limit = $this->get_bio_plan_settings('chats_per_month_limit', $user_bio_plan_id);

                    $Biouser = UserBio::where('user_id', $user_id)->first();

                    if ($stat_bio_chats_per_month_limit != NULL)
                        $Biouser->chats_per_month_limit = $stat_bio_chats_per_month_limit;


                    if ($start_bio_images_per_month_limit != NULL)
                        $Biouser->images_per_month_limit = $start_bio_images_per_month_limit;

                    if ($start_bio_words_per_month_limit != NULL)
                        $Biouser->words_per_month_limit = $start_bio_words_per_month_limit;

                    if ($start_bio_synthesized_characters_per_month_limit != NULL)
                        $Biouser->synthesized_characters_per_month_limit = $start_bio_synthesized_characters_per_month_limit;

                    if ($start_bio_transcriptions_per_month_limit != NULL)
                        $Biouser->transcriptions_per_month_limit = $start_bio_transcriptions_per_month_limit;

                    //double check if Bio Plan is not NULL
                    if ($Biouser->plan_id == NULL || $Biouser->plan_id != $user_bio_plan_id)
                        $Biouser->plan_id = $user_bio_plan_id;

                    if ($Mainuser->bio_plan == NULL || $Mainuser->bio_plan != $user_bio_plan_id)
                        $Mainuser->bio_plan = $user_bio_plan_id;

                    if ($Biouser->plan_id == 0)
                        $Biouser->plan_id = 'free';

                    //next double check expiration date
                    $current_bio_plan_expire = $Biouser->plan_expiration_date;
                    $user_plan_expire_from_main = $Mainuser->expired_date;
                    Log::debug('Current Time of Expired inside Plan Token _Centralize in Main ' . $user_plan_expire_from_main);
                    Log::debug('Time of Expired inside Plan Token _Centralize in Bio ' . $current_bio_plan_expire);
                    $current_bio_plan_expire_date = $current_bio_plan_expire ? Carbon::parse($current_bio_plan_expire)->toDateString() : null;
                    $user_plan_expire_from_main_date = $user_plan_expire_from_main ? Carbon::parse($user_plan_expire_from_main)->toDateString() : null;

                    if ($current_bio_plan_expire_date != $user_plan_expire_from_main_date)
                        $Biouser->plan_expiration_date = $user_plan_expire_from_main;

                    if ($user_plan_expire_from_main_date == NULL) {
                        $Biouser->plan_expiration_date = $where_payment_bundle_from->ends_at;
                        $Mainuser->expired_date = $where_payment_bundle_from->ends_at;
                    }


                    $Biouser->save();
                    //end reset 1st time Bio Plan here
                    $Mainuser->save();
                    //save token_upgraded=1 to UserMain after update token
                    $where_payment_bundle_from->save();
                }

            }


        }


        // จัดโครงสร้าง token_logs ให้ละเอียดและชัดเจนมากขึ้น ว่า token เพิ่มหรือลด จากไหน หรือใช้จากไหน และใช้ไปกับอะไร
        //add Function add user_subscriptions table record of user to Desing user that level reach
        //plan =2 and access_level = 5


        if ($design_plan_id >= 2) {

            // need plan SocialPost ID to check and Plan End date
            $DesignUser = UserDesign::where('id', $user_id)->first();
            $email_Desing_user = $DesignUser->email;
            $DesignUser->access_level = 5;
            $DesignUser->save();


            $DesignUser_ExampleSub = UserDesignSubscriptions::latest('usp_id')->first();

            //clone existing
            $newDesignUser = $DesignUser_ExampleSub->replicate();

            $newDesignUser->usp_id = $DesignUser_ExampleSub->usp_id + 1;
            $newDesignUser->user_id = $user_id;
            $newDesignUser->plan_period_start = Carbon::now();


            $newDesignUser->plan_period_end = Carbon::now()->addDays(31);


            $newDesignUser->payer_email = $email_Desing_user;

            $newDesignUser->created = Carbon::now();
            $newDesignUser->created_at = Carbon::now();
            $newDesignUser->save();

        }


    }

    //Done wait for upgrade
    public function update_language_centralize($user_id, $user_email, $lang)
    {
        //MainCoIn   language
        //MainMarketing  lang
        $userdata_lang = array(
            'lang' => $lang,
            'language' => $lang,
        );
        $db = "main_db";
        $table = "users";

        $this->update_column_all($userdata_lang, $user_id, $user_email, $db, $table);

        // SocialPost   language
        unset($userdata_lang['lang']);
        $db = "main_db";
        $table = "sp_users";
        $this->update_column_all($userdata_lang, $user_id, $user_email, $db, $table);


        // blog Bio.bio   language
        $db = "bio_blog_db";
        $table = "users";
        $this->update_column_all($userdata_lang, $user_id, $user_email, $db, $table);

        // SEO  language
        $db = "seo_db";
        $table = "users";
        $this->update_column_all($userdata_lang, $user_id, $user_email, $db, $table);


        // Sync  language
        $db = "sync_db";
        $table = "user";
        $this->update_column_all($userdata_lang, $user_id, $user_email, $db, $table);


        // Bio  language,lang
        $userdata_lang['lang'] = $lang;
        $userdata_lang['language'] = $this->getDisplayLanguageForLocaleCode($lang);
        $db = "bio_db";
        $table = "users";
        $this->update_column_all($userdata_lang, $user_id, $user_email, $db, $table);
        unset($userdata_lang['lang']);
        $userdata_lang['language'] = $lang;


        // Design language
        $db = "digitalasset_db";
        $table = "users";
        $this->update_column_all($userdata_lang, $user_id, $user_email, $db, $table);


        // Mobile  language
        $db = "mobileapp_db";
        $table = "users";
        $this->update_column_all($userdata_lang, $user_id, $user_email, $db, $table);


        // CRM default_language,language
        $userdata_lang['default_language'] = $lang;
        $db = "crm_db";
        $table = "tblleads";
        $this->update_column_all($userdata_lang, $user_id, $user_email, $db, $table);
        unset($userdata_lang['default_language']);


    }

    //Update SocialPost Team Setting  Plan Sync
    // *************** Important Plan Upgrade / Downgrade ***************
    public function update_team_permissions($key, $value, $team_id = 0, $owner = 0)
    {

        $sp_team_set = SPTeam::where('owner', $owner)->where('ids', $team_id)->first();
        // $data = ['products' => ['desk' => ['price' => 100]]];
        $permissions = $sp_team_set->permissions;

        Log::debug('Value of new Token in SocialPost Team to update ' . $value);

        //data_set($permissions, 'permissions.'.$key, $value);
        //$permissions_array = Arr::wrap($permissions);
        $permissions_json = json_decode($permissions, true);
        $permissions_json[$key] = $value;

        $permissions = json_encode($permissions_json);
        $sp_team_set->permissions = $permissions;
        $sp_team_update = $sp_team_set->save();

        if ($sp_team_update > 0)
            Log::debug('Sucess update Plan SP_Team permisssion  ' . $key);

    }


    //Get Team Settings

    function get_team_data($key, $value = "", $team_id = 0)
    {


    }

    //GET Bio plan_settings from Plan_ID
    public function get_bio_plan_settings($key, $plan_id)
    {
        $BioPlan = PlanBio::where('plan_id', $plan_id)->first();

        if ($BioPlan) {
            $BioPlan_settings = json_decode($BioPlan->settings, true);
            if (array_key_exists($key, $BioPlan_settings)) {
                return $BioPlan_settings[$key];
            }
        }

        return null;
    }

    public function get_bio_users_plan_settings($key, $value = "", $user_id = 0)
    {


    }


    //Update Bio plan_settings Plan Sync
    // ************************* Important for Pland Upgrade / Downgrade ***************
    public function update_bio_users_plan_settings_team($key, $value, $user_id = 0)
    {


    }


    //This function not for add Tokens for Sync it should add to $value
    //มันต้องอัพเดทผ่านค่า $value ซึ่งต้องไปตั้งเงื่อนไขก่อนหน้านี้ ไม่ใช่ใน function นี้
    public function update_bio_users_plan_settings($key, $value, $user_id = 0)
    {
        //Checking Bio Plan Correct
        //fixing or  stripe_status = trialing
        $main_coin_plan = SubscriptionMain::where('stripe_status', 'active')
            ->orWhere('stripe_status', 'trialing')
            ->where('user_id', $user_id)
            ->first();

        //$main_coin_plan->plan_id;

        //fixing bug In case if user was invited to join Platform
        //1. if user not has any active subscription
        //2. proved that user was invited to join in Team
        //3. then copy Free Plan setting or current setting from Bio users table
        //and then update remaining_words and remaining_images
        // all of above do the sae thing to SocialPost and both Bio and SocialPost should
        // keep Plan ID or Package to "Team"


        if ($main_coin_plan !== null && $main_coin_plan->plan_id !== null && $main_coin_plan->plan_id > 0) {
            $PlansFromMain = Plan::where('id', $main_coin_plan->plan_id)->first();

            if ($PlansFromMain->id > 0)
                $Bio_plan_should_be = $PlansFromMain->bio_id;

            Log::debug('update_bio_users_plan_settings Bio Plan SHoud be ' . $Bio_plan_should_be);
        } else {


            //maybe this user is using Team package
            $team_check = Team_Members_Main::where('user_id', $user_id)->first();

            if ($team_check !== null && $team_check->id !== null && $team_check->id > 0) {

                Log::debug('update_bio_users_plan_settings Bio Plan SHoud be ' . 'free from Team plan invited');

                // $Bio_plan_should_be='free';

                $Bio_plan_should_be = 'team';
                //fixing  Bio_plan_should_be = Team
                // fixing then when user are using Team Package then
                // find the real package from The owner of Team Package
                // Or from the log setting when this user was inviting
                // to join Team
                //fixing fixing


            }
        }


        $user_bio = UserBio::where('user_id', $user_id)->first();
        // $data = ['products' => ['desk' => ['price' => 100]]];
        $plan_settings = $user_bio->plan_settings;


        if ($user_bio->plan_id != $Bio_plan_should_be) {


            //bug fixing add expired date update
            $trial_ends_at = $main_coin_plan->trial_ends_at; //assuming these are Carbon instances
            $ends_at = $main_coin_plan->ends_at;

            if ($trial_ends_at->greaterThan($ends_at)) {
                // echo 'Trial ends after main end date.';
                $user_bio->plan_expiration_date = $main_coin_plan->trial_ends_at;

            } else if ($trial_ends_at->lessThan($ends_at)) {
                //echo 'Trial ends before main end date.';
                $user_bio->plan_expiration_date = $main_coin_plan->ends_at;
            } else {
                $user_bio->plan_expiration_date = $main_coin_plan->ends_at;
            }

            $user_bio->plan_id = $Bio_plan_should_be;
            $user_bio->save;
            Log::debug('Sucess Updated Synced Plan ID of Bio from ' . $user_bio->plan_id . ' to ' . $Bio_plan_should_be);
        }

        Log::debug('Value of new Token in Bio to update ' . $value);

        //data_set($plan_settings, 'plan_settings.'.$key, $value);
        //$plan_settings_array = Arr::wrap($plan_settings);


        Log::debug('Found old data aix_words_current_month ' . $user_bio['aix_words_current_month']);

        Log::debug('Found old data aix_images_current_month ' . $user_bio['aix_images_current_month']);

        //add  old value because SmartBio auto subtrack old usage
        /*  if ($key == 'words_per_month_limit')
            $value += $user_bio['aix_words_current_month'];

        if ($key == 'images_per_month_limit')
            $value += $user_bio['aix_images_current_month']; */


        Log::info($plan_settings);


        if ($this->isJson($plan_settings)) {
            $plan_settings_json = json_decode(trim($plan_settings, '"'), true);
            if (is_array($plan_settings_json)) {
                if ($key == 'words_per_month_limit') {
                    $value += intval($user_bio['aix_words_current_month']);
                    Log::debug('New words value + is' . $value);
                }

                if ($key == 'images_per_month_limit') {
                    $value += intval($user_bio['aix_images_current_month']);

                    Log::debug('New images value + is' . $value);
                }

                //fixing fixing should add Token log from this and every time Plan correction Token


                $plan_settings_json[$key] = $value;
            } else {
                Log::debug('Decoding failed with error: ' . json_last_error_msg());
            }

            if (is_array($plan_settings_json) == false)
                trim($plan_settings_json, '"');

            $plan_settings = json_encode($plan_settings_json);
            //stripslashes($plan_settings);
        } else if (is_array($plan_settings)) {
            trim($plan_settings, '"');
            if ($key == 'words_per_month_limit')
                $value += intval($user_bio['aix_words_current_month']);

            if ($key == 'images_per_month_limit')
                $value += intval($user_bio['aix_images_current_month']);


            //fixing fixing should add Token log from this and every time Plan correction Token
            $plan_settings[$key] = $value;
            trim($plan_settings, '"');
            $plan_settings = json_encode($plan_settings);
            //stripslashes($plan_settings);
        } else {
            Log::debug('Unexpected type. Expected array or JSON string.');
            trim($plan_settings, '"');
            $plan_settings = json_encode($plan_settings);
            //stripslashes($plan_settings);
        }

        $user_bio = UserBio::where('user_id', $user_id)->first();
        if ($user_bio)
            Log::debug("Check update Bio user plain ID LINE 4383 " . $user_bio->plan_id);


        $user_bio->plan_settings = stripslashes($plan_settings);


        //$plan_settings_array->$key= $value;
        $user_bio_update = $user_bio->save();

        if ($user_bio_update > 0) {


            Log::debug('Sucess update Bio user plan_settings ' . $key);
        }

        $user_bio = UserBio::where('user_id', $user_id)->first();
        if ($user_bio)
            Log::debug("Check update Bio user plain ID LINE 4395 after update plan setiings " . $user_bio->plan_id);


    }


    function getLocaleCodeForDisplayLanguage($name)
    {
        $languageCodes = array(
            "aa" => "Afar",
            "ab" => "Abkhazian",
            "ae" => "Avestan",
            "af" => "Afrikaans",
            "ak" => "Akan",
            "am" => "Amharic",
            "an" => "Aragonese",
            "ar" => "Arabic",
            "as" => "Assamese",
            "av" => "Avaric",
            "ay" => "Aymara",
            "az" => "Azerbaijani",
            "ba" => "Bashkir",
            "be" => "Belarusian",
            "bg" => "Bulgarian",
            "bh" => "Bihari",
            "bi" => "Bislama",
            "bm" => "Bambara",
            "bn" => "Bengali",
            "bo" => "Tibetan",
            "br" => "Breton",
            "bs" => "Bosnian",
            "ca" => "Catalan",
            "ce" => "Chechen",
            "ch" => "Chamorro",
            "co" => "Corsican",
            "cr" => "Cree",
            "cs" => "Czech",
            "cu" => "Church Slavic",
            "cv" => "Chuvash",
            "cy" => "Welsh",
            "da" => "Danish",
            "de" => "German",
            "dv" => "Divehi",
            "dz" => "Dzongkha",
            "ee" => "Ewe",
            "el" => "Greek",
            "en" => "English",
            "eo" => "Esperanto",
            "es" => "Spanish",
            "et" => "Estonian",
            "eu" => "Basque",
            "fa" => "Persian",
            "ff" => "Fulah",
            "fi" => "Finnish",
            "fj" => "Fijian",
            "fo" => "Faroese",
            "fr" => "French",
            "fy" => "Western Frisian",
            "ga" => "Irish",
            "gd" => "Scottish Gaelic",
            "gl" => "Galician",
            "gn" => "Guarani",
            "gu" => "Gujarati",
            "gv" => "Manx",
            "ha" => "Hausa",
            "he" => "Hebrew",
            "hi" => "Hindi",
            "ho" => "Hiri Motu",
            "hr" => "Croatian",
            "ht" => "Haitian",
            "hu" => "Hungarian",
            "hy" => "Armenian",
            "hz" => "Herero",
            "ia" => "Interlingua (International Auxiliary Language Association)",
            "id" => "Indonesian",
            "ie" => "Interlingue",
            "ig" => "Igbo",
            "ii" => "Sichuan Yi",
            "ik" => "Inupiaq",
            "io" => "Ido",
            "is" => "Icelandic",
            "it" => "Italian",
            "iu" => "Inuktitut",
            "ja" => "Japanese",
            "jv" => "Javanese",
            "ka" => "Georgian",
            "kg" => "Kongo",
            "ki" => "Kikuyu",
            "kj" => "Kwanyama",
            "kk" => "Kazakh",
            "kl" => "Kalaallisut",
            "km" => "Khmer",
            "kn" => "Kannada",
            "ko" => "Korean",
            "kr" => "Kanuri",
            "ks" => "Kashmiri",
            "ku" => "Kurdish",
            "kv" => "Komi",
            "kw" => "Cornish",
            "ky" => "Kirghiz",
            "la" => "Latin",
            "lb" => "Luxembourgish",
            "lg" => "Ganda",
            "li" => "Limburgish",
            "ln" => "Lingala",
            "lo" => "Lao",
            "lt" => "Lithuanian",
            "lu" => "Luba-Katanga",
            "lv" => "Latvian",
            "mg" => "Malagasy",
            "mh" => "Marshallese",
            "mi" => "Maori",
            "mk" => "Macedonian",
            "ml" => "Malayalam",
            "mn" => "Mongolian",
            "mr" => "Marathi",
            "ms" => "Malay",
            "mt" => "Maltese",
            "my" => "Burmese",
            "na" => "Nauru",
            "nb" => "Norwegian Bokmal",
            "nd" => "North Ndebele",
            "ne" => "Nepali",
            "ng" => "Ndonga",
            "nl" => "Dutch",
            "nn" => "Norwegian Nynorsk",
            "no" => "Norwegian",
            "nr" => "South Ndebele",
            "nv" => "Navajo",
            "ny" => "Chichewa",
            "oc" => "Occitan",
            "oj" => "Ojibwa",
            "om" => "Oromo",
            "or" => "Oriya",
            "os" => "Ossetian",
            "pa" => "Panjabi",
            "pi" => "Pali",
            "pl" => "Polish",
            "ps" => "Pashto",
            "pt" => "Portuguese",
            "qu" => "Quechua",
            "rm" => "Raeto-Romance",
            "rn" => "Kirundi",
            "ro" => "Romanian",
            "ru" => "Russian",
            "rw" => "Kinyarwanda",
            "sa" => "Sanskrit",
            "sc" => "Sardinian",
            "sd" => "Sindhi",
            "se" => "Northern Sami",
            "sg" => "Sango",
            "si" => "Sinhala",
            "sk" => "Slovak",
            "sl" => "Slovenian",
            "sm" => "Samoan",
            "sn" => "Shona",
            "so" => "Somali",
            "sq" => "Albanian",
            "sr" => "Serbian",
            "ss" => "Swati",
            "st" => "Southern Sotho",
            "su" => "Sundanese",
            "sv" => "Swedish",
            "sw" => "Swahili",
            "ta" => "Tamil",
            "te" => "Telugu",
            "tg" => "Tajik",
            "th" => "Thai",
            "ti" => "Tigrinya",
            "tk" => "Turkmen",
            "tl" => "Tagalog",
            "tn" => "Tswana",
            "to" => "Tonga",
            "tr" => "Turkish",
            "ts" => "Tsonga",
            "tt" => "Tatar",
            "tw" => "Twi",
            "ty" => "Tahitian",
            "ug" => "Uighur",
            "uk" => "Ukrainian",
            "ur" => "Urdu",
            "uz" => "Uzbek",
            "ve" => "Venda",
            "vi" => "Vietnamese",
            "vo" => "Volapuk",
            "wa" => "Walloon",
            "wo" => "Wolof",
            "xh" => "Xhosa",
            "yi" => "Yiddish",
            "yo" => "Yoruba",
            "za" => "Zhuang",
            "zh" => "Chinese",
            "zu" => "Zulu"
        );
        //return array_search($name, $languageCodes);
        if (null !== array_search($name, $languageCodes))
            return array_search($name, $languageCodes);
        else
            return "en";
    }

    function getDisplayLanguageForLocaleCode($langcode)
    {
        $languageCodes = array(
            "aa" => "Afar",
            "ab" => "Abkhazian",
            "ae" => "Avestan",
            "af" => "Afrikaans",
            "ak" => "Akan",
            "am" => "Amharic",
            "an" => "Aragonese",
            "ar" => "Arabic",
            "as" => "Assamese",
            "av" => "Avaric",
            "ay" => "Aymara",
            "az" => "Azerbaijani",
            "ba" => "Bashkir",
            "be" => "Belarusian",
            "bg" => "Bulgarian",
            "bh" => "Bihari",
            "bi" => "Bislama",
            "bm" => "Bambara",
            "bn" => "Bengali",
            "bo" => "Tibetan",
            "br" => "Breton",
            "bs" => "Bosnian",
            "ca" => "Catalan",
            "ce" => "Chechen",
            "ch" => "Chamorro",
            "co" => "Corsican",
            "cr" => "Cree",
            "cs" => "Czech",
            "cu" => "Church Slavic",
            "cv" => "Chuvash",
            "cy" => "Welsh",
            "da" => "Danish",
            "de" => "German",
            "dv" => "Divehi",
            "dz" => "Dzongkha",
            "ee" => "Ewe",
            "el" => "Greek",
            "en" => "English",
            "eo" => "Esperanto",
            "es" => "Spanish",
            "et" => "Estonian",
            "eu" => "Basque",
            "fa" => "Persian",
            "ff" => "Fulah",
            "fi" => "Finnish",
            "fj" => "Fijian",
            "fo" => "Faroese",
            "fr" => "French",
            "fy" => "Western Frisian",
            "ga" => "Irish",
            "gd" => "Scottish Gaelic",
            "gl" => "Galician",
            "gn" => "Guarani",
            "gu" => "Gujarati",
            "gv" => "Manx",
            "ha" => "Hausa",
            "he" => "Hebrew",
            "hi" => "Hindi",
            "ho" => "Hiri Motu",
            "hr" => "Croatian",
            "ht" => "Haitian",
            "hu" => "Hungarian",
            "hy" => "Armenian",
            "hz" => "Herero",
            "ia" => "Interlingua (International Auxiliary Language Association)",
            "id" => "Indonesian",
            "ie" => "Interlingue",
            "ig" => "Igbo",
            "ii" => "Sichuan Yi",
            "ik" => "Inupiaq",
            "io" => "Ido",
            "is" => "Icelandic",
            "it" => "Italian",
            "iu" => "Inuktitut",
            "ja" => "Japanese",
            "jv" => "Javanese",
            "ka" => "Georgian",
            "kg" => "Kongo",
            "ki" => "Kikuyu",
            "kj" => "Kwanyama",
            "kk" => "Kazakh",
            "kl" => "Kalaallisut",
            "km" => "Khmer",
            "kn" => "Kannada",
            "ko" => "Korean",
            "kr" => "Kanuri",
            "ks" => "Kashmiri",
            "ku" => "Kurdish",
            "kv" => "Komi",
            "kw" => "Cornish",
            "ky" => "Kirghiz",
            "la" => "Latin",
            "lb" => "Luxembourgish",
            "lg" => "Ganda",
            "li" => "Limburgish",
            "ln" => "Lingala",
            "lo" => "Lao",
            "lt" => "Lithuanian",
            "lu" => "Luba-Katanga",
            "lv" => "Latvian",
            "mg" => "Malagasy",
            "mh" => "Marshallese",
            "mi" => "Maori",
            "mk" => "Macedonian",
            "ml" => "Malayalam",
            "mn" => "Mongolian",
            "mr" => "Marathi",
            "ms" => "Malay",
            "mt" => "Maltese",
            "my" => "Burmese",
            "na" => "Nauru",
            "nb" => "Norwegian Bokmal",
            "nd" => "North Ndebele",
            "ne" => "Nepali",
            "ng" => "Ndonga",
            "nl" => "Dutch",
            "nn" => "Norwegian Nynorsk",
            "no" => "Norwegian",
            "nr" => "South Ndebele",
            "nv" => "Navajo",
            "ny" => "Chichewa",
            "oc" => "Occitan",
            "oj" => "Ojibwa",
            "om" => "Oromo",
            "or" => "Oriya",
            "os" => "Ossetian",
            "pa" => "Panjabi",
            "pi" => "Pali",
            "pl" => "Polish",
            "ps" => "Pashto",
            "pt" => "Portuguese",
            "qu" => "Quechua",
            "rm" => "Raeto-Romance",
            "rn" => "Kirundi",
            "ro" => "Romanian",
            "ru" => "Russian",
            "rw" => "Kinyarwanda",
            "sa" => "Sanskrit",
            "sc" => "Sardinian",
            "sd" => "Sindhi",
            "se" => "Northern Sami",
            "sg" => "Sango",
            "si" => "Sinhala",
            "sk" => "Slovak",
            "sl" => "Slovenian",
            "sm" => "Samoan",
            "sn" => "Shona",
            "so" => "Somali",
            "sq" => "Albanian",
            "sr" => "Serbian",
            "ss" => "Swati",
            "st" => "Southern Sotho",
            "su" => "Sundanese",
            "sv" => "Swedish",
            "sw" => "Swahili",
            "ta" => "Tamil",
            "te" => "Telugu",
            "tg" => "Tajik",
            "th" => "Thai",
            "ti" => "Tigrinya",
            "tk" => "Turkmen",
            "tl" => "Tagalog",
            "tn" => "Tswana",
            "to" => "Tonga",
            "tr" => "Turkish",
            "ts" => "Tsonga",
            "tt" => "Tatar",
            "tw" => "Twi",
            "ty" => "Tahitian",
            "ug" => "Uighur",
            "uk" => "Ukrainian",
            "ur" => "Urdu",
            "uz" => "Uzbek",
            "ve" => "Venda",
            "vi" => "Vietnamese",
            "vo" => "Volapuk",
            "wa" => "Walloon",
            "wo" => "Wolof",
            "xh" => "Xhosa",
            "yi" => "Yiddish",
            "yo" => "Yoruba",
            "za" => "Zhuang",
            "zh" => "Chinese",
            "zu" => "Zulu"
        );

        //return array_search($name, $languageCodes);
        if (isset($languageCodes[$langcode]))
            return $languageCodes[$langcode];
        else
            return "English";

    }

    public function fix_user_openai_chat_ID($db, $table, $chat_id)

    {

        if ($db == 'bio_db')
            $column = 'chat_id_mobile';
        else
            $column = 'chat_id';


        $chat_main_id_find = DB::connection($db)->table($table)->where($column, $chat_id)->first();

        if (isset($chat_main_id_find->id))
            Log::debug('Fix Chat ID of chats : ' . $chat_main_id_find->id);

        if (isset($chat_main_id_find->id) || isset($chat_main_id_find->chat_id)) {
            if (isset($chat_main_id_find->id) && $chat_main_id_find->id > 0)
                return $chat_main_id_find->id;
            else
                return $chat_main_id_find->chat_id;

        } else {
            return 0;
        }


    }

    public function lowChatSave($user_request, $user_id, $save_to_where, $user_email, $from, $chat_role)
    {
        // $chat = UserOpenaiChat::where('id', $user_request->chat_id)->first();

        if (isset($user_request['chat_id']))
            $chat_id = $user_request['chat_id'];
        else
            $chat_id = $user_request['chat_id_mobile'];


        if ($save_to_where == "SocialPost") {
            $message = new UserOpenaiChatMessageSocialPost();
            $db = 'main_db';
            $table_chat = 'sp_user_openai_chat';
        } else if ($save_to_where == "Design") {
            $message = new UserOpenaiChatMessageDesign();
            $db = 'digitalasset_db';
            $table_chat = 'user_openai_chat';
        } else if ($save_to_where == "MobileAppV2") {
            $message = new UserOpenaiChatMessageMobile();
            $db = 'mobileapp_db';
            $table_chat = 'willdev_user_chat';
        } else if ($save_to_where == "MainMarketing") {
            $message = new UserOpenaiChatMessageMainMarketing();
            $db = 'main_db';
            $table_chat = 'conversation_list';
        } else if ($save_to_where == "Bio") {
            $message = new UserOpenaiChatMessageBio();
            $db = 'bio_db';
            $table_chat = 'chats';
        } else if ($save_to_where == "SyncNodeJS") {
            $message = new UserOpenaiChatMessageSyncNodeJS();
            $db = 'sync_db';
            $table_chat = 'user_openai_chat';
        } else {
            $message = new UserOpenaiChatMessage();
            $save_to_where == "MainCoIn";
            $db = 'main_db';
            $table_chat = 'user_openai_chat';
        }

        //final check chat_id
        $final_chat_id = $this->fix_user_openai_chat_ID($db, $table_chat, $user_request['chat_id_mobile']);


        Log::debug('.........Final fix Chat-ID' . $final_chat_id);

        if ($final_chat_id != 0)
            $chat_id_save = $final_chat_id;
        else
            $chat_id_save = $chat_id;


        if ($user_request['chat_id'] == $final_chat_id) {
            Log::debug('!!!!!!!!!!! Perfect Match!!!!!!!!!!! with out correction !!!!!!!');
        }

        $message->user_openai_chat_id = $chat_id_save;

        $message->user_id = $user_id;
        $message->input = $user_request['input'];

        if ($save_to_where == "MainMarketing")
            $message->content = $user_request['input'];


        $message->response = $user_request['response'];
        $message->output = $user_request['response'];


        Log::debug('Saving Main with response' . $message->response);
        Log::debug('Saving Main with response ' . $message->output);


        $message->hash = Str::random(256);
        // $message->credits = countWords($user_request['response']);
        $message->credits = Str::of($user_request['response'])->wordCount();
        $message->words = Str::of($user_request['response'])->wordCount();


        if ($save_to_where == "Bio") {

            $message->content = $user_request['response'];
            $message->chat_id = $chat_id_save;
            $message->role = $chat_role;


        }

        //eof every Platforms save if no more ferture additionall save value
        $message->save();


        //correct SyncNodeJS ChatLogs
        if ($save_to_where == "SyncNodeJS" && $message->input != NULL) {

            $save_id_message = $message->id;

            Log::debug('save SyncNodeJS messsage that not NUll content ID ' . $save_id_message);

            $id_before = $save_id_message;
            $id_before -= 1;
            Log::debug('Case SyncNodeJS correction input message');
            //$message_before=DB::connection('bio_db')->where('chat_message_id',$id_before)->first();
            $message_before = UserOpenaiChatMessageSyncNodeJS::where('id', $id_before)->first();
            Log::info($message_before);

            if (($message_before->input == NULL) && ($message_before->user_openai_chat_id == $message->user_openai_chat_id)) {


                $contains = Str::contains($user_request['input'], 'user ');

                if ($contains == true) {
                    $chat_input_role = explode('user ', $user_request['input']);
                    $message_before->text = $chat_input_role[1];
                } else {

                    $message_before->text = $user_request['input'];
                }


                $message_before->uid = $user_id;
                $message_before->save();

                //correction message
                $message->uid = $user_id;
                $message->text = $message->output;
                $message->chat_id = $message_before->chat_id;
                $message->save();


            }

        }

        //correct SmartBio Chat Logs
        if ($save_to_where == "Bio" && $message->content != NULL) {

            $save_id_message = $message->chat_message_id;

            Log::debug('save Bio messsage that not NUll content ID ' . $save_id_message);

            $id_before = $save_id_message;
            $id_before -= 1;
            Log::debug('Case Bio correction input message');
            //$message_before=DB::connection('bio_db')->where('chat_message_id',$id_before)->first();
            $message_before = UserOpenaiChatMessageBio::where('chat_message_id', $id_before)->first();
            Log::info($message_before);

            /* // Convert JSON string to object
            $data = json_decode($message_before);

            // Get chat_id_mobile
            $chat_id_mobile = $data->chat_id_mobile; */

            // Get chat_id_mobile
            $chat_id_mobile = $message_before->chat_id_mobile;
            Log::debug("Found Chat mobile id or chat_id ");
            Log::info($chat_id_mobile);

            if (($message_before->content == NULL) && ($message_before->user_openai_chat_id == $message->user_openai_chat_id)) {


                $contains = Str::contains($user_request['input'], 'user ');

                if ($contains == true) {
                    $chat_input_role = explode('user ', $user_request['input']);
                    $message_before->content = $chat_input_role[1];
                } else {

                    $message_before->content = $user_request['input'];
                }

                $message_before->role = 'user';
                //Log::debug('Found Role in Bio Update message '.$chat_input_role[0]);

                //Log::debug('Found content input in Bio Update message '.$chat_input_role[1]);
                $message_before->chat_id = $message->chat_id;
                $message_before->save();


            }

            //Update Total message
            $check_total = UserOpenaiChatMessageBio::where('chat_id', $chat_id_save)->get();
            $total_chats_bio = $check_total->count();

            //$check_Chats_Bio=UserOpenaiChatBio::where('chat_id',$chat_id_save)->where('chat_id_mobile',$user_request['chat_id_mobile'])->first();

            if (isset($total_chats_bio) && $total_chats_bio > 0) {

                $check_Chats_Bio = array(
                    'total_messages' => $total_chats_bio,
                );

                $chatData = DB::connection('bio_db')->table('chats')
                    ->where('chat_id', $chat_id_save)
                    ->update($check_Chats_Bio);


                //then Fix title incase of PDF or files CHAT

                //move from Eloquent to DB style
                //$chat_bio=UserOpenaiChatBio::where('chat_id_mobile', $chat_id_mobile)->first();
                //$chat_main_coin=UserOpenaiChat::where('chat_id', $chat_id_mobile)->first();


                $updateData = array();
                $chat_main_coin = UserOpenaiChat::where('chat_id', $chat_id_mobile)->first();

                if ($chat_main_coin) {
                    $main_chat_title = $chat_main_coin->title;
                    $updateData = ['name' => $main_chat_title, 'title' => $main_chat_title]; // Prepare an array with columns to update
                }

                if (count($updateData) > 0 && $chat_id_mobile != NULL && $chat_id_mobile != "0") {
                    DB::connection('bio_db')->table('chats')
                        ->where('chat_id_mobile', $chat_id_mobile)
                        ->where('chat_id_mobile', $chat_main_coin->chat_id)
                        ->update($updateData); // Update the data
                }

                //eof Fix title incase of PDF or files CHAT


            }


        }

        //correct Main.Co.In Chat Logs
        if ($save_to_where == "MainCoIn" && $message->input != NULL) {

            $save_id_message = $message->id;

            Log::debug('save MainCoIn messsage that not NUll content ID ' . $save_id_message);

            $id_before = $save_id_message;
            $id_before -= 1;
            Log::debug('Case MainCoIn correction input message');
            //$message_before=DB::connection('bio_db')->where('chat_message_id',$id_before)->first();
            $message_before = UserOpenaiChatMessage::where('id', $id_before)->first();
            Log::info($message_before);

            if (($message_before->input == NULL) && ($message_before->user_openai_chat_id == $message->user_openai_chat_id)) {


                $message->chat_id = $message_before->chat_id;
                $message->save();
                $message_before->delete();


            }

            $user_data_db = UserMain::where('id', $user_id)->first();
            $remaining_images = $user_data_db->remaining_images;
            $remaining_words = $user_data_db->remaining_words;
            //$user_email = $user_data_db->email;

            $old_reamaining_word = $user_data_db->remaining_words;
            $old_reamaining_image = $user_data_db->remaining_images;

            $token_update_type = "text";

            if ($from == 'bio')
                $chatGPT_catgory = 'Chat_SmartBio';

            if ($from == 'main_coin')
                $chatGPT_catgory = 'Chat_Default AI Chat Bot';

            if ($from == 'MobileAppV2')
                $chatGPT_catgory = 'Chat_mobilepp';

            $usage = $message->credits;

            $remaining_words -= $usage;

            $token_array = array(

                'remaining_images' => $remaining_images,
                'remaining_words' => $remaining_words,
            );

            if ($from == 'bio')
                $log_chat_bio_array = $this->update_token_centralize($user_id, $user_email, $token_array, $usage, $from, $old_reamaining_word, $old_reamaining_image, $chatGPT_catgory, $token_update_type);

            if ($from == 'MobileAppV2')
                $log_chat_bio_array = $this->update_token_centralize($user_id, $user_email, $token_array, $usage, $from, $old_reamaining_word, $old_reamaining_image, $chatGPT_catgory, $token_update_type);


            if ($log_chat_bio_array['log_token_id'] != NULL || $log_chat_bio_array['log_token_plus_id'] != NULL || $log_chat_bio_array['log_token_plus_id2'] != NULL) {

                $message->token_synced = 1;
                $message->save();
                Log::debug('Success Log Token Found log_token_id ' . $log_chat_bio_array['log_token_id']);
                Log::debug('Success Log Token Found log_token_plus_id ' . $log_chat_bio_array['log_token_plus_id']);
                Log::debug('Success Log Token Found log_token_plus_id2 ' . $log_chat_bio_array['log_token_plus_id2']);
            } else {
                Log::debug('Not Found log_token_id ' . $log_chat_bio_array['log_token_id']);
                Log::debug('Not Found log_token_plus_id ' . $log_chat_bio_array['log_token_plus_id']);
                Log::debug('Not Found log_token_plus_id2 ' . $log_chat_bio_array['log_token_plus_id2']);
            }


        }


        if ($save_to_where == "Design") {

            $usage = $message->credits;
            $user = UserMain::where('id', $user_id)->first();
            $token_update_type = "Chat_User";
            $chatGPT_catgory = "Chat_";
            $old_reamaining_word = $user->remaining_words;
            $old_reamaining_image = $user->remaining_images;


            if ($user->remaining_words != -1) {
                $user->remaining_words -= $usage;
            }

            if ($user->remaining_words < -1) {
                $user->remaining_words = 0;
            }

            $token_array = array(
                'remaining_words' => $user->remaining_words,
                'remaining_images' => $user->remaining_images,
            );

            $this->update_token_centralize($user_id, $user_email, $token_array, $usage, $from, $old_reamaining_word, $old_reamaining_image, $chatGPT_catgory, $token_update_type);

            /*  $user = UserMain::where('id',$user_id)->first();

             if ($user->remaining_words != -1) {
                 $user->remaining_words -= $message->credits;
             }

             if ($user->remaining_words < -1) {
                 $user->remaining_words = 0;
             }
             $user->save(); */

        }

        if ($save_to_where == "MobileAppV2") {

            $model_from_Mobile = UserOpenaiChatMessageMobile::where('id', $message->id);

        }


        return response()->json([]);
    }

    public function reset_user_Bio($user_id, $user_email, $upFromWhere)
    {
        if ($upFromWhere == 'main_coin') {

            if ($user_id == 1) {
                //Reset all users
                $updated_i = 0;
                Log::debug('Debug FOund Reset Bio user from Main ID before find Email ' . $user_id);
                $userbio_data = UserBio::where('user_id', '!=', $user_id);

                foreach ($userbio_data as $userbio) {
                    //find old Bio setting value

                    if ($userbio->plan_settings != NULL) {
                        $userbio_plan = json_decode($userbio->plan_settings, true);

                        $old_transcriptions_per_month_limit = $userbio_plan['transcriptions_per_month_limit'];
                        $old_images_per_month_limit = $userbio_plan['images_per_month_limit'];
                        $old_words_per_month_limit = $userbio_plan['words_per_month_limit'];
                        $old_documents_per_month_limit = $userbio_plan['documents_per_month_limit'];
                        $old_chats_per_month_limit = $userbio_plan['chats_per_month_limit'];

                    } else {
                        $old_transcriptions_per_month_limit = $userbio->transcriptions_per_month_limit;
                        $old_images_per_month_limit = $userbio->images_per_month_limit;
                        $old_words_per_month_limit = $userbio->words_per_month_limit;
                        $old_documents_per_month_limit = $userbio->documents_per_month_limit;
                        $old_chats_per_month_limit = $userbio->chats_per_month_limit;
                    }


                    //find user plan id
                    $userbio_plan_id = $userbio->plan_id;

                    if ($userbio_plan_id == 'free')
                        $userbio_plan_id = 0;
                    else if ($userbio_plan_id == 'team')
                        $userbio_plan_id = 99;
                    else
                        $userbio_plan_id = intval($userbio_plan_id) + 100;

                    $new_plan = PlanBio::where('plan_id', $userbio_plan_id)->first();
                    $new_plan_settings = json_decode($new_plan->settings, true);

                    //update new plan setting
                    $new_plan_settings['transcriptions_per_month_limit'] = $old_transcriptions_per_month_limit;

                    //$userbio->plan_settings->images_per_month_limit=$old_images_per_month_limit;
                    //$userbio->plan_settings->words_per_month_limit=$old_words_per_month_limit;

                    $new_plan_settings['documents_per_month_limit'] = $old_documents_per_month_limit;
                    $new_plan_settings['chats_per_month_limit'] = $old_chats_per_month_limit;

                    //case use BackUp data from Main
                    $usermain_data = UserMain::where('id', '=', $userbio->user_id)->first();
                    $new_plan_settings['words_per_month_limit'] = $usermain_data->remaining_words;
                    $new_plan_settings['images_per_month_limit'] = $usermain_data->remaining_images;

                    $userbio->plan_settings = json_encode($new_plan_settings);
                    $userbio->save();

                    if ($userbio->id > 0)
                        $updated_i++;


                }


                if ($updated_i > 0)
                    return 1;
                else
                    return 0;


            } else {

                //bug
                //Wait for update userBio plan id in MainUser and BIoUsers and Expiratin date


                Log::debug('Debug FOund Reset each Bio user from Main ID before find Email ' . $user_id);

                /*  $userbio_data = UserBio::where('user_id', '=', $user_id)->first();
            //find old Bio setting value
            $userbio_plan=json_decode($userbio_data->plan_settings,true);
            $old_transcriptions_per_month_limit=$userbio_plan['transcriptions_per_month_limit'];
             */

                $userbio_data1 = UserBio::where('user_id', $user_id)->first();
                //check if plan_settings is a valid JSON

                if ($userbio_data1->plan_settings == NULL || $userbio_data1->plan_settings == '' || $userbio_data1->plan_settings == null || Str::length($userbio_data1->plan_settings) < 10) {

                    if ($userbio->words_per_month_limit != NULL) {
                        $old_transcriptions_per_month_limit = $userbio->transcriptions_per_month_limit;
                        $old_images_per_month_limit = $userbio->images_per_month_limit;
                        $old_words_per_month_limit = $userbio->words_per_month_limit;
                        $old_documents_per_month_limit = $userbio->documents_per_month_limit;
                        $old_chats_per_month_limit = $userbio->chats_per_month_limit;
                    } else {

                        $user_bio_plan_id = $userbio_data1->plan_id;
                        Log::debug('userbio_data1->plan_settings is NULL ');
                        $bio_user_setting_from_plan = PlanBio::where('plan_id', $user_bio_plan_id)->first();
                        $userbio_data1->plan_settings = $bio_user_setting_from_plan->settings;
                        $userbio_data1->save();
                        Log::debug('userbio_data1->plan_settings is NULL and save new value success from plan ');
                        $userbio_plan = json_decode($userbio_data1->plan_settings, true);
                    }
                } else {

                    Log::info($userbio_data1->plan_settings);
                    //trim($userbio_data->plan_settings);
                    //Log::info($userbio_data->plan_settings);
                }
                $userbio_data = UserBio::where('user_id', '=', $user_id)->first();

                if ($this->isJson(trim($userbio_data->plan_settings, '"'))) {
                    $userbio_plan = json_decode(trim($userbio_data->plan_settings, '"'), true);
                    $old_transcriptions_per_month_limit = $userbio_plan['transcriptions_per_month_limit'];
                } else {
                    //Handle the situation when plan_settings is not a valid JSON
                    //For example you can set $old_transcriptions_per_month_limit to default
                    $userbio_plan = json_encode($userbio_data->plan_settings);
                    $old_transcriptions_per_month_limit = $userbio_plan['transcriptions_per_month_limit'];

                }


                //bug if want to update plan_settings more than 1 time or many times
                //then read old value that effect the numbers of usage of user
                //like images_per_month_limit,words_per_month_limit,synthesized_characters_per_month_limit,transcriptions_per_month_limit
                if (!isset($old_images_per_month_limit)) {
                    $old_images_per_month_limit = $userbio_plan['images_per_month_limit'];
                    $old_words_per_month_limit = $userbio_plan['words_per_month_limit'];
                    $old_documents_per_month_limit = $userbio_plan['documents_per_month_limit'];
                    $old_chats_per_month_limit = $userbio_plan['chats_per_month_limit'];
                }


                //find user plan id
                $userbio_plan_id = $userbio_data->plan_id;

                if ($userbio_plan_id == 'free')
                    $userbio_plan_id = 0;
                else if ($userbio_plan_id == 'team')
                    $userbio_plan_id = 99;
                else if ($userbio_plan_id > 100 && $userbio_plan_id > 0)
                    $userbio_plan_id = intval($userbio_plan_id);
                else
                    $userbio_plan_id = intval($userbio_plan_id) + 100;

                Log::debug('Debug FOund Reset each Bio user from Main ID  ' . $user_id . ' and plan id ' . $userbio_plan_id);
                //use this opprotunity to update plan id in MainUser and BIoUsers and Expiratin date


                $new_plan = PlanBio::where('plan_id', $userbio_plan_id)->first();

                if (isset($new_plan->settings))
                    $new_plan_settings = json_decode($new_plan->settings, true);
                else
                    Log::debug('Debug FOund Reset each Bio user from Main  ' . $user_id . ' and plan id ' . $userbio_plan_id . ' and new plan is null');


                //update new plan setting

                $new_plan_settings['transcriptions_per_month_limit'] = $old_transcriptions_per_month_limit;

                //$userbio_data->plan_settings->images_per_month_limit=$old_images_per_month_limit;
                //$userbio_data->plan_settings->words_per_month_limit=$old_words_per_month_limit;

                $new_plan_settings['documents_per_month_limit'] = $old_documents_per_month_limit;
                $new_plan_settings['chats_per_month_limit'] = $old_chats_per_month_limit;

                //case use BackUp data from Main
                $usermain_data = UserMain::where('id', $user_id)->first();

                //use this opprotunity to update plan id in MainUser and BIoUsers and Expiratin date
                if ($usermain_data->bio_plan != $userbio_plan_id) {
                    $usermain_data->bio_plan = $userbio_plan_id;
                    $usermain_data->save();
                    Log::debug('Debug FOund Reset each Bio user from Main  ' . $user_id . ' and plan id ' . $userbio_plan_id . ' and update plan id in MainUser to ' . $usermain_data->bio_plan);
                }

                //recheck if userBio plan is same as MainUser plan from plan settings
                if ($userbio_data->plan_id != $userbio_plan_id) {
                    $userbio_data->plan_id = $userbio_plan_id;
                    //$userbio_data->save();

                }

                $new_plan_settings['words_per_month_limit'] = $usermain_data->remaining_words;
                $new_plan_settings['images_per_month_limit'] = $usermain_data->remaining_images;


                $userbio_data->plan_settings = json_encode($new_plan_settings);
                stripslashes($userbio_data->plan_settings);


                try {
                    if ($userbio_data->save()) {
                        // successful save
                        Log::debug('User Bio data was saved successfully. ID is ' . $userbio_data->user_id);
                    } else {
                        // unsuccessful save
                        Log::debug('Failed to save User Bio data');
                    }
                } catch (\Exception $e) {
                    Log::debug('Save User Bio Failed with this error ' . $e->getMessage());
                    // or log the error, or show it to the user, etc.
                }

                if ($userbio_data->user_id > 0) {
                    Log::debug('Debug Save reset BIo plan succes for user ' . $user_id);
                    return 1;
                } else {
                    Log::debug('Debug Save reset BIo plan fail for user ' . $user_id);
                    return 0;

                }


            }


        }

    }

    public function del_user_all_platforms($user_id, $user_email, $upFromWhere)
    {
        if ($upFromWhere == 'bio') {
            Log::debug('Debug FOund Bio user ID before find Email ' . $user_id);
            $userbio_data = UserBio::where('user_id', '=', $user_id);

            if (isset($userbio_data->email))
                $user_email = $userbio_data->email;
            else
                $user_email = NULL;
        }

        if ($user_email != 'seoasia.co@gmail.com' && $user_id > 1) {

            //Del mainUser
            if ($user_email != '' && $user_email != NULL)
                $usermaincoin = UserMain::where('email', '=', $user_email)->where('id', '=', $user_id);
            else
                $usermaincoin = UserMain::where('id', '=', $user_id);


            if ($usermaincoin)
                $usermaincoin->delete();


            //SocialPost
            if ($user_email != '' && $user_email != NULL)
                $usersocial = UserSP::where('email', '=', $user_email)->where('id', '=', $user_id);
            else
                $usersocial = UserSP::where('id', '=', $user_id);

            //MobileAppV2
            if ($user_email != '' && $user_email != NULL)
                $usersocial = UserMobile::where('email', '=', $user_email)->where('id', '=', $user_id);
            else
                $usersocial = UserMobile::where('id', '=', $user_id);


            if ($usersocial)
                $usersocial->delete();

            //Design
            if ($user_email != '' && $user_email != NULL)
                $userdesign = UserDesign::where('email', '=', $user_email)->where('id', '=', $user_id);
            else
                $userdesign = UserDesign::where('id', '=', $user_id);


            if ($userdesign)
                $userdesign->delete();

            //Bio
            if ($user_email != '' && $user_email != NULL)
                $userbio = UserBio::where('email', '=', $user_email)->where('user_id', '=', $user_id);
            else
                $userbio = UserBio::where('user_id', '=', $user_id);

            if ($userbio)
                $userbio->delete();

            //BioBlog
            if ($user_email != '' && $user_email != NULL)
                $userbioblog = UserBioBlog::where('email', '=', $user_email)->where('id', '=', $user_id);
            else
                $userbioblog = UserBioBlog::where('id', '=', $user_id);

            if ($userbioblog)
                $userbioblog->delete();

            //Sync
            if ($user_email != '' && $user_email != NULL)
                $usersync = UserSyncNodeJS::where('email', '=', $user_email)->where('id', '=', $user_id);
            else
                $usersync = UserSyncNodeJS::where('id', '=', $user_id);

            if ($usersync)
                $usersync->delete();

            //CRM  Lead need not to be updated
            if ($user_email != '' && $user_email != NULL)
                $usercrm = UserCRM::where('email', '=', $user_email)->where('id', '=', $user_id);
            else
                $usercrm = UserCRM::where('id', '=', $user_id);


            if ($usercrm)
                $usercrm->delete();

            //SEO
            if ($user_email != '' && $user_email != NULL)
                $userseo = UserSEO::where('email', '=', $user_email)->where('id', '=', $user_id);
            else
                $userseo = UserSEO::where('id', '=', $user_id);


            if ($userseo)
                $userseo->delete();


            //Course Laravel
            if ($user_email != '' && $user_email != NULL)
                $usercourse = UserCourse::where('email', '=', $user_email)->where('id', '=', $user_id);
            else
                $usercourse = UserCourse::where('id', '=', $user_id);


            if ($usercourse)
                $usercourse->delete();

            //Live Shopping that upgrade from Old PunBot
            if ($user_email != '' && $user_email != NULL)
                $userliveshop = UserLiveShop::where('email', '=', $user_email)->where('id', '=', $user_id);
            else
                $userliveshop = UserLiveShop::where('id', '=', $user_id);


            if ($userliveshop)
                $userliveshop->delete();


        }


    }

    public function socialpost_permission_update_user_team($user_id, $plan_id)
    {

    }


    public function socialpost_permission_update_user($user_id, $plan_id)
    {
        if ($plan_id == 0 || $plan_id == NULL)
            $plan_id = 1;

        Log::debug('Success fix Plan ID bug' . $plan_id);

        $plan_item = DB::connection('main_db')->table('sp_plans')
            ->select('*')
            ->where('id', '=', $plan_id)
            ->limit(1)
            ->get();


        if (!empty($plan_item)) {
            $plan = $plan_item[0]->id;
            $permissions = $plan_item[0]->permissions;
            if ($plan_item[0]->trial_day != -1) {
                $expiration_date = time() + $plan_item[0]->trial_day * 86400;
            }
        }

        $save_team = [

            "permissions" => $permissions
        ];


        $team_id = DB::connection('main_db')->table('sp_team')
            ->where('owner', $user_id)
            ->update($save_team);

        //updated user downgraded and downgraded
        //bug What if? the $plan = $plan_id
        $check_planupdated = DB::connection('main_db')->table('sp_users')->where('id', $user_id)->first();

        //$plan is the plan before upgrade/downgrade of current user
        //$plan_id is the plan after upgrade/downgrade of current user
        //so $plan < (int)($plan_id it mean user upgrade because change to the higher number
        //so $plan > (int)($plan_id it mean user downgrade because change to the lower number

        $plan = $check_planupdated->plan;

        $main_user_at_socialpost_update = UserMain::where('id', $user_id)->first();
        $remaining_words_at_socialpostupdate = $main_user_at_socialpost_update->remaining_words;
        Log::debug('Success fix Plan ID bug' . $plan_id . ' and plan for SocialPost Database ' . $remaining_words_at_socialpostupdate);

        Log::debug('Success fix Plan ID bug' . $plan_id . ' and plan for SocialPost Database ' . $plan);


        if ($plan_id == 0 || $plan_id == 1 || ((int)$plan > (int)($plan_id))) {
            $user_main = UserMain::where('id', $user_id)->first();
            $user_main->token_downgraded = 1;
            $user_main->token_upgraded = 0;
            $token_delete_img = $user_main->remaining_images;
            //$user_main->save();

            //reset token in case of no godern TOken
            //record Token Log
            $reset_token = 0;
            if ($user_main->remaining_words > 0 || $user_main->remaining_images > 0) {
                $user_main->remaining_words = 0;
                $user_main->remaining_images = 0;
                $reset_token = 1;
            }

            if ($reset_token == 1) {
                // Save was successful
                Log::debug('User Downgrade!!!!! was saved successfully and send Token Log save');

                $this->record_token_log('image', 'reset', 'main_coin', $user_id, 'PlanDowngrade_Main', $token_delete_text = 0);
                $this->record_token_log('text', 'reset', 'main_coin', $user_id, 'PlanDowngrade_Main', $token_delete_img);
                if ($user_main->save()) {
                    Log::debug('User Downgrade!!!!! was saved successfully after  Token Log save');
                } else {
                    $updated = DB::connection('main_db')
                        ->table('users')
                        ->where('id', $user_id)
                        ->update([
                            'remaining_words' => 0,
                            'remaining_images' => 0
                        ]);

                    if ($updated > 0) {
                        // the row was updated
                        Log::debug('User Downgrade!!!!! Token log was saved successfully in back up DB way ');
                    } else {
                        // the row was not updated
                        $user_main->remaining_words = 0;
                        $user_main->remaining_images = 0;
                        $user_main->save();
                    }
                }
            } else {
                // Save failed
                Log::debug('User Downgrade!!!!!  save operation failed after 3 times try');
            }
        }

        if ((int)$plan < (int)($plan_id)) {
            $user_main = UserMain::where('id', $user_id)->first();
            $user_main->token_upgraded = 1;
            $user_main->token_downgraded = 0;

            //$user_main->save();
            if ($user_main->save()) {
                // Save was successful

                Log::debug('User Upgrade!!!!! was saved successfully');
                Log::debug('User Upgrade!!!!! was saved successfully and Token remaining_words ' . $user_main->remaining_words . ' and remaining_images ' . $user_main->remaining_images);
            } else {
                // Save failed
                Log::debug('User Upgrade!!!!!  save operation failed');
            }
        }


        return $team_id;


    }

    public function record_rest_token_log($token_type)
    {
        if ($token_type == 'both') {


        }


    }


    public function record_token_log($token_type, $amount, $platform, $user_id, $log_type, $token_delete)
    {
        $clear_token = 0;

        if ($token_type == 'image') {
            $main_user = UserMain::where('id', $user_id)->first();
            $old_reamaining_image = $main_user->remaining_images;
            $old_reamaining_word = $main_user->remaining_words;
            $old_reamaining_word -= $token_delete;

            $token_log = new TokenLogs();
            $token_log->type = $log_type;
            $token_log->amount = $amount;
            $token_log->platform = $platform;
            $token_log->user_id = $user_id;

            $token_log->token_before = $old_reamaining_image + $old_reamaining_word;
            $token_log->token_image_before = $old_reamaining_image;
            $token_log->token_text_before = $old_reamaining_word;

            if ($amount == 'reset') {
                $token_log->amount = $old_reamaining_image;
                $token_log->token_after = $token_log->token_before - $old_reamaining_image;
                $token_log->token_image_after = 0;
                $token_log->token_text_after = $old_reamaining_word;
            } else {
                $token_log->token_after = $token_log->token_before - $amount;
                $token_log->token_image_after = $old_reamaining_image - $amount;
                $token_log->token_text_after = $old_reamaining_word;
            }
            $token_id_save = $token_log->save();
        }

        if ($token_type == 'text') {

            Log::debug('Debug Found token_type = text and amount ' . $amount . ' and token_delete ' . $token_delete);
            $main_user = UserMain::where('id', $user_id)->first();
            $old_reamaining_image = $main_user->remaining_images;
            //$old_reamaining_image-=$token_delete;

            $old_reamaining_word = $main_user->remaining_words;
            $token_log = new TokenLogs();
            $token_log->type = $log_type;
            $token_log->amount = $amount;
            $token_log->platform = $platform;
            $token_log->user_id = $user_id;

            $token_log->token_before = $old_reamaining_image + $old_reamaining_word - $token_delete;
            $token_log->token_image_before = $old_reamaining_image - $token_delete;
            $token_log->token_text_before = $old_reamaining_word;

            if ($amount == 'reset') {
                $token_log->amount = $old_reamaining_word;
                $token_log->token_after = $token_log->token_before - $old_reamaining_word;
                $token_log->token_image_after = $old_reamaining_image - $token_delete;
                $token_log->token_text_after = 0;
            } else {
                $token_log->token_after = $token_log->token_before - $amount;
                $token_log->token_image_after = $old_reamaining_image - $token_delete;
                $token_log->token_text_after = $old_reamaining_word - $amount;
            }
            $token_id_save = $token_log->save();
        }


        //finally check if user is in any team status active


        if ($platform == 'bio' || Str::contains(Str::lower($platform), 'bio_')) {
            $team_main_check = Team_Members_Bio::where('user_id', $user_id)->first();
            if (isset($team_main_check->user_id)) {
                $token_log->team_bio_id = $team_main_check->team_id;
                $token_log->save();
            } else {
                $user_main = UserBio::where('user_id', $user_id)->first();
                $user_email = $user_main->email;
                $team_main_email_check = Team_Members_Bio::where('email', $user_email)->first();

                $token_log->team_bio_id = $team_main_email_check->team_id;
                $token_log->save();


            }
        } else if ($platform == 'socialpost' || Str::contains(Str::lower($platform), 'socialpost')) {

            $team_main_check = Team_Members_SocialPost::where('uid', $user_id)->first();
            if (isset($team_main_check->user_id)) {

                $token_log->team_sp_id = $team_main_check->team_id;
                $token_log->save();

            }


        } else {

            $token_log = TokenLogs::where('id', $token_id_save);
            Log::debug('Case Main to record Team ID ');
            $team_main_check = Team_Members_Main::where('user_id', $user_id)->first();


            if (isset($team_main_check->user_id)) {
                //fixing fixing bug case $token_id_save is NULL because it never save both case text and image never save
                Log::debug('Found User ID in Case Main to record Team ID ' . $team_main_check->team_id . " and User ID " . $team_main_check->user_id);

                $token_log->team_main_id = $team_main_check->team_id;
                $token_log->save();

            } else {
                $user_main = UserMain::where('id', $user_id)->first();
                $team_main_email_check = Team_Members_Main::where('email', $user_email);

                Log::debug('Found User from Email in Case Main to record Team ID ' . $team_main_email_check->team_id);

                $token_log->team_main_id = $team_main_email_check->team_id;
                $token_log->save();

            }


        }


    }


    public function bio_plan_settings_update_user($user_id, $plan_id)
    {

        /* $user_bio_plan_arr=SettingBio::where('key','plan_free')->orderBy('id','asc')->first();
        $user_bio_plan_arr = json_decode($user_bio_plan_arr, true);
        Log::debug('this is Setting Bio info from DB : ');
        Log::info($user_bio_plan_arr);
        Log::debug('this is Value Setting Bio info from DB : ');
        Log::info($free_plan);
        $user_bio_plan_array_con = json_decode($free_plan,true);
        Log::debug('this is Setting dECODE  FROM Main  array : ');
        Log::info($user_bio_plan_array_con);
        $free_plan_setting=$user_bio_plan_array_con['settings']; */

        $user_bio_plan_arr = PlanBio::where('plan_id', $plan_id)->orderBy('plan_id', 'asc')->first();
        $free_plan_setting = $user_bio_plan_arr->settings;


        $userdata = [
            'plan_settings' => json_encode($free_plan_setting),

        ];

        stripslashes($userdata['plan_settings']);


        $update_id = DB::connection('bio_db')->table('users')
            ->where('user_id', $user_id)
            ->update($userdata);

        return $update_id;


    }


// Function to convert array into
// stdClass object
    public function ToObject($Array)
    {

        // Create new stdClass object
        $object = new stdClass();

        // Use loop to convert array into
        // stdClass object
        foreach ($Array as $key => $value) {
            if (is_array($value)) {
                $value = ToObject($value);
            }
            $object->$key = $value;
        }
        return $object;
    }

    public function openAICustomAddOrUpdateSave($request_arr)
    {
        $request = $this->ToObject($request_arr);

        Log::debug('After convert to Object ');
        //Log::info($request);

        if ($request->template_id != NULL) {
            $template = OpenAIGenerator::where('id', $request->template_id)->firstOrFail();
        } else {
            $template = new OpenAIGenerator();
        }

        $template->title = $request->title;
        $template->description = $request->description;
        $template->image = $request->image;
        $template->color = $request->color;
        $template->prompt = $request->prompt;

        $inputNames = explode(',', $request->input_name);
        $inputDescriptions = explode(',', $request->input_description);
        $inputTypes = explode(',', $request->input_type);

        $i = 0;
        $prompt_name = 'write a text about';
        $array = [];
        foreach ($inputNames as $inputName) {
            $array[$i]['name'] = Str::slug($inputName);
            $array[$i]['type'] = $inputTypes[$i];
            $array[$i]['question'] = $inputName;
            $array[$i]['description'] = $inputDescriptions[$i];

            if ($i > 0) {
                $prompt_name .= 'and';
            }

            $prompt_name .= ' ';
            $prompt_name .= '**' . $inputName . '**';

            $i++;
        }

        //write a text about   description**  and  **description-second**

        $questions = json_encode($array, JSON_UNESCAPED_SLASHES);
        $template->active = 1;
        $template->slug = Str::slug($request->title) . '-' . Str::random(6);
        $template->questions = $questions;
        $template->type = 'text';
        $template->custom_template = 1;
        $template->prompt = $prompt_name;
        $template->filters = $request->filters;
        foreach (explode(',', $request->filters) as $filter) {
            if (OpenaiGeneratorFilter::where('name', $filter)->first() == null) {
                $newFilter = new OpenaiGeneratorFilter();
                $newFilter->name = $filter;
                $newFilter->save();
            }
        }
        $template->premium = $request->premium;
        $template->bio_template_id = $this->bio_template_id;

        $template->save();

        $ins_openai_id = $template->id;
        return $ins_openai_id;

    }


    public function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }


}








