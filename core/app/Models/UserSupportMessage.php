<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSupportMessage extends Model
{
    use HasFactory;
    protected $connection = 'main_db';
}
