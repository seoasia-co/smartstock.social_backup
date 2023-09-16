<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
/* use Laravel\Sanctum\HasApiTokens; */
use Illuminate\Database\Eloquent\Model;

class SPTeam extends Model
{
    use  HasFactory, Notifiable;
    protected $connection = 'main_db';
    protected $table='sp_team';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ids',
        'owner',
        'pid',
        'permissions',
        'data',
   
  
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
   
 
    
}
