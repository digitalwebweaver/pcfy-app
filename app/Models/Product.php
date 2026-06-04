<?php

namespace App\Models;

use App\Traits\HasSeoMeta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model implements HasMedia
{
    use SoftDeletes, HasSlug, InteractsWithMedia, HasSeoMeta;

    protected $fillable = [
        'brand_id', 'category_id', 'sku', 'name', 'slug',
        'short_description', 'description', 'grade',
        'price', 'mrp', 'stock_status', 'stock_qty', 'warranty_months',
        'specs', 'quick_specs',
        'is_featured', 'is_active', 'sort_order',
        'meta_title', 'meta_description', 'og_image',
    ];

    protected $casts = [
        'specs'       => 'array',
        'quick_specs' => 'array',
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
        'price'       => 'integer',
        'mrp'         => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug')->doNotGenerateSlugsOnUpdate();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(300)->height(220)->nonQueued();
        $this->addMediaConversion('medium')->width(800)->height(600)->nonQueued();
    }

    // Relationships
    public function brand()    { return $this->belongsTo(Brand::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function specs_rows() { return $this->hasMany(ProductSpec::class)->orderBy('sort_order'); }

    // Accessors
    public function getPriceInrAttribute(): string { return '₹' . number_format($this->price / 100, 0, '.', ','); }
    public function getMrpInrAttribute(): string   { return $this->mrp ? '₹' . number_format($this->mrp / 100, 0, '.', ',') : ''; }
    public function getDiscountPercentAttribute(): int
    {
        if (!$this->mrp || $this->mrp <= $this->price) return 0;
        return (int) round((($this->mrp - $this->price) / $this->mrp) * 100);
    }
    public function getPrimaryImageAttribute(): ?string
    {
        return $this->getFirstMediaUrl('images', 'medium') ?: null;
    }
    public function getThumbAttribute(): ?string
    {
        return $this->getFirstMediaUrl('images', 'thumb') ?: null;
    }

    // Scopes
    public function scopeActive($q)    { return $q->where('is_active', true); }
    public function scopeFeatured($q)  { return $q->where('is_featured', true); }
    public function scopeGrade($q, $g) { return $q->where('grade', strtoupper($g)); }
}
