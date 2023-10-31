<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon\Carbon;
use Stevebauman\Purify\Facades\Purify;

class PostSEO extends Model
{
    use HasFactory;
    
    protected $connection='seo_db';
    protected $table = 'blogs';
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'comment',
        'password',
        'status',
        'post_type',
        'visibility',
        'publish_on',
    ];

    /**
     * Blog belongs to User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserSEO::class, 'user_id', 'id');
    }

    /**
     * Blog has many Blog_meta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blog_meta()
    {
        return $this->hasMany(BlogMeta::class, 'blog_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'object_id', 'id')
                    ->where('parent_id', '0')
                    ->where('approve', '1')
                    ->orderBy('created_at', config('Discussion.comment_order', 'asc'))->with('child_comments');
    }

    public function child_comments()
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id')
                    ->where('approve', '1')
                    ->orderBy('created_at', config('Discussion.comment_order', 'asc'));
    }

    /**
     * Blog has many Comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blog_comments()
    {
        return $this->hasMany(Comment::class, 'object_id', 'id');
    }

    /**
     * Blog has one Blog Seo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function blog_seo()
    {
        return $this->hasOne(BlogSeo::class, 'blog_id', 'id');
    }

    public function blog_categories()
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_blog_categories', 'blog_id', 'blog_category_id');
    }

    public function blog_tags()
    {
        return $this->belongsToMany(BlogTag::class, 'blog_blog_tags', 'blog_id', 'blog_tag_id');
    }

    /**
     * Blog has one Feature_img.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function feature_img()
    {
        return $this->hasOne(BlogMeta::class, 'blog_id', 'id')
                    ->select(['blog_id', 'title', 'value'])
                    ->where('title', '=', 'ximage');
    }

    /**
     * Blog has one video.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function video()
    {
        return $this->hasOne(BlogMeta::class, 'blog_id', 'id')
                    ->select(['blog_id', 'title', 'value'])
                    ->where('title', '=', 'xvideo');
    }

    public function generateSlug($slug, $id=Null)
    {
        if (!empty($id)) {
            // for Update blog ,check same blog id
            $where  = static::where('id', '!=' ,$id)->whereSlug($slug)->exists();
        }else {
            // for create Page
            $where  = static::whereSlug($slug)->exists();
        }

        if ($where) {

            $original = $slug;
            $count = 2;

            while (static::whereSlug($slug)->exists()) {
                $slug = "{$original}-" . $count++;
            }
            return $slug;
        }
        return $slug;
    }

    public function laraBlogLink($id)
    {
        $permalink = config('Permalink.settings');
        $rewritecode = config('menu.permalink_structure');
        $blog = Blog::select('id', 'slug', 'publish_on')->with('blog_categories', 'user')->firstWhere('id', $id);

        if ($blog) {
            
            $date = explode( ' ', str_replace( array( '-', ':' ), ' ', (new Carbon($blog->publish_on))->format('Y-m-d H:i:s' )) );
            $rewritereplace = array(
                $date[0],
                $date[1],
                $date[2],
                $date[3],
                $date[4],
                $date[5],
                $blog->slug,
                optional(optional($blog->blog_categories)[0])->title,
                optional($blog->user)->fullname,
                $blog->id,
            );
            
            return $link = str_replace( $rewritecode, $rewritereplace, $permalink );
        }

    }

    public function laraBlogCategoryLink($id)
    {
        $blog_category = BlogCategory::with('blog')->firstWhere('id', $id);
        return $link = optional($blog_category)->slug;
    }
	
	
	public function laraBlogTagLink($id)
    {
        $blog_tags = BlogTag::with('blog')->firstWhere('id', $id);
        return $link = optional($blog_tags)->slug;
    }
    
    public function author($id)
    {
        $author = UserSEO::firstWhere('id', $id);
        return $name = optional($author)->name;
    }

    public function recentBlogs($atts = array())
    {

        $limit = $atts['limit'];
        $order = $atts['order'];
        $orderby = $atts['orderby'];

        $blogs_query = Blog::with('blog_meta', 'blog_seo', 'blog_categories', 'blog_tags', 'user');

        if(!optional(Auth::user())->hasRole(config('constants.roles.admin'))) {
            $blogs_query->where('visibility', '!=', 'Pr');
        }
        $blogs_query->WherePublishBlog()
                    ->where('visibility', '!=', 'Pr')
                    ->limit($limit)
                    ->orderBy($orderby, $order);
        $blogs = $blogs_query->get();
        return $blogs;

    }

    public function categoryBlogs($atts = array())
    {
        $limit = $atts['limit'];
        $order = $atts['order'];
        $orderby = $atts['orderby'];

        if(!optional(Auth::user())->hasRole(config('constants.roles.admin'))) {
            $blogcategories  = BlogCategory::withCount(['blog' => function($query) {
                $query->where('visibility', '!=', 'Pr');
            }])
            ->limit($limit)
            ->orderBy($orderby, $order)
            ->get();
            
        }else {
            $blogcategories  = BlogCategory::withCount('blog')
            ->limit($limit)
            ->orderBy($orderby, $order)
            ->get();
        }
        return $blogcategories;
    }

    public function archiveBlogs()
    {
        $archives_query = Blog::selectRaw('YEAR(publish_on) year, MONTH(publish_on) month, MONTHNAME(publish_on) month_name, count(*) data');
        
        if(!optional(Auth::user())->hasRole(config('constants.roles.admin'))) {
            $archives_query->where('visibility', '!=', 'Pr')->WherePublishBlog();
        }
        $archives_query->WherePublishBlog(config('blog.blog_type'))->groupBy('year', 'month', 'month_name')
                        ->orderBy('month', 'asc')
                        ->limit(3);
        $archives = $archives_query->get();

        foreach ($archives as $archive) {
            if (strlen($archive->month) == 1) {
                $archive->month = '0'.$archive->month;
            }
        }
        return $archives;      
    }

    public function getCreatedAtAttribute( $value ) {
        $dateFormat = config('Site.custom_date_format').' '.config('Site.custom_time_format');
        return (new Carbon($value))->format($dateFormat);
    }

    public function setCreatedAtAttribute( $value ) {
        $this->attributes['created_at'] = (new Carbon($value))->format('Y-m-d H:i:s');
    }

    public function getPublishOnAttribute( $value ) {
        $dateFormat = config('Site.custom_date_format').' '.config('Site.custom_time_format');
        return (new Carbon($value))->format($dateFormat);
    }

    public function setPublishOnAttribute( $value ) {
        $this->attributes['publish_on'] = (new Carbon($value))->format('Y-m-d H:i:s');
    }

    public function setSlugAttribute( $value ) {
        return $this->attributes['slug'] = $this->generateSlug($value, $this->id);
    }

    public function getContentAttribute($value)
    {
        return Purify::clean($value);
    }

    public function scopeWherePublishBlog($query, $post_type=Null)
    {
        $post_type = $post_type ? $post_type : config('blog.post_type');
        return $query->where(['status' => 1, 'post_type' => $post_type]);
    }

    public function getBlogMeta($blog_id, $key)
    {
        return BlogMeta::where(['blog_id' => $blog_id, 'title' => $key])->value('value');
    }

}
