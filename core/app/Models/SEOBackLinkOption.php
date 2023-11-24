<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


//use App\Models\Page;

class SEOBackLinkOption extends Model
{
    use HasFactory;

    protected $connection='punbotseo_db';
    protected $table = 'backlinks_option';
    protected $fillable = [
        'website_id',
        'bl_type',
        'platform',
        'plat_form_table',
        'platform_u_id',
        'keyword',
        'keyword_lang',
        'day_time_post',
        'keyword_en',
        'access_token',
        'refresh_token',
        'email_api',
        'username_api',
        'keyword_url',

     
    ];

    /* public function getWebsByOwner($ownerId) {
        $webs = Webs::where('user_id', $ownerId)->get();
        return $webs;
    } */





}
