<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOpenaiChatMessageBio extends Model
{
    use HasFactory;
    protected $connection = 'bio_db';
    protected $table = 'chats_messages';
    protected $primaryKey = 'chat_message_id';

    protected $fillable = [
        'chat_id_mobile',
        'chat_id',
        
    ];

    public function chat(){
        return $this->belongsTo(UserOpenaiChatBio::class, 'chat_id', 'chat_id');
    }
}
