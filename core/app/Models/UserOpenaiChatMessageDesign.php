<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOpenaiChatMessageDesign extends Model
{

    use HasFactory;
    protected $connection = 'digitalasset_db';
    protected $table = 'user_openai_chat_messages';

    protected $fillable = [
        'user_id',
           
    ];

    public function chat(){
        return $this->belongsTo(UserOpenaiChatDesign::class, 'user_openai_chat_id', 'id');
    }
}
