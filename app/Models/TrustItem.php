<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustItem extends Model
{
    protected $fillable = ['icon_svg', 'title', 'subtitle', 'is_visible', 'sort_order'];
    protected $casts    = ['is_visible' => 'boolean'];

    public function scopeVisible($q) { return $q->where('is_visible', true); }
}
