<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPunbot extends Model
{
    use HasFactory;
    protected $connection = 'social_db';
    protected $table = 'accounts';
    
}
