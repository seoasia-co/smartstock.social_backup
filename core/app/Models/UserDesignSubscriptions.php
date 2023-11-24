<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserDesignSubscriptions extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'digitalasset_db';
    protected $table='user_subscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'plan_id',
        'payment_method',
        'stripe_subscription_id',
        'stripe_customer_id' ,
        'stripe_plan_id' ,
        'plan_amount' ,
        'plan_amount_currency' ,
        'plan_interval' ,
        'plan_interval_count' ,
        'plan_period_start' ,
        'plan_period_end' ,
        'payer_email' ,
        'created',
        'status' ,

    ];

   
   
}
