<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOpenaiChatMobile extends Model
{
    use HasFactory;

    protected $connection = 'mobileapp_db';
    protected $table = "willdev_user_chat";

    protected $fillable = [
        "user_id","chat_id","role","text","openai_chat_category_id"
    ];

    public function user()
    {
        // โดยเอา คอลัม id ของ UserMoible มาบันทึกในตารางตัวเองชื่อ user_id เพื่อเชื่อมโยงกัน
        return $this->hasOne("App\Models\UserMobile", "id", "user_id");
    }
}
