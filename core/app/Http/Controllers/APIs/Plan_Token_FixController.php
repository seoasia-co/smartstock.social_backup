<?php

namespace App\Http\Controllers\APIs;

use Illuminate\Http\Request;

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
use App\Models\User;

use App\Models\SubscriptionMain;
use App\Models\SubscriptionBio;

use Log;
use Session;
use Cookie;
use Carbon\Carbon;
use stdClass;
use Str;
use Storage;
use App\Models\Plan;
use App\Models\PlanMobile;


class Plan_Token_FixController  extends \App\Http\Controllers\Controller
{
    public $upFromWhere;
    public $obj_SMAIUpdateProfile;

    public function __construct()
    {
        
    }

            public function setUp($fromwhere)
        {
            Log::debug("Start Plan_ID_FixController setUp ".$fromwhere);
                
            $this->upFromWhere = $fromwhere;
            $this->obj_SMAIUpdateProfile = new SMAIUpdateProfileController();
        }

   //fixing fixing becuase the below code is not yet tested
    public function team_plan_token_fix()
    {
        //fixing fixing becuase the below code is not yet tested
        $team_users = User::where('team_id', '!=', 0)->get();
        foreach ($team_users as $team_user) {
            $team_user_id = $team_user->id;
            $team_user_email = $team_user->email;
            $team_user_data = $team_user->toArray();
            $team_user_data_name = 'users';
            $team_user_upFromWhere = 'main_coin';
            $this->plan_token_fix($team_user_id, $team_user_email, $team_user_data, $team_user_data_name, $team_user_upFromWhere);
        }
    }

    public function update_team_fix_token($userdata, $user_id, $user_email, $case)
    {
        //use the example from function update_fix_token
        //but step 1. receive api call from Main.co.in to update Token for user who was invited to use Team Plan
        //step 2. check how many Token from api that user has and how many Token that Team Manager has




    }


    //เพิ่มเงื่อนไขการเพิ่ม Token เชื่อมโยงกับการสมัคร Plan ใหม่ subscription id
    //if subscription id had already added then no need to add Token again
    public function update_fix_token($userdata, $user_id, $user_email, $case)
    {


        try {
            
            $Mainuser = UserMain::where('id', $user_id)->first();
        
            if ($Mainuser) {
                // Log the user information
                Log::debug('User found:', ['id' => $Mainuser->id, 'name' => $Mainuser->name]);
        
                //$Mainuser = UserMain::where('id', $user_id)->first();
                $Mainuser->token_upgraded = 1;
                
                //check case Team Plan
                $Plan_Mainuser = Plan::where('id', $Mainuser->plan);
                $Checked_IsTeam = $Plan_Mainuser->is_team_plan_child;

        if ($Checked_IsTeam > 0 && ($Plan_Mainuser->id >= 200 && $Plan_Mainuser->id < 300)) {
            // Use Team Manager ID in case the Plan is Team type
            $Team_Manager_User_id = $Mainuser->parent_user_id;

        }
            } else {
                Log::debug('User not found for ID: ' . $user_id);
            }
        } catch (\Exception $e) {
            // Handle any exceptions (e.g., database connection issues)
            Log::error('Error fetching user: ' . $e->getMessage());
        }

        
        //bof fixing fixing becuase the below code is not yet tested
        if (isset($Team_Manager_User_id) && $Team_Manager_User_id > 0)
        $find_user_id = $Team_Manager_User_id;
        else
        $find_user_id = $user_id;



    $where_payment_bundle_from = SubscriptionMain::where('stripe_status', 'active')->orWhere('stripe_status', 'trialing')->where('user_id', $find_user_id)->latest()->first();


    if (isset($Team_Manager_User_id) && $Team_Manager_User_id > 0) {
        $return_token_update = $this->update_token_centralize($user_id, $user_email, $token_array, $usage, $from, $old_reamaining_word, $old_reamaining_image, $chatGPT_catgory, $token_update_type, $token_plus_array, $case);
    } else {
        //check if Subscription ID never add Tokens for start up Package

        if (isset($where_payment_bundle_from->id) && $where_payment_bundle_from->id > 0) {
            $token_id_added = $where_payment_bundle_from->id;
            $token_log_added = TokenLogs::where('token_log_added', $token_id_added)->first();

        }

        if ($token_log_added !== null && $token_log_added->id > 0) {
            Log::debug('This Subscription ID  ' . $token_id_added . ' was ever added the StartUp Tokens of Plan so SKIP Tokens start Up add');
        }
            else
        {



        //start update Token for new Subscription ID (new payment or transaction)
        $user_old_data = UserMain::where('id', $user_id)->orderBy('id', 'asc')->first();
        //fixing move to Main Plan only
        $central_remaining = UserDesign::where('id', $user_id)->first();
       


        //defind remaining_words
        //fix when call from plan sync
        $userdata['remaining_words'] = $user_old_data->remaining_words;
        $userdata['remaining_images'] = $user_old_data->remaining_images;
        

        //defind others old mobile old Main
        $userdata['total_words'] = $user_old_data->total_words;
        $userdata['total_images'] = $user_old_data->total_images;

        $userdata['expiration_date'] = $user_old_data->plan_expiration_date;
        $userdata['plan_expire_date'] = $user_old_data->plan_expiration_date;
        $userdata['expired_date'] = $user_old_data->plan_expiration_date;

        $userdata['available_words'] = $user_old_data->available_words;
        $userdata['available_images'] = $user_old_data->available_images;

        
        //add condition if TokensLog not yet added this transaction id then  add remaining_num tokens
       //add to this if
       
        if (isset($userdata['remaining_words'])) {

                         
            // add then column name  token_upgraded and token_downgraded to users table
            // set to 0 if no yet added and set to 1 if added when token_downgraded==1 and token_upgraded==1 that mean time to reset
            $check_main_plan = Plan::where('id', $userdata['plan'])->orderBy('id', 'asc')->first();
            $main_plan_id = $check_main_plan->id;


            //start check and fix Bio Plan
            $correct_bio_plan = $check_main_plan->bio_id;

            if ($main_plan_id == 8 || $main_plan_id == 0) {
                $plan_bio_check_id = 0;
            } else {
                $cur_plan_bio = UserBio::where('user_id', $user_id)->first();

                $plan_bio_check_id = $cur_plan_bio->plan_id;
            }

            //for Bio Token
            $check_plus_remaining = PlanBio::where('plan_id', $plan_bio_check_id)->orderBy('plan_id', 'asc')->first();

            //check if Tokens from package is Golden Tokens
            if (isset($golden_tokens) && $golden_tokens == 1) {
                //add tokens to  main Golden Tokens
                //2. should add? golden_freeze_date , golden_expired_date
                $golden_tokens_save = array(

                    'golden_tokens' => $plus_remaining_images + $plus_remaining_words,
                );

                $this->obj_SMAIUpdateProfile->update_column_all($golden_tokens_save, $user_id, $user_email, 'main_db', 'users');
            }



            // if main Plan have extra Token to Plus +
            //Step 1. Check Where is the Bundle payment or transaction subscription come from
            //2. check column main_token_synced, bio_token_synced


            //$where_payment_bundle_from=SubscriptionMain::where('stripe_status','active')->orWhere('stripe_status', 'trialing')->where('user_id',$user_id)->whereIn('plan_id', [5,7,10,11])->latest()->first();
            $where_payment_bundle_from = SubscriptionMain::where('stripe_status', 'active')->orWhere('stripe_status', 'trialing')->where('user_id', $user_id)->latest()->first();
            if ($where_payment_bundle_from) {
                $from_payment = 'SubscriptionMain';
                Log::debug('FOund bundle subscription in Main then use it');

            }
      

            $bio_token_synced = $where_payment_bundle_from->bio_token_sync;
            $main_token_synced = $where_payment_bundle_from->main_token_sync;


            //bug now in main_coin is main_token_sync=0 so it should be 1 and update when in mail Paypal or Stripe is success
            //fixing fixing Double check by add Subscription ID to TokenLogs table
            //to record that reamining_words or images already added

            //this is triple check that Token won't be added again
            if ($main_token_synced == 0) {
                $plus_remaining_images = $check_main_plan->total_images;
                $plus_remaining_words = $check_main_plan->total_words;
            } else {
                $plus_remaining_images = 0;
                $plus_remaining_words = 0;
            }
            Log::debug('After checked $main_token_synced $plus_remaining_words in Main value is ' . $plus_remaining_words);

           //this is triple check that Token won't be added again
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

            } else {

                if ($user_old_data->remaining_words == $central_remaining->remaining_words + $plus_remaining_words)
                    Log::debug('The Old remaining Main = Central maybe because subscription still keep Token reason');
                else
                    Log::debug('The Old remaining Main > Central maybe because  subscription already added Token reason and Maybe Error and this Sync happend outside upgrading in Main or someting Error!!!!!!!!!! ');
                
                    
                    Log::debug('Case user_old_data->remaining_words >= central_remaining->remaining_words + plus_remaining_words');
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
            //mobile app
            $this->obj_SMAIUpdateProfile->update_column_all($userdata_mobile_plan_array, $user_id, $user_email, 'mobileapp_db', 'users');

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

           
            //step2 final centralize token choose one of them
            //$this->obj_SMAIUpdateProfile->plans_token_centralize($user_id, $user_email, $token_array, $usage = 0, $from = 'main_coin', $old_reamaining_word, $old_reamaining_image, $chatGPT_catgory = "PlanUpgrade_from_MainCoIn", $token_update_type = 'both', $expired, $design_plan_id, $token_plus_array, $from_payment, $case);

        }
            //eof update Token for new Subscription ID (new payment or transaction)

            if(!isset($usage))
            $usage=0;

            if(!isset($from))
            $from=$this->upFromWhere;
            
            //step2 final centralize token choose one of them
            $return_token_update =$this->obj_SMAIUpdateProfile->update_token_centralize($user_id, $user_email, $token_array, $usage, $from, $old_reamaining_word, $old_reamaining_image, $chatGPT_catgory = "PlanUpgrade_from_MainCoIn", $token_update_type = 'both', $token_plus_array, $case, $where_payment_bundle_from->id);

            
            //fix the others monthy limit value อัพเดทค่าอื่นๆเพิ่มเติม นอกเหนือจาก remaining_images and words
            //and where_payment_bundle_from->bio_token_sync += 1
            //where_payment_bundle_from->main_token_sync += 1
            if ($return_token_update['log_token_id'] != NULL || $return_token_update['log_token_plus_id'] != NULL || $return_token_update['log_token_plus_id2'] != NULL) {

                if ($from_payment == 'SubscriptionMain') {
    
                    $Mainuser = UserMain::where('id', $user_id)->first();
    
                    //search Subscripttion from $user_id or Team Manager ID
                    $where_payment_bundle_from = SubscriptionMain::where('stripe_status', 'active')->orWhere('stripe_status', 'trialing')->where('user_id', $find_user_id)->latest()->first();
    
    
                    if ($where_payment_bundle_from->bio_token_sync == NULL || $where_payment_bundle_from->bio_token_sync < 1) {
    
                        if ($where_payment_bundle_from->bio_token_sync == NULL)
                            $where_payment_bundle_from->bio_token_sync = 0;
    
                        if ($where_payment_bundle_from->main_token_sync == NULL)
                            $where_payment_bundle_from->main_token_sync = 0;
    
                        //then plus times How many Tokens (remaining_words,remaining_images)
                        $where_payment_bundle_from->bio_token_sync += 1;
                        $where_payment_bundle_from->main_token_sync += 1;
    
    
                        $user_main_plan_id = $Mainuser->plan;
                        $user_bio_plan = PlanBio::where('main_plan_id', $user_main_plan_id)->first();
                        $user_bio_plan_id = $user_bio_plan->plan_id;
    
                       
                        $start_bio_synthesized_characters_per_month_limit = $this->obj_SMAIUpdateProfile->get_bio_plan_settings('synthesized_characters_per_month_limit', $user_bio_plan_id);
                        Log::debug('Start Bio Synthesized Characters Per Month Limit : ' . $start_bio_synthesized_characters_per_month_limit);
                        $start_bio_transcriptions_per_month_limit = $this->obj_SMAIUpdateProfile->get_bio_plan_settings('transcriptions_per_month_limit', $user_bio_plan_id);
                        Log::debug('Start Bio Transcriptions Per Month Limit : ' . $start_bio_transcriptions_per_month_limit);
    
                        $stat_bio_chats_per_month_limit = $this->obj_SMAIUpdateProfile->get_bio_plan_settings('chats_per_month_limit', $user_bio_plan_id);
    
                       //bof the others monthy limit value of Bio Plan
                       //add the others monthy limit value of others platforms here 

                        $Biouser = UserBio::where('user_id', $user_id)->first();
    
                        if ($stat_bio_chats_per_month_limit != NULL)
                            $Biouser->chats_per_month_limit = $stat_bio_chats_per_month_limit;
    
    
                        if ($start_bio_synthesized_characters_per_month_limit != NULL)
                            $Biouser->synthesized_characters_per_month_limit = $start_bio_synthesized_characters_per_month_limit;
    
                        if ($start_bio_transcriptions_per_month_limit != NULL)
                            $Biouser->transcriptions_per_month_limit = $start_bio_transcriptions_per_month_limit;
    
                        //eof the others monthy limit value of Bio Plan
                   
    
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
            
        
        
        
        }



    }
    







    }



}


