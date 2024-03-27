<?php

namespace App\Http\Controllers\APIs;

use Illuminate\Http\Request;

class Plan_ID_FixController extends Controller
{
    //

    public function __construct()
    {
        //$this->middleware('auth:api');
    }


    public function update_team_plan_id_feature()
    {
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
 
    public function update_plan_id_feature($user_id, $user_email, $userdata, $userdata_name, $upFromWhere)
    {
        $golden_tokens = 0;
        $userdata_plan = array();
        Log::debug("Start update Bio Profile to all Platforms in up_plan_main_coin ");


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


            // not working because each plan value not the same
            //$this->update_column_all($userdata_name,$user_id,$user_email);

            if (isset($userdata['plan']))
                $each_plan = Plan::where('id', $userdata['plan'])->orderBy('id', 'asc')->first();
            else
                Log::debug('Error _userdata_plan not set');

            //step2.7.1.1 Update social post
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

            //********fix socialpost plan features    
            $this->socialpost_permission_update_user($user_id, $socialpost_plan);
            //********fix socialpost plan id
            $this->update_column_all($userdata_plan, $user_id, $user_email, 'main_db', 'sp_users');

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
                $result_return = $this->reset_user_Bio($user_id, $user_email, $this->upFromWhere);

                //********fix bio plan id
                $this->update_column_all($userdata_plan, $user_id, $user_email, 'bio_db', 'users');
                
                unset($userdata_plan['plan_id']);
            }

            //mostly it depend on MainCoIn
            $design_plan = $each_plan->design_id;

            $userdata_plan['plan'] = $design_plan;
            //fix design plan id
            $this->update_column_all($userdata_plan, $user_id, $user_email, 'digitalasset_db', 'users');

            //mostly it depend on MainCoIn
            $mobile_plan = $each_plan->mobile_id;
            $userdata_plan['plan'] = $mobile_plan;
            //fix mobile plan id
            $this->update_column_all($userdata_plan, $user_id, $user_email, 'mobileapp_db', 'users');

            //mostly it depend on MainCoIn
            $sync_plan = $each_plan->sync_id;
            $userdata_plan['plan'] = $sync_plan;
            //fix sync plan id
            $this->update_column_all($userdata_plan, $user_id, $user_email, 'sync_db', 'user');


            // prepare for next update
            //for smartcontent.marketing
            $userdata['package_id'] = $main_marketing_id;

        }



    }

}
