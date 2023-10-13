<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBioOpenaiTemplate extends Model
{
    use HasFactory;
    protected $connection = 'bio_db';
    // Model definition continues...
    protected $table = 'templates';
    protected $primaryKey = 'template_id';
   
    // STORAGE
    public const STORAGE_LOCAL = "public";
    public const STORAGE_AWS = "s3";

    protected $fillable = [
        'template_id' ,
        'template_category_id' ,
        'name' ,
        'prompt' ,
        'settings' ,
        'icon' ,
        'order' ,
        'total_usage' ,
        'is_enabled' ,
        'datetime' ,
        'last_datetime' ,
        'openai_id' ,
        'mobile_template_id' ,
        'mobile_old_template_id',     
    ];


}
