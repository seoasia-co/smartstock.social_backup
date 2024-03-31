<?php

namespace App\Http\Controllers\APIs;

use Illuminate\Http\Request;
use App\Http\Controllers\APIs\SMAIUpdateProfileController;

use App\Models\UserMain;
use App\Models\SP_UserCaption;
use App\Models\UserSP;
use App\Models\UserSEO;
use App\Models\UserCourse;
use App\Models\UserDesign;
use App\Models\UserLiveShop;

use App\Models\UserBioBlog;
use App\Models\UserBio;
use App\Models\UserSyncNodeJS;
use App\Models\UserMobile;
use App\Models\PlanBio;
use App\Models\SPTeam;
use App\Models\TokenLogs;
use App\Models\UserCRM;

use App\Models\SubscriptionMain;


class Plan_ID_FixController extends Controller
{
    //
    public $upFromWhere;
    public $obj_SMAIUpdateProfile;

    public function __construct($fromwhere)
    {
        
        $this->upFromWhere=$fromwhere;
        $this->obj_SMAIUpdateProfile = new SMAIUpdateProfileController();
    }

    //fixing fixing becuase the below code is not yet tested
    public function update_team_plan_id_feature()
    {
        //fixing fixing becuase the below code is not yet tested
        $team_users = User::where('team_id', '!=', 0)->get();
        foreach ($team_users as $team_user) {
            $team_user_id = $team_user->id;
            $team_user_email = $team_user->email;
            $team_user_data = $team_user->toArray();
            $team_user_data_name = 'users';
            $team_user_upFromWhere = 'main_coin';
            $this->update_plan_id_feature($team_user_id, $team_user_email, $team_user_data, $team_user_data_name, $team_user_upFromWhere);
        }
    }
 
    public function update_plan_id_feature($userdata, $user_id, $user_email, $case)
    {
        $golden_tokens = 0;
        $userdata_plan = array();
        Log::debug("Start update plan id and feature all Platforms in update_plan_id_feature ");


        //1.update plan to all platforms
        if (isset($userdata['plan']) && ($this->upFromWhere == 'main_coin' || Str::contains($this->upFromWhere, 'MainCoIn_'))) {
            
            //check if user is golden
            if ($userdata['plan'] == 7 || $userdata['plan'] == 4) {
                $golden_tokens = 1;

            }

        }

        
        
         //step2.7.1 Update social post and all platforms as it should be from main config
         if (isset($userdata['plan'])) {

            Log::debug('Yes userdata_plan has been set');
            $userdata_plan = array(
                'plan' => $userdata['plan'],
            );

            if (isset($userdata['plan']))
                $each_plan = Plan::where('id', $userdata['plan'])->orderBy('id', 'asc')->first();
            else
                Log::debug('Error _userdata_plan not set');

            //step2.7.1.1 Update social post
            if (isset($each_plan)) {

                $bio_plan_id = $each_plan->bio_id;
                Log::debug('Found Bio Plan ' . $bio_plan_id);

                $design_plan_id = $each_plan->design_id;
                Log::debug('Case isset $each_plan from Main  true');

                $socialpost_plan = $each_plan->socialpost_id;
                Log::debug('Found Social Plan ' . $socialpost_plan);

                $userdata_plan['plan'] = $socialpost_plan;
                Log::debug('success set userdata_plan ' . $userdata_plan['plan']);

                $package_type_main = $each_plan->package_type;

                Log::debug('and main smartcontent.marketing Package Type ' . $package_type_main);
            }

            if ($socialpost_plan == 0)
                $socialpost_plan = 1;

            //********fix socialpost plan features    
            $this->obj_SMAIUpdateProfile->socialpost_permission_update_user($user_id, $socialpost_plan);
            //********fix socialpost plan id
            $this->obj_SMAIUpdateProfile->update_column_all($userdata_plan, $user_id, $user_email, 'main_db', 'sp_users');

            Log::debug('Success passed socialpost_permission_update_user');
            //eof step2.7.1.1 Update social post


            $main_coin_plan = $userdata['plan'];
            if ($main_coin_plan == 8 || $main_coin_plan == 0) {
                $main_marketing_id = 1;
            } else {
                $main_marketing_id = $main_coin_plan;
            }


            //step2.7.1.2 Update MainCoIn back to free Plan if it's trial end or package end
            if ($main_coin_plan === 0 || $main_coin_plan == 8) {
                //Update MainCoIn back to free Plan
                $main_subscription = SubscriptionMain::where('user_id', $user_id)->where('stripe_status', 'trialing')->latest('id')->first();
                if (isset($main_subscription)) {
                    $main_subscription->stripe_status = 'cancelled';
                    $main_subscription->save();
                }
            }

            //get Bio pln from main if it not set
            if (!isset($bio_plan_id)) {
                //try other ways to get bio plan id
                $bioplan_id_from_main = UserMain::where('id', $user_id)->first();
                $bio_plan = $bioplan_id_from_main->bio_plan;

            } else {

                $bio_plan = $bio_plan_id;
            }

            if ($package_type_main == 'bundle') {


                unset($userdata_plan['plan']);
                $userdata_plan['plan_id'] = $bio_plan;

                //updated Nov2023 add stripcslashes
                //$this->bio_plan_settings_update_user($user_id,$bio_plan);

                //********fix bio plan features
                //fix_user_plan_settings_Bio
                $result_return = $this->obj_SMAIUpdateProfile->reset_user_Bio($user_id, $user_email, $this->upFromWhere);

                //********fix bio plan id
                $this->obj_SMAIUpdateProfile->update_column_all($userdata_plan, $user_id, $user_email, 'bio_db', 'users');
                
                unset($userdata_plan['plan_id']);
            }

            //mostly it depend on MainCoIn
            $design_plan = $each_plan->design_id;

            $userdata_plan['plan'] = $design_plan;
            //fix design plan id
            $this->obj_SMAIUpdateProfile->update_column_all($userdata_plan, $user_id, $user_email, 'digitalasset_db', 'users');

            //mostly it depend on MainCoIn
            $mobile_plan = $each_plan->mobile_id;
            $userdata_plan['plan'] = $mobile_plan;
            //fix mobile plan id
            $this->obj_SMAIUpdateProfile->update_column_all($userdata_plan, $user_id, $user_email, 'mobileapp_db', 'users');

            //mostly it depend on MainCoIn
            $sync_plan = $each_plan->sync_id;
            $userdata_plan['plan'] = $sync_plan;
            //fix sync plan id
            $this->obj_SMAIUpdateProfile->update_column_all($userdata_plan, $user_id, $user_email, 'sync_db', 'user');


            // prepare for next update
            //for smartcontent.marketing
            $userdata['package_id'] = $main_marketing_id;


            $check_main_plan = Plan::where('id', $userdata['plan'])->orderBy('id', 'asc')->first();
            $main_plan_id = $check_main_plan->id;
            $correct_bio_plan = $check_main_plan->bio_id;


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

                $this->obj_SMAIUpdateProfile->update_column_all($user_main_plans_id, $user_id, $user_email, 'main_db', 'users');

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










        }



    }

}
