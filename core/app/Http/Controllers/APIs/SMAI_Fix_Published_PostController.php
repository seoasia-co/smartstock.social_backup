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
}
