<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBioOpenai extends Model
{
    use HasFactory;
    protected $connection = 'bio_db';
    // Model definition continues...
    protected $table = 'documents';
    protected $primaryKey = 'document_id';
    // STORAGE
    public const STORAGE_LOCAL = "public";
    public const STORAGE_AWS = "s3";

    protected $fillable = [
        'document_id', 
        'user_id' ,
        'project_id' ,
        'template_id' ,
        'template_category_id' ,
        'name' ,
        'type' ,
        'input' ,
        'content' ,
        'words' ,
        'settings' ,
        'model' ,
        'api_response_time' ,
        'datetime' ,
        'last_datetime' ,
        'openai_id' ,
        'input_openai' ,
        'response' ,
        'output' ,
        'hash' ,
        'credits' ,
        'title' ,
        'slug' ,
        'storage' ,
        'main_user_openai_id',
        'created_at' ,
        'updated_at' ,
        'origin_user_openai_id' ,
    
    
    ];

    public function generator(){
        return $this->belongsTo(OpenAIGenerator::class , 'openai_id','document_id' );
    } 
}
