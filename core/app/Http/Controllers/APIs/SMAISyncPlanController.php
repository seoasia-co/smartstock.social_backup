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


use App\Http\Controllers\APIs\SMAIUpdateProfileController;

class SMAISyncPlanController extends Controller
{

    //Working almost done

    public  function SMAI_Check_Universal_UserPlans($user_id,$database,$platform)
    {

        //1.where's the Plan of each $platform
        //2.when the Plan was or will be changed

        if($database=='main_db' && $platform=='MainCoIn')
        $user =UserMain::where('id', '=', $user_id)->orderBy('id','asc')->first();
        else if($database=='main_db' && $platform=='SocialPost')
        $user =UserSP::where('id', '=', $user_id)->orderBy('id','asc')->first();
        else if($database=='digitalasset_db' && $platform=='Design')
        $user =UserDesign::where('id', '=', $user_id)->orderBy('id','asc')->first();
        else if($database=='digitalasset_db' && $platform=='MobileAppV2')
        $user =UserMobile::where('id', '=', $user_id)->orderBy('id','asc')->first();
        else
        $user=NULL;

       
        if($user!= NULL)
        {


        $user_plan=$user->plan;
        $user_plan_expire=$user->plan_expire_date;
        $user_sp_plan=$user->sp_plan;
        $user_mobile_plan=$user->mobile_plan;
        $user_design_plan=$user->design_plan;
        $user_sync_plan=$user->sync_plan;



        $return_plan= array(

            "plan_id" => $user_plan,
            "expire" => $user_plan_expire,
            "mobile_plan_id" => $user_mobile_plan,
            "sp_plan_id" => $user_sp_plan,


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