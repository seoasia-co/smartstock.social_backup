<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use App\Helpers\Helper;

class PunbotOrdersEcommerce extends Model 
{

    use HasFactory;

    protected $connection='punbot_db';
    protected $table = 'ecommerce_cart';
    protected $fillable = [
    
        'user_id' ,
        'store_id' ,
        'subscriber_id' ,
        'currency' ,
        'status' ,
        'ordered_at' ,
        'payment_method',
        'updated_at'  ,
        'initial_date'  ,
        'confirmation_response' ,
        'buyer_email' ,
        'buyer_mobile' ,
            
     
    ];

}