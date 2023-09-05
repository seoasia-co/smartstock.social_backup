<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanBio extends Model
{
    use HasFactory;
    protected $connection = 'bio_db';
    protected $table = "plans";

    protected $fillable = [
        'user_id' ,
            'plan_id' ,
            'processor' ,
            'type'  ,
            'frequency' ,
            'code' ,
            'discount_amount' ,
            'base_amount' ,
            'email' ,
            'payment_id' ,
            'name' ,
            'plan'  ,
            'billing' ,
            'business' ,
            'taxes_ids' ,
            'total_amount' ,
            'currency' ,
            'datetime' 
    ];
}
