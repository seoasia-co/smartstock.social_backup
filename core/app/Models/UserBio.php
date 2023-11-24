<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserBio extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'bio_db';
    protected $table='users';

    protected $primaryKey = 'user_id';
    //protected $primaryKey = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_newsletter_subscribed',
        

      
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
       /*  'plan_settings' => 'array', */
    ];



  /*   protected function setKeysForSaveQuery(Builder $query)
    {
        $query
            ->where('user_id', '=', $this->getAttribute('user_id'));

        return $query;
    } */

   /*  public function fullName()
    {
        return $this->name . ' ' . $this->surname;
    } */

 
}
