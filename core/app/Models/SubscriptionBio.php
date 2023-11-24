<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionBio extends Model
{
    use HasFactory;
    protected $connection = 'bio_db';
    protected $table = 'payments';

    protected $fillable = [
        
        'user_id' ,
        'plan_id' ,
        'plan' ,
        'main_token_sync' ,
        'type',
        'status',
        
    ];

    

}
