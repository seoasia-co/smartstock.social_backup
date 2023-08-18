<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOrder extends Model
{
    use HasFactory;
    protected $connection = 'main_db';

    public function plan(){
        return $this->belongsTo(PaymentPlans::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
