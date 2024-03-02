<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team_Members_SocialPost extends Model
{
    use HasFactory;
    protected $connection = 'main_db';
    protected $table = 'sp_team_member';
}
