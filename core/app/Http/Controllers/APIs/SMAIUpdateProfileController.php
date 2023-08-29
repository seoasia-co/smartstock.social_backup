<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Jobs\SendConfirmationEmail;

use App\Models\UserMain;
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
use App\Models\Plan;

use Log;
use Session;
use Cookie;
use Carbon\Carbon;

use App\Models\UserMobile;
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



class SMAIUpdateProfileController extends Controller
{
  

    //protected $request; 
    protected $hash_password;
    // request as an attribute of the controllers


    public function checkColumnExist($column,$table,$db){
        try{
            $has_table = Schema::connection($db)->hasColumn($table,$column);
            return $has_table;
        }
        catch(\Exception $e){
            return false;
        }
    }


    public function __construct($request_mobile)
    {
       

         $data=$request_mobile->data;
        
        if(isset($request_mobile->data['password']))
        {
            if($password !=null && $password != NULL)
            {
            $password=$request_mobile->data['password'];
            $this->hash_password=Hash::make($request_mobile->data['password']);
            }
            else{
                $this->hash_password=Hash::make(Str::random(24));
            }
        }
        else{
            $this->hash_password=Hash::make(Str::random(24));
        } 

        $this->hash_password=Hash::make(Str::random(24));


    }


    public function ids(){
        return uniqid();
    }

    public function db_session_create()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        if(isset($_COOKIE['PHPSESSID'])) 
       {
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


         Log::debug('Team socialpost ID : '.$team_id[0]->ids);
         Log::debug('User socialpost ID : '.$user_uid[0]->ids);        
            
                Session::put('uid', $team_id[0]->ids);
                Session::put('team_id', $user_uid[0]->ids);
                //set_session(["team_id" => $user_uid[0]->ids]);
                $sessionId = Session::getId();
                Log::debug('Debug Current Session ID : '.$sessionId);
               // dd($sessionId);
                Log::debug('Debug PHP Session ID : '.$_COOKIE['PHPSESSID']);
                
                $current_session_id = DB::table('sessions')
                ->select('pub_id','id','session_PHPSESSID')
                ->where('id', '=', $sessionId)
                ->orderBy('pub_id', 'desc')
                ->limit(1)
                ->get();

               
                $current_time = \Carbon\Carbon::now()->timestamp;

                if(count($current_session_id)>0)
                DB::connection('main_db')->update('update sessions set session_PHPSESSID = ? where id LIKE ? order by pub_id desc', [$_COOKIE['PHPSESSID'],$sessionId]);
                else
                DB::connection('main_db')->insert('insert into sessions (id, session_PHPSESSID, user_id, last_activity, ids, team_id, payload) values (?, ?, ?, ?, ?, ?, ?)', [$sessionId, $_COOKIE['PHPSESSID'], Auth::user()->id, $current_time, $user_uid[0]->ids, $team_id[0]->ids, 'payload']);
        
                //eof socialpost subfolder session login create
         }



    } 
    
    
    public function up_profile_socialpost($request,$main_id=null)
    {
       

        if(isset($request->password))
        {
             $privatehash_password=Hash::make($request->password);
        }
        else{
             $privatehash_password=Hash::make(Str::random(24));
        }

        if(isset($request->surname))
        $surname_ins=$request->surname;
        else
        $surname_ins='';

        if(isset($request->avatar))
        $avatar=$request->avatar;
        else if ( isset($request->profileImage) && substr($request->profileImage, 0, 4)=="http")
        $avatar=$request->profileImage;
        else if ( isset($request->image) && substr($request->image, 0, 4)=="http")
        $avatar=$request->image;
        else
        $avatar = '';

         //TODO SocialPost Demo
         $sosialPost_connected=1;
         if($sosialPost_connected==1)
         {
         
         $fullname=$request->name." ".$surname_ins;
         $username=$request->email;
         $email=$request->email;
         $password=$this->hash_password;
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
 
 
         if(!empty($plan_item)){
             $plan = $plan_item[0]->id;
             $permissions = $plan_item[0]->permissions;
             if($plan_item[0]->trial_day != -1){
                 $expiration_date = time() + $plan_item[0]->trial_day*86400;
             }
         }
         else{
             $plan=1;
             $expiration_date=time() + 14*86400;
         }
 
         $timezone = NULL;
             if( session("timezone") ){
                 $timezone = session("timezone");
             }
             if( Session::get('timezone') ){
                 $timezone = Session::get('timezone');
             }
 
             /* 
             Codeigniter
             $language = db_get("*", TB_LANGUAGE_CATEGORY, ["is_default" => 1, "status" => 1]);
             */
             $settings_two = Schema::hasTable((new SettingTwo())->getTable()) ? SettingTwo::first() : null;  
             
             $language_code= "en";
             if(!empty($settings_two)){
                 $language_code = $settings_two->languages_default ;
             }

         if(isset($main_id) && $main_id != NULL &&  $main_id != null )
         $ins_id=$main_id;
         else
         $ins_id='';

         //$status=2;
         $ids=$this->ids();

         if($ins_id>0 && $ins_id !='' )
            {
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
            }
            else{

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
           $user_id =  DB::connection('main_db')->table('sp_users')->insertGetId($data);
 
         $save_team = [
             "ids" => SMAISessionAuthController::ids(),
             "owner" => $user_id,
             "pid" => $plan,
             "permissions" => $permissions
         ];
       
      
        /*  db_insert(TB_TEAM, $save_team);  */
        $team_id =  DB::connection('main_db')->table('sp_team')->update( $save_team);
 
        if(isset($main_id) && $main_id != NULL &&  $main_id != null )
         return -1;
        else
         return $user_id;
          //EOF TODO SocialPost Demo
 
     }



    }

    public function up_profile_mobileApp($request,$user_id)
    {
        if(isset($request->password))
        {
             $privatehash_password=Hash::make($request->password);
        }
        else{
             $privatehash_password=Hash::make(Str::random(24));
        }
        //TODO Mobile Old app Demo
        $MobileApp_connected=1;
        if($MobileApp_connected==1)
        {
    
            $assign_plan=Plan::where('price',0)->orderBy('id','asc')->first();
            $userdata = [
                'id' => $user_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => $this->hash_password,
                'user_type' => 'company',
                'created_by' => 1,
                'plan' => $assign_plan->id,
                'available_words'=>$assign_plan->max_words,
                'total_words'=>$assign_plan->max_words,
                'available_images'=>$assign_plan->max_images,
                'total_images'=>$assign_plan->max_images,
                'email_verified_at'=> Carbon::now(),
                'plan_expire_date' => Carbon::now()->addDays(14),
            ];

            $user =UserMain::where('id', '=', $user_id)
        ->update($userdata);
    
    
        }
    
    
        //EOF TODO Mobile Old app Demo
    }

    public function up_profile_main_co_in($request,$user_id,$whatup,$db,$table)
    {

        $userdata=[];

        // for check if column existing
        // $user_column_on= $this->checkColumnExist($column,$table,$db);


        if(isset($request->password))
        {
             $privatehash_password=Hash::make($request->password);
        }
        else{
             $privatehash_password=Hash::make(Str::random(24));
        }


        if (in_array("profile", $whatup))
        {

        //basic_profile universal
        if(isset($request->password))
        $userdata['password']=$privatehash_password;


        //basic_profile main co in
        if(isset($request->surname))
        $userdata['surname']=$request->surname;
        
        //basic_profile universal
        if(isset($request->name))
        $userdata['name']=$request->name;

        //basic_profile universal
        if(isset($request->email))
        $userdata['email']=$request->email;


         //basic_profile socialpost mobile bio
        if(isset($request->username))
        $userdata['username']=$request->username;    

    }

    if (in_array("plan", $whatup))
    /* if($whatup=="plan") */
    {

        //plan universal
        if(isset($request->plan))
        $userdata['plan']=$request->plan;

        //plan main_marketing=package_id  /  main_coin=plan
        if(isset($request->package_id))
        $userdata['package_id']=$request->package_id;

          //extra_profile bio
     if(isset($request->plan_id))
     $userdata['plan_id']=$request->plan_id;


     //extra_profile bio
     if(isset($request->plan_settings))
     $userdata['plan_settings']=$request->plan_settings;

     

        //plan universal
        if(isset($request->remaining_words))
        $userdata['remaining_words']=$request->remaining_words;

         //plan universal
        if(isset($request->remaining_words))
        $userdata['remaining_images']=$request->remaining_images;

        //plan mobile_new
        if(isset($request->words_left))
        $userdata['words_left']=$request->words_left;


        //plan mobile_new
        if(isset($request->image_left))
        $userdata['image_left']=$request->image_left;
        

           //plan mobile_old
        if(isset($request->available_words))
        $userdata['available_words']=$request->available_words;

        //plan mobile_old
        if(isset($request->available_images))
        $userdata['available_images']=$request->available_images;

        //plan mobile_old
        if(isset($request->total_words))
        $userdata['total_words']=$request->total_words;
        
        //plan mobile_old
        if(isset($request->total_images))
        $userdata['total_images']=$request->total_images;


        //plan universal
        if(isset($request->expiration_date))
        $userdata['expiration_date']=$request->expiration_date;

        //plan mobile_old
        if(isset($request->plan_expire_date))
        $userdata['plan_expire_date']=$request->plan_expire_date;

        //plan main
        if(isset($request->expired_date))
        $userdata['expired_date']=$request->expired_date;

        //extra_profile bio
     if(isset($request->plan_expiration_date))
     $userdata['plan_expiration_date']=$request->plan_expiration_date;



     //plan main
     if(isset($request->last_login_at))
     $userdata['last_login_at']=$request->last_login_at;


     //plan main
     if(isset($request->last_login_ip))
     $userdata['last_login_ip']=$request->last_login_ip;


      //plan main
      if(isset($request->under_which_affiliate_user))
      $userdata['under_which_affiliate_user']=$request->under_which_affiliate_user;
        

    }

    if (in_array("extra_profile", $whatup))
   /*  if($whatup=="extra_profile") */
    {

        //extra_profile socialpost
        if(isset($request->ids))
        $userdata['ids']=$request->remaining_images;

        //extra_profile socialpost
        if(isset($request->is_admin))
        $userdata['is_admin']=$request->is_admin;

        //extra_profile socialpost
        if(isset($request->role))
        $userdata['role']=$request->role;

        //extra_profile socialpost
        if(isset($request->fullname))
        $userdata['fullname']=$request->fullname;

        //extra_profile socialpost
        if(isset($request->timezone))
        $userdata['timezone']=$request->timezone;

        //extra_profile socialpost
        if(isset($request->language))
        $userdata['language']=$request->language;

        //extra_profile socialpost
        if(isset($request->login_type))
        $userdata['login_type']=$request->login_type;

        //extra_profile socialpost
        if(isset($request->avatar))
        $userdata['avatar']=$request->avatar;


       //extra_profile socialpost
        if(isset($request->last_login))
        $userdata['last_login']=$request->last_login;

        //extra_profile main ,socialpost ,Design
        if(isset($request->status))
        $userdata['status']=$request->status;
     
 
        //extra_profile socialpost
        if(isset($request->changed))
        $userdata['changed']=$request->changed;


        //extra_profile mobile_new=User,   mobile_old=user,super admin / main_marketing=Member,admin 
        if(isset($request->user_type))
        $userdata['user_type']=$request->user_type;

        //extra_profile mobile_old
        if(isset($request->created_by))
        $userdata['created_by']=$request->created_by;

        //extra_profile Design
    if(isset($request->phone_no))
    $userdata['phone_no']=$request->phone_no;


     //extra_profile Design
     if(isset($request->profile_pic))
     $userdata['profile_pic']=$request->profile_pic;

     //extra_profile mobile_new
     if(isset($request->image))
     $userdata['image']=$request->image;
     
     //extra_profile bio
     if(isset($request->timezone))
     $userdata['timezone']=$request->timezone;

      //extra_profile bio
      if(isset($request->is_newsletter_subscribed))
      $userdata['is_newsletter_subscribed']=$request->is_newsletter_subscribed;


    }



            $user =UserMain::where('id', '=', $user_id)
            ->update($userdata);

        

    }

    public function up_profile_main_marketing($request,$user_id)
    {
        if(isset($request->password))
        {
             $privatehash_password=Hash::make($request->password);
        }
        else{
             $privatehash_password=Hash::make(Str::random(24));
        }

        $affiliate_user_id = Cookie::get('affiliate_user_id');
        if($affiliate_user_id === null) $affiliate_user_id = 0;
        //set_agency_config();

        //$affiliate_user_id = 0;
        $package_info = DB::connection('main_db')->table('packages')->where(['is_default'=>'1'])->first();
        $validity = isset($package_info->validity) ? $package_info->validity : 0;
        $package_id = isset($package_info->id) ? $package_info->id : 0;
        $to_date = date('Y-m-d');
        $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
        $curtime = date("Y-m-d H:i:s");
        $userdata = [
            'id' => $user_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $this->hash_password,
            'user_type'=>'Member',
            'package_id'=>$package_id,
            'created_at'=>$curtime,
            'updated_at'=>$curtime,
            'expired_date'=>$expiry_date,
            'last_login_at'=>date('Y-m-d H:i:s'),
            'last_login_ip'=>get_real_ip(),
            'under_which_affiliate_user'=>$affiliate_user_id

        ];
        $user =UserMain::where('id', '=', $user_id)
        ->update($userdata);
        
    }


    public function up_profile_design($request,$main_id)
    {

        Log::debug('before insert new Design Name :'.$request->name);
        Log::debug('before insert new Design Name :'.$request->email);
        if(!empty($request->name) && !empty($request->email) )
        {
			$where = array( 'email' => ($request->email ));
			//$result = $this->Common_DML->get_data( 'users', $where, 'COUNT(*) As total' );
            $result = DB::connection('digitalasset_db')->table('users')->where(['email' => $request->email])->get();
            $total=$result->count();

            if(isset($request->password))
            {
                 $privatehash_password=Hash::make($request->password);
            }
            else{
                 $privatehash_password=Hash::make(Str::random(24));
            }

            if(isset($request->avatar))
            $profile_pic=$request->avatar;
            else if ( isset($request->profileImage) && substr($request->profileImage, 0, 4)=="http")
            $profile_pic=$request->profileImage;
            else if ( isset($request->image) && substr($request->image, 0, 4)=="http")
            $profile_pic=$request->image;
            else
            $profile_pic='';

            Log::debug('while Insert new Design Name found user :'.$total);
			if(!empty($result) &&  $total==0)
            {
				$array = array(
                    'id' => $main_id,
					'name' => trim(($request->name)),
					'email' => trim(($request->email)),
                    'phone_no' => '+xxxxxxxxx',
                    'profile_pic' => $profile_pic,
					'password' => $this->hash_password,
                    'code' => '',
                    'source' => '',
					'status' => 1,
					'datetime' => date('Y-m-d H:i:s', \Carbon\Carbon::now()->timestamp),
                    'remaining_words' => 6500,
                    'remaining_images' => 150
				);

                // change to Laravel insert
				//$insert_id = $this->Common_DML->put_data( 'users', $array );
                $update_id  = DB::connection('digitalasset_db')->table('users')->update($array);


				$folder = 'user_'.$insert_id;
				if (!is_dir('digital_asset/uploads/'.$folder)) {
					mkdir('./digital_asset/uploads/' . $folder, 0777, TRUE);
					mkdir('./digital_asset/uploads/' . $folder . '/campaigns', 0777, TRUE);
					mkdir('./digital_asset/uploads/' . $folder . '/images', 0777, TRUE);
					mkdir('./digital_asset/uploads/' . $folder . '/templates', 0777, TRUE);
				}

				$data = array(
					'user_id' => $insert_id,
					'email' => ($request->email),
					'member_login' => true,
					'access_level' => 1,
					'profile_pic' => '',
					'name' => ($request->name)
				);


				//$this->session->set_userdata( $data );


				//echo json_encode( array( 'status' => 1, 'msg' =>html_escape($this->lang->line('ltr_auth_reset_msg3')), 'url' => base_url() . 'dashboard' ) );	
                //Log::debug('after insert new Design user Status1 : '.json_encode( array( 'status' => 1, 'msg' =>html_escape($this->lang->line('ltr_auth_reset_msg3')), 'url' => base_url() . 'dashboard' ) ));
            }else{
                $update_id  = DB::connection('digitalasset_db')->table('users')->update($array);
				//echo json_encode( array( 'status' => 0, 'msg' =>html_escape($this->lang->line('ltr_auth_reset_msg4'))) );
			}
			
            //die();	
		}
		//echo json_encode( array( 'status' => 0, 'msg' =>html_escape($this->lang->line('ltr_auth_reset_msg5'))) );
		
        //die();

    }


    public function up_profile_mobileAppV2($request,$user_id)
    {

        if(iseet($request->image))
        $user_avatar=$request->image;
        else
        $user_avatar='';


        $assign_plan=PlanMobile::where('price',0)->orderBy('id','asc')->first();
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

            $user =UserMobile::where('id', '=', $user_id)
        ->update($userdata);

    }


    public function up_profile_mobileAppV2_email($request,$user_id,$user_email)
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

            $user =UserMobile::where('email', '=', $user_email)
        ->update($userdata);

    }


    public function up_profile_bio($request,$user_id,$user_email)
    {


    }

    public function up_profile_bio_blog($request,$user_id,$user_email)
    {


    }


    public function up_profile_crm($request,$user_id,$user_email)
    {


    }


 



}
