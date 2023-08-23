<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionMobile extends Model
{
    use HasFactory;
    protected $connection = 'mobileapp_db';
    protected $table = "willdev_subscription_plan";

    protected $fillable = [
        'plan_name','image','duration','duration_type','total_words','number_of_images','plan_type','plan_price','discount_price','status','most_popular','include_plan_detail','not_include_plan_detail','description'
    ];

    public static function subscribes($id)
    {
        return UserMobile::where('subscription_id',$id)->count();
    }
}
