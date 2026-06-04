<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id', 'parent_id', 'label', 'type', 'url',
        'page_id', 'category_id', 'icon_svg', 'badge',
        'open_new_tab', 'is_visible', 'sort_order',
    ];
    protected $casts = ['open_new_tab' => 'boolean', 'is_visible' => 'boolean'];

    public function menu()     { return $this->belongsTo(Menu::class); }
    public function parent()   { return $this->belongsTo(MenuItem::class, 'parent_id'); }
    public function children() { return $this->hasMany(MenuItem::class, 'parent_id')->where('is_visible', true)->orderBy('sort_order'); }
    public function page()     { return $this->belongsTo(Page::class); }
    public function category() { return $this->belongsTo(Category::class); }

    public function getHrefAttribute(): string
    {
        return match($this->type) {
            'page'     => $this->page ? route('page', $this->page->slug) : '#',
            'category' => $this->category ? route('shop.category', $this->category->slug) : '#',
            default    => $this->url ?: '#',
        };
    }
}
