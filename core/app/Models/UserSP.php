<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
/* use Laravel\Sanctum\HasApiTokens; */

class UserSP extends Authenticatable
{
    use  HasFactory, Notifiable;
    protected $connection = 'main_db';
    protected $table='sp_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'permissions_id',
        'status',
        'permissions',
        'connect_email',
        'connect_password',
        'provider_id',
        'provider',
        'access_token',
        
        'surname',
        
        
        'affiliate_id',
        'affiliate_code',
        'remaining_words',
        'remaining_images',
        'email_confirmation_code',
        'email_confirmed',
        'password_reset_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    // relation with Permissions
    public function permissionsGroup()
    {
        return $this->belongsTo('App\Models\Permissions', 'permissions_id');
    }
}
