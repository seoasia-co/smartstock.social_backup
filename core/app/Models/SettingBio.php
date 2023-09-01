<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingBio extends Model
{
    use HasFactory;
    protected $connection = 'bio_db';
    protected $table = 'settings';
    protected  $guarded =  [];
}
