<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOpenaiChatMessageSocialPost extends Model
{
    use HasFactory;
    protected $connection = 'main_db';
    protected $table = 'sp_user_openai_chat_messages';

    protected $fillable = [
        'user_id',
           
    ];

    public function chat(){
        return $this->belongsTo(UserOpenaiChatSocialPost::class, 'user_openai_chat_id', 'id');
    }
}
