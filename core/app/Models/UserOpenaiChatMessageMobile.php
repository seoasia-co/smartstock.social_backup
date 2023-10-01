<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOpenaiChatMessageMobile extends Model
{
    use HasFactory;
    protected $connection = 'mobileapp_db';
    protected $table = 'willdev_user_chat';


    protected $fillable = [
        "chat_id",
        
    ];

    public function chat(){
        return $this->belongsTo(UserOpenaiChatMobile::class, 'user_openai_chat_id', 'id');
    }
}
