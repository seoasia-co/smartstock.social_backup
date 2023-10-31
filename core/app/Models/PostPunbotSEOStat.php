<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use App\Helpers\Helper;

class PostPunbotSEOStat extends Model 
{

    use HasFactory;

    protected $connection='punbotseo_db';
    protected $table = 'shared_log';
    protected $fillable = [
    
            'bl_type',
            'backlink_id' ,
            'shared_to' ,
            'website_id',
            'keyword' ,
            'post_to_url' ,
            'post_to_email' ,
            'post_id' ,
            'schedule_time',
            
     
    ];

}