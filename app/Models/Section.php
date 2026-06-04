<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['page', 'section_key', 'label', 'is_visible', 'sort_order', 'content'];
    protected $casts    = ['is_visible' => 'boolean', 'content' => 'array'];

    public function scopeVisible($q)   { return $q->where('is_visible', true); }
    public function scopeForPage($q, string $page) { return $q->where('page', $page); }

    public function get(string $key, $default = null)
    {
        return $this->content[$key] ?? $default;
    }
}
