<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BlogMeta extends Model
{
    use HasFactory;

    protected $connection='seo_db';

    protected $table = 'blog_metas';
    protected $fillable = [
        'blog_id',
        'title',
        'value',
    ];

    /**
     * BlogMeta belongs to Blog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id', 'id');
    }

    public function getCreatedAtAttribute( $value ) {
        $dateFormat = config('Site.custom_date_format').' '.config('Site.custom_time_format');
        return (new Carbon($value))->format($dateFormat);
    }

    public function setCreatedAtAttribute( $value ) {
        $this->attributes['created_at'] = (new Carbon($value))->format('Y-m-d H:i:s');
    }
}
