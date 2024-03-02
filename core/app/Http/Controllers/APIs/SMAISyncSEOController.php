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
use App\Models\SEOWebOption;
use App\Models\PunbotBloggerUser;
use App\Models\SEOBackLinkOption;
use App\Models\PunbotWordpressUser;
use App\Models\PunbotMediumUser;


class SMAISyncSEOController extends Controller
{

    public function cron_seo_on_off_posts($id = '')
    {

        Log::debug("Now Starting SMAISyncSEOController manage_cron_all_posts Controller");
        $status = false;
        $web = Webs::findorFail($id);
        $webSettings = DB::connection('punbotseo_db')->table('ai_automation')->where('website_id', '=', $id)->first();
        //$allPermissionCount = Permission::all()->count();

        Log::debug("after Webs Models ");
        //Log::info($webSettings);

        if (!empty($web)) {

            $web_s = Webs::where('website_id', $id)->first();
            $webSettings_save = SEOAiAutomation::where('website_id', $id)->first();

            if ($webSettings->active == 0) {

                Log::debug('Case 0 ' . $webSettings->active);

                $webSettings_save->active = 1;
                $webSettings_save->save();
                $web_s->user_status = 1;
                $web_s->save();
                $status = true;

            } else {

                Log::debug('Case 1 update to 0 ' . $webSettings->active);
                $webSettings_save->active = 0;
                $webSettings_save->save();
                $web_s->user_status = 0;
                $web_s->save();
                $status = true;
            }

        }

        if ($status) {
            $response['status'] = $status;
            $response['msg'] = __('common.webs_update_success');
            return response()->json($response);
        }

        $response['status'] = $status;
        $response['msg'] = __('common.something_went_wrong');
        return response()->json($response);
    }

    public function import_seo_backlink_punbot($id = '')
    {
        $status = false;
        Log::debug("Now Starting SMAISyncSEOController import_seo_backlink_punbot Controller");
        $status = false;
        $web = Webs::findorFail($id);
        $webSettings = DB::connection('punbotseo_db')->table('ai_automation')->where('website_id', '=', $id)->first();

        if (!empty($web)) {

            $web_s = Webs::where('website_id', $id)->first();
            $user_id = $web_s->user_id;
            $webOptions = SEOWebOption::where('website_id', '=', $id)->first();
            $keyword = $webOptions->keyword;
            $keyword_lang = $webOptions->keyword_lang;
            $keyword_en = $webOptions->keyword_en;
            $keyword_url = $webOptions->url;
            $return_backlink_array = array();

            $platform_table_array = array(
                'blogger_users_info',
                'wordpress_users_info',
                'medium_users_info',

                /*  'facebook_rx_fb_user_info',
                 'facebook_rx_fb_page_info',
                 'facebook_rx_fb_group_info',
                 'twitter_users_info',
                 'linkedin_users_info',
                 'reddit_users_info',
                 'youtube_channel_info', */


                /*
                 'pinterest_users_info',
                'instagram_users_info',
                'tiktok_users_info',
                'vimeo_users_info', */

                /*
               'tumblr_users_info',
               'wix_users_info',
               'weebly_users_info',
               'joomla_users_info', */


                /* 'livejournal_users_info',
               'ghost_users_info',
               'squarespace_users_info',
               'shopify_users_info',
               'magento_users_info',
               'bigcommerce_users_info',
               'opencart_users_info',
               'prestashop_users_info',
               'wocommerce_users_info',
               'drupal_users_info',
               'jimdo_users_info',
               'yola_users_info',
               'webs_users_info',
               'webflow_users_info',
               'strikingly_users_info',
               'godaddy_users_info',
               'webnode_users_info',
               'zoho_users_info',
               'site123_users_info',
               'mozello_users_info',
               'simplesite_users_info',
               'simplero_users_info',
               'silex_users_info',
               'sitebuilder_users_info',
               'sitey_users_info',
               'siterubix_users_info', */

                /*   'dailymotion_users_info',
                  'twitch_users_info',
                  'soundcloud_users_info',
                  'mixcloud_users_info',
                  'spotify_users_info',
                  'anchor_users_info',
                  'podbean_users_info',
                  'buzzsprout_users_info',
                  'blubrry_users_info',
                  'transistor_users_info',
                  'simplecast_users_info',
                  'captivate_users_info',
                  'resonate_users_info',
                  'libsyn_users_info', */
            );

            foreach ($platform_table_array as $platform_table) {
                switch ($platform_table) {
                    case 'blogger_users_info':
                        {
                            $bloggers = PunbotBloggerUser::where('user_id', $user_id)->get();
                            foreach ($bloggers as $blogger) {
                                $access_token = $blogger->access_token;
                                $refresh_token = $blogger->refresh_token;
                                $name = $blogger->name;
                                $email = $blogger->email;
                                $blogger_id = $blogger->id;
                                $username_api = $blogger->blogger_id;
                                $website_id = $id;

                                if ($this->check_double_backlink($user_id, $website_id, $blogger_id, $platform_table) == false)
                                    $new_backlink = SEOBackLinkOption::create([
                                        'website_id' => $id,
                                        'bl_type' => 'backlink',
                                        'platform' => 'blogger',
                                        'plat_form_table' => 'blogger_users_info',
                                        'platform_u_id' => $blogger_id,
                                        'keyword' => $keyword,
                                        'keyword_lang' => $keyword_lang,
                                        'keyword_url' => $keyword_url,
                                        'day_time_post' => '',
                                        'keyword_en' => $keyword_en,
                                        'access_token' => $access_token,
                                        'refresh_token' => $refresh_token,
                                        'email_api' => $email,
                                        'username_api' => $username_api,
                                    ]);


                                if ($new_backlink->id > 0) {

                                    $status = true;
                                    $blacklink = SEOBackLinkOption::where('id', '=', $new_backlink->id)->first();
                                    array_push($return_backlink_array, array(
                                        'column1' => $new_backlink->id,
                                        'column2' => $username_api,
                                        'column3' => $blacklink->active,
                                        'column4' => $keyword,
                                        'column5' => $blacklink->platform,

                                    ));
                                }

                            }

                        }
                        break;

                    case 'wordpress_users_info':
                        {
                            $bloggers = PunbotWordpressUser::where('user_id', $user_id)->get();
                            foreach ($bloggers as $blogger) {
                                $access_token = $blogger->access_token;
                                $refresh_token = '';
                                $name = $blogger->name;
                                $email = '';
                                $blogger_id = $blogger->id;
                                $username_api = $blogger->blog_id;

                                if ($this->check_double_backlink($user_id, $website_id, $blogger_id, $platform_table) == false)
                                    $new_backlink = SEOBackLinkOption::create([
                                        'website_id' => $id,
                                        'bl_type' => 'backlink',
                                        'platform' => 'wordpress',
                                        'plat_form_table' => 'wordpress_users_info',
                                        'platform_u_id' => $blogger_id,
                                        'keyword' => $keyword,
                                        'keyword_lang' => $keyword_lang,
                                        'keyword_url' => $keyword_url,
                                        'day_time_post' => '',
                                        'keyword_en' => $keyword_en,
                                        'access_token' => $access_token,
                                        'refresh_token' => $refresh_token,
                                        'email_api' => $email,
                                        'username_api' => $username_api,
                                    ]);

                                if ($new_backlink->id > 0) {
                                    $status = true;
                                    $blacklink = SEOBackLinkOption::where('id', '=', $new_backlink->id)->first();
                                    array_push($return_backlink_array, array(
                                        'column1' => $new_backlink->id,
                                        'column2' => $username_api,
                                        'column3' => $blacklink->active,
                                        'column4' => $keyword,
                                        'column5' => $blacklink->platform,

                                    ));
                                }

                            }

                        }
                        break;

                    case 'medium_users_info':
                        {
                            $bloggers = PunbotMediumUser::where('user_id', $user_id)->get();
                            foreach ($bloggers as $blogger) {
                                $access_token = $blogger->access_token;
                                $refresh_token = '';
                                $name = $blogger->name;
                                $email = '';
                                $blogger_id = $blogger->id;
                                $username_api = $blogger->medium_id;

                                if ($this->check_double_backlink($user_id, $website_id, $blogger_id, $platform_table) == false)
                                    $new_backlink = SEOBackLinkOption::create([
                                        'website_id' => $id,
                                        'bl_type' => 'backlink',
                                        'platform' => 'medium',
                                        'plat_form_table' => 'medium_users_info',
                                        'platform_u_id' => $blogger_id,
                                        'keyword' => $keyword,
                                        'keyword_lang' => $keyword_lang,
                                        'keyword_url' => $keyword_url,
                                        'day_time_post' => '',
                                        'keyword_en' => $keyword_en,
                                        'access_token' => $access_token,
                                        'refresh_token' => $refresh_token,
                                        'email_api' => $email,
                                        'username_api' => $username_api,
                                    ]);

                                if ($new_backlink->id > 0) {
                                    $status = true;
                                    $blacklink = SEOBackLinkOption::where('id', '=', $new_backlink->id)->first();
                                    array_push($return_backlink_array, array(
                                        'column1' => $new_backlink->id,
                                        'column2' => $username_api,
                                        'column3' => $blacklink->active,
                                        'column4' => $keyword,
                                        'column5' => $blacklink->platform,

                                    ));

                                }

                            }

                        }
                        break;


                }


            }


        }
        if ($status) {
            $response['status'] = $status;
            $response['msg'] = __('common.webs_update_success');
            return response()->json($response);
        }

        $response['status'] = $status;
        $response['msg'] = __('common.something_went_wrong');
        $response['data'] = $return_backlink_array;
        return response()->json($response);

    }

    public function check_double_backlink($user_id, $website_id, $blogger_id, $platform_table)
    {
        $backlink = SEOBackLinkOption::where('website_id', '=', $website_id)->where('platform_u_id', '=', $blogger_id)->where('plat_form_table', '=', $platform_table)->where('user_id', $user_id)->first();
        if (!empty($backlink)) {
            return true;
        } else {
            return false;
        }
    }


}


