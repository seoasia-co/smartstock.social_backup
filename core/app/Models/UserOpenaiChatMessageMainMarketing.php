<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOpenaiChatMessageMainMarketing extends Model
{
    use HasFactory;
    protected $connection = 'main_db';
    protected $table = 'conversation_details';

    protected $fillable = [
        
        'user_id',
	    'openai_chat_category_id', 
        'chat_id',
    ];

    public function chat(){
        return $this->belongsTo(UserOpenaiChatMainMarketing::class, 'conversation_list_id', 'id');
    }
}
