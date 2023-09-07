<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOpenaiChatMessageMobile extends Model
{
    use HasFactory;
    protected $connection = 'mobileapp_db';
    protected $table = 'user_openai_chat_messages';

    public function chat(){
        return $this->belongsTo(UserOpenaiChatMobile::class, 'user_openai_chat_id', 'id');
    }
}
