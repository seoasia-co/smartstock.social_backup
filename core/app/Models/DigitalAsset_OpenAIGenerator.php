<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalAssest_OpenAIGenerator extends Model
{
    use HasFactory;
    protected $connection = 'digitalasset_db';
    // Model definition continues...
    protected $table = 'openai';
}
