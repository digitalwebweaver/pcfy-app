<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    protected $table = 'seo_meta';

    protected $fillable = [
        'seoable_type', 'seoable_id',
        'meta_title', 'meta_description',
        'og_title', 'og_image',
        'canonical', 'noindex', 'schema_json',
    ];

    protected $casts = ['noindex' => 'boolean'];

    public function seoable()
    {
        return $this->morphTo();
    }
}
