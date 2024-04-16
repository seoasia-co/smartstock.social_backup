<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserSmartPos extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'smartmenu_db';
    protected $table = 'users';

    protected $fillable = [
        'google_id',
        'fb_id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'api_token',
        'phone',
        'remember_token',
        'created_at',
        'updated_at',
        'active',
        'stripe_id',
        'card_brand',
        'card_last_four',
        'trial_ends_at',
        'verification_code',
        'phone_verified_at',
        'plan_id',
        'plan_status',
        'cancel_url',
        'update_url',
        'checkout_id',
        'subscription_plan_id',
        'stripe_account',
        'birth_date',
        'lat',
        'lng',
        'working',
        'onorder',
        'numorders',
        'rejectedorders',
        'paypal_subscribtion_id',
        'mollie_customer_id',
        'mollie_mandate_id',
        'tax_percentage',
        'extra_billing_information',
        'mollie_subscribtion_id',
        'paystack_subscribtion_id',
        'paystack_id',
        'restaurant_id',
        'deleted_at',
        'expotoken',
        'pm_type',
        'pm_last_four',
    ];
    
}
