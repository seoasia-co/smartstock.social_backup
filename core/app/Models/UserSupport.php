<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSupport extends Model
{
    use HasFactory;
    protected $connection = 'main_db';
    protected $table = 'user_support';

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function messages(){
        return $this->hasMany(UserSupportMessage::class)->orderBy('created_at', 'asc');
    }
}
