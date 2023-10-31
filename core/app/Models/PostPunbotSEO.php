<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use App\Helpers\Helper;

class PostPunbotSEO extends Model 
{

    use HasFactory;

    protected $connection='punbotseo_db';
    protected $table = 'posts';
    protected $primaryKey ='post_id';
    protected $fillable = [
    
            'post_id',
            'post_title',
            'post_description' ,
            'post_category' ,
            'post_image',
            'post_status' ,
            'post_date_created' ,
            'post_version' ,
            'big_post_id' ,
            'image_source_url' ,
            'image_source_license' ,
            'note' ,
            'spin' ,
            'translated' ,
            'link_dec' ,
            'translated_from_id' ,
            'shared' ,
            'shared_to' ,
            'website_id',
            'keyword' ,
            'linkv' ,
            'post_image_id' ,
            'keyword_url' ,
            'keyword_en' ,
            'img_author',
            'img_source' ,
     
    ];

}