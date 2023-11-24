<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use App\Helpers\Helper;

class PunbotMediumUser extends Model 
{

    use HasFactory;

    protected $connection='punbot_db';
    protected $table = 'blogger_users_info';
    protected $fillable = [
    
            'user_id',
            'access_token',
            'refresh_token' ,
            'medium_id',
            'name' ,
            'user_name',
            'profile_pic',
            'blog_count',
            'add_date',
            
     
    ];

}