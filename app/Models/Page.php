<?php

namespace App\Models;

use App\Traits\HasSeoMeta;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Page extends Model
{
    use HasSlug, HasSeoMeta;
    protected $fillable = [
        'title', 'slug', 'body', 'template', 'is_published',
        'meta_title', 'meta_description', 'show_in_nav', 'sort_order',
    ];
    protected $casts = ['is_published' => 'boolean', 'show_in_nav' => 'boolean'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug')->doNotGenerateSlugsOnUpdate();
    }

    public function scopePublished($q) { return $q->where('is_published', true); }
}
