<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


//use App\Models\Page;

class SEOAiAutomation extends Model
{
    use HasFactory;

    protected $connection='punbotseo_db';
    protected $table = 'ai_automation';
    protected $fillable = [
        'website_id',
        'active',
        'daily_crons',
        'main',
        'backlinks',
        'post_today',
        'post_today_count',
     
    ];

    /* public function getWebsByOwner($ownerId) {
        $webs = Webs::where('user_id', $ownerId)->get();
        return $webs;
    } */





}
