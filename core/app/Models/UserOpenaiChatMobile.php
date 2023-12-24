<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOpenaiChatMobile extends Model
{
    use HasFactory;

    protected $connection = 'mobileapp_db';
    protected $table = "user_openai_chat";

    protected $fillable = [
       
        'user_id',
	    'openai_chat_category_id', 
        'chat_id',
    ];

    public function messages(){
        return $this->hasMany(UserOpenaiChatMessageMobile::class);
    }

    public function category(){
        // โดยเชื่อมตารางตัวเองคือ user_openai_chat  คอลัม openai_chat_category_id กับ id ของ OpenaiGeneratorChatCategory
        return $this->belongsTo(OpenaiGeneratorChatCategory::class, 'openai_chat_category_id', 'id' );
    }
}
