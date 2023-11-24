<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionMain extends Model
{
    use HasFactory;
    protected $connection = 'main_db';
    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id' ,
        'plan_id' ,
        'name' ,
        'stripe_status' ,
        'stripe_id' ,
        'stripe_price' ,
        'quantity',
        'trial_ends_at' ,
        'ends_at' ,
        'created_at' ,
        'paid_with' ,
    ];

    

}
