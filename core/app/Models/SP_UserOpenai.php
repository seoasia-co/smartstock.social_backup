<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SP_UserOpenai extends Model
{

    use HasFactory;
    protected $connection = 'main_db';
    protected $table = 'sp_user_openai';

    protected $fillable = [
     
        'user_id' ,
        'openai_id' ,
        'input' ,
        'response' ,
        'output' ,
        'hash' ,
        'credits' ,
        'words' ,
        'created_at' ,
        'updated_at' ,
        'title' ,
        'slug' ,
        'storage' ,
        'file_size' ,
        'origin_user_openai_id' ,
                
         
        ];

    public function generator(){
        return $this->belongsTo(OpenAIGenerator::class , 'openai_id','id' );
    }
}
