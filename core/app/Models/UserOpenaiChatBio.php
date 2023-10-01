<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOpenaiChatBio extends Model
{
    use HasFactory;

    protected $connection = 'bio_db';
    protected $table = "chats";

    protected $fillable = [
        "user_id",
        "chat_id",
        "role",
        "text",
        "openai_chat_category_id",
        'chat_id_mobile',
        'title',
        'chat_assistant_id',
        'name',
        'settings',
        'user_openai_chat_id',
    ];

    public function messages(){
        return $this->hasMany(UserOpenaiChatMessageBio::class);
    }

    public function category(){
        // โดยเชื่อมตารางตัวเองคือ user_openai_chat  คอลัม openai_chat_category_id กับ id ของ OpenaiGeneratorChatCategory
        return $this->belongsTo(OpenaiGeneratorChatCategory::class, 'openai_chat_category_id', 'id' );
    }
}
