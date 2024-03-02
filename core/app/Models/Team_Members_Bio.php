<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team_Members_Bio extends Model
{
    use HasFactory;
    protected $connection = 'bio_db';
    protected $table = 'teams_members';
}
