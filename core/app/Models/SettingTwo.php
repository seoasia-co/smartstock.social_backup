<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingTwo extends Model
{
    use HasFactory;
    protected $connection = 'mysql_sync';
    protected $guarded =  [];

    protected $table = 'settings_two';

    public $timestamps = false;
}
