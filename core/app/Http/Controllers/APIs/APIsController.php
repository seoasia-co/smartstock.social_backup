<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
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
use App\Models\Setting;
use App\Models\Topic;
use App\Models\TopicCategory;
use App\Models\Webmail;
use App\Models\WebmasterSection;
use App\Models\WebmasterSetting;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Mail;

use App\Http\Controllers\APIs\SMAISyncTokenController;
use App\Http\Controllers\APIs\SMAIUpdateProfileController;
use App\Http\Controllers\APIs\SMAISyncPlanController;
use App\Http\Controllers\APIs\SMAISyncSEOController;

use Log;
use Str;

use App\Http\Controllers\Auth\SMAISessionAuthController;
use App\Models\UserMain;

use App\Http\Controllers\AIController;
use App\Models\OpenAIGenerator;
use App\Models\UserOpenAI;
use App\Http\Controllers\SMAI_SEO_PUNBOTController;

use App\Models\PostPunbotSEO;
use App\Models\PostSEO;
use App\Models\PostCourse;
use Carbon\Carbon;
use App\Models\BlogMeta;
use App\Models\SEOWebOption;

use App\Models\SEOAiAutomation;

use Modules\CourseSetting\Entities\Course;
use Facebook\Facebook;

use App\Models\PunbotProductEcommerce;
use App\Models\PunbotCommentsFBLive;
use App\Models\PunbotOrdersEcommerce;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use DB;
use PDO;



class APIsController extends Controller
{
    public function __construct()
    {
        // Check API Status
        if (!Helper::GeneralWebmasterSettings("api_status")) {
            // API disabled
            exit();
        }

        //test close Helper
        //Helper::SaveVisitorInfo(url()->current());


    }

    public function api()
    {
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
<meta charset=\"utf-8\">
<title>API v1 | Restful Web Services</title>
<body>
<br>
<div>
Restful Web Services: <br>
---------------------------------------- <br>
{ GET }     /api/v1/website/status <br>
{ GET }     /api/v1/website/info <br>
{ GET }     /api/v1/website/contacts <br>
{ GET }     /api/v1/website/style <br>
{ GET }     /api/v1/website/social <br>
{ GET }     /api/v1/website/settings <br>
{ GET }     /api/v1/menu/ <br>
{ GET }     /api/v1/banners/ <br>
{ GET }     /api/v1/section/ <br>
{ GET }     /api/v1/categories/ <br>
{ GET }     /api/v1/topics/ <br>
{ GET }     /api/v1/topic/ <br>
{ GET }     /api/v1/topic/fields/ <br>
{ GET }     /api/v1/topic/photos/ <br>
{ GET }     /api/v1/topic/photo/ <br>
{ GET }     /api/v1/topic/maps/ <br>
{ GET }     /api/v1/topic/map/ <br>
{ GET }     /api/v1/topic/files/ <br>
{ GET }     /api/v1/topic/file/ <br>
{ GET }     /api/v1/topic/comments/ <br>
{ GET }     /api/v1/topic/comment/ <br>
{ GET }     /api/v1/topic/related/ <br>
{ GET }     /api/v1/user/ <br>
{ POST }   /api/v1/subscribe <br>
{ POST }   /api/v1/comment <br>
{ POST }   /api/v1/order <br>
{ POST }   /api/v1/contact <br>
---------------------------------------- <br>
For more details check <a href='http://smartfordesign.net/smartend/documentation/api.html' target='_blank'><strong>API documentation</strong></a>
</div>
</body>
</html>
        ";
        exit();
    }

    public function website_status()
    {
        // Get Site Settings
        $Setting = Setting::find(1);
        // Response Details
        $msg = "";
        if ($Setting->site_status == 0) {
            $msg = nl2br($Setting->close_msg);
        }
        $response_details = [
            'status' => $Setting->site_status,
            'close_msg' => $msg
        ];
        // Response MSG
        $response = [
            'msg' => 'Website Status details',
            'details' => $response_details
        ];
        return response()->json($response, 200);
    }

    public function website_info($lang = '')
    {
        // Get Site Settings
        $Setting = Setting::find(1);

        // By Language
        $lang = $this->getLanguage($lang);
        $site_title_var = "site_title_$lang";
        $site_desc_var = "site_desc_$lang";
        $site_keywords_var = "site_keywords_$lang";

        // Response Details
        $response_details = [
            'site_url' => $Setting->site_url,
            'site_title' => $Setting->$site_title_var,
            'site_desc' => $Setting->$site_desc_var,
            'site_keywords' => $Setting->$site_keywords_var,
            'site_webmails' => $Setting->site_webmails
        ];
        // Response MSG
        $response = [
            'msg' => 'Main information about the Website',
            'details' => $response_details
        ];
        return response()->json($response, 200);
    }

    public function getLanguage($lang)
    {
        // List of active languages for API
        $Language = Language::where("status", true)->where("code", $lang)->first();

        if ($lang == "" || empty($Language)) {
            $lang = env('DEFAULT_LANGUAGE');
        }
        return $lang;
    }

    public function website_contacts($lang = '')
    {
        // Get Site Settings
        $Setting = Setting::find(1);

        // By Language
        $lang = $this->getLanguage($lang);
        $address_var = "contact_t1_$lang";
        $working_time_var = "contact_t7_$lang";

        // Response Details
        $response_details = [
            'address' => $Setting->$address_var,
            'phone' => $Setting->contact_t3,
            'fax' => $Setting->contact_t4,
            'mobile' => $Setting->contact_t5,
            'email' => $Setting->contact_t6,
            'working_time' => $Setting->$working_time_var
        ];
        // Response MSG
        $response = [
            'msg' => 'List of Contacts Details',
            'details' => $response_details
        ];
        return response()->json($response, 200);
    }

    public function website_style($lang = '')
    {
        // Get Site Settings
        $Setting = Setting::find(1);

        // By Language
        $lang = $this->getLanguage($lang);
        $style_logo_var = "style_logo_$lang";

        // Response Details
        $response_details = [
            'logo' => ($Setting->$style_logo_var != "") ? url("") . "/uploads/settings/" . $Setting->$style_logo_var : null,
            'fav_icon' => ($Setting->style_fav != "") ? url("") . "/uploads/settings/" . $Setting->style_fav : null,
            'apple_icon' => ($Setting->style_apple != "") ? url("") . "/uploads/settings/" . $Setting->style_apple : null,
            'style_color_1' => $Setting->style_color1,
            'style_color_2' => $Setting->style_color2,
            'layout_mode' => $Setting->style_type,
            'bg_type' => $Setting->style_bg_type,
            'bg_pattern' => ($Setting->style_bg_pattern != "") ? url("") . "/uploads/pattern/" . $Setting->style_bg_pattern : null,
            'bg_color' => $Setting->style_bg_color,
            'bg_image' => ($Setting->style_bg_image != "") ? url("") . "/uploads/settings/" . $Setting->style_bg_image : null,
            'footer_style' => $Setting->style_footer,
            'footer_bg' => ($Setting->style_footer_bg != "") ? url("") . "/uploads/settings/" . $Setting->style_footer_bg : null,
            'newsletter_subscribe_status' => $Setting->style_subscribe,
            'preload_status' => $Setting->style_preload
        ];
        // Response MSG
        $response = [
            'msg' => 'List of Style Settings',
            'details' => $response_details
        ];
        return response()->json($response, 200);
    }

    public function website_social()
    {
        // Get Site Settings
        $Setting = Setting::find(1);

        // Response Details
        $response_details = [
            'facebook' => $Setting->social_link1,
            'twitter' => $Setting->social_link2,
            'google' => $Setting->social_link3,
            'linkedin' => $Setting->social_link4,
            'youtube' => $Setting->social_link5,
            'instagram' => $Setting->social_link6,
            'pinterest' => $Setting->social_link7,
            'tumblr' => $Setting->social_link8,
            'flickr' => $Setting->social_link9,
            'whatsapp' => $Setting->social_link10,
        ];
        // Response MSG
        $response = [
            'msg' => 'List of Social Networks Links',
            'details' => $response_details
        ];
        return response()->json($response, 200);
    }

    public function website_settings()
    {
        // Get Site Settings
        $WebmasterSetting = WebmasterSetting::find(1);

        // Response Details
        $response_details = [
            'new_comments_status' => $WebmasterSetting->new_comments_status,
            'allow_register_status' => $WebmasterSetting->register_status,
            'register_permission_group' => $WebmasterSetting->permission_group,
            'contact_text_page_id' => $WebmasterSetting->contact_page_id,
            'header_menu_id' => $WebmasterSetting->header_menu_id,
            'footer_menu_id' => $WebmasterSetting->footer_menu_id,
            'latest_news_section_id' => $WebmasterSetting->latest_news_section_id,
            'newsletter_contacts_group' => $WebmasterSetting->newsletter_contacts_group,
            'home_content1_section_id' => $WebmasterSetting->home_content1_section_id,
            'home_content2_section_id' => $WebmasterSetting->home_content2_section_id,
            'home_content3_section_id' => $WebmasterSetting->home_content3_section_id,
            'home_banners_section_id' => $WebmasterSetting->home_banners_section_id,
            'home_text_banners_section_id' => $WebmasterSetting->home_text_banners_section_id,
            'side_banners_section_id' => $WebmasterSetting->side_banners_section_id,
            'languages' => Helper::languagesList()
        ];
        // Response MSG
        $response = [
            'msg' => 'General Website Settings',
            'details' => $response_details
        ];
        return response()->json($response, 200);
    }

    public function menu($menu_id, $lang = '')
    {
        if ($menu_id > 0) {
            // Get menu details
            $Menu = Menu::where('father_id', $menu_id)->where('status', 1)->orderby('row_no', 'asc')->get();
            if (count($Menu) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";

                // Response Details
                $response_details = [];
                foreach ($Menu as $MenuLink) {
                    $SubMenu = Menu::where('father_id', $MenuLink->id)->where('status', 1)->orderby('row_no', 'asc')->get();
                    $sub_response_details = [];
                    if (count($SubMenu) > 0) {
                        foreach ($SubMenu as $SubMenuLink) {
                            $m_link = "";
                            if ($SubMenuLink->type == 3 || $SubMenuLink->type == 2) {
                                $m_link = $SubMenuLink->webmasterSection->name;
                            } elseif ($SubMenuLink->type == 1) {
                                $m_link = $MenuLink->link;
                            }
                            $sub_response_details[] = [
                                'id' => $SubMenuLink->id,
                                'title' => $SubMenuLink->$title_var,
                                'section_id' => $SubMenuLink->cat_id,
                                'href' => $m_link
                            ];
                        }
                    }

                    $m_link = "";
                    $sub_count = count($SubMenu);
                    if ($MenuLink->type == 3) {
                        // Section with drop list
                        $m_link = $MenuLink->webmasterSection->name;
                        $sub_count = count($MenuLink->webmasterSection->sections);
                        foreach ($MenuLink->webmasterSection->sections as $SubSection) {
                            $sub_response_details[] = [
                                'id' => $SubSection->id,
                                'title' => $SubSection->$title_var,
                                'section_id' => $MenuLink->cat_id,
                                'href' => "topics/cat/" . $SubSection->id
                            ];
                        }
                    } elseif ($MenuLink->type == 2) {
                        // Section Link
                        $m_link = $MenuLink->webmasterSection->name;
                    } elseif ($MenuLink->type == 1) {
                        $m_link = $MenuLink->link;
                    }
                    $response_details[] = [
                        'id' => $MenuLink->id,
                        'title' => $MenuLink->$title_var,
                        'section_id' => $MenuLink->cat_id,
                        'href' => $m_link,
                        'sub_links_count' => $sub_count,
                        'sub_links' => $sub_response_details
                    ];
                    // sub links

                }
                // Response MSG
                $response = [
                    'msg' => 'List of Menu Links',
                    'links_count' => count($Menu),
                    'links' => $response_details
                ];
                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];
                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];
            return response()->json($response, 404);
        }
    }

    public function banners($group_id, $lang = '')
    {
        if ($group_id > 0) {
            // Get banners
            $Banners = Banner::where('section_id', $group_id)->where('status', 1)->orderby('row_no', 'asc')->get();
            if (count($Banners) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $details_var = "details_$lang";
                $file_var = "file_$lang";

                // Response Details
                $response_details = [];
                $type = "";
                foreach ($Banners as $Banner) {
                    $type = $Banner->webmasterBanner->type;
                    $response_details[] = [
                        'id' => $Banner->id,
                        'title' => $Banner->$title_var,
                        'details' => nl2br($Banner->$details_var),
                        'file' => ($Banner->$file_var != "") ? url("") . "/uploads/banners/" . $Banner->$file_var : null,
                        'video_type' => $Banner->video_type,
                        'youtube_link' => $Banner->youtube_link,
                        'link_url' => $Banner->link_url,
                        'icon' => $Banner->icon
                    ];
                }
                // Response MSG
                $response = [
                    'msg' => 'List of Banners',
                    'type' => $type,
                    'banners_count' => count($Banners),
                    'banners' => $response_details
                ];
                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];
                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];
            return response()->json($response, 404);
        }
    }

    public function section($section_id, $lang = '')
    {
        if ($section_id > 0) {
            // Get categories
            $WebmasterSections = WebmasterSection::where('id', $section_id)->where('status', 1)->get();
            if (count($WebmasterSections) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $section_title = "";
                $type = "";
                $sections_status = "";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');

                // Response Details
                $response_details = [];
                foreach ($WebmasterSections as $WebmasterSection) {
                    if ($WebmasterSection->$title_var != "") {
                        $section_title = $WebmasterSection->$title_var;
                    } else {
                        $section_title = $WebmasterSection->$title_var2;
                    }
                    $type = $WebmasterSection->type;
                    $sections_status = $WebmasterSection->sections_status;
                }
                // Response MSG
                $response = [
                    'msg' => 'Website Section Details',
                    'section_id' => $section_id,
                    'title' => $section_title,
                    'href' => "/" . $WebmasterSection->name,
                    'type' => $type,
                    'categories_status' => $sections_status
                ];
                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];
                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];
            return response()->json($response, 404);
        }
    }

    public function categories($section_id, $lang = '')
    {
        if ($section_id > 0) {
            $WebmasterSection = WebmasterSection::find($section_id);
            if (!empty($WebmasterSection)) {
                // if private redirect back to home
                if ($WebmasterSection->type == 4 || $WebmasterSection->type == 7) {
                    // Empty MSG
                    $response = [
                        'msg' => 'There is no data'
                    ];
                    return response()->json($response, 404);
                }
            }

            // Get categories
            $Sections = Section::where('webmaster_id', $section_id)->where('father_id', '0')->where('status', 1)->orderby('row_no', 'asc')->get();
            if (count($Sections) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                $type = "";
                $section_title = "";

                // Response Details
                $response_details = [];
                foreach ($Sections as $Section) {
                    $type = $Section->webmasterSection->type;
                    if ($Section->webmasterSection->$title_var != "") {
                        $section_title = $Section->webmasterSection->$title_var;
                    } else {
                        $section_title = $Section->webmasterSection->$title_var2;
                    }

                    $SubSections = Section::where('webmaster_id', $section_id)->where('father_id', $Section->id)->where('status', 1)->orderby('row_no', 'asc')->get();
                    $sub_response_details = [];
                    foreach ($SubSections as $SubSection) {
                        if ($SubSection->$title_var != "") {
                            $SubCat_title = $SubSection->$title_var;
                        } else {
                            $SubCat_title = $SubSection->$title_var2;
                        }
                        $sub_response_details[] = [
                            'id' => $SubSection->id,
                            'title' => $SubCat_title,
                            'icon' => $SubSection->icon,
                            'photo' => ($SubSection->photo != "") ? url("") . "/uploads/sections/" . $SubSection->photo : null,
                            'href' => "topics/cat/" . $SubSection->id,
                        ];
                    }
                    if ($Section->$title_var != "") {
                        $cat_title = $Section->$title_var;
                    } else {
                        $cat_title = $Section->$title_var2;
                    }
                    $response_details[] = [
                        'id' => $Section->id,
                        'title' => $cat_title,
                        'icon' => $Section->icon,
                        'photo' => ($Section->photo != "") ? url("") . "/uploads/sections/" . $Section->photo : null,
                        'href' => "topics/cat/" . $Section->id,
                        'sub_categories_count' => count($SubSections),
                        'sub_categories' => $sub_response_details
                    ];

                }
                // Response MSG
                $response = [
                    'msg' => 'List of Categories',
                    'section_id' => $section_id,
                    'section_title' => $section_title,
                    'type' => $type,
                    'categories_count' => count($Sections),
                    'categories' => $response_details
                ];
                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];
                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];
            return response()->json($response, 404);
        }
    }

    public function topics($section_id, $page_number = 1, $topics_count = 0, $lang = '')
    {
        if ($section_id > 0) {
            $WebmasterSection = WebmasterSection::find($section_id);
            if (!empty($WebmasterSection)) {
                // if private redirect back to home
                if ($WebmasterSection->type == 4 || $WebmasterSection->type == 7) {
                    // Empty MSG
                    $response = [
                        'msg' => 'There is no data'
                    ];
                    return response()->json($response, 404);
                }
            }

            if ($page_number < 1) {
                $page_number = 1;
            }
            Paginator::currentPageResolver(function () use ($page_number) {
                return $page_number;
            });

            // Get topics
            $Topics = Topic::where([['webmaster_id', '=', $section_id], ['status',
                1], ['expire_date', '>=', date("Y-m-d")], ['expire_date', '<>', null]])->orWhere([['webmaster_id', '=', $section_id], ['status', 1], ['expire_date', null]])->orderby('row_no', 'asc');

            if ($topics_count > 0) {
                $Topics = $Topics->paginate($topics_count);
            } else {
                $Topics = $Topics->get();
            }

            if (count($Topics) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                $details_var = "details_$lang";
                $details_var2 = "details_" . env('DEFAULT_LANGUAGE');
                $type = "";
                $section_title = "";

                // Response Details
                $response_details = [];
                foreach ($Topics as $Topic) {
                    $type = $Topic->webmasterSection->type;
                    if ($Topic->webmasterSection->$title_var != "") {
                        $section_title = $Topic->webmasterSection->$title_var;
                    } else {
                        $section_title = $Topic->webmasterSection->$title_var2;
                    }


                    $Joined_categories = [];
                    foreach ($Topic->categories as $category) {
                        if ($category->section->$title_var != "") {
                            $Cat_title = $category->section->$title_var;
                        } else {
                            $Cat_title = $category->section->$title_var2;
                        }
                        $Joined_categories[] = [
                            'id' => $category->id,
                            'title' => $Cat_title,
                            'icon' => $category->section->icon,
                            'photo' => ($category->section->photo != "") ? url("") . "/uploads/sections/" . $category->section->photo : null,
                            'href' => "topics/cat/" . $category->id
                        ];
                    }

                    // additional fields
                    $Additional_fields = [];
                    foreach ($Topic->webmasterSection->customFields->where("in_listing", true) as $customField) {
                        if ($customField->in_page) {

                            $cf_saved_val = "";
                            $cf_saved_val_array = array();
                            if (count($Topic->fields) > 0) {
                                foreach ($Topic->fields as $t_field) {
                                    if ($t_field->field_id == $customField->id) {
                                        if ($customField->type == 7) {
                                            // if multi check
                                            $cf_saved_val_array = explode(", ", $t_field->field_value);
                                            $cf_details_var = "details_" . @Helper::currentLanguage()->code;
                                            $cf_details_var2 = "details_" . env('DEFAULT_LANGUAGE');
                                            if ($customField->$cf_details_var != "") {
                                                $cf_details = $customField->$cf_details_var;
                                            } else {
                                                $cf_details = $customField->$cf_details_var2;
                                            }
                                            $cf_details_lines = preg_split('/\r\n|[\r\n]/', $cf_details);
                                            $line_num = 1;
                                            foreach ($cf_details_lines as $cf_details_line) {
                                                if (in_array($line_num, $cf_saved_val_array)) {
                                                    $cf_saved_val .= $cf_details_line . ", ";
                                                }
                                                $line_num++;
                                            }
                                            $cf_saved_val = substr($cf_saved_val, 0, -2);
                                        } else {
                                            $cf_saved_val = $t_field->field_value;
                                        }
                                    }
                                }
                            }

                            if (($cf_saved_val != "" || count($cf_saved_val_array) > 0) && ($customField->lang_code == "all" || $customField->lang_code == "$lang")) {
                                $Additional_fields[] = [
                                    'type' => $customField->type,
                                    'title' => $customField->$title_var,
                                    'value' => $cf_saved_val,
                                ];
                            }
                        }
                    }

                    $video_file = $Topic->video_file;
                    if ($Topic->video_type == 0) {
                        $video_file = ($Topic->video_file != "") ? url("") . "/uploads/topics/" . $Topic->video_file : "";
                    }
                    if ($Topic->$title_var != "") {
                        $Topic_title = $Topic->$title_var;
                    } else {
                        $Topic_title = $Topic->$title_var2;
                    }
                    if ($Topic->$details_var != "") {
                        $Topic_details = $Topic->$details_var;
                    } else {
                        $Topic_details = $Topic->$details_var2;
                    }
                    $response_details[] = [
                        'id' => $Topic->id,
                        'title' => $Topic_title,
                        'details' => $Topic_details,
                        'date' => $Topic->date,
                        'video_type' => $Topic->video_type,
                        'video_file' => $video_file,
                        'photo_file' => ($Topic->photo_file != "") ? url("") . "/uploads/topics/" . $Topic->photo_file : null,
                        'audio_file' => ($Topic->audio_file != "") ? url("") . "/uploads/topics/" . $Topic->audio_file : null,
                        'icon' => $Topic->icon,
                        'visits' => $Topic->visits,
                        'href' => "topic/" . $Topic->id,
                        'fields_count' => count($Additional_fields),
                        'fields' => $Additional_fields,
                        'Joined_categories_count' => count($Topic->categories),
                        'Joined_categories' => $Joined_categories,
                        'user' => [
                            'id' => $Topic->user->id,
                            'name' => $Topic->user->name,
                            'href' => "user/" . $Topic->user->id . "/topics",
                        ]

                    ];

                }
                // Response MSG
                $response = [
                    'msg' => 'List of Topics',
                    'section_id' => $section_id,
                    'section_title' => $section_title,
                    'type' => $type,
                    'topics_count' => count($Topics),
                    'topics' => $response_details
                ];
                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];
                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];
            return response()->json($response, 404);
        }
    }

    public function category($cat_id, $page_number = 1, $topics_count = 0, $lang = '')
    {
        if ($cat_id > 0) {
            if ($page_number < 1) {
                $page_number = 1;
            }
            Paginator::currentPageResolver(function () use ($page_number) {
                return $page_number;
            });

            $category_topics = array();
            $TopicCategories = TopicCategory::where('section_id', $cat_id)->get();
            foreach ($TopicCategories as $category) {
                $category_topics[] = $category->topic_id;
            }

            $Topics = Topic::where(function ($q) {
                $q->where([['status', 1], ['expire_date', '>=', date("Y-m-d")], ['expire_date', '<>', null]])->orWhere([['status', 1], ['expire_date', null]]);
            })->whereIn('id', $category_topics)->orderby('row_no', env("FRONTEND_TOPICS_ORDER", "asc"));

            if ($topics_count > 0) {
                $Topics = $Topics->paginate($topics_count);
            } else {
                $Topics = $Topics->get();
            }

            if (count($Topics) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                $details_var = "details_$lang";
                $details_var2 = "details_" . env('DEFAULT_LANGUAGE');
                $cat_title = "";

                $CurrentCategory = Section::find($cat_id);
                if (!empty($CurrentCategory)) {
                    $cat_title = $CurrentCategory->$title_var;
                }

                // Response Details
                $response_details = [];
                foreach ($Topics as $Topic) {
                    $Joined_categories = [];
                    foreach ($Topic->categories as $category) {
                        if ($category->section->$title_var != "") {
                            $Cat_title = $category->section->$title_var;
                        } else {
                            $Cat_title = $category->section->$title_var2;
                        }
                        $Joined_categories[] = [
                            'id' => $category->id,
                            'title' => $Cat_title,
                            'icon' => $category->section->icon,
                            'photo' => ($category->section->photo != "") ? url("") . "/uploads/sections/" . $category->section->photo : null,
                            'href' => "topics/cat/" . $category->id
                        ];
                    }

                    // additional fields
                    $Additional_fields = [];
                    foreach ($Topic->webmasterSection->customFields->where("in_listing", true) as $customField) {
                        if ($customField->in_page) {

                            $cf_saved_val = "";
                            $cf_saved_val_array = array();
                            if (count($Topic->fields) > 0) {
                                foreach ($Topic->fields as $t_field) {
                                    if ($t_field->field_id == $customField->id) {
                                        if ($customField->type == 7) {
                                            // if multi check
                                            $cf_saved_val_array = explode(", ", $t_field->field_value);
                                            $cf_details_var = "details_" . @Helper::currentLanguage()->code;
                                            $cf_details_var2 = "details_" . env('DEFAULT_LANGUAGE');
                                            if ($customField->$cf_details_var != "") {
                                                $cf_details = $customField->$cf_details_var;
                                            } else {
                                                $cf_details = $customField->$cf_details_var2;
                                            }
                                            $cf_details_lines = preg_split('/\r\n|[\r\n]/', $cf_details);
                                            $line_num = 1;
                                            foreach ($cf_details_lines as $cf_details_line) {
                                                if (in_array($line_num, $cf_saved_val_array)) {
                                                    $cf_saved_val .= $cf_details_line . ", ";
                                                }
                                                $line_num++;
                                            }
                                            $cf_saved_val = substr($cf_saved_val, 0, -2);
                                        } else {
                                            $cf_saved_val = $t_field->field_value;
                                        }
                                    }
                                }
                            }

                            if (($cf_saved_val != "" || count($cf_saved_val_array) > 0) && ($customField->lang_code == "all" || $customField->lang_code == "$lang")) {
                                $Additional_fields[] = [
                                    'type' => $customField->type,
                                    'title' => $customField->$title_var,
                                    'value' => $cf_saved_val,
                                ];
                            }
                        }
                    }

                    $video_file = $Topic->video_file;
                    if ($Topic->video_type == 0) {
                        $video_file = ($Topic->video_file != "") ? url("") . "/uploads/topics/" . $Topic->video_file : "";
                    }
                    if ($Topic->$title_var != "") {
                        $Topic_title = $Topic->$title_var;
                    } else {
                        $Topic_title = $Topic->$title_var2;
                    }
                    if ($Topic->$details_var != "") {
                        $Topic_details = $Topic->$details_var;
                    } else {
                        $Topic_details = $Topic->$details_var2;
                    }
                    $response_details[] = [
                        'id' => $Topic->id,
                        'title' => $Topic_title,
                        'details' => $Topic_details,
                        'date' => $Topic->date,
                        'video_type' => $Topic->video_type,
                        'video_file' => $video_file,
                        'photo_file' => ($Topic->photo_file != "") ? url("") . "/uploads/topics/" . $Topic->photo_file : null,
                        'audio_file' => ($Topic->audio_file != "") ? url("") . "/uploads/topics/" . $Topic->audio_file : null,
                        'icon' => $Topic->icon,
                        'visits' => $Topic->visits,
                        'href' => "topic/" . $Topic->id,
                        'fields_count' => count($Additional_fields),
                        'fields' => $Additional_fields,
                        'Joined_categories_count' => count($Topic->categories),
                        'Joined_categories' => $Joined_categories,
                        'user' => [
                            'id' => $Topic->user->id,
                            'name' => $Topic->user->name,
                            'href' => "user/" . $Topic->user->id . "/topics",
                        ]

                    ];

                }
                // Response MSG
                $response = [
                    'msg' => 'List of Topics',
                    'cat_id' => $cat_id,
                    'cat_title' => $cat_title,
                    'topics_count' => count($Topics),
                    'topics' => $response_details
                ];
                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];
                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];
            return response()->json($response, 404);
        }
    }

    public function topic($topic_id, $lang = '')
    {
        if ($topic_id > 0) {

            // Get topic details
            $Topics = Topic::where([['id', '=', $topic_id], ['status',
                1], ['expire_date', '>=', date("Y-m-d")], ['expire_date', '<>', null]])->orWhere([['id', '=', $topic_id], ['status', 1], ['expire_date', null]])->orderby('row_no', 'asc')->get();

            if (count($Topics) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                $details_var = "details_$lang";
                $details_var2 = "details_" . env('DEFAULT_LANGUAGE');

                $type = "";
                $section_id = "";
                $section_title = "";

                // Response Details
                $response_details = [];
                foreach ($Topics as $Topic) {

                    $WebmasterSection = WebmasterSection::find($Topic->webmaster_id);
                    if (!empty($WebmasterSection)) {
                        // if private redirect back to home
                        if ($WebmasterSection->type == 4 || $WebmasterSection->type == 7) {
                            // Empty MSG
                            $response = [
                                'msg' => 'There is no data'
                            ];
                            return response()->json($response, 404);
                        }
                    }

                    $type = $Topic->webmasterSection->type;
                    $section_id = $Topic->webmasterSection->id;
                    if ($Topic->webmasterSection->$title_var != "") {
                        $section_title = $Topic->webmasterSection->$title_var;
                    } else {
                        $section_title = $Topic->webmasterSection->$title_var2;
                    }

                    // additional fields
                    $Additional_fields = [];
                    foreach ($Topic->webmasterSection->customFields->where("in_page", true) as $customField) {
                        if ($customField->in_page) {
                            $cf_saved_val = "";
                            $cf_saved_val_array = array();
                            if (count($Topic->fields) > 0) {
                                foreach ($Topic->fields as $t_field) {
                                    if ($t_field->field_id == $customField->id) {
                                        if ($customField->type == 7) {
                                            // if multi check
                                            $cf_saved_val_array = explode(", ", $t_field->field_value);
                                            $cf_details_var = "details_" . @Helper::currentLanguage()->code;
                                            $cf_details_var2 = "details_" . env('DEFAULT_LANGUAGE');
                                            if ($customField->$cf_details_var != "") {
                                                $cf_details = $customField->$cf_details_var;
                                            } else {
                                                $cf_details = $customField->$cf_details_var2;
                                            }
                                            $cf_details_lines = preg_split('/\r\n|[\r\n]/', $cf_details);
                                            $line_num = 1;
                                            foreach ($cf_details_lines as $cf_details_line) {
                                                if (in_array($line_num, $cf_saved_val_array)) {
                                                    $cf_saved_val .= $cf_details_line . ", ";
                                                }
                                                $line_num++;
                                            }
                                            $cf_saved_val = substr($cf_saved_val, 0, -2);

                                        } else {
                                            $cf_saved_val = $t_field->field_value;
                                        }
                                    }
                                }
                            }

                            if (($cf_saved_val != "" || count($cf_saved_val_array) > 0) && ($customField->lang_code == "all" || $customField->lang_code == "$lang")) {
                                $Additional_fields[] = [
                                    'type' => $customField->type,
                                    'title' => $customField->$title_var,
                                    'value' => $cf_saved_val,
                                ];
                            }
                        }
                    }

                    // categories
                    $Joined_categories = [];
                    foreach ($Topic->categories as $category) {
                        if ($category->section->$title_var != "") {
                            $Cat_title = $category->section->$title_var;
                        } else {
                            $Cat_title = $category->section->$title_var2;
                        }
                        $Joined_categories[] = [
                            'id' => $category->id,
                            'title' => $Cat_title,
                            'icon' => $category->section->icon,
                            'photo' => ($category->section->photo != "") ? url("") . "/uploads/sections/" . $category->section->photo : null,
                            'href' => "topics/cat/" . $category->id
                        ];
                    }
                    // photos
                    $Photos = [];
                    foreach ($Topic->photos as $photo) {
                        $Photos[] = [
                            'id' => $photo->id,
                            'title' => $photo->title,
                            'url' => ($photo->file != "") ? url("") . "/uploads/topics/" . $photo->file : null,
                            'href' => "/topic/photo/" . $photo->id
                        ];
                    }
                    // maps
                    $Maps = [];
                    foreach ($Topic->maps as $map) {

                        if ($map->$title_var != "") {
                            $map_title = $map->$title_var;
                        } else {
                            $map_title = $map->$title_var2;
                        }
                        if ($map->$details_var != "") {
                            $map_details = $map->$details_var;
                        } else {
                            $map_details = $map->$details_var2;
                        }

                        $Maps[] = [
                            'id' => $map->id,
                            'longitude' => $map->longitude,
                            'latitude' => $map->latitude,
                            'title' => $map_title,
                            'details' => $map_details,
                            'href' => "/topic/map/" . $map->id
                        ];
                    }
                    // attach files
                    $Attach_files = [];
                    foreach ($Topic->attachFiles as $attachFile) {
                        if ($attachFile->$title_var != "") {
                            $attachFile_title = $attachFile->$title_var;
                        } else {
                            $attachFile_title = $attachFile->$title_var2;
                        }
                        $Attach_files[] = [
                            'id' => $attachFile->id,
                            'title' => $attachFile_title,
                            'url' => ($attachFile->file != "") ? url("") . "/uploads/topics/" . $attachFile->file : null,
                            'href' => "/topic/file/" . $attachFile->id
                        ];
                    }
                    // comments
                    $Comments = [];
                    foreach ($Topic->approvedComments as $comment) {
                        $Comments[] = [
                            'id' => $comment->id,
                            'name' => $comment->name,
                            'email' => $comment->email,
                            'date' => $comment->date,
                            'comment' => nl2br($comment->comment),
                            'href' => "/topic/comment/" . $comment->id
                        ];
                    }
                    // related topics
                    $Related_topics = [];
                    foreach ($Topic->relatedTopics as $relatedTopic) {
                        if ($relatedTopic->topic->$title_var != "") {
                            $relatedTopic_title = $relatedTopic->topic->$title_var;
                        } else {
                            $relatedTopic_title = $relatedTopic->topic->$title_var2;
                        }
                        $Related_topics[] = [
                            'id' => $relatedTopic->topic->id,
                            'title' => $relatedTopic_title,
                            'date' => $relatedTopic->topic->date,
                            'href' => "topic/" . $relatedTopic->topic->id,
                            'photo_file' => ($relatedTopic->topic->photo_file != "") ? url("") . "/uploads/topics/" . $relatedTopic->topic->photo_file : null
                        ];
                    }

                    $video_file = $Topic->video_file;
                    if ($Topic->video_type == 0) {
                        $video_file = ($Topic->video_file != "") ? url("") . "/uploads/topics/" . $Topic->video_file : "";
                    }

                    if ($Topic->$title_var != "") {
                        $Topic_title = $Topic->$title_var;
                    } else {
                        $Topic_title = $Topic->$title_var2;
                    }
                    if ($Topic->$details_var != "") {
                        $Topic_details = $Topic->$details_var;
                    } else {
                        $Topic_details = $Topic->$details_var2;
                    }

                    $response_details[] = [
                        'id' => $Topic->id,
                        'title' => $Topic_title,
                        'details' => $Topic_details,
                        'date' => $Topic->date,
                        'video_type' => $Topic->video_type,
                        'video_file' => $video_file,
                        'photo_file' => ($Topic->photo_file != "") ? url("") . "/uploads/topics/" . $Topic->photo_file : null,
                        'audio_file' => ($Topic->audio_file != "") ? url("") . "/uploads/topics/" . $Topic->audio_file : null,
                        'icon' => $Topic->icon,
                        'visits' => $Topic->visits,
                        'href' => "topic/" . $Topic->id,
                        'fields_count' => count($Additional_fields),
                        'fields' => $Additional_fields,
                        'Joined_categories_count' => count($Joined_categories),
                        'Joined_categories' => $Joined_categories,
                        'photos_count' => count($Photos),
                        'photos' => $Photos,
                        'attach_files_count' => count($Attach_files),
                        'attach_files' => $Attach_files,
                        'maps_count' => count($Maps),
                        'maps' => $Maps,
                        'comments_count' => count($Comments),
                        'comments' => $Comments,
                        'related_topics_count' => count($Related_topics),
                        'related_topics' => $Related_topics,
                        'user' => [
                            'id' => $Topic->user->id,
                            'name' => $Topic->user->name,
                            'href' => "user/" . $Topic->user->id . "/topics",
                        ]

                    ];

                }
                // Response MSG
                $response = [
                    'msg' => 'Details of topic',
                    'section_id' => $section_id,
                    'section_title' => $section_title,
                    'type' => $type,
                    'topic' => $response_details
                ];
                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];
                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];
            return response()->json($response, 404);
        }
    }

    public function topic_photos($topic_id, $lang = '')
    {
        if ($topic_id > 0) {

            // Get topic details
            $Topics = Topic::where([['id', '=', $topic_id], ['status',
                1], ['expire_date', '>=', date("Y-m-d")], ['expire_date', '<>', null]])->orWhere([['id', '=', $topic_id], ['status', 1], ['expire_date', null]])->orderby('row_no', 'asc')->get();

            if (count($Topics) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                $topic_title = "";
                $photo_file = "";

                // Response Details
                $response_details = [];
                foreach ($Topics as $Topic) {

                    $WebmasterSection = WebmasterSection::find($Topic->webmaster_id);
                    if (!empty($WebmasterSection)) {
                        // if private redirect back to home
                        if ($WebmasterSection->type == 4 || $WebmasterSection->type == 7) {
                            // Empty MSG
                            $response = [
                                'msg' => 'There is no data'
                            ];
                            return response()->json($response, 404);
                        }
                    }

                    if ($Topic->$title_var != "") {
                        $topic_title = $Topic->$title_var;
                    } else {
                        $topic_title = $Topic->$title_var2;
                    }
                    $photo_file = $Topic->photo_file;

                    // photos
                    $response_details = [];
                    foreach ($Topic->photos as $photo) {
                        $response_details[] = [
                            'id' => $photo->id,
                            'title' => $photo->title,
                            'url' => ($photo->file != "") ? url("") . "/uploads/topics/" . $photo->file : null,
                            'href' => "/topic/photo/" . $photo->id
                        ];
                    }

                }
                // Response MSG
                $response = [
                    'msg' => 'Photos of topic',
                    'topic_id' => $topic_id,
                    'topic_title' => $topic_title,
                    'topic_link' => "topic/" . $topic_id,
                    'topic_photo' => ($photo_file != "") ? url("") . "/uploads/topics/" . $photo_file : null,
                    'photos_count' => count($response_details),
                    'photos' => $response_details
                ];
                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];
                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];
            return response()->json($response, 404);
        }
    }

    public function topic_photo($photo_id, $lang = '')
    {
        if ($photo_id > 0) {

            // Get Photo details
            $Photo = Photo::find($photo_id);

            if (!empty($Photo)) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $topic_title = "";
                $photo_file = "";

                $response_details[] = [
                    'id' => $Photo->id,
                    'title' => $Photo->title,
                    'url' => ($Photo->file != "") ? url("") . "/uploads/topics/" . $Photo->file : null
                ];

                // Response MSG
                $response = [
                    'msg' => 'Photo details',
                    'topic_id' => $Photo->topic_id,
                    'photo' => $response_details
                ];
                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];
                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];
            return response()->json($response, 404);
        }
    }

    public function topic_maps($topic_id, $lang = '')
    {
        if ($topic_id > 0) {

            // Get topic details
            $Topics = Topic::where([['id', '=', $topic_id], ['status',
                1], ['expire_date', '>=', date("Y-m-d")], ['expire_date', '<>', null]])->orWhere([['id', '=', $topic_id], ['status', 1], ['expire_date', null]])->orderby('row_no', 'asc')->get();

            if (count($Topics) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                $details_var = "details_$lang";
                $details_var2 = "details_" . env('DEFAULT_LANGUAGE');
                $topic_title = "";
                $photo_file = "";

                // Response Details
                $response_details = [];
                foreach ($Topics as $Topic) {

                    $WebmasterSection = WebmasterSection::find($Topic->webmaster_id);
                    if (!empty($WebmasterSection)) {
                        // if private redirect back to home
                        if ($WebmasterSection->type == 4 || $WebmasterSection->type == 7) {
                            // Empty MSG
                            $response = [
                                'msg' => 'There is no data'
                            ];
                            return response()->json($response, 404);
                        }
                    }

                    if ($Topic->$title_var != "") {
                        $topic_title = $Topic->$title_var;
                    } else {
                        $topic_title = $Topic->$title_var2;
                    }
                    $photo_file = $Topic->photo_file;

                    // maps
                    $response_details = [];
                    foreach ($Topic->maps as $map) {

                        if ($map->$title_var != "") {
                            $map_title = $map->$title_var;
                        } else {
                            $map_title = $map->$title_var2;
                        }
                        if ($map->$details_var != "") {
                            $map_details = $map->$details_var;
                        } else {
                            $map_details = $map->$details_var2;
                        }

                        $response_details[] = [
                            'id' => $map->id,
                            'longitude' => $map->longitude,
                            'latitude' => $map->latitude,
                            'title' => $map_title,
                            'details' => $map_details,
                            'href' => "/topic/map/" . $map->id
                        ];
                    }

                }
                // Response MSG
                $response = [
                    'msg' => 'Maps of topic',
                    'topic_id' => $topic_id,
                    'topic_title' => $topic_title,
                    'topic_link' => "topic/" . $topic_id,
                    'topic_photo' => ($photo_file != "") ? url("") . "/uploads/topics/" . $photo_file : null,
                    'maps_count' => count($response_details),
                    'maps' => $response_details
                ];
                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];
                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];
            return response()->json($response, 404);
        }
    }

    public function topic_map($map_id, $lang = '')
    {
        if ($map_id > 0) {

            // Get map details
            $Map = Map::find($map_id);

            if (!empty($Map)) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                $details_var = "details_$lang";
                $details_var2 = "details_" . env('DEFAULT_LANGUAGE');

                if ($map->$title_var != "") {
                    $map_title = $map->$title_var;
                } else {
                    $map_title = $map->$title_var2;
                }
                if ($map->$details_var != "") {
                    $map_details = $map->$details_var;
                } else {
                    $map_details = $map->$details_var2;
                }

                $response_details[] = [
                    'id' => $Map->id,
                    'longitude' => $Map->longitude,
                    'latitude' => $Map->latitude,
                    'title' => $map_title,
                    'details' => $map_details
                ];

                // Response MSG
                $response = [
                    'msg' => 'Map details',
                    'topic_id' => $Map->topic_id,
                    'map' => $response_details
                ];
                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];
                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];
            return response()->json($response, 404);
        }
    }

    public function topic_files($topic_id, $lang = '')
    {
        if ($topic_id > 0) {

            // Get topic details
            $Topics = Topic::where([['id', '=', $topic_id], ['status',
                1], ['expire_date', '>=', date("Y-m-d")], ['expire_date', '<>', null]])->orWhere([['id', '=', $topic_id], ['status', 1], ['expire_date', null]])->orderby('row_no', 'asc')->get();

            if (count($Topics) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                $topic_title = "";
                $photo_file = "";

                // Response Details
                $response_details = [];
                foreach ($Topics as $Topic) {

                    $WebmasterSection = WebmasterSection::find($Topic->webmaster_id);
                    if (!empty($WebmasterSection)) {
                        // if private redirect back to home
                        if ($WebmasterSection->type == 4 || $WebmasterSection->type == 7) {
                            // Empty MSG
                            $response = [
                                'msg' => 'There is no data'
                            ];
                            return response()->json($response, 404);
                        }
                    }

                    if ($Topic->$title_var != "") {
                        $topic_title = $Topic->$title_var;
                    } else {
                        $topic_title = $Topic->$title_var2;
                    }
                    $photo_file = $Topic->photo_file;

                    // attach files
                    $response_details = [];
                    foreach ($Topic->attachFiles as $attachFile) {
                        if ($attachFile->$title_var != "") {
                            $attachFile_title = $attachFile->$title_var;
                        } else {
                            $attachFile_title = $attachFile->$title_var2;
                        }
                        $response_details[] = [
                            'id' => $attachFile->id,
                            'title' => $attachFile_title,
                            'url' => ($attachFile->file != "") ? url("") . "/uploads/topics/" . $attachFile->file : null,
                            'href' => "/topic/file/" . $attachFile->id
                        ];
                    }

                }

                // Response MSG
                $response = [
                    'msg' => 'Attach files of topic',
                    'topic_id' => $topic_id,
                    'topic_title' => $topic_title,
                    'topic_link' => "topic/" . $topic_id,
                    'topic_photo' => ($photo_file != "") ? url("") . "/uploads/topics/" . $photo_file : null,
                    'files_count' => count($response_details),
                    'files' => $response_details
                ];

                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];

                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];

            return response()->json($response, 404);
        }
    }

    public function topic_file($file_id, $lang = '')
    {
        if ($file_id > 0) {

            // Get topic details
            $AttachFile = AttachFile::find($file_id);

            if (!empty($AttachFile)) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                if ($AttachFile->$title_var != "") {
                    $attachFile_title = $AttachFile->$title_var;
                } else {
                    $attachFile_title = $AttachFile->$title_var2;
                }
                $response_details[] = [
                    'id' => $AttachFile->id,
                    'title' => $attachFile_title,
                    'url' => ($AttachFile->file != "") ? url("") . "/uploads/topics/" . $AttachFile->file : null
                ];

                // Response MSG
                $response = [
                    'msg' => 'Attach file details',
                    'topic_id' => $AttachFile->topic_id,
                    'file' => $response_details
                ];

                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];

                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];

            return response()->json($response, 404);
        }
    }

    public function topic_comments($topic_id, $lang = '')
    {
        if ($topic_id > 0) {

            // Get topic details
            $Topics = Topic::where([['id', '=', $topic_id], ['status',
                1], ['expire_date', '>=', date("Y-m-d")], ['expire_date', '<>', null]])->orWhere([['id', '=', $topic_id], ['status', 1], ['expire_date', null]])->orderby('row_no', 'asc')->get();

            if (count($Topics) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                $topic_title = "";
                $photo_file = "";

                // Response Details
                $response_details = [];
                foreach ($Topics as $Topic) {

                    $WebmasterSection = WebmasterSection::find($Topic->webmaster_id);
                    if (!empty($WebmasterSection)) {
                        // if private redirect back to home
                        if ($WebmasterSection->type == 4 || $WebmasterSection->type == 7) {
                            // Empty MSG
                            $response = [
                                'msg' => 'There is no data'
                            ];
                            return response()->json($response, 404);
                        }
                    }

                    if ($Topic->$title_var != "") {
                        $topic_title = $Topic->$title_var;
                    } else {
                        $topic_title = $Topic->$title_var2;
                    }
                    $photo_file = $Topic->photo_file;

                    // comments
                    $response_details = [];
                    foreach ($Topic->approvedComments as $comment) {
                        $response_details[] = [
                            'id' => $comment->id,
                            'name' => $comment->name,
                            'email' => $comment->email,
                            'date' => $comment->date,
                            'comment' => nl2br($comment->comment),
                            'href' => "/topic/comment/" . $comment->id
                        ];
                    }

                }
                // Response MSG
                $response = [
                    'msg' => 'Comments of topic',
                    'topic_id' => $topic_id,
                    'topic_title' => $topic_title,
                    'topic_link' => "topic/" . $topic_id,
                    'topic_photo' => ($photo_file != "") ? url("") . "/uploads/topics/" . $photo_file : null,
                    'comments_count' => count($response_details),
                    'comments' => $response_details
                ];
                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];

                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];

            return response()->json($response, 404);
        }
    }

    public function topic_comment($comment_id, $lang = '')
    {
        if ($comment_id > 0) {

            // Get topic details
            $Comment = Comment::find($comment_id);

            if (!empty($Comment)) {
                $response_details[] = [
                    'id' => $Comment->id,
                    'name' => $Comment->name,
                    'email' => $Comment->email,
                    'date' => $Comment->date,
                    'comment' => nl2br($Comment->comment)
                ];
                // Response MSG
                $response = [
                    'msg' => 'Comment details',
                    'topic_id' => $Comment->topic_id,
                    'comment' => $response_details
                ];

                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];

                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];

            return response()->json($response, 404);
        }
    }

    public function topic_related($topic_id, $lang = '')
    {
        if ($topic_id > 0) {

            // Get topic details
            $Topics = Topic::where([['id', '=', $topic_id], ['status',
                1], ['expire_date', '>=', date("Y-m-d")], ['expire_date', '<>', null]])->orWhere([['id', '=', $topic_id], ['status', 1], ['expire_date', null]])->orderby('row_no', 'asc')->get();

            if (count($Topics) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                $topic_title = "";
                $photo_file = "";

                // Response Details
                $response_details = [];
                foreach ($Topics as $Topic) {

                    $WebmasterSection = WebmasterSection::find($Topic->webmaster_id);
                    if (!empty($WebmasterSection)) {
                        // if private redirect back to home
                        if ($WebmasterSection->type == 4 || $WebmasterSection->type == 7) {
                            // Empty MSG
                            $response = [
                                'msg' => 'There is no data'
                            ];
                            return response()->json($response, 404);
                        }
                    }

                    if ($Topic->$title_var != "") {
                        $topic_title = $Topic->$title_var;
                    } else {
                        $topic_title = $Topic->$title_var2;
                    }
                    $photo_file = $Topic->photo_file;

                    // related topics
                    $response_details = [];
                    foreach ($Topic->relatedTopics as $relatedTopic) {
                        if ($relatedTopic->topic->$title_var != "") {
                            $relatedTopic_title = $relatedTopic->topic->$title_var;
                        } else {
                            $relatedTopic_title = $relatedTopic->topic->$title_var2;
                        }
                        $response_details[] = [
                            'id' => $relatedTopic->topic->id,
                            'title' => $relatedTopic_title,
                            'date' => $relatedTopic->topic->date,
                            'href' => "topic/" . $relatedTopic->topic->id,
                            'photo_file' => ($relatedTopic->topic->photo_file != "") ? url("") . "/uploads/topics/" . $relatedTopic->topic->photo_file : null,
                        ];
                    }

                }
                // Response MSG
                $response = [
                    'msg' => 'Related topics of topic',
                    'topic_id' => $topic_id,
                    'topic_title' => $topic_title,
                    'topic_link' => "topic/" . $topic_id,
                    'topic_photo' => ($photo_file != "") ? url("") . "/uploads/topics/" . $photo_file : null,
                    'related_topics_count' => count($response_details),
                    'related_topics' => $response_details
                ];

                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];

                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];

            return response()->json($response, 404);
        }
    }

    public function topic_fields($topic_id, $lang = '')
    {
        if ($topic_id > 0) {

            // Get topic details
            $Topics = Topic::where([['id', '=', $topic_id], ['status',
                1], ['expire_date', '>=', date("Y-m-d")], ['expire_date', '<>', null]])->orWhere([['id', '=', $topic_id], ['status', 1], ['expire_date', null]])->orderby('row_no', 'asc')->get();

            if (count($Topics) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);
                $title_var = "title_$lang";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                $topic_title = "";
                $photo_file = "";

                // Response Details
                $response_details = [];
                foreach ($Topics as $Topic) {

                    $WebmasterSection = WebmasterSection::find($Topic->webmaster_id);
                    if (!empty($WebmasterSection)) {
                        // if private redirect back to home
                        if ($WebmasterSection->type == 4 || $WebmasterSection->type == 7) {
                            // Empty MSG
                            $response = [
                                'msg' => 'There is no data'
                            ];
                            return response()->json($response, 404);
                        }
                    }

                    if ($Topic->$title_var != "") {
                        $topic_title = $Topic->$title_var;
                    } else {
                        $topic_title = $Topic->$title_var2;
                    }
                    $photo_file = $Topic->photo_file;

                    // additional fields
                    $response_details = [];
                    foreach ($Topic->webmasterSection->customFields->where("in_page", true) as $customField) {
                        if ($customField->in_page) {
                            $cf_saved_val = "";
                            $cf_saved_val_array = array();
                            if (count($Topic->fields) > 0) {
                                foreach ($Topic->fields as $t_field) {
                                    if ($t_field->field_id == $customField->id) {
                                        if ($customField->type == 7) {
                                            // if multi check
                                            $cf_saved_val_array = explode(", ", $t_field->field_value);
                                            $cf_details_var = "details_" . @Helper::currentLanguage()->code;
                                            $cf_details_var2 = "details_" . env('DEFAULT_LANGUAGE');
                                            if ($customField->$cf_details_var != "") {
                                                $cf_details = $customField->$cf_details_var;
                                            } else {
                                                $cf_details = $customField->$cf_details_var2;
                                            }
                                            $cf_details_lines = preg_split('/\r\n|[\r\n]/', $cf_details);
                                            $line_num = 1;
                                            foreach ($cf_details_lines as $cf_details_line) {
                                                if (in_array($line_num, $cf_saved_val_array)) {
                                                    $cf_saved_val .= $cf_details_line . ", ";
                                                }
                                                $line_num++;
                                            }
                                            $cf_saved_val = substr($cf_saved_val, 0, -2);
                                        } else {
                                            $cf_saved_val = $t_field->field_value;
                                        }
                                    }
                                }
                            }

                            if (($cf_saved_val != "" || count($cf_saved_val_array) > 0) && ($customField->lang_code == "all" || $customField->lang_code == "$lang")) {
                                $response_details[] = [
                                    'type' => $customField->type,
                                    'title' => $customField->$title_var,
                                    'value' => $cf_saved_val,
                                ];
                            }
                        }
                    }

                }
                // Response MSG
                $response = [
                    'msg' => 'Additional Fields of topic',
                    'topic_id' => $topic_id,
                    'topic_title' => $topic_title,
                    'topic_link' => "topic/" . $topic_id,
                    'topic_photo' => ($photo_file != "") ? url("") . "/uploads/topics/" . $photo_file : null,
                    'fields_count' => count($response_details),
                    'fields' => $response_details
                ];

                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];

                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];

            return response()->json($response, 404);
        }
    }

    public function user_topics($user_id, $page_number = 1, $topics_count = 0, $lang = '')
    {
        if ($user_id > 0) {
            if ($page_number < 1) {
                $page_number = 1;
            }
            Paginator::currentPageResolver(function () use ($page_number) {
                return $page_number;
            });

            // Get topics
            $Topics = Topic::where([['created_by', '=', $user_id], ['status',
                1], ['expire_date', '>=', date("Y-m-d")], ['expire_date', '<>', null]])->orWhere([['created_by', '=', $user_id], ['status', 1], ['expire_date', null]])->orderby('row_no', 'asc');

            if ($topics_count > 0) {
                $Topics = $Topics->paginate($topics_count);
            } else {
                $Topics = $Topics->get();
            }

            if (count($Topics) > 0) {
                // By Language
                $lang = $this->getLanguage($lang);

                $title_var = "title_$lang";
                $title_var2 = "title_" . env('DEFAULT_LANGUAGE');
                $details_var = "details_$lang";
                $details_var2 = "details_" . env('DEFAULT_LANGUAGE');
                $user_name = "";

                // Response Details
                $response_details = [];
                $ic = 0;
                foreach ($Topics as $Topic) {

                    $WebmasterSection = WebmasterSection::find($Topic->webmaster_id);
                    if (!empty($WebmasterSection)) {
                        // if private redirect back to home
                        if ($WebmasterSection->type == 4 || $WebmasterSection->type == 7) {
                            continue;
                        }
                    }
                    $ic++;
                    $type = $Topic->webmasterSection->type;
                    $section_name = $Topic->webmasterSection->name;
                    $section_id = $Topic->webmasterSection->id;
                    $user_name = $Topic->user->name;


                    $Joined_categories = [];
                    foreach ($Topic->categories as $category) {

                        if ($category->section->$title_var != "") {
                            $category_title = $category->section->$title_var;
                        } else {
                            $category_title = $category->section->$title_var2;
                        }

                        $Joined_categories[] = [
                            'id' => $category->id,
                            'title' => $category_title,
                            'icon' => $category->section->icon,
                            'photo' => ($category->section->photo != "") ? url("") . "/uploads/sections/" . $category->section->photo : null,
                            'href' => "topics/cat/" . $category->id
                        ];
                    }

                    // additional fields
                    $Additional_fields = [];
                    foreach ($Topic->webmasterSection->customFields->where("in_listing", true) as $customField) {
                        if ($customField->in_page) {

                            $cf_saved_val = "";
                            $cf_saved_val_array = array();
                            if (count($Topic->fields) > 0) {
                                foreach ($Topic->fields as $t_field) {
                                    if ($t_field->field_id == $customField->id) {
                                        if ($customField->type == 7) {
                                            // if multi check
                                            $cf_saved_val_array = explode(", ", $t_field->field_value);
                                            $cf_details_var = "details_" . @Helper::currentLanguage()->code;
                                            $cf_details_var2 = "details_" . env('DEFAULT_LANGUAGE');
                                            if ($customField->$cf_details_var != "") {
                                                $cf_details = $customField->$cf_details_var;
                                            } else {
                                                $cf_details = $customField->$cf_details_var2;
                                            }
                                            $cf_details_lines = preg_split('/\r\n|[\r\n]/', $cf_details);
                                            $line_num = 1;
                                            foreach ($cf_details_lines as $cf_details_line) {
                                                if (in_array($line_num, $cf_saved_val_array)) {
                                                    $cf_saved_val .= $cf_details_line . ", ";
                                                }
                                                $line_num++;
                                            }
                                            $cf_saved_val = substr($cf_saved_val, 0, -2);
                                        } else {
                                            $cf_saved_val = $t_field->field_value;
                                        }
                                    }
                                }
                            }

                            if (($cf_saved_val != "" || count($cf_saved_val_array) > 0) && ($customField->lang_code == "all" || $customField->lang_code == "$lang")) {

                                if ($customField->$title_var != "") {
                                    $customField_title = $customField->$title_var;
                                } else {
                                    $customField_title = $customField->$title_var2;
                                }

                                $Additional_fields[] = [
                                    'type' => $customField->type,
                                    'title' => $customField_title,
                                    'value' => $cf_saved_val,
                                ];
                            }
                        }
                    }

                    $video_file = $Topic->video_file;
                    if ($Topic->video_type == 0) {
                        $video_file = ($Topic->video_file != "") ? url("") . "/uploads/topics/" . $Topic->video_file : "";
                    }

                    if ($Topic->$title_var != "") {
                        $Topic_title = $Topic->$title_var;
                    } else {
                        $Topic_title = $Topic->$title_var2;
                    }
                    if ($Topic->$details_var != "") {
                        $Topic_details = $Topic->$details_var;
                    } else {
                        $Topic_details = $Topic->$details_var2;
                    }

                    $response_details[] = [
                        'id' => $Topic->id,
                        'title' => $Topic_title,
                        'details' => $Topic_details,
                        'date' => $Topic->date,
                        'video_type' => $Topic->video_type,
                        'video_file' => $video_file,
                        'photo_file' => ($Topic->photo_file != "") ? url("") . "/uploads/topics/" . $Topic->photo_file : null,
                        'audio_file' => ($Topic->audio_file != "") ? url("") . "/uploads/topics/" . $Topic->audio_file : null,
                        'icon' => $Topic->icon,
                        'visits' => $Topic->visits,
                        'href' => "topic/" . $Topic->id,
                        'fields_count' => count($Additional_fields),
                        'fields' => $Additional_fields,
                        'Joined_categories_count' => count($Topic->categories),
                        'Joined_categories' => $Joined_categories,
                        'section_id' => $section_id,
                        'section_name' => $section_name,
                        'section_type' => $type,

                    ];

                }
                // Response MSG
                $response = [
                    'msg' => 'List of Topics for user',
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'topics_count' => $ic,
                    'topics' => $response_details
                ];

                return response()->json($response, 200);
            } else {
                // Empty MSG
                $response = [
                    'msg' => 'There is no data'
                ];

                return response()->json($response, 200);
            }
        } else {
            // Empty MSG
            $response = [
                'msg' => 'There is no data'
            ];
            return response()->json($response, 404);
        }
    }

    public function ContactPageSubmit(Request $request)
    {

        $this->validate($request, [
            'api_key' => 'required',
            'contact_name' => 'required',
            'contact_email' => 'required|email',
            'contact_subject' => 'required',
            'contact_message' => 'required'
        ]);

        // check api_key
        if ($request->api_key == Helper::GeneralWebmasterSettings("api_key")) {
            // SITE SETTINGS
            $WebsiteSettings = Setting::find(1);
            $site_title_var = "site_title_" . @Helper::currentLanguage()->code;
            $site_email = $WebsiteSettings->site_webmails;
            $site_url = $WebsiteSettings->site_url;
            $site_title = $WebsiteSettings->$site_title_var;

            $Webmail = new Webmail;
            $Webmail->cat_id = 0;
            $Webmail->group_id = null;
            $Webmail->title = $request->contact_subject;
            $Webmail->details = $request->contact_message;
            $Webmail->date = date("Y-m-d H:i:s");
            $Webmail->from_email = $request->contact_email;
            $Webmail->from_name = $request->contact_name;
            $Webmail->from_phone = $request->contact_phone;
            $Webmail->to_email = $WebsiteSettings->site_webmails;
            $Webmail->to_name = $site_title;
            $Webmail->status = 0;
            $Webmail->flag = 0;
            $Webmail->save();

            // SEND Notification Email
            if (@Helper::GeneralSiteSettings('notify_messages_status')) {
                $recipient = explode(",", str_replace(" ", "", $site_email));
                $message_details = __('frontend.name') . ": " . $request->contact_name . "<hr>" . __('frontend.phone') . ": " . $request->contact_phone . "<hr>" . __('frontend.email') . ": " . $request->contact_email . "<hr>" . __('frontend.message') . ":<br>" . nl2br($request->contact_message);

                Mail::to($recipient)->send(new NotificationEmail(
                    [
                        "title" => $request->contact_subject,
                        "details" => $message_details,
                        "from_email" => $request->contact_email,
                        "from_name" => $request->contact_name
                    ]
                ));
            }

            // response MSG
            $response = [
                'code' => '1',
                'msg' => 'Message Sent successfully'
            ];
            return response()->json($response, 201);
        } else {
            // Empty MSG
            $response = [
                'code' => '-1',
                'msg' => 'Authentication failed'
            ];
            return response()->json($response, 500);
        }
    }

    public function subscribeSubmit(Request $request)
    {

        $this->validate($request, [
            'api_key' => 'required',
            'subscribe_name' => 'required',
            'subscribe_email' => 'required|email'
        ]);
        // check api_key
        if ($request->api_key == Helper::GeneralWebmasterSettings("api_key")) {
            // General Webmaster Settings
            $WebmasterSettings = WebmasterSetting::find(1);

            $Contacts = Contact::where('email', $request->subscribe_email)->get();
            if (count($Contacts) > 0) {
                // response MSG
                $response = [
                    'code' => '2',
                    'msg' => 'You are already subscribed'
                ];
                return response()->json($response, 200);
            } else {
                $subscribe_names = explode(' ', $request->subscribe_name, 2);

                $Contact = new Contact;
                $Contact->group_id = $WebmasterSettings->newsletter_contacts_group;
                $Contact->first_name = @$subscribe_names[0];
                $Contact->last_name = @$subscribe_names[1];
                $Contact->email = $request->subscribe_email;
                $Contact->status = 1;
                $Contact->save();


                // response MSG
                $response = [
                    'code' => '1',
                    'msg' => 'You have subscribed successfully'
                ];
                return response()->json($response, 201);
            }
        } else {
            // Empty MSG
            $response = [
                'code' => '-1',
                'msg' => 'Authentication failed'
            ];
            return response()->json($response, 500);
        }
    }

    public function commentSubmit(Request $request)
    {

        $this->validate($request, [
            'api_key' => 'required',
            'topic_id' => 'required',
            'comment_name' => 'required',
            'comment_email' => 'required|email',
            'comment_message' => 'required'
        ]);

        // check api_key
        if ($request->api_key == Helper::GeneralWebmasterSettings("api_key")) {
            // General Webmaster Settings
            $WebmasterSettings = WebmasterSetting::find(1);

            $next_nor_no = Comment::where('topic_id', '=', $request->topic_id)->max('row_no');
            if ($next_nor_no < 1) {
                $next_nor_no = 1;
            } else {
                $next_nor_no++;
            }

            $Comment = new Comment;
            $Comment->row_no = $next_nor_no;
            $Comment->name = $request->comment_name;
            $Comment->email = $request->comment_email;
            $Comment->comment = $request->comment_message;
            $Comment->topic_id = $request->topic_id;;
            $Comment->date = date("Y-m-d H:i:s");
            $Comment->status = $WebmasterSettings->new_comments_status;
            $Comment->save();

            // Site Details
            $WebsiteSettings = Setting::find(1);
            $site_title_var = "site_title_" . @Helper::currentLanguage()->code;
            $site_email = $WebsiteSettings->site_webmails;
            $site_url = $WebsiteSettings->site_url;
            $site_title = $WebsiteSettings->$site_title_var;

            // Topic details
            $Topic = Topic::where('status', 1)->find($request->topic_id);
            if (!empty($Topic)) {
                $tpc_title_var = "title_" . @Helper::currentLanguage()->code;
                $tpc_title = $WebsiteSettings->$tpc_title_var;

                // SEND Notification Email
                if (@Helper::GeneralSiteSettings('notify_comments_status')) {
                    $recipient = explode(",", str_replace(" ", "", $site_email));
                    $message_details = __('frontend.name') . ": " . $request->comment_name . "<hr>" . __('frontend.email') . ": " . $request->comment_email . "<hr>" . __('frontend.comment') . ":<br>" . nl2br($request->comment_message);

                    Mail::to($recipient)->send(new NotificationEmail(
                        [
                            "title" => "Comment: " . $tpc_title,
                            "details" => $message_details,
                            "from_email" => $request->comment_email,
                            "from_name" => $request->comment_name
                        ]
                    ));
                }

                // response MSG
                $response = [
                    'code' => '1',
                    'msg' => 'Your Comment Sent successfully'
                ];
                return response()->json($response, 201);
            } else {
                // response MSG
                $response = [
                    'code' => '0',
                    'msg' => 'There is no data'
                ];
                return response()->json($response, 404);
            }
        } else {
            // Empty MSG
            $response = [
                'code' => '-1',
                'msg' => 'Authentication failed'
            ];
            return response()->json($response, 500);
        }
    }

    public function orderSubmit(Request $request)
    {

        $this->validate($request, [
            'api_key' => 'required',
            'topic_id' => 'required',
            'order_name' => 'required',
            'order_phone' => 'required',
            'order_email' => 'required|email'
        ]);

        // check api_key
        if ($request->api_key == Helper::GeneralWebmasterSettings("api_key")) {
            $WebsiteSettings = Setting::find(1);
            $site_title_var = "site_title_" . @Helper::currentLanguage()->code;
            $site_email = $WebsiteSettings->site_webmails;
            $site_url = $WebsiteSettings->site_url;
            $site_title = $WebsiteSettings->$site_title_var;

            $Topic = Topic::where('status', 1)->find($request->topic_id);
            if (!empty($Topic)) {
                $tpc_title_var = "title_" . @Helper::currentLanguage()->code;
                $tpc_title = $WebsiteSettings->$tpc_title_var;

                $Webmail = new Webmail;
                $Webmail->cat_id = 0;
                $Webmail->group_id = null;
                $Webmail->contact_id = null;
                $Webmail->father_id = null;
                $Webmail->title = "ORDER " . ", " . $Topic->$tpc_title_var;
                $Webmail->details = $request->order_message;
                $Webmail->date = date("Y-m-d H:i:s");
                $Webmail->from_email = $request->order_email;
                $Webmail->from_name = $request->order_name;
                $Webmail->from_phone = $request->order_phone;
                $Webmail->to_email = $WebsiteSettings->site_webmails;
                $Webmail->to_name = $WebsiteSettings->$site_title_var;
                $Webmail->status = 0;
                $Webmail->flag = 0;
                $Webmail->save();


                // SEND Notification Email
                if (@Helper::GeneralSiteSettings('notify_orders_status')) {
                    $recipient = explode(",", str_replace(" ", "", $site_email));
                    $message_details = __('frontend.name') . ": " . $request->order_name . "<hr>" . __('frontend.phone') . ": " . $request->order_phone . "<hr>" . __('frontend.email') . ": " . $request->order_email . "<hr>" . __('frontend.notes') . ":<br>" . nl2br($request->order_message);

                    Mail::to($recipient)->send(new NotificationEmail(
                        [
                            "title" => "Order: " . $tpc_title,
                            "details" => $message_details,
                            "from_email" => $request->order_email,
                            "from_name" => $request->order_name
                        ]
                    ));
                }

                // response MSG
                $response = [
                    'code' => '1',
                    'msg' => 'Your Order Sent successfully'
                ];
                return response()->json($response, 201);
            } else {
                // response MSG
                $response = [
                    'code' => '0',
                    'msg' => 'There is no data'
                ];
                return response()->json($response, 404);
            }
        } else {
            // Empty MSG
            $response = [
                'code' => '-1',
                'msg' => 'Authentication failed'
            ];
            return response()->json($response, 500);
        }

    }

    //Done
    //SMAI Sync

    

    public function smaisync_main_tokens(Request $request)
    {
        $user_id = $request->user_id;
        $usage = $request->usage;
        $data = $request->data;
        $main_message_id = $request->main_useropenai_message_id;

        Log::debug('DEbug Data from Start MainCoIn Sync Token : '.$data);
        Log::debug('DEbug REquest from Start  MainCoIn Sync Token : '.$request);
        Log::debug('DEbug Usage from Start  MainCoIn Sync Token : '.$usage);

        Log::debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        Log::debug('DEbug Main Message ID from Start  MainCoIn Sync Token : '. $main_message_id );
        Log::debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
       

        if(isset($request->params_input))
        $params =$request->params_input;
        else if(isset($request['params_input']))
        $params =$request['params_input'];
        else 
        $params = json_decode($request->params_input, true);


        if (isset($params['gpt_category']))
            $chatGPT_catgory = $params['gpt_category'];
        else
            $chatGPT_catgory = NULL;

        if (isset($params['chat_name']))
            $chat_name = $params['chat_name'];
        else
            $chat_name = NULL;


        if (isset($params['platform']))
            $from = $params['platform'];
        else
            $from = '';


        if (isset($params['chat_id']))
            $chat_id = $params['chat_id'];
        else
            $chat_id = '';
           
        if (isset($params['response']))
        {
            $response = $params['response'];
            $response_text =$response;

        }
        else{
            $response_text=NULL;
        }

        Log::debug('$data  MainCoIn that from response smaisync_tokens from APIsController : ' . info(print_r($data, true)));
        Log::debug('$params  MainCoIn smaisync_tokens from APIsController : ' . info(print_r($params, true)));
        Log::info(print_r($params, true));
        Log::debug('User ID  MainCoIn log in smaisync_tokens in Main APIsController from Digital_Asset : ' . $user_id);
        Log::debug('With Response!!!!!!!!!!! '.$response);

        

        Log::debug('check main inAPIs main_useropenai_message_id before send '.$main_message_id);
        //update UserOpenAI entry CHAT
        $new_update_main_lower_save = new SMAISyncTokenController($data, $usage, $chatGPT_catgory, $chat_id,NULL,$chat_name,$params,$user_id,$response_text,$main_message_id);
        $new_update_main_lower_save->lowGenerateSaveAll($usage,$response,$main_message_id);

/*        //continue... From update UserOpenAI entry Zone Fix Documents or UserOpenAI Table
       $update_fix_Bio_Doc=0;
       if (( Str::contains($chatGPT_catgory, 'DocText')==true) && ( Str::contains($chatGPT_catgory, 'SmartBio')==false) && (Str::contains($chatGPT_catgory,"Bio")==false))
       $update_fix_Bio_Doc=1;
       if( $user_id > 0 &&  $from != 'bio' && $update_fix_Bio_Doc==1 &&  Str::length($response) > 2 && (Str::contains($response,"null")==false) )
       {
          $new_update_main_lower_save->Save_Bio_Documents($params,$output=NULL, $response=NULL ,$user_id,$usage,$main_message_id);
       }
       // Eof Zone Fix Documents or UserOpenAI Table */

       
       
       //this below should be token centralize
        $user_data_db=UserMain::where('id',$user_id)->first();
        $remaining_images=$user_data_db->remaining_images;
        $remaining_words=$user_data_db->remaining_words;
        $user_email = $user_data_db->email;



        $old_reamaining_word=$user_data_db->remaining_words;
        $old_reamaining_image=$user_data_db->remaining_images;

        $remaining_words-=$usage;

        if(isset($usage) && $usage>0)
        $token_update_type="text";
        else
        $token_update_type="both";

        $token_array= array(
            
            'remaining_images' => $remaining_images,
            'remaining_words' => $remaining_words,
        );


        $new_token_centralize=NEW SMAIUpdateProfileController();
        $new_token_centralize->update_token_centralize($user_id,$user_email,$token_array,$usage,$from,$old_reamaining_word,$old_reamaining_image,$chatGPT_catgory,$token_update_type);

    }

    public function smai_translation_lang_folder(Request $request)
    {

         $contents=$request->contents;
         $connection=$request->connection;
         //var_dump($contents); // show contents
         $contents=json_decode($contents,true);

         $new_lang_key_array=$contents['key'];
         $new_lang_desc_array=$contents['desc'];

        //Log::info( $new_lang_desc_array);

        //start or stop
        //$conn_smart=array();
        $new_punbot_func=NEW SMAI_SEO_PUNBOTController();
        $lang_table='translation_example_lang';

        if($connection=='bio_db')
        {
                           date_default_timezone_set('Asia/Bangkok');
	                       // Host Name
                            $db_hostname_s = 'localhost';
                            // Database Name
                            $db_name_s = 'cafealth_smartbio';
                            // Database Username
                            $db_username_s = 'cafealth_smartbio';
                            // Database Password
                            $db_password_s = 'n0[R}PZ.92hF';
                            // define( 'DB_CHARSET', 'utf8mb4' );
                            try {

                                $conn_smart = new PDO("mysql:host=$db_hostname_s;dbname=$db_name_s",$db_username_s,$db_password_s, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
                                $conn_smart->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                
                            }
                            catch(PDOException $e){
                                Log::info($e->getMessage('utf8mb4'));
                            }

        }

        if($connection=='main_old_coin')
        {
                           date_default_timezone_set('Asia/Bangkok');
                            // Host Name
                            $db_hostname_s = 'localhost';
                            // Database Name
                            $db_name_s = 'cafealth_smartcontent_coin';
                            // Database Username
                            $db_username_s = 'cafealth_smartcontent_coin';
                            // Database Password
                            $db_password_s ='2[nolVnpJ@V6';
                            // define( 'DB_CHARSET', 'utf8mb4' );
                            try {

                                $conn_smart = new PDO("mysql:host=$db_hostname_s;dbname=$db_name_s",$db_username_s,$db_password_s, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
                                $conn_smart->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                
                            }
                            catch(PDOException $e){
                                Log::info($e->getMessage('utf8mb4'));
                            }

        }

       //$return_lang_ins= $new_punbot_func->ins_lang_smart_content($lang_table,$new_lang_key_array,$new_lang_desc_array,$conn_smart);
       $return_lang_ins=1;
       
       if($return_lang_ins==1)
       {
         // Response MSG
         $response = [
            'msg' => 'Translate Status details',
            'details' => $return_lang_ins
        ];
        return response()->json($response, 200);
       }
       else{
        // Response MSG
        $response = [
            'msg' => 'Translate Status details',
            'details' => $return_lang_ins
        ];
        return response()->json($response, 404);
       }


    
        

    }


    public function smai_translation(Request $request)
    {

        Log::debug('Debug request from smai_translation from APIsController : ');
        Log::info($request);
        $new_translation=NEW AIController();
        $text_translated=$new_translation->buildOutput($request);
        // Log::debug('Debug response translated '.$text_translated);
         Log::info($text_translated);
        return  $text_translated;


    }

    public function smai_text_gen(Request $request)
    {

        $new_text_gen=NEW AIController();
        $text_gen=$new_text_gen->buildOutput($request);
        // Log::debug('Debug response translated '.$text_translated);
         Log::info($text_gen);
        return  $text_gen;


    }

    public function smai_text_gen_inside($request)
    {

        $new_text_gen=NEW AIController();
        $text_gen=$new_text_gen->buildOutput($request);
        // Log::debug('Debug response translated '.$text_translated);
         Log::info($text_gen);
        return  $text_gen;


    }

    public function smaisync_tokens(Request $request)
    {
        $user_id = $request->user_id;
        $usage = $request->usage;
        $data_req = $request->data;


        if(is_array($data_req)==false)
        {
        Log::debug('DEbug Data from Start  Sync Token : '.$data_req);
        Log::debug('DEbug REquest from Start   Sync Token : '.$request);
        Log::debug('DEbug Usage from Start  Sync Token : '.$usage);
        }
        else{
            Log::info($data_req);

        }
        

        //$params = json_decode($request->params_input, true);

       // Log::debug('DEbug $request->params_input from Start  Sync Token : '.$request->params_input);
       // Log::debug('DEbug $request[params_input] from Start  Sync Token : '.$request['params_input']);
        if(isset($request->params_input))
        $params =$request->params_input;
        else if(isset($request['params_input']))
        $params =$request['params_input'];
        else 
        $params = json_decode($request->params_input, true);

        if(is_array($params)==FALSE)
        $params = json_decode($params, true);


        Log::debug('!!!!! Final Debug Params : ');
        Log::info($params);

        //Log::info($params['platform']);
        if (isset($params->gpt_category))
        $chatGPT_catgory =$params->gpt_category;
        else if (isset($params['gpt_category']))
            $chatGPT_catgory = $params['gpt_category'];
        else
            $chatGPT_catgory = NULL;


            if (isset($params->chat_name))
            $chat_name =$params->chat_name;
            if (isset($params['chat_name']))
            $chat_name = $params['chat_name'];
            else
            $chat_name = NULL;


        if (isset($params->platform))
              $from =$params->platform;
        else if (isset($params['platform']))
            $from = $params['platform'];
        else
            $from = NULL;




        if (isset($params->chat_id))
           $chat_id =$params->chat_id;
        else if (isset($params['chat_id']))
            $chat_id = $params['chat_id'];
        else
            $chat_id = NULL;


            if (isset($params->chat_main_id))
            $chat_main_id =$params->chat_main_id;
         else if (isset($params['chat_main_id']))
             $chat_main_id = $params['chat_main_id'];
         else
             $chat_main_id = NULL; 
            
             

            if (isset($params->model))
            $model_gpt =$params->model;
            else if (isset($params['model']))
            $model_gpt = $params['model'];
            else
            $model_gpt = NULL;


           if (isset($params->prompt))
           $prompt =$params->prompt;
           else if (isset($params['prompt']))
           $prompt = $params['prompt'];
           else
           $prompt = NULL;



        Log::debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!! ');   
        Log::debug('From Platform !!!!!!! '.$from);   
        Log::debug(' cAtgory !!!!!!!!!!!!!!!!!!!!!!!!!!!! '.$chatGPT_catgory);   
        Log::debug('$data_req  that from response smaisync_tokens from APIsController : ' . info(print_r($data_req, true)));
        Log::debug('$params smaisync_tokens from APIsController : ' . info(print_r($params, true)));
        Log::info(print_r($params, true));
        Log::debug('User ID log in smaisync_tokens in Main APIsController from Digital_Asset : ' . $user_id);


        /*  if($prompt=='SKIP' && $model_gpt=='SKIP')
        {
            Log::debug('!!!!!! Start add new chat from contructore !!!!');
            $add_new_chat=NEW SMAISyncTokenController($data_req, $usage, $chatGPT_catgory, $chat_id,$chat_main_id,$chat_name,$params,$user_id);
        }  */
     
        if(Str::contains($chatGPT_catgory,'Images_'))
        {

            // $user_id,$usage,$data_image,$image_params
            $new_update_main_image = new SMAISyncTokenController($data_req, $usage, $chatGPT_catgory, $chat_id=NULL,NULL,NULL,$params,$user_id);
            
            if($chatGPT_catgory!='Images_SmartContentCoIn')
            {
            $return_arr = $new_update_main_image->imageOutput_save_main_coin($user_id, $usage, $data_req, $params,$size=NULL, $post=NULL,  $style=NULL, $lighting=NULL, $mood=NULL, $number_of_images=1, $image_generator='DE', $negative_prompt=NULL,NULL);
            }
            else{

                if (isset($params->prompt))
                $prompt =$params->prompt;
                else if (isset($params['prompt']))
                $prompt = $params['prompt'];
                else
                $prompt = NULL;


                if (isset($params->size))
                $size =$params->size;
                else if (isset($params['size']))
                $size = $params['size'];
                else
                $size = NULL;

                
                if (isset($params->contents))
                $contents_img =$params->contents;
                else if (isset($params['contents']))
                $contents_img = $params['contents'];
                else
                $contents_omg = NULL;


                if (isset($params->file_size))
                $file_size =$params->file_size;
                else if (isset($params['file_size']))
                $file_size = $params['file_size'];
                else
                $file_size = NULL;

                

                $main_img_openai=UserOpenai::where('output',$contents_img)->first();
               


                $style=NULL;
                $lighting=NULL;
                $mood=NULL;

                $image_arr = array(
                    'style' => $style,
                    'artist' => 'Leonardo da Vinci',
                    'lighting' => $lighting,
                    'mood' => $mood,
                );
                $image_arr['main_image_id'] =$main_img_openai->id;
                $image_arr['size'] = $size;
                $image_arr['file_size'] = $file_size;

                $path_array=array();
                array_push($path_array, $contents_img);

                $return_arr=array(

                    'path_array' => $path_array,
                    'image_array' => $image_arr,
                );

                


            }
           

            Log::debug('Return array from new_update_main_image ');
            Log::info($return_arr);

            $path_array =$return_arr['path_array'];
            $image_array =$return_arr['image_array'];

            //save image to BIo OpenAI
            $number_of_images=$params['n'];
            $prompt=$params['prompt'];
            $image_array['size']=$params['size'];
            $main_image_id=$image_array['main_image_id'];
            //$image_array['img_width']
            //$image_array['img_height']
            $new_update_main_image->imageOutput_save_Bio($user_id,$prompt, $number_of_images,$path_array,$image_array,$main_image_id);
           


            //save image to SocialPost OpenAI
            if(Str::contains($chatGPT_catgory,'SocialPost')==false)
            $new_update_main_image->imageOutput_save_SocialPost($user_id,$prompt, $number_of_images,$path_array,$image_array,$main_image_id);
            


            //save image to Design OpenAI
            $new_update_main_image->imageOutput_save_Design($user_id,$prompt, $number_of_images,$path_array,$main_image_id);

            //save image to Sync OpenAI
            $new_update_main_image->imageOutput_save_Sync($user_id,$prompt, $number_of_images,$path_array,$main_image_id);

            //save image to MobielApp OpenAI
            $new_update_main_image->imageOutput_save_MobileAppV2($user_id,$prompt, $number_of_images,$path_array,$main_image_id);

        }
        else
        {

        $new_update_digitalasset = new SMAISyncTokenController($data_req, $usage, $chatGPT_catgory, $chat_id,$chat_main_id,$chat_name,$params,$user_id);
        
          
        // if not called from SocialPost add extra update to MainCoIn table
        if ($from != 'main_coin')
        {
           $main_message_array = $new_update_digitalasset->SMAI_UpdateGPT_MainCoIn($user_id, $usage, $data_req, $params,$from,NULL);
           
           Log::debug('debug return message array !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
           Log::info($main_message_array);

           if(isset($main_message_array['message_id']))
           $main_message_id=$main_message_array['message_id'];
           
           if($usage==NULL || !isset($usage))
            {
                if(isset($main_message_array['total_used_tokens']))
                $usage=$main_message_array['total_used_tokens'];
            }
        }
        else
        {
            if(isset($params->main_useropenai_message_id))
             $main_message_id =  $params->main_useropenai_message_id;
         
             if(isset($params['main_useropenai_message_id']))
             $main_message_id = $params['main_useropenai_message_id'];

        
        
        }

        if(isset($main_message_id))
        {
        Log::debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');

        
        Log::debug(' Check main_message_id Before next Step '.$main_message_id );
       
        Log::debug('!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
        
        
        //update DEsign
        $new_update_digitalasset->SMAI_UpdateGPT_DigitalAsset($user_id, $usage, $data_req, $params,$from,$main_message_id);



        // if not called from SocialPost add extra update to MobileApp table

        if ($from != 'MobileAppV2')
            $new_update_digitalasset->SMAI_UpdateGPT_MobileApp($user_id, $usage, $data_req, $params,$from,$main_message_id);


        // if not called from SocialPost add extra update to SocialPost SP table
        $new_update_digitalasset->SMAI_UpdateGPT_SocialPost($user_id, $usage, $data_req, $params,$from,$main_message_id);

        if ($from != 'bio' )
        { 
            if(Str::contains($chatGPT_catgory,'DocText_SocialPost')==true)
             {
               $new_update_digitalasset->SMAI_UpdateGPT_Bio($user_id, $usage, $data_req, $params,$from,$main_message_id);
             }
            else if((Str::contains($chatGPT_catgory,'OtherSocialText_SmartBio')==false) && (Str::contains($chatGPT_catgory,'DocText')==false))
            { 
              $new_update_digitalasset->SMAI_UpdateGPT_Bio($user_id, $usage, $data_req, $params,$from,$main_message_id);
            }
            else if(Str::contains($chatGPT_catgory,'DocText_SmartContentCoIn')==true )
            {

              $new_update_digitalasset->Save_Bio_Documents($params, NULL,NULL,$user_id,$usage,$main_message_id);

            }
            else{

            }
      
        }


        if ($from != 'SyncNodeJS')
        $new_update_digitalasset->SMAI_UpdateGPT_SyncNodeJS($user_id, $usage, $data_req, $params,$from,$main_message_id);

       
            if ($from != 'main_marketing')
        {
            // $new_update_digitalasset->SMAI_UpdateGPT_MainMarketing($user_id, $usage, $data_req, $params,$from);

        }
        /* $response = [
            'code' => '1',
            'msg' => 'success'
        ];
        return response()->json($response, 201); */

        //$data_json = json_decode($data_req ,true);

      }


    }

   

        $user_data_db=UserMain::where('id',$user_id)->first();
        $remaining_images=$user_data_db->remaining_images;
        $remaining_words=$user_data_db->remaining_words;
        $user_email = $user_data_db->email;

        $old_reamaining_word=$user_data_db->remaining_words;
        $old_reamaining_image=$user_data_db->remaining_images;
        



         

        if(Str::contains($chatGPT_catgory,'Images_'))
        {
            Log::debug('Debug case Images GPT with usage '.$usage);

            $remaining_images-=$usage;
            $token_update_type="image";
            $token_array= array(
                
                'remaining_images' => $remaining_images,
                'remaining_words' => $remaining_words,
            );

            $new_token_centralize=NEW SMAIUpdateProfileController();
            $new_token_centralize->update_token_centralize($user_id,$user_email,$token_array,$usage,$from,$old_reamaining_word,$old_reamaining_image,$chatGPT_catgory,$token_update_type);

        }
        else{

            Log::debug('Debug case None-Images GPT with usage '.$usage);
            

            $remaining_words-=$usage;



            if(isset($usage) && $usage>0)
            $token_update_type="text";
            else
            $token_update_type="both";

            $token_array= array(
                
                'remaining_images' => $remaining_images,
                'remaining_words' => $remaining_words,
            );

           
           
            if($usage!=NULL && isset($usage))
           {
            $new_token_centralize=NEW SMAIUpdateProfileController();
            $new_token_centralize->update_token_centralize($user_id,$user_email,$token_array,$usage,$from,$old_reamaining_word,$old_reamaining_image,$chatGPT_catgory,$token_update_type);
           }

        }


    }

    //Done
    public function smaicheck_column(Request $request)
    {

        Log::debug('Debug this in check Column APIsController ');
        Log::debug('and info request ');
        Log::info($request);
        //read user_id , key (column name), database
        $user_id = $request->user_id;
        $key = $request->key;
        $database = $request->database;

        $checktoken_digitalasset = new SMAISyncTokenController();
        $token_total = $checktoken_digitalasset->SMAI_Check_DigitalAsset_UserColumn($user_id, $key, $database);

        return $token_total;
    }

    public function smaiupdate_column(Request $request)
    {
        Log::debug('Debug this in update Column APIsController '.$platform);
        Log::debug('and info request ');
        Log::info($request);

        //read user_id , key (column name), database
        $user_id = $request->user_id;
        $key = $request->key;
        $database = $request->database;

        $update_column_digitalasset = new SMAISyncTokenController();
        $update_column_digitalasset->SMAI_Update_UserColumn($user_id, $key, $database);


    }

    //Done
    //SMAI for add new user from all Platforms
    public function smainewuser_createallfreetrial(Request $request)
    {

        //read data, key (column name), database

        if (isset($request->userId))
            $uid = $request->userId;
        else
            $uid = $request->id;

        if (isset($request->email))
            $user_email = $request->email;

        $new_signup = new SMAISessionAuthController($request);

        //1.TODO add new user SocialPost Demo
        //create session DB

        $user_id = $new_signup->freetrial_socialpost($request, $uid);
        //EOF TODO SocialPost Demo

        echo $user_id;

        //

        if ($user_id == -1)
            $user_id = $uid;

        //2.TODO add new user Main .co.in Demo
        $new_signup->freetrial_main_co_in($request, $user_id);

        //TODO add new user old Mobile app Demo
        $new_signup->freetrial_mobileApp($request, $user_id);
        //EOF TODO Mobile app Demo


        //3.TODO add new user  Design app Demo
        //$id = auth()->user()->id;
        //$user = User::where('id', $id )->first();

        $new_signup->freetrial_design($request, $user_id);


        //4.TODO add new user newMobileV2 app platform
        if (isset($request->platform))
            $from = $request->platform;
        else
            $from = '';

        if ($from == "MobileAppV2")
            $new_signup->freetrial_mobileAppV2_email($request, $user_id, $user_email);
        else
            $new_signup->freetrial_mobileAppV2($request, $user_id);


        //5.TODO add new user Smart BIO app
        // $new_signup->freetrial_bio($request,$user_id);
        if ($from != "SmartBio") {
            $new_signup->freetrial_bio($request, $user_id);
        }


        //6.TODO add new user Smart Bio Blog app
        $new_signup->freetrial_bio_blog($request, $user_id);


        //7.TODO add new user CRM app
        $new_signup->freetrial_crm($request, $user_id);

        //8.TODO add new user Sync app
        $new_signup->freetrial_sync_node($request, $user_id);

        //9. TODO add new user Course Laravel app
        $new_signup->freetrial_course($request, $user_id);

        //10. TODO add new user Live Shopping app
        $new_signup->freetrial_liveshop($request, $user_id);

        //11. TODO add new user SEO app
        $new_signup->freetrial_seo($request, $user_id);


    }


    //Working
    //SMAI check user plan from all Platforms
    public function smaicheck_plans(Request $request)
    {
        $platform = $request->platform;
        $database = $request->database;
        $user_id = $request->user_id;

        Log::debug('Debug this in check Plan APIsController '.$platform);
        Log::debug('and info request ');
        Log::info($request);

        $check_plans_user = new SMAISyncPlanController();

        $return_plan = array();
        $return_plan = $check_plans_user->SMAI_Check_Universal_UserPlans($user_id, $database, $platform);

        if ($return_plan != 0)
            return json_encode($return_plan);
        else
            return 0;

    }

    //SMAI for update plan user from all Platforms
    public function smaiuser_update_plan(Request $request)
    {

        Log::debug("API reach APIs Controller of smaiuser_update_plan of User Email : ");
        $request_update = $request->data;
        $user_id = $request->user_id;
        $user_email = $request->email;
        $whatup = $request->whatup;

        if (isset($request->upFromWhere))
            $upFromWhere = $request->upFromWhere;
        else
            $upFromWhere = NULL;

        Log::info($user_email);

        $update_plan_user = new SMAISyncPlanController($request, $user_id, $user_email, $whatup, $upFromWhere);
        $update_plan_user->SMAI_Update_Universal_UserPlans($request, $user_id, $user_email, $whatup, $upFromWhere);


    }

    //SMAI for update any column of user from all Platforms
    public function smaiuser_update_column(Request $request)
    {

    }

    //SMAI for update profile_column_group of user from all Platforms
    public function smaiuser_update_profile(Request $request)
    {

        Log::debug("API reach APIs Controller of smaiuser_update_profile of User Email : ");
        $request_update = $request->data;
        $user_id = $request->user_id;
        $user_email = $request->email;
        $whatup = $request->whatup;

        if (isset($request->upFromWhere))
            $upFromWhere = $request->upFromWhere;
        else
            $upFromWhere = NULL;

        Log::info($user_email);

        $update_profile_user = new SMAIUpdateProfileController($request, $user_id, $user_email, $whatup, $upFromWhere);

        if (in_array("BioReset", $whatup))
        {
            return response()->json($update_profile_user);

        }


    }

    public function smai_seo_manage_cron_all_posts($id)
    {

        Log::debug('Start accept value form Website ID '.$id);
        Log::info($id);

        $new_seo_sync=NEW SMAISyncSEOController();
        $response_onoff=$new_seo_sync->cron_seo_on_off_posts($id);
        return $response_onoff;


    }
    public function smai_seo_import_punbot_backlinks($id)
    {

        Log::debug('Start accept smai_seo_import_punbot_backlinks value form Website ID '.$id);
        Log::info($id);

        $response = 'will import for soon for web id '.$id;
       

        $new_seo_sync=NEW SMAISyncSEOController();
        $response_bl_punbot=$new_seo_sync->import_seo_backlink_punbot($id);
        return $response_bl_punbot; 

        //return response()->json( $response );


    }

    public function smai_seo_open_footer_posts($id)
    {

        Log::debug('Start accept smai_seo_open_footer_posts value form Website ID '.$id);
        Log::info($id);

        /* $new_seo_sync=NEW SMAISyncSEOController();
        $response_onoff=$new_seo_sync->cron_seo_on_off_posts($id);
        return $response_onoff; */


    }

    public function smai_seo_user_create_cron_posts(Request $request)
    {
        if(isset($request->siteid))
        $website_id=$request->siteid;

        if(isset($request->Keyword))
        $keywords = $request->Keyword;

        if(isset($request->Keyword_en))
        $keywords_en = $request->Keyword_en;

        if(isset($request->post_category))
        $post_category=$request->post_category;

        if(isset($request->user_id))
        $user_id = $request->user_id;


        $description = $keywords ;
        $creativity = 1;
        $number_of_results = 1;
        $tone_of_voice = 0;
        $maximum_length = 2000;

        if(isset($request->Keyword_Lang))
        $language = $request->Keyword_Lang;
        else
        $language = "en";


        if(isset($request->Keyword_en))
        $keywords_en = $request->Keyword_en;
        else
        $keywords_en = $keywords;
        

        
        
        
        $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords.
    Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice
    ";

       // $post = OpenAIGenerator::where('slug', $post_type)->first();

      // Log::debug('All Qry strign form Cron Sync.Smartcontent Node.js  SEO');
       //Log::info($request);

        $user_id=$request->user_id;

        if($user_id<0 || $user_id==NULL || $user_id=='')
        $user_id=1;

        $user = UserMain::where('id',$user_id)->first();
        if ($user->remaining_words <= 0  and $user->remaining_words != -1) {
            $data = array(
                'errors' => ['You have no credits left. Please consider upgrading your plan.'],
            );
            return response()->json($data, 419);
        }


            if(isset($request->n_posts))
            $total_topics=$request->n_posts;
            else
            $total_topics=1;

            if(isset($request->post_type))
            $post_type=$request->post_type;
            else
            $post_type = 'paragraph_generator';

               //post title for real post
                $title_request=array(

                    'image_generator' => NULL,
                    'post_type' => 'post_title_generator',
                    //SETTINGS
                    'number_of_results' => $total_topics,
                    'maximum_length' => 2000,
                    'creativity' => 1,
                    'language' => $language,
                    'negative_prompt' => 0,
                    'tone_of_voice' => 0,
                    'description'=> $keywords,
                
                );

                $post_title_array=$this->smai_text_gen_inside($title_request);
                $post_titles=$post_title_array['html'];

                if(Str::contains($post_titles,'<br>')==true)
                $post_titles=str_replace('<br>','',$post_titles);

                if(Str::contains($post_titles,'<br/>')==true)
                $post_titles=str_replace('<br/>','',$post_titles);

                $post_title=trim($post_titles);

                 //post title for image AI
                 $title_en_request=array(

                    'image_generator' => NULL,
                    'post_type' => 'post_title_generator',
                    //SETTINGS
                    'number_of_results' => $total_topics,
                    'maximum_length' => 2000,
                    'creativity' => 1,
                    'language' => 'en',
                    'negative_prompt' => 0,
                    'tone_of_voice' => 0,
                    'description'=> $keywords,
                
                );

                $post_title_en_array=$this->smai_text_gen_inside($title_en_request);
                $post_titles_en=$post_title_en_array['html'];

                if(Str::contains($post_titles_en,'<br>')==true)
                $post_titles_en=str_replace('<br>','',$post_titles_en);

                if(Str::contains($post_titles_en,'<br/>')==true)
                $post_titles_en=str_replace('<br/>','',$post_titles_en);
                
                $post_titles_en=trim($post_titles_en);
                    
                
                
                /* foreach ($post_title_array as $post_title) 
                    { */
                        
                        
                        //if want to reaponse
                        //return response()->json($response, 200);
                        
                        //shortcut to create new content
                        $body_request=array(

                            'image_generator' => NULL,
                            'post_type' => $post_type,
                            //SETTINGS
                            'number_of_results' => 1,
                            'maximum_length' => 1000,
                            'creativity' => 1,
                            'language' => $language,
                            'negative_prompt' => 0,
                            'tone_of_voice' => 0,
                            'description'=> $keywords,
                            'keywords' => $keywords,
                            'article_title' => $post_title,
                            'focus_keywords' => $keywords,
                        
                        );

                        $responsedText_array= $this->smai_text_gen_inside($body_request);
                        $responsedText=trim($responsedText_array['html']);
                       
                        for($i=-10;$i<10;$i++)
                        {
                            for($j=-10;$j<10;$j++)
                            {
                                $responsedText = str_replace('between '.$i.' and '.$j,' ',$responsedText);
                            }
                        }
                        $responsedText = str_replace('between 0 and 1','',$responsedText);
                        $responsedText = str_replace('or negative.','',$responsedText);
                        $responsedText = str_replace('between -2 and 2 (neutral).','',$responsedText);
                        $responsedText = str_replace('between -1 and 1','',$responsedText);
                        $responsedText = str_replace('between 1 and 0.','',$responsedText);
                        
                        $message_id=$responsedText_array['message_id'];

                        $post_title=Str::replace('"', '', $post_title);
                        $post_title= str_replace('"','',$post_title);

                       
                        //find keyword URL for add to keyword href
                        $new_smai_seo_fnc=NEW SMAI_SEO_PUNBOTController(); 
                        $keywords_url = $new_smai_seo_fnc->get_cur_keywordlink($website_id,$keywords,NULL);
                        
                        if(isset($request->post_target))
                        $post_target=$request->post_target;
                        else
                        $post_target='punbot_seo';
                        
                        switch ($post_target) {
                            case 'punbot_seo':
                                {
                                    //Or Add Post to SEO by Models
                                    $keywords_url = $new_smai_seo_fnc->get_cur_keywordlink($website_id,$keywords,NULL);
                                    
                                    $posttime = Carbon::now();
                                    $new_post=PostPunbotSEO::create([
                                        
                                        'post_title' => $post_title,
                                        'post_description' => $responsedText,
                                        'post_category' => $post_category,
                                        'post_image' => 'default.png',
                                        'post_status' => 1,
                                        'post_date_created' => $posttime,
                                        'post_version' => 'v2-th',
                                        'big_post_id' => $message_id ,
                                        'note' => 'content from chatGPT',
                                        'keyword' => $keywords,
                                        'website_id' => $website_id,   
                                        'keyword_url' => $keywords,
                                        'keyword_en' => $keywords_en,
                                    
                                    ]);

                                    $postid = $new_post->id;
                                    Log::debug('Post ID from PostPunbotSEO '.$postid);
                                    Log::debug('Post Title from PostPunbotSEO '.$post_title);
                        
                            }
                                break;

                                case 'seo_db':
                                    {
                                        
                                        $image_request=array(

                                            'image_generator' => 'DE',
                                            'post_type' => 'image',
                                            //SETTINGS
                                            'number_of_results' => 1,
                                            'maximum_length' => 2000,
                                            'creativity' => 1,
                                            'language' => 'en',
                                            'negative_prompt' => 'ugly, tiling, poorly drawn hands, poorly drawn, poorly drawn face, out of frame, extra limbs, disfigured, deformed, body out of frame, blurry, bad anatomy, blurred, watermark, grainy, signature, cut off, draft, duplicate, coppy, multi, two faces, disfigured, kitsch, oversaturated, grain, low-res, mutation, mutated, extra limb, missing limb, floating limbs, disconnected limbs, malformed hands, blur, out of focus, long neck, long body, disgusting, childish, mutilated, mangled, old, heterochromia, dots, bad quality, weapons, NSFW, draft',
                                            'tone_of_voice' => 0,
                                            'description'=> $post_titles_en ,
                                            'keywords' => $keywords_en,
                                            'image_number_of_images'  => 1,
                                            'image_mood' =>  'happy',
                                            'image_lighting' => 'bright ',
                                            'image_style' => NULL,
                                            'size' => '1024x1024',
                                            'post_type' => 'ai_image_generator',
                                        
                                        );
                                        
                                        $seo_image=$this->smai_text_gen_inside($image_request);
                                        $responsedText = $new_smai_seo_fnc->link_dec_seo($responsedText,$keywords,$website_id,NULL);
                                        
                                        //add image at the top of central post
                                        $content_image = '<img src="'.$seo_image[0].'" alt="'.$keywords.'" style="width:100%;height:auto;">';
                                        $responsedText = $content_image.$responsedText;

                                        if (!preg_match('/[^A-Za-z0-9]/', $post_title))
                                        $slug=Str::slug($post_title, '-');
                                        else
                                        $slug=$new_smai_seo_fnc->slugify($post_title);
                                       
                                        /*  <figure>
                                           <img src="images/tokyo-street.jpg" alt="A motion blurred street with an in-focus taxi." />
                                        </figure> */

                                        
                                        $posttime = Carbon::now();
                                        // echo $posttime->toDateTimeString();
                                        
                                        $new_post=PostSEO::create([
                                            
                                            'user_id' => $user_id,
                                            'title' => $post_title,
                                            'slug' => $slug,
                                            'content' => $responsedText,
                                            'comment' =>  0,
                                            'status' =>  1,
                                            'post_type' => 'blog',
                                            'visibility' => 'Pu',
                                            'publish_on' => $posttime,
                                            'created_at' => $posttime,
                                        
                                        ]);
                                        $postid = $new_post->id;
                                        Log::debug('Post ID from PostSEO '.$postid);
                                        Log::debug('Post Title from PostSEO '.$post_title);
                                        
                                        if($postid>0)
                                        {
                                                //Blog meta data
                                                $new_blog_meta=BlogMeta::create([
                                                    'blog_id' => $postid,
                                                    'title' => 'ximage',
                                                    'value' =>  $seo_image[0]
                                                ]);

                                                //add stat record script below
                                                $web_automation=SEOAiAutomation::where('website_id',$website_id)->first();
                                                $web_automation->post_today_count=$web_automation->post_today_count+1;
                                                $web_automation->save();
                                        }

                                    }
                                    break;


                                    case 'course_db':
                                        {

                                           
                                            $image_thumb_request=array(

                                                'image_generator' => 'DE',
                                                'post_type' => 'image',
                                                //SETTINGS
                                                'number_of_results' => 1,
                                                'maximum_length' => 2000,
                                                'creativity' => 1,
                                                'language' => 'en',
                                                'negative_prompt' => 'ugly, tiling, poorly drawn hands, poorly drawn, poorly drawn face, out of frame, extra limbs, disfigured, deformed, body out of frame, blurry, bad anatomy, blurred, watermark, grainy, signature, cut off, draft, duplicate, coppy, multi, two faces, disfigured, kitsch, oversaturated, grain, low-res, mutation, mutated, extra limb, missing limb, floating limbs, disconnected limbs, malformed hands, blur, out of focus, long neck, long body, disgusting, childish, mutilated, mangled, old, heterochromia, dots, bad quality, weapons, NSFW, draft',
                                                'tone_of_voice' => 0,
                                                'description'=> 'studio photography set of high detail of '.$keywords_en.', perfect composition, cinematic light photo studio, beige color scheme, indirect lighting, 8k, elegant and luxury style',
                                                'keywords' => $keywords_en,
                                                'image_number_of_images'  => 1,
                                                'image_mood' =>  'happy',
                                                'image_lighting' => 'bright ',
                                                'image_style' => 'minimalist',
                                                'size' => '256x256',
                                                'post_type' => 'ai_image_generator',
                                            
                                            );

                                            $image_request=array(

                                                'image_generator' => 'DE',
                                                'post_type' => 'image',
                                                //SETTINGS
                                                'number_of_results' => 1,
                                                'maximum_length' => 2000,
                                                'creativity' => 1,
                                                'language' => 'en',
                                                'negative_prompt' => 'ugly, tiling, poorly drawn hands, poorly drawn, poorly drawn face, out of frame, extra limbs, disfigured, deformed, body out of frame, blurry, bad anatomy, blurred, watermark, grainy, signature, cut off, draft, duplicate, coppy, multi, two faces, disfigured, kitsch, oversaturated, grain, low-res, mutation, mutated, extra limb, missing limb, floating limbs, disconnected limbs, malformed hands, blur, out of focus, long neck, long body, disgusting, childish, mutilated, mangled, old, heterochromia, dots, bad quality, weapons, NSFW, draft',
                                                'tone_of_voice' => 0,
                                                'description'=> 'studio photography set of high detail of '.$keywords_en.', with perfect composition, cinematic light photo studio, beige color scheme, indirect lighting, 8k, elegant and luxury style',
                                                'keywords' => $keywords_en,
                                                'image_number_of_images'  => 1,
                                                'image_mood' =>  'happy',
                                                'image_lighting' => 'bright ',
                                                'image_style' => 'minimalist',
                                                'size' => '1024x1024',
                                                'post_type' => 'ai_image_generator',
                                            
                                            );
                                            $course_thumb=$this->smai_text_gen_inside($image_thumb_request);
                                            $course_image=$this->smai_text_gen_inside($image_request);

                                            Log::debug('Course Image from PostCourse '.$course_image[0]);
                                            Log::debug('Course Thumb from PostCourse '.$course_thumb[0]);
                                            //Or Add Post to SEO by Models
                                            $posttime = Carbon::now();
                                            // echo $posttime->toDateTimeString();
                                            $slug=Str::slug($post_title, '-');
                                            $new_post=Course::create([
                                                'title' => $post_title,
                                                'slug' => $slug,
                                                'duration' => '10',
                                                'publish' => 1,
                                                'level' => 2,
                                                'trailer_link' => 'https://www.youtube.com/',
                                                'host' => 'Youtube',
                                                'about' => $responsedText,
                                                'status' => 1,
                                                'category_id' => 1,
                                                'subcategory_id' => 1,
                                                'user_id' => 1,
                                                'price' => 20,
                                                'discount_price' => 10,
                                                'lang_id' => 19,
                                                'reveiw' => 0,
                                                'total_enrolled' => 1,
                                                'reveune' => '50',
                                                'image' => $course_image[0],
                                                'thumbnail' =>  $course_thumb[0],
                                                
                                            ]);


                                            $postid = $new_post->id;
                                            Log::debug('Post ID from PostCourse '.$postid);
                                            Log::debug('Post Title from PostCourse '.$post_title);
                    
                                        }
                                        break;

                                    //add more case here
                                    //for example protfolio_generator, product_generator, blog_generator, etc.


                                }







                

           



    }

    public function smai_bubblechat_get_info(Request $request)
    {
        $ipAddress = $request->ip();

      //  Log::debug('Bubble request from IP '.$ipAddress);
        //Log::info(print_r($request, true));

        $block_bio= $request->block_id;
        $block_bio_data=$request->block_bio_link_data;
        //Log::info($block_bio);
       //Log::info($block_bio_data);
        foreach($block_bio_data as $blcok_data)
        {
            if(($blcok_data['type']=='socialschat'))
            {
            //Log::debug('Found Type Social from email ');
            //Log::info($blcok_data['settings']['socialschat']['email_chat']);
            //Log::info($blcok_data['is_enabled']);

            $blcok_data['settings']['is_enabled']=$blcok_data['is_enabled'];
            $return_data=json_encode($blcok_data['settings']);
           // Log::info($blcok_data['settings']['is_enabled']);
            return $return_data;

            }
            

        }

      

       

    }

   

        public function getCommentDetail($comment_id)
        {
            $accesstoken='EAARmpR9yiuUBOxuSKoFjs0jK7dte4KV6XkpSKvuzbSSeEnZASH3J41eeg2LizoTjMTXMZB3XsDZB8hnXmZB3Wz9NRxbO3bSjozymBa4KZBwZAb64VoSNSBrpEb6LEhZA7HZBJRRWM482JPt2UEsB38oVQvpygXZCZC3qWgbZCubLWUi3Ejns1X8dFVo2RaSR2klMCycxZAFosWOl6vfjr6gZD'; // Your page access token
            $fb = new Facebook([
                'app_id' => '1238759290079973',
                'app_secret' => '12b019180410c15be86235175ebcbb3f',
                'default_graph_version' => 'v18.0',
            ]);



            try {
                $response = $fb->get('/' . $comment_id . '?fields=message,created_time,from,message_tags,object', $accesstoken);
                $comment = $response->getGraphNode();
                
                $comment=$comment->asArray();
                $commentData = [
                    'id' => $comment_id,
                    'message' => $comment['message'],
                    'created_time' => $comment['created_time']->format('Y-m-d H:i:s'),
                ];
                
               Log::debug('Comment response Detail from '.$commentData['id']);
               Log::info($comment);

                // Now you can access the comment details
                $commentData['id'];             // Comment ID
                $commentData['message'];        // Comment message/content
                $commentData['created_time'];   // Comment creation date and time
                 
            } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                // Handle API error responses
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                // Handle SDK error exceptions
            }


           
        }

    public function smai_fblivecomment_get_info(Request $request)
    {
        $fb = new Facebook([
            'app_id' => '1238759290079973',
            'app_secret' => '12b019180410c15be86235175ebcbb3f',
            'default_graph_version' => 'v10.0',
        ]);
        $accesstoken='EAARmpR9yiuUBOxuSKoFjs0jK7dte4KV6XkpSKvuzbSSeEnZASH3J41eeg2LizoTjMTXMZB3XsDZB8hnXmZB3Wz9NRxbO3bSjozymBa4KZBwZAb64VoSNSBrpEb6LEhZA7HZBJRRWM482JPt2UEsB38oVQvpygXZCZC3qWgbZCubLWUi3Ejns1X8dFVo2RaSR2klMCycxZAFosWOl6vfjr6gZD'; // Your page access token
        $pageId = '102217868688';

       Log::debug('Start accept smai_fblivecomment_get_info value Comment ');
       // Log::info($request);

        $comments=$request->data;
        $user_id=2;
        //map sotre id to current post_id
        $store_id=3;
        $currency='USD';

        //$comments=$request;

       foreach ($comments as $comment) 
       {
            //Log::debug('Comment ID '.$comment['id']);
            Log::debug('Comment Message '.$comment['message']);
            // Log::debug('Comment created_time '.$comment['created_time']);
           
          
          if(Str::length($comment['id']) > 5)
           {
             
              $products=PunbotProductEcommerce::where('store_id',$store_id)->get();

              $post_id_arr=explode('_',$comment['id']);
              $post_id=$post_id_arr[0];

              $collection_mysql='ecommerce_product_'.$post_id;
              $collection_firebase=$post_id.'_stock';

              $products_firebase=[];
              
              //push array products before Firebase Check has collection
              foreach($products as $product)
              {

                array_push( $products_firebase,
                 ['id' => '', 
                 'product_name' => $product->product_name, 
                 'cf_code' => $product->product_name, 
                 'product_description' => $product->product_description,
                 'stock_item' => $product->stock_item]
                );

                //clean comment
                //$cleanedComment = Str::squish($comment['message']);
                $cleanedComment = str_replace(' ', '', $comment['message']);
               // $cfComment = preg_replace( '/[\W]/', '', $cleanedComment);

                $cfComment  = preg_replace("/^[0-9A-Zก-ฮ]$/", "", $cleanedComment );

                //preg_match("/^[0-9A-Zก-ฮ]$/", $msg

                //Log::debug('!!!!!!!!!!!!!!!!! Final cfComment '.$cfComment);

                $cf_code=$product->cf_code;

                 //Log::debug('!!!!!!!!!!!!!!!!! CF COde '.$cf_code);

                        if($cfComment==$cf_code)
                        {
                            Log::debug('Product ID match '.$product->id.' with comment '.$cf_code);
                            //insert new order

                            $filename_order='ecommerce_cart.json';
                            $contents_order = json_encode(array(

                                'subscriber_id' => $comment['id'],
                                'user_id' => $user_id,
                                'store_id' => $store_id,
                                'currency' => $currency,
                                'status' => "pending",
                                'ordered_at' => Carbon::now(),
                                'payment_method'=>'',
                                'updated_at' => Carbon::now(),
                                'initial_date' => Carbon::now(),
                                'confirmation_response' => '[]',
                                'buyer_email' => '',
                                'buyer_mobile' => '',
                                'payment_amount' => $product->original_price,

                            ));

                            //$this->smai_save_comments_json($filename_order,$contents_order);

                            /* 
                            $new_order=PunbotOrdersEcommerce::firstOrCreate(

                                ['subscriber_id' => $comment['id']],

                                [
                                'user_id' => $user_id,
                                'store_id' => $store_id,
                                
                                'currency' => $currency,
                                'status' => "pending",
                                'ordered_at' => Carbon::now(),
                                'payment_method'=>'',
                                'updated_at' => Carbon::now(),
                                'initial_date' => Carbon::now(),
                                'confirmation_response' => '[]',
                                'buyer_email' => '',
                                'buyer_mobile' => '',
                                'payment_amount' => $product->original_price,
                                ]


                            );

                            if($new_order->id>0)
                            {
                                //update product
                                $product->sales_count=$product->sales_count+1;
                                $product->stock_item=$product->stock_item-1;
                                $product->save();
                                Log::debug('Product ID update Stock sucess '.$product->id);
                            
                            } */



                            
                        }


                        

                       /*  PunbotCommentsFBLive::create(array(

                            'comment_id' => $comment['id'],
                            'message' => $comment['message'],
                            'created_at' => $comment['created_time'],
                            'post_id' => $post_id,
                           
                            

                        )); */

                        //Temporarily save to json file
                        /* $comment_new= PunbotCommentsFBLive::firstOrCreate(

                            ['comment_id' => $comment['id']],
                            [

                            'message' => $comment['message'],
                            'created_at' => Carbon::now(),
                            'post_id' => $post_id,
                            
                            
                            ]
                        ); */

                        $filename=$post_id.'.json';
                        $contents = json_encode($comment);
                        //$this->smai_save_comments_json($filename,$contents);





              }

              //insert Check if Firebase has collection
              $this->insertStockToFirebase($collection_firebase,$products_firebase);



            






        
           } 
           
          
       }


       /*  $insert_ecommerce_data =array
        (
          'user_id' => $user_id,
          'store_id' => $store_id,
          'subscriber_id' => $subscriber_id,
          'currency' => $currency,
          'status' => "pending",
          'ordered_at' => Carbon::now(),
          'payment_method'=>'',
          'updated_at' => $curdate,
          'initial_date' => $curdate,
          'confirmation_response' => '[]',
          'buyer_email' => $buyer_email,
          'buyer_mobile' => $buyer_phone
        ); */
   
    }

   public function smai_save_comments_json($filename,$contents)
   {

            //$filename = 'file.json';
            //$contents = 'new content';

            if (Storage::exists($filename)) {
                // File exists, open and decode its contents
                $existingContents = json_decode(Storage::get($filename), true);
                
                // Push new content to the existing array
                $existingContents[] = $contents;
                
                // Encode the updated contents
                $updatedContents = json_encode($existingContents);
                
                // Save the updated contents back to the file
                Storage::put($filename, $updatedContents);
                Log::debug('File exists, open and decode its contents '.$filename);
                
                //$path = Storage::disk('local')->getAdapter()->applyPathPrefix($filename);
                //$path =  $this->assertFileExists(Storage::disk("local")->getPath().$filename);
                //Log::info($path);

            } else {
                // File does not exist, create it and put the new content
                Storage::put($filename, json_encode([$contents]));
                Log::debug('File does not exist, create it and put the new content '.$filename);
            }  

    }

    public function insertStockToFirebase($collection,$products)
{
   // $factory = (new Factory)->withServiceAccount(__DIR__.'/google-service-account.json');
  // $factory = (new Factory)->withServiceAccount(__DIR__.'/FirebaseKey.json');
   

   $factory=NULL;

   if($factory==NULL)
   $firebase = DB::connection('punbot_firebase');
   else
   $firebase=$factory;

        if($this->checkAndCreateCollection($collection) < 1)
        {
        $database = $firebase->createDatabase();

            $productsRef = $database->getReference($collection);

            /* $products = [
                ['id' => 1, 'name' => 'Product 1', 'qty' => 10],
                ['id' => 2, 'name' => 'Product 2', 'qty' => 20],
                ['id' => 3, 'name' => 'Product 3', 'qty' => 30],
                ['id' => 4, 'name' => 'Product 4', 'qty' => 40],
                ['id' => 5, 'name' => 'Product 5', 'qty' => 50],
            ]; */

            foreach ($products as $product)
            {
                $productsRef->push($product);
            }

        }
        else
        {
            Log::debug('Collection '.$collection.' already exists.');
        }
}


function checkAndCreateCollection($collectionName) 
{
    //$firebase = (new Factory)->withServiceAccount('./path/to/serviceAccount.json');
    //$firebase = (new Factory)->withServiceAccount(__DIR__.'/FirebaseKey.json');
    $firebase=DB::connection('punbot_firebase');
    $database = $firebase->createDatabase();

    $reference = $database->getReference($collectionName);
    $snapshot = $reference->getSnapshot();

    if(!$snapshot->exists()) {
        // If no data, create the collection with default data.
        $defaultData = ['key' => 'value']; // Change this with your default data
        $updatedDocRef = $database->getReference($collectionName)->set($defaultData);

        Log::debug("".$collectionName." collection has been created.");
        return 1;
    }
    else {
        Log::debug("".$collectionName." collection already exists.");
        return 0;
    }
}


}
