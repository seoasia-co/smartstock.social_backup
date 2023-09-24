<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOpenaiChatMessageSyncNodeJS extends Model
{
    use HasFactory;
    protected $connection = 'sync_db';
    protected $table = 'stt';

    public function chat(){
        return $this->belongsTo(UserOpenaiSyncChatNodeJS::class, 'user_openai_chat_id', 'id');
    }
}
