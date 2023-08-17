<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mobile_UserOpenai extends Model
{
    use HasFactory;
    protected $connection = 'mobileapp_db';
    // Model definition continues...
    protected $table = 'user_openai';

    public function generator(){
        return $this->belongsTo(OpenAIGenerator::class , 'openai_id','id' );
    }
}
