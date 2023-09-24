<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Stevebauman\Purify\Facades\Purify;

//use App\Models\Page;

class Webs extends Model
{
    use HasFactory;

    protected $connection='punbotseo_db';
    protected $table = 'websites';
    protected $fillable = [
        'website_id',
        'wp_app_password',
        'email',
        'user_name',
        'user_status',
     
    ];

    public function getWebsByOwner($ownerId) {
        $webs = Webs::where('user_id', $ownerId)->get();
        return $webs;
    }





}
