<?php

namespace App\Http\Controllers\Auth;

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
use App\Models\PlanMobile;

use App\Models\UserBioBlog;
use App\Models\UserBio;
use App\Models\UserSyncNodeJS;

use App\Models\SettingBio;

use App\Models\Settings;

class SMAISessionAuthController extends Controller
{
    //

    //protected $request; 
    protected $hash_password;
    public $freetrial_plan_images;
    public $freetrial_plan_words;
    // request as an attribute of the controllers
    public function __construct($request_mobile=NULL)
    {
       
        //freetrial images and words
        $plan_main=Plan::where('id',8)->first();
        $this->freetrial_plan_images=$plan_main->total_images;
        $this->freetrial_plan_words =$plan_main->total_words;

        /* if(isset($request->password))
        {
            $this->hash_password=Hash::make($request->password);
        }
        else{
            $this->hash_password=Hash::make(Str::random(24));
        }  */

        if(isset($request_mobile->data))
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
        else  if(isset($request_mobile['password']))
       
        {

            $this->hash_password=Hash::make($request_mobile['password']);
            Log::debug("Found Password form Sign Up: hash ". $this->hash_password);


        }
        else{
            $this->hash_password=Hash::make(Str::random(24));
        } 

        


    }


    public function ids(){
        return uniqid();
    }

    public function db_session_create($user_id=NULL,$session_php=NULL)
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        if(isset($_COOKIE['PHPSESSID']) || $session_php!=NULL) 
       {

        Log::debug(" Found PHP Session ID ".$session_php);
       // dd(Auth::user()->id);
        //socialpost subfolder session login create
       
        
        if($user_id != NULL)
        {
          Auth::loginUsingId($user_id);
          $user_id= $user_id;
        }
        else
        {
          $user_id=Auth::user()->id;
        }

        $team_id = DB::connection('main_db')->table('sp_team')
                ->select('id', 'ids')
                ->where('owner', '=', $user_id)
                ->limit(1)
                ->get();
        $user_uid = DB::connection('main_db')->table('sp_users')
                ->select('id', 'ids')
                ->where('id', '=', $user_id)
                ->limit(1)
                ->get(); 
         
       // dd($team_id[0]->ids);
       // dd($user_uid[0]->ids);


         Log::debug('Team socialpost ID : '.$team_id[0]->ids);
         Log::debug('User socialpost ID : '.$user_uid[0]->ids);        
            
                Session::put('uid', $team_id[0]->ids);
                Session::put('team_id', $user_uid[0]->ids);
                //set_session(["team_id" => $user_uid[0]->ids]);

                if($session_php!=NULL)
                $sessionId = $session_php;
                else if ($_COOKIE['PHPSESSID'] != NULL)
                $sessionId = $_COOKIE['PHPSESSID'];
                else
                $sessionId = Session::getId();


                Log::debug('Debug Current Session get ID : '.$sessionId);
               // dd($sessionId);
               // Log::debug('Debug PHP Session ID : '.$_COOKIE['PHPSESSID']);
                
                $current_session_id = DB::connection('main_db')->table('sessions')
                ->select('pub_id','id','session_PHPSESSID')
                ->where('id', '=', $sessionId)
                ->orderBy('pub_id', 'desc')
                ->limit(1)
                ->get();

               
                $current_time = \Carbon\Carbon::now()->timestamp;

                if($session_php==NULL)
                $session_php=$_COOKIE['PHPSESSID'];

                if(count($current_session_id)>0)
                DB::connection('main_db')->update('update sessions set session_PHPSESSID = ? where id LIKE ? order by pub_id desc', [$session_php,$sessionId]);
                else
                DB::connection('main_db')->insert('insert into sessions (id, session_PHPSESSID, user_id, last_activity, ids, team_id, payload) values (?, ?, ?, ?, ?, ?, ?)', [$sessionId, $session_php, $user_id, $current_time, $user_uid[0]->ids, $team_id[0]->ids, 'payload']);
        
                //eof socialpost subfolder session login create
         }



    } 
    
    
    public function freetrial_socialpost($request,$main_id=null)
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
                     'remaining_words' => $this->freetrial_plan_words,
                     'remaining_images' => $this->freetrial_plan_images,
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
                     'remaining_words' => $this->freetrial_plan_words,
                     'remaining_images' => $this->freetrial_plan_images,
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
        $team_id =  DB::connection('main_db')->table('sp_team')->insert( $save_team);
 
        if(isset($main_id) && $main_id != NULL &&  $main_id != null )
         return -1;
        else
         return $user_id;
          //EOF TODO SocialPost Demo
 
     }



    }

    public function freetrial_mobileApp($request,$user_id)
    {
        if(isset($request->password))
        {
             $privatehash_password=Hash::make($request->password);
        }
        else{
             $privatehash_password=Hash::make(Str::random(24));
        }

        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }

        //TODO Mobile app Demo
        $MobileApp_connected=1;
        if($MobileApp_connected==1)
        {
    
            $assign_plan=Plan::where('price',0)->orderBy('id','asc')->first();
            $userdata = [
                'id' => $user_id,
                'name' => $name_ins,
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
    
    
        //EOF TODO Mobile app Demo
    }

    public function freetrial_main_co_in($request,$user_id)
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
        
         //TODO DEMO

         $APP_Status="Demo";

         $affCode = null;
        if ($request->affiliate_code != null) {
            $affUser = DB::connection('main_db')->table('users')->where('affiliate_code', $request->affiliate_code)->first();
            if ($affUser != null) {
                $affCode = $affUser->id;
            }
        } 

         /* if (env('APP_STATUS') == 'Demo') { */

            if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }


        if($user_id>0)
        {
            $updated_user=0;
            
                $userdata = [
                'id' => $user_id,
                'name' => $name_ins,
                'surname' => $surname_ins,
                'email' => $request->email,
                'email_confirmation_code' => Str::random(67),
                'remaining_words' => $this->freetrial_plan_words,
                'remaining_images' => $this->freetrial_plan_images,
                'password' => $this->hash_password,
                'email_verification_code' => Str::random(67),
                'affiliate_id' => $affCode,
                'affiliate_code' => Str::upper(Str::random(12)),
                'created_at'=>date('Y-m-d H:i:s'),
            ];

            //bug bug add check if user exist and use update

            $user_old=UserMain::where('email', $request->email)->orderBy('id','asc')->get();   

            $found_user= $user_old->count();

          if( $found_user < 1)  
          {
            $user_id =  DB::connection('main_db')->table('users')->insertGetId($userdata);

          }
        }
       else{

        $userdata = [
            
            'name' => $name_ins,
            'surname' => $surname_ins,
            'email' => $request->email,
            'email_confirmation_code' => Str::random(67),
            'remaining_words' => $this->freetrial_plan_words,
            'remaining_images' => $this->freetrial_plan_images,
            'password' => $this->hash_password,
            'email_verification_code' => Str::random(67),
            'affiliate_id' => $affCode,
            'affiliate_code' => Str::upper(Str::random(12)),
        ];

            if($this->check_old_user('main_db','users',$request->email) > 0)
            {
              $user =UserMain::where('id', '=', $user_id)->update($userdata);
            }
            
        }

    }

    public function freetrial_main_marketing($request,$user_id)
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

        if($user_id==1)
        $user_type='Admin';
        else
        $user_type='Member';

        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }

        //$affiliate_user_id = 0;
        $package_info = DB::connection('main_db')->table('packages')->where(['is_default'=>'1'])->first();
        $validity = isset($package_info->validity) ? $package_info->validity : 0;
        $package_id = isset($package_info->id) ? $package_info->id : 0;
        $to_date = date('Y-m-d');
        $expiry_date=date("Y-m-d",strtotime('+'.$validity.' day',strtotime($to_date)));
        $curtime = date("Y-m-d H:i:s");
        $userdata = [
            'id' => $user_id,
            'name' => $name_ins,
            'email' => $request->email,
            'password' => $this->hash_password,
            'user_type'=>$user_type,
            'package_id'=>$package_id,
            'created_at'=>$curtime,
            'updated_at'=>$curtime,
            'expired_date'=>$expiry_date,
            'last_login_at'=>date('Y-m-d H:i:s'),
            'last_login_ip'=>'',
            'under_which_affiliate_user'=>$affiliate_user_id

        ];

        if($this->check_old_user('main_db','users',$request->email) > 0)
        {
            $user =UserMain::where('id', '=', $user_id)
            ->update($userdata);
        }
        
    }


    public function freetrial_design($request,$main_id)
    {

        Log::debug('before insert new Design Name :'.$request->name);
        Log::debug('before insert new Design Name :'.$request->email);
        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }
        if(!empty($name_ins) && !empty($request->email) )
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
					'name' => $name_ins,
					'email' => trim(($request->email)),
                    'phone_no' => '+xxxxxxxxx',
                    'profile_pic' => $profile_pic,
					'password' => $this->hash_password,
                    'code' => '',
                    'source' => '',
					'status' => 1,
					'datetime' => date('Y-m-d H:i:s', \Carbon\Carbon::now()->timestamp),
                    'remaining_words' => $this->freetrial_plan_words,
                    'remaining_images' => $this->freetrial_plan_images
				);

                // change to Laravel insert
				//$insert_id = $this->Common_DML->put_data( 'users', $array );



                if($this->check_old_user('digitalasset_db','users',$request->email) < 1)
                 {
                $insert_id  = DB::connection('digitalasset_db')->table('users')->insert($array);
                 }

            if(isset($insert_id))
            {
				$folder = 'user_'.$insert_id;
                $design_folder = str_replace("smartstock.social","smartcontent.co.in",$_SERVER["DOCUMENT_ROOT"]);

                $design_folder .="/digital_asset/uploads/";

                Log::debug("Design folder from gobal : ");
                Log::info( $design_folder );

				if (!is_dir($design_folder.$folder)) {
					mkdir($design_folder. $folder, 0777, TRUE);
					mkdir($design_folder. $folder . '/campaigns', 0777, TRUE);
					mkdir($design_folder . $folder . '/images', 0777, TRUE);
					mkdir($design_folder. $folder . '/templates', 0777, TRUE);
				}

				$data = array(
					'user_id' => $insert_id,
					'email' => ($request->email),
					'member_login' => true,
					'access_level' => 1,
					'profile_pic' => '',
					'name' => ($request->name)
				);

            }


				//$this->session->set_userdata( $data );


				//echo json_encode( array( 'status' => 1, 'msg' =>html_escape($this->lang->line('ltr_auth_reset_msg3')), 'url' => base_url() . 'dashboard' ) );	
                //Log::debug('after insert new Design user Status1 : '.json_encode( array( 'status' => 1, 'msg' =>html_escape($this->lang->line('ltr_auth_reset_msg3')), 'url' => base_url() . 'dashboard' ) ));
            }else{
				//echo json_encode( array( 'status' => 0, 'msg' =>html_escape($this->lang->line('ltr_auth_reset_msg4'))) );
			}
			
            //die();	
		}
		//echo json_encode( array( 'status' => 0, 'msg' =>html_escape($this->lang->line('ltr_auth_reset_msg5'))) );
		
        //die();

    }


    public function freetrial_mobileAppV2($request,$user_id)
    {

        if(isset($request->image))
        $user_avatar=$request->image;
        else
        $user_avatar='';

        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }


        $assign_plan=PlanMobile::where('plan_price',0)->orderBy('id','asc')->first();
            $userdata = [
                'id' => $user_id,
                'name' => $name_ins,
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

            $user_old=UserMobile::where('email', $request->email)->orderBy('id','asc')->first();   

        if($this->check_old_user('mobileapp_db','users',$request->email) > 0)  
          {
            $user =UserMobile::where('id', '=', $user_id)
             ->update($userdata);
          }
          else{
            $insert_id  = DB::connection('mobileapp_db')->table('users')->insert($userdata);

          }

    }


    public function freetrial_mobileAppV2_email($request,$user_id,$user_email)
    {
        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }

        //$assign_plan=PlanMobile::where('price',0)->orderBy('id','asc')->first();
            $userdata = [
                'id' => $user_id,
                'name' => $name_ins,
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

            $user_old=UserMobile::where('email', $request->email)->orderBy('id','asc')->first();   

          if($user_old->id > 0)  
          {
            $user =UserMobile::where('email', '=', $user_email)
             ->update($userdata);
          }
          else{
            $insert_id  = DB::connection('mobileapp_db')->table('users')->insert($userdata);

          }
    }


   public function freetrial_bio_blog($request,$user_id)
    {
        if(isset($request->username))
        $username_ins=$request->username;
        else
        $username_ins=$request->email;

        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }

        $userdata = [
            'id' => $user_id,
            'name' => $name_ins,
            'email' => $request->email,
            'username' => $username_ins,
            'password' => $this->hash_password,
        ];

        if($this->check_old_user('bio_blog_db','users',$request->email) < 1)  
          {
        $insert_id  = DB::connection('bio_blog_db')->table('users')->insert($userdata);
          }
    }

   public function freetrial_sync_node($request,$user_id)
    {
        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }


        $userdata = [
            'id' => $user_id,
            'uid' =>  Str::random(32),
            'name' => $name_ins,
            'email' => $request->email,
            'password' => $this->hash_password,
        ];

        if($this->check_old_user('sync_db','user',$request->email) < 1)  
          {
        $insert_id  = DB::connection('sync_db')->table('user')->insert($userdata);
          }

    }

   public function freetrial_crm($request,$user_id)
    {
        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }
       
        $userdata = [
            'id' => $user_id,
            'name' => $name_ins,
            'email' => $request->email,
            
        ];
/* 
     $user =LeadsCRM::where('tblleads', '=', $user_email)
    ->update($userdata); */

    if($this->check_old_user('crm_db','tblleads',$request->email) < 1)  
    {
       $insert_id  = DB::connection('crm_db')->table('tblleads')->insert($userdata);
    }

    }



    public function freetrial_social($request,$user_id)
    {
        if(isset($request->username))
        $username_ins=$request->username;
        else
        {
        $username_ins=$request->email;
        }

        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }

        $userdata = [
            'id' => $user_id,
            'name' => $name_ins,
            'email' => $request->email,
            'username' => $username_ins,
            'password' => $this->hash_password,
            'user_role' => 'general',
        ];

        if($this->check_old_user('social_db','users',$request->email) < 1)  
          {
        $insert_id  = DB::connection('social_db')->table('users')->insert($userdata);
          }
    }


    public function freetrial_bio($request,$user_id)
    {


        $free_plan_arr=SettingBio::where('key','plan_free')->orderBy('id','asc')->first();
        
        $free_plan_arr = json_decode($free_plan_arr, true);
        //$free_plan_arr=json_encode($free_plan_arr);
        Log::debug('this is Setting Bio info from DB : ');
        Log::info($free_plan_arr);

        $free_plan=$free_plan_arr['value'];


      /*   foreach ($free_plan_arr['value'] as $key => $value) {
            echo $value["settings"] . "\n";
            $free_plan_setting=json_encode($value["settings"]);
        } */

 


        Log::debug('this is Value Setting Bio info from DB : ');
        Log::info($free_plan);

               
        //$free_plan_array_con = unserialize($free_plan);
        $free_plan_array_con = json_decode($free_plan,true);

        Log::debug('this is Setting dECODE  FROM Main  array : ');
        Log::info($free_plan_array_con);

        //$free_plan_decode=json_encode($free_plan);
        $free_plan_setting=$free_plan_array_con['settings'];

        //$free_plan_setting

       

        Log::debug('this is Setting Bio encode array : ');
        Log::info($free_plan_setting);

        $billing_arr= array(
            "type" => "personal",
            "name" => "",
            "address" => "",
            "city" => "",
            "county" => "",
            "zip" => "",
            "country" => "",
            "phone" => "",
            "tax_id" => "",
        );

        $billing = json_encode(['type' => 'personal', 'name' => '', 'address' => '', 'city' => '', 'county' => '', 'zip' => '', 'country' => '', 'phone' => '', 'tax_id' => '',]);
        $api_key = md5($request->email . microtime() . microtime());
        $referral_key = md5(rand() . $request->email . microtime() . $request->email. microtime());


        $status = 0;
        $source = null;
        $email_activation_code = null;
        $lost_password_code = null;
        $is_newsletter_subscribed = 0;
        $plan_id = 'free';
        $plan_expiration_date = null;
        $timezone = 'UTC';
        $is_admin_created = false;
        $plan_trial_done=0;
        $referred_by=null;
        $language='english';
        $continent_code='AS';
        $country_code=null;
        $city_name=null;

        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }


        $userdata = [

            'user_id' => $user_id,
            'name' => $name_ins,
            'email' => $request->email,
            'password' => $this->hash_password,
            'is_newsletter_subscribed' => 0,
            'plan_id'  => 'free',
            'plan_settings' => json_encode($free_plan_setting),
            'plan_expiration_date' => date('Y-m-d H:i:s', \Carbon\Carbon::now()->timestamp),
            'lost_password_code' => md5($request->email . microtime()),
            'billing' => $billing ,
            'referral_key' => $referral_key ,
            'api_key' =>  $api_key,
            'datetime' => date('Y-m-d H:i:s', \Carbon\Carbon::now()->timestamp),
            'email_activation_code' => $email_activation_code,
            'lost_password_code' => $lost_password_code,
            'plan_trial_done' => $plan_trial_done,
            'referred_by' => $referred_by,
            'language' => $language,
            'timezone' => $timezone,
            'status' => 1,
            'source' => $source,
            'continent_code' => $continent_code,
            'country' => $country_code,
            'city_name' => $city_name,
            'total_logins' => 0,

            
        ];

        if($this->check_old_user('bio_db','users',$request->email) < 1)  
    {

        $insert_id  = DB::connection('bio_db')->table('users')->insert($userdata);
    }


    }

    public function freetrial_course($request,$user_id)
    {
        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }

        $userdata = [
            'id' => $user_id,
            'name' => $name_ins,
            'email' => $request->email,
            'password' => $this->hash_password,
        ];

        if($this->check_old_user('course_db','users',$request->email) < 1)  
        {
        $insert_id  = DB::connection('course_db')->table('users')->insert($userdata);
        }

    }

    public function freetrial_liveshop($request,$user_id)
    {
        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }

        if(strpos($name_ins, " ") !== false)
        {
            $firstname=$this->get_first_last_name($name_ins,'firstname');
            $lastname=$this->get_first_last_name($name_ins,'lastname');

        }
        else{
            $firstname=$name_ins;
            $lastname='';
        }
        $userdata = [
            'id' => $user_id,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'name' => $name_ins,
            'email' => $request->email,
            'password' => $this->hash_password,
            'role_id' => 2,
        ];

        if($this->check_old_user('liveshop_db','users',$request->email) < 1)  
        {
        $insert_id  = DB::connection('liveshop_db')->table('users')->insert($userdata);
        }

    }


    public function freetrial_seo($request,$user_id)
    {
        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }

        if(strpos($name_ins, " ") !== false)
        {
            $firstname=$this->get_first_last_name($name_ins,'firstname');
            $lastname=$this->get_first_last_name($name_ins,'lastname');

        }
        else{
            $firstname=$name_ins;
            $lastname='';
        }


        $userdata = [
            'id' => $user_id,
            'name' => $name_ins,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'email' => $request->email,
            'password' => $this->hash_password,

        ];

        if($this->check_old_user('seo_db','users',$request->email) < 1)  
        {
        $insert_id  = DB::connection('seo_db')->table('users')->insert($userdata);
        }

    }

    public function freetrial_punbot($request,$user_id)
    {
        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }

        if(strpos($name_ins, " ") !== false)
        {
            $firstname=$this->get_first_last_name($name_ins,'firstname');
            $lastname=$this->get_first_last_name($name_ins,'lastname');

        }
        else{
            $firstname=$name_ins;
            $lastname='';
        }

       $password_ins=md5($request->password);
        $userdata = [
            'id' => $user_id,
            'name' => $name_ins,
            'user_type' => 'Member',
            'email' => $request->email,
            'password' => $password_ins,
            'status' => '1',
            'package_id' => '1',
            'deleted' => '0',

        ];

        if($this->check_old_user('punbot_db','users',$request->email) < 1)  
        {
        $insert_id  = DB::connection('punbot_db')->table('users')->insert($userdata);
        }

    }

    public function check_old_user($db,$table,$email)
    {

        if($db=='bio_db')
        $user_old=DB::connection($db)->table($table)->where('email', $email)->orderBy('user_id','asc')->get();   
        else
        $user_old=DB::connection($db)->table($table)->where('email', $email)->orderBy('id','asc')->get();   

        $found_user= $user_old->count();
        return $found_user;

    }

    public function freetrial_user_api($request,$user_id,$raw_password=NULL)
    {
        

        if($request->name!= NULL)
        {
        $name_ins=$request->name;
        } 
        else
        {
           $name_form_email=explode("@",$request->email); 
           $name_ins=$name_form_email[0];
        }

        if($raw_password!=NULL)
        $password_ins=$raw_password;
        else if(isset($request->raw_password))
        $password_ins=$request->raw_password;
        else
        $password_ins=$this->hash_password;

        $userdata = [
            'id' => $user_id,
            'name' => $name_ins,
            'email' => $request->email,
            'password' =>  $password_ins,
        ];

        if($this->check_old_user('mysql','users',$request->email) < 1)  
          {
              $insert_id  = DB::connection('mysql')->table('users')->insert($userdata);
          }
          else{

            Log::debug('this user already in the DB '.$request->email);
          }
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
