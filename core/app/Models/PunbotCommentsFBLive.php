<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use App\Helpers\Helper;

class PunbotCommentsFBLive extends Model 
{

    use HasFactory;

    protected $connection='punbot_db';
    protected $table = 'comments_fb_page_live';
    protected $fillable = [
    
        'post_id' ,
        'comment_id' ,
        'message' ,
            
     
    ];

}