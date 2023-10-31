<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ImagesBio extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'bio_db';
    protected $table='images';

    protected $primaryKey = 'image_id';
    //protected $primaryKey = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id' ,
            'project_id' ,
            'name' ,
            'input' ,
            'image' ,
            'style' ,
            'artist' ,
            'lighting' ,
            'mood' ,
            'size' ,
            'settings' ,
            'api' ,
            'api_response_time' ,
            'datetime' ,
        

      
    ];

 


 
}
