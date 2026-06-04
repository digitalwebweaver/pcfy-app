<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'role', 'city', 'body', 'rating', 'is_verified', 'is_active', 'sort_order'];
    protected $casts    = ['is_verified' => 'boolean', 'is_active' => 'boolean'];

    public function scopeActive($q) { return $q->where('is_active', true); }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', $this->name);
        return strtoupper(implode('', array_map(fn($p) => $p[0] ?? '', array_slice($parts, 0, 2))));
    }
}
