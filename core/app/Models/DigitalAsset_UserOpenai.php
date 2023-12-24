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
    // STORAGE
    public const STORAGE_LOCAL = "public";
    public const STORAGE_AWS = "s3";

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
