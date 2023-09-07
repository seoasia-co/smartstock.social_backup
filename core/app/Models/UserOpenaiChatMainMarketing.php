<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOpenaiChatMainMarketing extends Model
{
    use HasFactory;
    protected $connection = 'main_db';
    protected $table = 'conversation_list';
    protected $fillable = [
        
        'user_id',
	    'openai_chat_category_id', 
        'chat_id',
    ];

    public function messages(){
        return $this->hasMany(UserOpenaiChatMessageMainMarketing::class);
    }

    public function category(){
        return $this->belongsTo(OpenaiGeneratorChatCategory::class, 'openai_chat_category_id', 'id' );
    }

    public function user()
    {
        // โดยเอา คอลัม id ของ UserMoible มาบันทึกในตารางตัวเองชื่อ user_id เพื่อเชื่อมโยงกัน
        return $this->hasOne(UserMain::class, 'user_id', 'id');
    }
    
}
