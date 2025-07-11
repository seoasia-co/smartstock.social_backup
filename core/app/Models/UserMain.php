<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserMain extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $connection = 'main_db';
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
        'expired_date',
        'plan_expire_date',
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
    public function fullName()
    {
        return $this->name . ' ' . $this->surname;
    }

    public function email()
    {
        return $this->email;
    }

    public function openai()
    {
        return $this->hasMany(UserOpenai::class);
    }

    public function orders()
    {
        return $this->hasMany(UserOrder::class)->orderBy('created_at', 'desc');
    }

    public function plan()
    {
        return $this->hasMany(UserOrder::class)->where('type', 'subscription')->orderBy('created_at', 'desc')->first();
    }
}
