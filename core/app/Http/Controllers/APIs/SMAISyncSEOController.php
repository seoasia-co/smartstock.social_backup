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

use App\Models\PostPunbotSEO;

//import old function from punbot/seo
use App\Http\Controllers\SMAI_SEO_PUNBOTController;

use App\Models\PicStat;
use PDO;

class SMAISyncSEOController extends Controller
{

    private $seo_fnc;
    private $conn;

    public function __construct()
    {
        $this->seo_fnc=NEW SMAI_SEO_PUNBOTController();

        date_default_timezone_set('Asia/Bangkok');
          // Host Name
          $db_hostname = 'localhost';
          // Database Name
          $db_name = 'cafealth_punbot_seo';
          // Database Username
          $db_username = 'cafealth_punbot_seo';
          // Database Password
          $db_password = 'YSvKdba1e2}k';
          try {

              $conn = new PDO("mysql:host=$db_hostname;dbname=$db_name",$db_username,$db_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
              $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
              $this->conn = $conn;
                
          }
          catch(PDOException $e){
              echo $e->getMessage('utf8mb4');
          }
    }



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

    public function cron_update_thumnail($cron_seo = 0,)
    {
        $conn=$this->conn;
        $case_local_img = 0;
        Log::debug("\n\n<br> Debug before enter MySQL Loop Qry ");


        if ($cron_seo == 1) {
            Log::debug("\n\n<br> Debug after enter MySQL Loop Qry ");
            // Check the id is valid or not


        /*    $image_status = 'default.png';
            $statement = $conn->prepare("SELECT * FROM posts WHERE post_image=? ORDER BY post_id DESC LIMIT 1");
            $statement->execute(array($image_status));
            $total = $statement->rowCount();
            $result = $statement->fetch(PDO::FETCH_ASSOC);*/


            //convert to Eloquent Way
            $image_status = 'default.png';

            $post = PostPunbotSEO::where('post_image', $image_status)->orderBy('post_id', 'desc')->first();

            if ($post === null) {
                Log::debug ("\n\n> Debug after Qry Found No Thing");
                // in laravel you may want to redirect like this instead of header() function

            }

            Log::debug ("\n\n> Debug after  Qry Found Total");


                Log::debug("\n\n<br> Debug after  Qry Found Post : ");
                /*if ($post->post_version == 'original') {*/
                    if($post->post_version === 'original') {

                    Log::debug("THis is Original : Case Pixarbay");

                    //locale th-TH

                    Log::debug("\n\n<br> Debug now Edit Original Version Post ID : " . $post->post_id);
                    $edit_post_id = $post->post_id;
                    Log::debug("THis is Non-Original : Case Pexles of Keyword : ");


                    $keyword = $post->keyword;
                    $siteid = $post->website_id;
                    $keyword_en = $this->seo_fnc->get_cur_keyword_en($siteid, $keyword, $conn);

                    Log::info($keyword);
                    Log::debug("\n\n<br>");
                    $edit_post_version = $post->post_version;

                    /* $key_lang=gg_translate_detectv3($keyword);

                               if($key_lang=='th')
                                $locale='th-TH';
                                else
                                $locale='en-US'; */
                    $locale = 'en-US';


                    Log::debug("\n\n<br> Debug locale : " . $locale);
                    $totalpage = 100000;
                    $title = $post->post_title;
                    $title = $this->seo_fnc->clean_file_title($title);

                    //switch between source pixarbay pexels and more .. bla bla bla
                    $what_check = 'status';


                    $check_local_img_stock = $this->seo_fnc->check_local_img($keyword, $conn);

                    Log::debug("\n\n Debog ID local Image " . $check_local_img_stock);
                    if ($this->seo_fnc->pixarbay_get_image_arr_check($what_check, $keyword_en) == 200) {
                        Log::debug('\n\n Case Pixabay Image \n\n');
                        $case_local_img = 0;
                        $perpage = 200;
                        $source_name = 'pixabay';
                        //$get_origi_post_img= explode(",", pexels_get_image_arr( $keyword,$keyword_en, $perpage, $totalpage, $title, $counts, $locale, $edit_post_version ));
                        $counts = $this->seo_fnc->get_cur_img_count($keyword, $source_name, $conn);
                        Log::debug("\n\n<br> Debug Pixabay Count " . $counts);
                        $get_origi_post_img = explode(",", $this->seo_fnc->pixarbay_get_image_arr($keyword, $keyword_en, $perpage, $totalpage, $title, $counts, $edit_post_version, $conn));


                    } else {


                        if ($check_local_img_stock > 0) {
                            Log::debug('\n\n Case Local Image \n\n');
                            $case_local_img = 1;

                            $local_img_id = $check_local_img_stock;

                            $get_origi_post_img = $this->seo_fnc->get_origi_post_img_fromLocal($local_img_id, $keyword, $title, $conn);
                            $source_name = $get_origi_post_img[10];
                        } else {
                            Log::debug('\n\n Case Pexels Image \n\n');


                            $perpage = 80;
                            $source_name = 'pexels';
                            $counts = $this->seo_fnc->get_cur_img_count($keyword, $source_name, $conn);
                            Log::debug("\n\n<br> Debug Pexels Count " . $counts);
                            //$get_origi_post_img= explode(",", pixarbay_get_image_arr( $keyword, $perpage, $totalpage, $title, $counts));
                            $get_origi_post_img = explode(",", $this->seo_fnc->pexels_get_image_arr($keyword, $keyword_en, $perpage, $totalpage, $title, $counts, $locale, $edit_post_version, $conn));


                        }

                    }


                    $source_id = $get_origi_post_img[0];

                    $source_img = $get_origi_post_img[1];
                    $source_small = $get_origi_post_img[2];
                    $source_mid = $get_origi_post_img[3];
                    $source_large = $get_origi_post_img[4];
                    $source_author = $get_origi_post_img[5];
                    $source_author_id = $get_origi_post_img[6];
                    $source_hd = $get_origi_post_img[7];
                    $source_original = $get_origi_post_img[8];
                    $source_tag = $get_origi_post_img[9];

                    if (isset($get_origi_post_img[10])) {
                        $source_tag .= ',' . $get_origi_post_img[10];
                        $source_tag .= ',' . $get_origi_post_img[11];
                    }

                    $all_get_origi_post_img = count($get_origi_post_img);
                    $search_keyword_postion = $all_get_origi_post_img - 1;
                    $img_search_keyword = $get_origi_post_img[$search_keyword_postion];


                    $counts_new = $this->seo_fnc->get_cur_img_count($keyword, $source_name, $conn) + 1;
                    //print_r($get_origi_post_img);
                    Log::debug(print_r($get_origi_post_img, true));

                    //insert count if success
                    Log::debug('\n\n<br>Debug Name of Image Save ' . $source_img);
                    $word_check = ".jpg";
                    //$word_check="99999999999999999999999999999999";
                    if (strlen($img_search_keyword) < 1)
                        $img_search_keyword = $keyword_en;


                        if (strpos($source_img, $word_check) !== false && strlen($source_small) > 5) {
                            Log::debug("\n\n>check success and Updating");
                        
                            // Insert into pic_stat
                            DB::connection('punbotseo_db')->table('pic_stat')->insert([
                                'source_id' => $source_id, 
                                'source' => $source_name, 
                                'post_image' => $source_img, 
                                'small' => $source_small, 
                                'mid' => $source_mid, 
                                'large' => $source_large, 
                                'author' => $source_author, 
                                'author_id' => $source_author_id, 
                                'counts' => $counts_new, 
                                'post_id' => $edit_post_id, 
                                'keywords' => $keyword, 
                                'hd_size' => $source_hd, 
                                'original_size' => $source_original, 
                                'tag' => $source_tag
                            ]);
                            
                            // Update posts
                            PostPunbotSEO::where('post_id', $edit_post_id)
                                         ->update(['post_image' => $source_img]);
                        
                            //$_SESSION['success'] = 'Image Stat insert successfully & Post has been updated successfully!';
                            Log::debug("\n\n>check success and Insert & Updated Done ");
                        }

                } else {
                    Log::debug("\n\n !!!!!! THiS is not Original : Case Pexels.com or Upsprash !!!!!!!");


                    //locale th-TH

                    Log::debug("\n\n<br> Debug now Edit Post ID : " . $post->post_id);
                    $edit_post_id = $post->post_id;
                    Log::debug("THis is Non-Original : Case Pexles of Keyword : ");
                    $keyword = $post->keyword;
                    $siteid = $post->website_id;
                    $keyword_en = $this->seo_fnc->get_cur_keyword_en($siteid, $keyword, $conn);
                    Log::info($keyword);
                    Log::debug("\n\n<br>");
                    $edit_post_version = $post->post_version;


                    /* $key_lang=gg_translate_detectv3($keyword);

                               if($key_lang=='th')
                                $locale='th-TH';
                                else
                                $locale='en-US'; */


                    $locale = 'en-US';
                    Log::debug("\n\n<br> Debug locale : " . $locale);
                    $totalpage = 100000;
                    $title = $post->post_title;
                    $title = $this->seo_fnc->clean_file_title($title);


                    //switch between source pixarbay pexels and more .. bla bla bla
                    $what_check = 'status';
                    if ($this->seo_fnc->unsplash_get_image_arr_check($what_check, $keyword_en) == 200) {
                        $perpage = 30;
                        $source_name = 'unsplash';

                        $counts = $this->seo_fnc->get_cur_img_count($keyword, $source_name, $conn);
                        Log::debug("\n\n<br> Debug Unsplash Count " . $countsa);
                        //$get_origi_post_img= explode(",", pixarbay_get_image_arr( $keyword, $perpage, $totalpage, $title, $counts));
                        $get_origi_post_img = explode(",", $this->seo_fnc->unsplash_get_image_arr($keyword, $keyword_en, $perpage, $totalpage, $title, $counts, $edit_post_version, $conn));
                    } else if ($this->seo_fnc->pexels_get_image_arr_check($what_check, $keyword) == 200) {
                        $perpage = 80;
                        $source_name = 'pexels';

                        $counts = $this->seo_fnc->get_cur_img_count($keyword, $source_name, $conn);
                        Log::debug("\n\n<br> Debug Pexels Count " . $counts);
                        //$get_origi_post_img= explode(",", pixarbay_get_image_arr( $keyword, $perpage, $totalpage, $title, $counts));
                        $get_origi_post_img = explode(",", $this->seo_fnc->pexels_get_image_arr($keyword, $keyword_en, $perpage, $totalpage, $title, $counts, $locale, $edit_post_version, $conn));
                    } else if ($this->seo_fnc->pixarbay_get_image_arr_check($what_check, $keyword) == 200) {

                        $perpage = 200;
                        $source_name = 'pixabay';
                        //$get_origi_post_img= explode(",", pexels_get_image_arr( $keyword,$keyword_en, $perpage, $totalpage, $title, $counts, $locale, $edit_post_version ));
                        $counts = $this->seo_fnc->get_cur_img_count($keyword, $source_name, $conn);
                        Log::debug("\n\n<br> Debug Pixabay Count " . $counts);
                        $get_origi_post_img = explode(",", $this->seo_fnc->pixarbay_get_image_arr($keyword, $keyword_en, $perpage, $totalpage, $title, $counts, $edit_post_version, $conn));


                    } else {
                        $check_local_img_stock = $this->seo_fnc->check_local_img($keyword, $conn);
                        if ($check_local_img_stock > 0) {
                            Log::debug("\n\n Debog ID local Image " . $check_local_img_stock);
                            $local_img_id = $check_local_img_stock;
                            $case_local_img = 1;

                            $get_origi_post_img = $this->seo_fnc->get_origi_post_img_fromLocal($local_img_id, $keyword, $title, $conn);

                            $source_name = $get_origi_post_img[10];
                        }

                    }


                    $source_id = $get_origi_post_img[0];

                    $source_img = $get_origi_post_img[1];
                    $source_small = $get_origi_post_img[2];
                    $source_mid = $get_origi_post_img[3];
                    $source_large = $get_origi_post_img[4];
                    $source_author = $get_origi_post_img[5];
                    $source_author_id = $get_origi_post_img[6];
                    $source_hd = $get_origi_post_img[7];
                    $source_original = $get_origi_post_img[8];
                    $source_tag = $get_origi_post_img[9];

                    if (isset($get_origi_post_img[10])) {
                        $source_tag .= ',' . $get_origi_post_img[10];
                        $source_tag .= ',' . $get_origi_post_img[11];
                    }

                    $all_get_origi_post_img = count($get_origi_post_img);
                    $search_keyword_postion = $all_get_origi_post_img - 1;
                    $img_search_keyword = $get_origi_post_img[$search_keyword_postion];


                    $counts_new = $this->seo_fnc->get_cur_img_count($keyword, $source_name, $conn) + 1;
                    //print_r($get_origi_post_img);
                    Log::debug(print_r($get_origi_post_img, true));

                    //insert count if success
                    Log::debug('\n\n<br>Debug Name of Image Save ' . $source_img);
                    $word_check = ".jpg";
                    //$word_check="99999999999999999999999999999999";
                    if (strlen($img_search_keyword) < 1)
                        $img_search_keyword = $keyword_en;


                    if (strpos($source_img, $word_check) !== false && strlen($source_small) > 5) {
                        Log::debug("\n\n<br>check success and Updating");

                       clearstatcache();
                        $img_file_size = filesize('./uploads/posts/' . $source_img);

                        if ($case_local_img > 0) {


                            if ($img_file_size > 1024) {
                                PicStat::where('id', $local_img_id)->update([
                                    'post_image' => $source_img,
                                    'post_id' => $edit_post_id,
                                    'counts' => $counts_new
                                ]);
                            
                                Log::debug("\n\n>check success Updated Post Stat Done of Post ID " . $edit_post_id);
                            
                            } else {
                                $source_img = 'expired.png';
                                PicStat::where('id', $local_img_id)->update([
                                    'post_image' => $source_img,
                                    'post_id' => $edit_post_id,
                                    'counts' => $counts_new
                                ]);
                            
                                Log::debug("\n\n>check success Updated Post Stat Done of Post ID " . $edit_post_id);
                            }
                            
                            $picsStat = PicStat::find($local_img_id);
                            
                            if ($picsStat === null) {
                                DB::connection('punbotseo_db')->table('pic_stat')->insert([
                                    'source_id' => $source_id,
                                    'source' => $source_name,
                                    'post_image' => $source_img,
                                    'small' => $source_small,
                                    'mid' => $source_mid,
                                    'large' => $source_large,
                                    'author' => $source_author,
                                    'author_id' => $source_author_id,
                                    'counts' => $counts_new,
                                    'post_id' => $edit_post_id,
                                    'keywords' => $keyword,
                                    'hd_size' => $source_hd,
                                    'original_size' => $source_original,
                                    'tag' => $source_tag,
                                    'search_keywords' => $img_search_keyword
                                ]);
                            }

                         }

                         //eof $case_local_img > 0
                            
                            if ($img_file_size > 1024) {
                                PostPunbotSEO::where('post_id', $edit_post_id)->update(['post_image' => $source_img]);
                                
                                //$_SESSION['success'] = 'Image Stat insert successfully & Post has been updated successfully!';
                                Log::debug("\n\n>check success and Insert & Updated Done ");
                            }

                    }

                }





        }


    }


}

