<?php

namespace App\Models;

use App\Traits\HasSeoMeta;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasSlug, HasSeoMeta;

    protected $fillable = [
        'parent_id', 'name', 'slug', 'description',
        'icon_svg', 'image', 'is_visible', 'sort_order',
        'meta_title', 'meta_description',
    ];

    protected $casts = ['is_visible' => 'boolean'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function parent()    { return $this->belongsTo(Category::class, 'parent_id'); }
    public function children()  { return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order'); }
    public function products()  { return $this->hasMany(Product::class); }

    public function scopeTopLevel($q) { return $q->whereNull('parent_id'); }
    public function scopeVisible($q)  { return $q->where('is_visible', true); }
}
