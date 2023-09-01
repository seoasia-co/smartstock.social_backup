<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
 use Laravel\Sanctum\HasApiTokens; 


use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSEO extends Authenticatable
{
    use  HasFactory, Notifiable;

    protected $connection = 'seo_db';
    protected $table='users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'profile',
        'password',
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
        'password' => 'hashed',
    ];

    public function getFullNameAttribute($value) {
        return "{$this->first_name} {$this->last_name}";
    }
    
    ########### Model RelationShip Functions Starts ###########

    public function blog()
    {
        return $this->hasMany(BlogSEO::class, 'user_id', 'id');
    }

    public function page()
    {
        return $this->hasMany(BlogSEO::class, 'user_id', 'id');
    }

    /*
    * Params-> model_class, table_name, foreign_key, local_key 
    */
    public static function get_roles($user_id)
    {
       $roles =  DB::table('model_has_roles')->select('role_id')->where('model_id', "=", $user_id)->get()->toArray();

       return array_column( $roles, 'role_id' );
    }

    ########### Model RelationShip Functions End ###########
    

/*     public function getCreatedAtAttribute( $value ) {
        $dateFormat = config('Site.custom_date_format').' '.config('Site.custom_time_format');
        return (new Carbon($value))->format($dateFormat);
    }

    public function setCreatedAtAttribute( $value ) {
        $this->attributes['created_at'] = (new Carbon($value))->format('Y-m-d H:i:s');
    } */
    
}
