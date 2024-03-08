<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenLogs extends Model
{
    use HasFactory;
    protected $connection = 'main_db';
    protected $table = 'token_logs';

    protected $fillable = [
        'user_id' ,
        'user_openai_id'  ,
            'user_openai_chat_id'  ,
            'amount'  ,
            'platform'  ,
            'token_before'  ,
            'token_after'  ,
            'type' ,
            'token_text_before' ,
            'token_text_after' ,
            'token_image_before' ,
            'token_image_after',
            'case_log',
            'subscriptions_id'

           
    ];
}
