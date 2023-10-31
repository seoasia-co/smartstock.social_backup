<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


//use App\Models\Page;

class SEOWebOption extends Model
{
    use HasFactory;

    protected $connection='punbotseo_db';
    protected $table = 'websites_option';
    protected $fillable = [
        'website_id',
        'keyword',
        'keyword_lang',
        'day_time_post',
        'keyword_en',
     
    ];

    /* public function getWebsByOwner($ownerId) {
        $webs = Webs::where('user_id', $ownerId)->get();
        return $webs;
    } */





}
