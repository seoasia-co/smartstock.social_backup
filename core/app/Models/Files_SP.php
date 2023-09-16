<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Files_SP extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'main_db';
    protected $table='sp_files';

    //protected $primaryKey = 'user_id';
   // protected $primaryKey = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "ids" ,
        "team_id" ,
        "is_folder" ,
        "pid",
        "name" ,
        "file" ,
        "type" ,
        "extension",
        "detect" ,
        "size",
        "is_image" ,
        "width" ,
        "height" ,
        "created" ,

      
    ];

 


 
}
