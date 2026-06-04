<?php

namespace App\Models;

use App\Traits\HasSeoMeta;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class BlogPost extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia, HasSeoMeta;

    protected $fillable = [
        'user_id', 'blog_category_id', 'title', 'slug', 'excerpt', 'body',
        'featured_image', 'status', 'published_at', 'is_featured',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured'  => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug')->doNotGenerateSlugsOnUpdate();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')->singleFile();
    }

    public function author()   { return $this->belongsTo(\App\Models\User::class, 'user_id'); }
    public function category() { return $this->belongsTo(BlogCategory::class, 'blog_category_id'); }
    public function tags()     { return $this->belongsToMany(BlogTag::class, 'blog_post_tag'); }

    public function scopePublished($q)
    {
        return $q->where('status', 'published')->where('published_at', '<=', now());
    }

    public function getReadTimeAttribute(): string
    {
        $words = str_word_count(strip_tags($this->body ?? ''));
        $mins  = max(1, round($words / 200));
        return "{$mins} min read";
    }
}
