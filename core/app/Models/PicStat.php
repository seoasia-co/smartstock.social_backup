<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use App\Helpers\Helper;

class PicStat extends Model 
{

    use HasFactory;

    protected $connection='punbotseo_db';
    protected $table = 'pic_stat';
   /*  protected $primaryKey ='id'; */
    protected $fillable = [
    ];

}