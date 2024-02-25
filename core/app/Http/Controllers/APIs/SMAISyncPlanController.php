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
use App\Models\UserBio;
use App\Models\UserDesign;
use App\Models\UserDesignSubscriptions;
use App\Models\UserSP;
use App\Models\UserSyncNodeJS;
use App\Models\SubscriptionMain;
use App\Models\SubscriptionBio;





use App\Http\Controllers\APIs\SMAIUpdateProfileController;

class SMAISyncPlanController extends Controller
{

    //Working almost done

    public  function SMAI_Check_Universal_UserPlans($user_id,$database,$platform)
    {

        //1.where's the Plan of each $platform
        //2.when the Plan was or will be changed

        if($database=='main_db' && ($platform=='MainCoIn' || $platform=='maincoin' || $platform=='main_coin'))
        $user =UserMain::where('id', '=', $user_id)->orderBy('id','asc')->first();
        else if($database=='main_db' && $platform=='SocialPost')
        $user =UserSP::where('id', '=', $user_id)->orderBy('id','asc')->first();
        else if($database=='digitalasset_db' && $platform=='Design')
        $user =UserDesign::where('id', '=', $user_id)->orderBy('id','asc')->first();
        else if($database=='mobileapp_db' && $platform=='MobileAppV2')
        $user =UserMobile::where('id', '=', $user_id)->orderBy('id','asc')->first();
        else if($database=='bio_db' && $platform=='Bio')
        $user =UserBio::where('user_id', '=', $user_id)->orderBy('user_id','asc')->first();
        else
        $user=NULL;

        if($user== NULL)
        Log::info('Urgent!!!!!!!! SMAI_Check_Universal_UserPlans: User not found in '.$database.' database with '.$platform.' platform');
        else
        Log::info('SMAI_Check_Universal_UserPlans: User found in '.$database.' database with '.$platform.' platform');
       

       
        if($user!= NULL)
        {


         

        // user plan from SocialPost table sp_users
        if($database=='main_db' && $platform=='SocialPost')
        {
            Log::debug('case main_db and SocialPost with Expired');
        $user_sp_plan=$user->plan;
        }
        else
        {
            Log::debug('case main_db and SocialPost with no Expired');
        $user_sp =UserSP::where('id', '=', $user_id)->orderBy('id','asc')->first();
        $user_sp_plan=$user_sp->plan;    
        }


        // user plan from MobileAppV2 table users
        if($database=='mobileapp_db' && $platform=='MobileAppV2')
        {
            Log::debug('case mobileapp_db and MobileAppV2 with Expired');
        $user_mobile_plan=$user->plan;
        $user_plan_expire=$user->plan_expire_date;
        }
        else
        {
            Log::debug('case mobileapp_db and MobileAppV2 with no Expired');
        $user_mobile =UserMobile::where('id', '=', $user_id)->orderBy('id','asc')->first();
        
            if(isset($user_mobile->plan))
            $user_mobile_plan=$user_mobile->plan;
            else
            {
            $user_mobile_plan=0;
            //$user_mobile->plan=0;
            //$user_mobile->save();

            }
        }

        // user plan from Design table users
        if($database=='digitalasset_db' && $platform=='Design')
        {
            Log::debug('case digitalasset_db and Design with Expired');
        $user_design_plan=$user->plan;
        $user_design_subscibe=UserDesignSubscriptions::where('user_id', '=', $user_id)->orderBy('id','asc')->first();
        $user_plan_expire=$user_design_subscibe->plan_period_end;
        }
        else
        {
            Log::debug('case digitalasset_db and Design with no Expired');
        $user_design =UserDesign::where('id', '=', $user_id)->orderBy('id','asc')->first();
        $user_design_plan=$user_design->plan;
        }

        // user plan from SyncNodeJS table users
        if($database=='sync_db' && $platform=='SyncNodeJS')
        {
            Log::debug('case sync_db and SyncNodeJS with Expired');

        $user_sync_plan=$user->plan;
        $user_plan_expire=$user->planexpire;
        }
        else
        {
            Log::debug('case sync_db and SyncNodeJS with no Expired');
        $user_sync =UserSyncNodeJS::where('id', '=', $user_id)->orderBy('id','asc')->first();
        $user_sync_plan=$user_sync->plan;
        
        }

        // user plan from Main table sp_users
        if($database=='main_db' && ($platform=='MainCoIn' || $platform=='maincoin' || $platform=='main_coin'))
        {
            
            Log::debug('case main_db and MainCoIn with Expired');
            $user_main_plan=$user->plan;
            $user_plan_expire=$user->expired_date;
            Log::debug('Plan FOund in MainCoIn'.$user_main_plan);
            Log::debug('Expired FOund in MainCoIn'.$user_plan_expire);
           
            //fixing the expired date and plan is NULL
            if($user_plan_expire==NULL || $user_main_plan==NULL)
            {
                $user_main_subsccription=SubscriptionMain::where('stripe_status','active')->orWhere('stripe_status', 'trialing')->where('user_id',$user_id)->whereIn('plan_id', [5,7,10,11])->latest()->first();
                
                if(isset($user_main_subsccription->plan_id))
                {
                    $user_main_plan=$user_main_subsccription->plan_id;
                    $user_plan_expire=$user_main_subsccription->ends_at;
                    Log::debug('Expired FOund in MainCoIn is NULL and now fixed new is '.$user_plan_expire);

                    //update the expired date and plan in maincoin uers table
                    $user->plan=$user_main_plan;
                    $user->expired_date=$user_plan_expire;
                    $user->save();

                }
                else
                {
                    $user_main_plan=NULL;
                    $user_plan_expire=NULL;
                    Log::debug('Expired FOund in MainCoIn is NULL and now is still be '.$user_plan_expire);
                }
            
            
            }




        }
        else
        {
            Log::debug('case main_db and MainCoIn with no Expired');
        $user_main =UserMain::where('id', '=', $user_id)->orderBy('id','asc')->first();
        $user_main_plan=$user_main->plan;    
        }

        // user plan from Bio table users
        if($database=='bio_db' && $platform=='Bio')
        {
        Log::debug('case bio_db and Bio with Expired');
        $user_plan=$user->plan_id;
        $user_plan_expire=$user->plan_expiration_date;
        $user_bio_plan=$user->plan_id;
        }
        else
        {
            Log::debug('case bio_db and Bio with no Expired');
            $user_plan=$user->plan;
            $user_bio =UserBio::where('user_id', '=', $user_id)->orderBy('user_id','asc')->first();
            $user_bio_plan=$user_bio->plan_id;
        }
        Log::info('SMAI_Check_Universal_UserPlans: User found in '.$database.' database with '.$platform.' platform and plan and Expired is '.$user_plan.' and '.$user_plan_expire);


        // user plan from MainCoIn   
        //$user_plan=$user->plan;

        //for MobileAppV2 plan_expire_date
        //$user_plan_expire=$user->plan_expire_date;

        //for Bio expiration_date

        //for MainCoIn expired_date



        $return_plan= array(

            "plan_id" => $user_plan,
            "expire" => $user_plan_expire,
            "mobile_plan_id" => $user_mobile_plan,
            "sp_plan_id" => $user_sp_plan,
            "design_plan_id" => $user_design_plan,
            "sync_plan_id" => $user_sync_plan,
            "main_plan_id" => $user_main_plan,
            "bio_plan_id" => $user_bio_plan,



        );


            return $return_plan;
       }
        else{

            return 0;

        }


    }


    public  function SMAI_Update_Universal_UserPlans($request,$user_id,$user_email,$whatup='plan',$upFromWhere=NULL)
    {


        //1.where's the Plan of each $platform
        // try start from Bio Plan that have SmartContent.co.in Main Plan 
        $new_plan_update = NEW SMAIUpdateProfileController($request,$user_id,$user_email,$whatup,$upFromWhere);

        //2.when the Plan was or will be changed






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


public  function SMAI_Check_SocialPost_UserPlans()
    {



    }

}