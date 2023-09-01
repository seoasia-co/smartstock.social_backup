<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;


class UserLiveShop extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'liveshop_db';
    protected $table='users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array <int,string>
     */
    protected $fillable = [
        'firstname',
        'name',
        'lastname',
        'email',
        'password',
        'gender',
        'language_id',
        'phone',
        'country_id',
        'skills',
        'role_id',
        'birthday',
        'subscription_status',
        'avatar'
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

    /**
     * Always encrypt the password when it is updated.
     *
     * @param $value
    * @return string
    */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function avatarUrl() {
        return $this->avatar ? asset('storage/avatars/'.$this->avatar):'/assets/img/avatar.png';
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user is admin
     */
    public function isAdmin() {
        return $this->role_id === 1;
    }

    /**
     * Check if the user is creator
     */
    public function isUser() {
        return $this->role_id === 2;
    }

/*     public function subscription() {
        return $this->belongsTo(Subscription::class);
    }

    public function subscriptions() {
        return $this->hasMany(Subscription::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

    public function applications() {
        return $this->hasMany(Application::class);
    }
    public function pmCampaigns() {
        return $this->hasMany(PmCampaign::class);
    }

    public function autoresponderCampaigns() {
        return $this->hasMany(AutoresponderCampaign::class);
    } */
}
