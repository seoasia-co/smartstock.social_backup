<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SP_UserOpenai extends Model
{
    use HasFactory;
    protected $table = 'sp_user_openai';

    public function generator(){
        return $this->belongsTo(OpenAIGenerator::class , 'openai_id','id' );
    }
}
