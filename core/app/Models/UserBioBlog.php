<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserBioBlog extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'bio_blog_db';
    protected $table='users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
       
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
    ];

  /*   public function subscription()
    {
        return $this->hasOne("App\Models\SubscriptionMobile", "id", "subscription_id");
    }

    public function plan()
    {
        return $this->hasOne("App\Models\PlanMobile", "id", "subscription_id");
    } */
    
}
