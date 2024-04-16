<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanSmartPos extends Model
{
    use HasFactory;
    protected $connection = 'smartmenu_db';
    protected $table = "plan";

    protected $fillable = [
      
        // Add fields from plan table
        'id',
        'name',
        'limit_items',
        'limit_orders',
        'price',
        'period',
        'paddle_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'description',
        'features',
        'limit_views',
        'enable_ordering',
        'stripe_id',
        'paypal_id',
        'mollie_id',
        'paystack_id',
        'user_id'
    ];
}
