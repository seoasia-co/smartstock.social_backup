<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use App\Helpers\Helper;

class PunbotWordpressUser extends Model 
{

    use HasFactory;

    protected $connection='punbot_db';
    protected $table = 'wordpress_users_info';
    protected $fillable = [
    
            'user_id',
            'access_token',
            'name' ,
            'blogger_id',
            'blog_id',
            'blog_url',
            'icon' ,
            'posts' ,
            'categories' ,
            'last_update_time' ,
           
            
     
    ];

}