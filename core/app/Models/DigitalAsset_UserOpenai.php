<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalAsset_UserOpenai extends Model
{
    use HasFactory;
    protected $connection = 'digitalasset_db';
    // Model definition continues...
    protected $table = 'user_openai';

    public function generator(){
        return $this->belongsTo(OpenAIGenerator::class , 'openai_id','id' );
    }
}
