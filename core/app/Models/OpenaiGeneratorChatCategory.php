<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenaiGeneratorChatCategory extends Model
{
    use HasFactory;
    protected $connection = 'main_db';
    protected $table = 'openai_chat_category';
}
