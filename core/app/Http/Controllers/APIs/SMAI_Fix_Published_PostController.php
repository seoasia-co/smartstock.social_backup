<?php

//this class for fix the published post that failed to be published in all platforms
//this class will be called by cron job every 5 minutes
//this class will check the published post and if it failed to be published in all platforms it will try to publish it again
// socialpost, punbot, and smartbot.buzz

namespace App\Http\Controllers\APIs;

use Illuminate\Http\Request;

class SMAI_Fix_Published_PostController extends Controller
{
    //

    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    public function socialpost_fix()
    {
        //fixing fixing becuase the below code is not yet tested
        $socialposts = SocialPost::where('status', 'published')->get();
        foreach ($socialposts as $socialpost) {
            $socialpost_id = $socialpost->id;
            $socialpost_data = $socialpost->toArray();
            $socialpost_data_name = 'socialposts';
            $socialpost_upFromWhere = 'main_coin';
            $this->socialpost_fix($socialpost_id, $socialpost_data, $socialpost_data_name, $socialpost_upFromWhere);
        }
    }




}
