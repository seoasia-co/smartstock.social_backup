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
use App\Models\SPTeam;
use App\Models\TokenLogs;


use App\Models\UserOpenaiChatMessage;
use App\Models\UserOpenaiChatDesign;
use App\Models\UserOpenaiChatMessageDesign;
use App\Models\UserOpenaiChatMainMarketing;
use App\Models\UserOpenaiChatMessageMainMarketing;
use App\Models\UserOpenaiChatMobile;
use App\Models\UserOpenaiChatMessageMobile;
use App\Models\UserOpenaiChatSocialPost;
use App\Models\UserOpenaiChatMessageSocialPost;

use App\Models\OpenaiGeneratorChatCategory;

use Illuminate\Support\Arr;
use App\Http\Controllers\Auth\SMAISessionAuthController;
use Storage;


use App\Mail\NotificationEmail;
use App\Models\AttachFile;
use App\Models\Banner;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Language;
use App\Models\Map;
use App\Models\Menu;
use App\Models\Photo;
use App\Models\Section;

use App\Models\Topic;
use App\Models\TopicCategory;
use App\Models\Webmail;
use App\Models\WebmasterSection;
use App\Models\WebmasterSetting;
use Helper;

use Illuminate\Pagination\Paginator;
use Mail;
use App\Models\UserBioOpenai;
use App\Models\ImagesBio;
use App\Models\Files_SP;
use App\Models\UserSyncNodeJSOpenai;
use Illuminate\Http\File;
use OpenAI;
use OpenAI\Laravel\Facades\OpenAI as FacadesOpenAI;
use function PHPUnit\Framework\lessThanOrEqual;
use App\Http\Controllers\APIs\SMAIUpdateProfileController;
//use File;

//SEO MOdels
use App\Models\Webs;
use App\Models\SEOAiAutomation;



class SMAISyncSEOController extends Controller
{

    public function cron_seo_on_off_posts($id='')
    {

        Log::debug("Now Starting SMAISyncSEOController manage_cron_all_posts Controller");
        $status = false;
        $web = Webs::findorFail($id);
        $webSettings = DB::connection('punbotseo_db')->table('ai_automation')->where('website_id', '=', $id)->first();
        //$allPermissionCount = Permission::all()->count();
        
        Log::debug("after Webs Models ");
        //Log::info($webSettings);

        if(!empty($web)) {

            $web_s = Webs::where('website_id',$id)->first();
            $webSettings_save = SEOAiAutomation::where('website_id', $id)->first();

            if($webSettings->active == 0) {

                Log::debug('Case 0 '.$webSettings->active);

                $webSettings_save->active = 1;
                $webSettings_save->save();
                $web_s->user_status=1;
                $web_s->save();
                $status = true;

            } else {
               
                Log::debug('Case 1 update to 0 '.$webSettings->active);
                $webSettings_save->active = 0;
                $webSettings_save->save();
                $web_s->user_status=0;
                $web_s->save();
                $status = true;
            }
        
        }

        if($status)
        {
            $response['status'] = $status;
            $response['msg'] = __('common.webs_update_success');
            return response()->json( $response );
        }

        $response['status'] = $status;
        $response['msg'] = __('common.something_went_wrong');
        return response()->json( $response );
    }






}


