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






class SMAIUpdateProfileController extends Controller
{


    //protected $request; 
    protected $hash_password;
    protected $skip_update_pss=0;
    protected $upFromWhere=NULL;
    protected $plus_new_images_token;
    protected $plus_new_words_token;

    // request as an attribute of the controllers



    public function __construct($request_update,$user_id,$user_email,$whatup,$upFromWhere=NULL)
    {
        $this->plus_new_images_token=0;
        $this->plus_new_words_token=0;
        $this->upFromWhere=$upFromWhere;

        Log::debug(" Start constructor of Class update Profile with these Params : ");

        Log::info($request_update);
        Log::info($user_id);
        Log::info($whatup);
        Log::info($upFromWhere);

        Log::info($request_update->data);

        if(isset($request_update->data['name']))
        {
        Log::debug(' Name for data : ');
        Log::info($request_update->data['name']);
        }
       

        if(isset($request_update->data['password']))
        {
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

        // for check if column existing
        // $user_column_on= $this->checkColumnExist($column,$table,$db);
        /* $request_update = json_decode($request_update,true);
        Log::info($request_update);
        $request = json_decode($request_update['data'],true ); */

        /* $request = json_encode($request_update->data);
        $request = json_decode($request,true); */
        
        $request=$request_update->data;
        Log::debug('Debug request Decode Json');
        Log::info($request);
      
        if (in_array("profile", $whatup)) {

            //basic_profile universal
            if (isset($request['password']) && $this->skip_update_pss != 1)
                $userdata['password'] = $this->hash_password;


            //basic_profile main co in
            if (isset($request['surname']))
                $userdata['surname'] = $request['surname'];

            //basic_profile universal
            if (isset($request['name']))
            {
                Log::debug('FOund name : '.$request['name']);
                Log::info($request['name']);
                $userdata['name'] = $request['name'];
            }

            //basic_profile universal
            if (isset($request['email']))
                $userdata['email'] = $request['email'];


            //basic_profile socialpost mobile bio
            if (isset($request['username']))
                $userdata['username'] = $request['username'];

        }

        if (in_array("plan", $whatup))  {

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

        if (in_array("password", $whatup) && $this->skip_update_pss != 1 )
        {
            Log::debug('Update Profile case Password ');

            if($this->upFromWhere  == 'bio')
            {
                $userdata['password'] =  $request_update->data['password'];
            }

            else
            {
            $userdata['password'] = $this->hash_password;
            }
            //send to medthod update password to all platforms
            $this->update_password_all($userdata,$user_id,$user_email);


        }
        else if ($whatup == 'profile' && $this->upFromWhere  == 'main_coin')
        {


        }
        else if ($whatup == 'profile' && $this->upFromWhere  == 'socialpost')
        {

            


        }
        else if (in_array("profile", $whatup) && $this->upFromWhere  == 'bio')
        {
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
            $this->up_profile_bio($userdata,$user_id,$user_email);   


        }
        else if( in_array("plan", $whatup) && $this->upFromWhere  == 'bio')
        {  
            // #ep2

            //send to medthod update bio profile to all platforms
            $this->up_plan_bio($userdata,$user_id,$user_email);  


        }
        else if($whatup == 'plan' && $this->upFromWhere  == 'socialpost')
        {



        }
        else{


        }


        // $this->upFromWhere  == 'socialpost_profile  ||  $this->upFromWhere  == 'socialpost_plan
        if ($this->upFromWhere  == 'socialpost_profile')
        {

       
        //1. update to profile of SocialPost
        //2. update basic profile to all Platforms
        //3. update extra profile to some Platforms


          }


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

    public function up_profile_main_co_in($request, $user_id, $whatup, $db, $table)
    {

        

        if ($this->upFromWhere  == 'socialpost')
        //1. update to profile of SocialPost
        //2. update basic profile to all Platforms
        //3. update extra profile to some Platforms

        $user = UserSP::where('id', '=', $user_id)
                ->update($userdata);


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


    public function up_profile_socialpost($request, $main_id = null)
    {


        if (isset($request->password) && $this->skip_update_pss != 1) {
            $privatehash_password = $this->hash_password;
        } 

        if (isset($request->surname))
            $surname_ins = $request->surname;
        else
            $surname_ins = '';

        if (isset($request->avatar))
            $avatar = $request->avatar;
        else if (isset($request->profileImage) && substr($request->profileImage, 0, 4) == "http")
            $avatar = $request->profileImage;
        else if (isset($request->image) && substr($request->image, 0, 4) == "http")
            $avatar = $request->image;
        else
            $avatar = '';

        //TODO SocialPost Demo
        $sosialPost_connected = 1;
        if ($sosialPost_connected == 1) {

            $fullname = $request->name . " " . $surname_ins;
            $username = $request->email;
            $email = $request->email;
            $password = $this->hash_password;
            $plan = 0;
            $expiration_date = 0;
            $permissions = NULL;

            /*  $plan_item = db_get("*", TB_PLANS, ["type" => 1, "status" => 1]); */
            $plan_item = DB::connection('main_db')->table('sp_plans')
                ->select('*')
                ->where('type', '=', 1)
                ->where('status', '=', 1)
                ->limit(1)
                ->get();


            if (!empty($plan_item)) {
                $plan = $plan_item[0]->id;
                $permissions = $plan_item[0]->permissions;
                if ($plan_item[0]->trial_day != -1) {
                    $expiration_date = time() + $plan_item[0]->trial_day * 86400;
                }
            } else {
                $plan = 1;
                $expiration_date = time() + 14 * 86400;
            }

            $timezone = NULL;
            if (session("timezone")) {
                $timezone = session("timezone");
            }
            if (Session::get('timezone')) {
                $timezone = Session::get('timezone');
            }

            /*
            Codeigniter
            $language = db_get("*", TB_LANGUAGE_CATEGORY, ["is_default" => 1, "status" => 1]);
            */
            $settings_two = Schema::hasTable((new SettingTwo())->getTable()) ? SettingTwo::first() : null;

            $language_code = "en";
            if (!empty($settings_two)) {
                $language_code = $settings_two->languages_default;
            }

            if (isset($main_id) && $main_id != NULL && $main_id != null)
                $ins_id = $main_id;
            else
                $ins_id = '';

            //$status=2;
            $ids = $this->ids();

            if ($ins_id > 0 && $ins_id != '') {
                $data = [

                    "id" => $ins_id,
                    "ids" => $ids,
                    "is_admin" => 0,
                    "role" => 0,
                    "fullname" => $fullname,
                    "username" => $username,
                    "email" => $email,
                    "password" => $this->hash_password,
                    "plan" => $plan,
                    "expiration_date" => $expiration_date,
                    "timezone" => $timezone,
                    "recovery_key" => NULL,
                    "language" => $language_code,
                    "login_type" => "direct",
                    "avatar" => $avatar,
                    "last_login" => time(),
                    "status" => 2,
                    "changed" => time(),
                    "created" => time(),

                ];
            } else {

                $data = [

                    "ids" => $ids,
                    "is_admin" => 0,
                    "role" => 0,
                    "fullname" => $fullname,
                    "username" => $username,
                    "email" => $email,
                    "password" => $this->hash_password,
                    "plan" => $plan,
                    "expiration_date" => $expiration_date,
                    "timezone" => $timezone,
                    "recovery_key" => NULL,
                    "language" => $language_code,
                    "login_type" => "direct",
                    "avatar" => $avatar,
                    "last_login" => time(),
                    "status" => 2,
                    "changed" => time(),
                    "created" => time(),

                ];


            }


            /* $user_id = db_insert(TB_USERS, $data);*/
            $user_id = DB::connection('main_db')->table('sp_users')->insertGetId($data);

            $save_team = [
                "ids" => SMAISessionAuthController::ids(),
                "owner" => $user_id,
                "pid" => $plan,
                "permissions" => $permissions
            ];


            /*  db_insert(TB_TEAM, $save_team);  */
            $team_id = DB::connection('main_db')->table('sp_team')->update($save_team);

            if (isset($main_id) && $main_id != NULL && $main_id != null)
                return -1;
            else
                return $user_id;
            //EOF TODO SocialPost Demo

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

        Log::debug('before update new Design Name :' . $request->name);
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

        if (iseet($request->image))
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
            'words_left' => 6500,
            'image_left' => 150,
            'user_type' => "User",
            'remaining_words' => 6500,
            'remaining_images' => 150,

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
            'words_left' => 6500,
            'image_left' => 150,
            'user_type' => "User",
            'remaining_words' => 6500,
            'remaining_images' => 150,

        ];

        $user = UserMobile::where('email', '=', $user_email)
            ->update($userdata);

    }


    public function up_profile_bio($userdata,$user_id,$user_email)
    {
        Log::debug("Start update Bio Profile to all Platforms in up_profile_bio ");


        //1.update name to all platforms
        if(isset($userdata['name']))
        {
            $userdata_name=array(
                'name' => $userdata['name'],
            );
        $this->update_column_all($userdata_name,$user_id,$user_email);

        }



        //Bio , Socialpost, Main
        if(isset($userdata['timezone']))
        {
        
        // to Main , Socialpost
        $userdata_timezone=array(
            'timezone' => $userdata['timezone'],
        );
        $this->update_column_all( $userdata_timezone,$user_id,$user_email,'main_db','users');

        $this->update_column_all( $userdata_timezone,$user_id,$user_email,'main_db','sp_users');

        }


        if(isset($userdata['is_newsletter_subscribed']))
        {
          //Only Smart Bio have newsletter subscribe
            
        }


       

    }

    public function up_freeplan_bio($userdata,$user_id,$user_email)
    {
        //update every Platforms to free plan id
        // Main.co.in to plan id  8
        //Main.marketing to package_id 1

    }


    public function up_plan_bio($userdata,$user_id,$user_email)
    {
        Log::debug("Start update Bio Profile to all Platforms in up_plan_bio ");


        //1.update plan to all platforms
        if(isset($userdata['plan']))
        {
            $userdata_plan=array(
                'plan' => $userdata['plan'],
            );
        
         
            

        // not working because each plan value not the same  
        //$this->update_column_all($userdata_name,$user_id,$user_email);

       
    
        $each_plan=PlanBio::where('plan_id',$userdata['plan'])->orderBy('plan_id', 'asc')->first();
        
        $socialpost_plan=$each_plan->socialpost_id;
        $userdata_plan['plan']=$socialpost_plan;

        $this->update_column_all( $userdata_plan,$user_id,$user_email,'main_db','sp_users');

        $main_coin_plan=$each_plan->main_plan_id;
        if($main_coin_plan==8)
        {
            $main_marketing_id=1;
        }
        else
        {
            $main_marketing_id=$main_coin_plan;
        }

        $userdata_plan['plan']=$main_coin_plan;
        

        $this->update_column_all( $userdata_plan,$user_id,$user_email,'main_db','users');


        $design_plan=$each_plan->design_id;
        $userdata_plan['plan']=$design_plan;
        $this->update_column_all( $userdata_plan,$user_id,$user_email,'digitalasset_db','users');


        $mobile_plan=$each_plan->mobile_id;
        $userdata_plan['plan']=$mobile_plan;
        $this->update_column_all( $userdata_plan,$user_id,$user_email,'mobileapp_db','users');

        $sync_plan=$each_plan->sync_id;
        $userdata_plan['plan']=$sync_plan;
        $this->update_column_all( $userdata_plan,$user_id,$user_email,'sync_db','user');




        // prepare for next update
        $userdata['package_id']= $main_marketing_id;
    
        }


        //Bio ,Main, Socialpost, Design,Mobile2 Sync
        $user_old_data=UserMain::where('id',$user_id)->orderBy('id', 'asc')->first();
        $user_bio_old_data=UserBio::where('id',$user_id)->orderBy('id', 'asc')->first();
        
        //defind remaining_words
        $userdata['remaining_words']=$user_old_data->remaining_words;
        $userdata['remaining_images']=$user_old_data->remaining_images;

      //defind others old mobile old Main
      $userdata['total_words']=$user_old_data->total_words;
      $userdata['total_images']=$user_old_data->total_images;

      $userdata['expiration_date']=$user_bio_old_data->plan_expire_date;
      $userdata['plan_expire_date']=$user_bio_old_data->plan_expire_date;
      $userdata['expired_date']=$user_bio_old_data->plan_expire_date;

      $userdata['available_words']=$user_old_data->available_words;
      $userdata['available_images']=$user_old_data->available_images;

        if(isset($userdata['remaining_words']))
        {

        $check_plus_remaining=Plan::where('id',$userdata['plan'])->orderBy('id', 'asc')->first();

        $plus_remaining_images=$check_plus_remaining->total_images;
        $plus_remaining_words=$check_plus_remaining->total_words;

        $this->plus_new_images_token=$plus_remaining_images;
        $this->plus_new_words_token=$plus_remaining_words;

        //update new data after plus new Plan
        $userdata['remaining_words']+=$plus_remaining_words;
        $userdata['remaining_images']+=$plus_remaining_images;
        $userdata['available_words']+=$plus_remaining_words;
        $userdata['available_images']+=$plus_remaining_images;
        $userdata['total_words']+=$plus_remaining_words;
        $userdata['total_images']+=$plus_remaining_images;



      
        
        //To Bio ,Main, Socialpost, Design,Mobile2 Sync
        $userdata_remaining_words=array(
            'remaining_words' => $userdata['remaining_words'],
            'remaining_images' => $userdata['remaining_images'],
        );

        $this->update_column_all( $userdata_remaining_words,$user_id,$user_email,'main_db','users');

        $this->update_column_all( $userdata_remaining_words,$user_id,$user_email,'main_db','sp_users');

        //$this->update_column_all( $userdata_remaining_words,$user_id,$user_email,'bio_db','users');

        $this->update_column_all( $userdata_remaining_words,$user_id,$user_email,'digitalasset_db','users');

        $this->update_column_all( $userdata_remaining_words,$user_id,$user_email,'mobileapp_db','users');


        $userdata_remaining_words['gpt_words_limit'] = $userdata['remaining_words'];
        $userdata_remaining_words['dalle_limit'] = $userdata['remaining_images'];
                
        $this->update_column_all( $userdata_remaining_words,$user_id,$user_email,'sync_db','user');

        unset($userdata_remaining_words['gpt_words_limit']);
        unset($userdata_remaining_words['dalle_limit']);


        }

        //Separate all of these for each platform 
            
        //plan main marketing && mobile old
            if (isset( $userdata['package_id']))
            {


              
                //To Main marketing co.in, Mobile old,
                $userdata_main_plan_array=array(
                    'package_id' => $userdata['package_id'],
                    
                    'total_words' => $userdata['total_words'],
                    'total_images' => $userdata['total_images'], 

                    'expiration_date' => $userdata['expiration_date'],
                    'plan_expire_date' => $userdata['plan_expire_date'],
                    'expired_date' => $userdata['expired_date'],

                    'available_words' => $userdata['available_words'],
                    'available_images' => $userdata['available_images'],
                       
                );

                $this->update_column_all( $userdata_main_plan_array,$user_id,$user_email,'main_db','users');    


                
            }

               //plan mobile_old
               //Done
               if (isset($userdata['available_images']))
               {
                   
               }
   
               //plan mobile_old
               //Done
               if (isset($userdata['total_words']))
               {
                   
               }
   
               //plan mobile_old
               //Done
               if (isset($userdata['total_images'] ))
               {
                   
               }
   
               //plan universal
               //Done
               if (isset($userdata['expiration_date']))
               {
                   
               }
   
               //plan mobile_old
               //Done
               if (isset($userdata['plan_expire_date']))
               {
                   
               }
   
               //plan main
               //Done
               if (isset($userdata['expired_date']))
               {
                   
               }

               
                //plan mobile_old
                //Done
                if (isset($userdata['available_words']))
                {
                    
                }

            //plan mobile_new
            $usersync_old_data=UserMobile::where('id',$user_id)->orderBy('id', 'asc')->first();
            $userdata['words_left']=$usersync_old_data->words_left;
            $userdata['image_left']=$usersync_old_data->image_left;

            $userdata['words_left']+= $this->plus_new_words_token ;
            $userdata['image_left']+= $this->plus_new_images_token ;

            if (isset($userdata['words_left']))
            {
                //To Bio ,Main, Socialpost, Design,Mobile2 Sync
                $userdata_mobile_plan_array=array(
                    'words_left' => $userdata['words_left'],
                    'image_left' => $userdata['image_left'],
                 
                );
            $this->update_column_all( $userdata_mobile_plan_array,$user_id,$user_email,'mobileapp_db','users');
                
            }


            //plan mobile_new
            if (isset($userdata['image_left']))
            {
                
            }

            //plan bio 
            //because call from bio Do nothing
            if (isset($userdata['plan_id']))
            {
                
            }


            //plan bio
            //because call from bio Do nothing
            if (isset($userdata['plan_settings']))
            {
                
            }
         

            //plan bio
            //because call from bio Do nothing
            if (isset($userdata['plan_expiration_date']))
            {

            }

            
            

       

    }


    public function up_profile_crm($request, $user_id, $user_email)
    {


    }

    public function update_password_all($userdata,$user_id,$user_email)
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


    public function update_email_all($userdata,$user_id,$user_email)
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

    public function update_column_all($userdata,$user_id,$user_email,$db=NULL,$table=NULL)
    {

        if($db!=NULL && $table != NULL )
        {
            //Database &&  Table
            /* $usermoible = UserMobile::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);  */
            Log::debug(" Start update Universal Column case Not Null db table that exist to all Platforms in update_column_all ");

            if($user_email!=NULL && Str::length($user_email) > 2)
            $user_data_update=DB::connection($db)->table($table)->where('email', $user_email)->where('id', $user_id)->orderBy('id','asc')->update($userdata);  
            else
            $user_data_update=DB::connection($db)->table($table)->where('id', $user_id)->orderBy('id','asc')->update($userdata);  


        }
    else
    {

   
        Log::debug(" Start update Universal Column case NULL db table that exist to all Platforms in update_column_all ");

        Log::debug(' With these info Data : ');
        Log::info($userdata);
           //Mobile
           $usermoible = UserMobile::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

            //Main CoIn  Main.marketing  Mobile old
            $usermaincoin = UserMain::where('email', '=', $user_email)->where('id', '=', $user_id)
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

            $usercrm =DB::connection('crm_db')->table('tblleads')->where('email', '=', $user_email)->where('id', '=', $user_id)->update($userdata);

            //SEO (first_name,last_name)

            if(isset($userdata['name']))
            {

                if(strpos($userdata['name'], " ") !== false)
                {
                    $firstname=$this->get_first_last_name($userdata['name'],'firstname');
                    $lastname=$this->get_first_last_name($userdata['name'],'lastname');

                }
                else{
                    $firstname=$userdata['name'];
                    $lastname='';
                }

                $userdata['first_name']=$firstname;
                $userdata['last_name']=$lastname;


            }

            $userseo = UserSEO::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

            //Unset first_name,last_name  after update
          
             if(isset($userdata['first_name']))
            {
                unset($userdata['first_name']);
                unset($userdata['last_name']);
            }


             //Course Laravel
             $usercourse = UserCourse::where('email', '=', $user_email)->where('id', '=', $user_id)
             ->update($userdata);

             
             
             //Live Shopping that upgrade from Old PunBot (firstname,lastname)
             if(isset($userdata['name']))
             {
 
                 if(strpos($userdata['name'], " ") !== false)
                 {
                     $firstname=$this->get_first_last_name($userdata['name'],'firstname');
                     $lastname=$this->get_first_last_name($userdata['name'],'lastname');
 
                 }
                 else{
                     $firstname=$userdata['name'];
                     $lastname='';
                 }
 
                 $userdata['firstname']=$firstname;
                 $userdata['lastname']=$lastname;
 
 
             }


             $userliveshop = UserLiveShop::where('email', '=', $user_email)->where('id', '=', $user_id)
             ->update($userdata);
            
              //Unset firstname,lastname after update
             if(isset($userdata['firstname']))
            {
                unset($userdata['firstname']);
                unset($userdata['lastname']);
            }

            //SocialPost

            if(isset($userdata['name']))
            {
                $userdata['fullname']=$userdata['name'];
                unset($userdata['name']);
            }

            $usersocial = UserSP::where('email', '=', $user_email)->where('id', '=', $user_id)
            ->update($userdata);

            if(isset($userdata['fullname']))
            {
                $userdata['name']=$userdata['fullname'];
                unset($userdata['fullname']);
            }

            
        }


    }

    public function check_old_user($db,$table,$email)
    {

        $user_old=DB::connection($db)->table($table)->where('email', $email)->orderBy('id','asc')->get();   

        $found_user= $user_old->count();
        return $found_user;

    }


    public function get_first_last_name($name,$firstOrlast)
    {
       // Split the full name into first and last name parts
        $name_parts = explode(" ", $name);

        // Assign the first name
        $first_name = $name_parts[0];

        // Assign the last name
        $last_name = $name_parts[1];

        if($firstOrlast=='firstname')
        return $first_name;
        else
        return $last_name;
    }

}
