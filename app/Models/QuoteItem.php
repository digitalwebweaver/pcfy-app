<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id', 'product_id', 'product_name', 'product_sku',
        'grade', 'quantity', 'unit_price', 'notes',
    ];

    protected $casts = ['unit_price' => 'integer'];

    public function quote()   { return $this->belongsTo(Quote::class); }
    public function product() { return $this->belongsTo(Product::class)->withTrashed(); }

    public function getLineTotalAttribute(): int
    {
        return ($this->unit_price ?? 0) * $this->quantity;
    }

    public function getUnitPriceInrAttribute(): string
    {
        return $this->unit_price ? '₹' . number_format($this->unit_price / 100, 0, '.', ',') : '—';
    }

    public function getLineTotalInrAttribute(): string
    {
        return $this->line_total ? '₹' . number_format($this->line_total / 100, 0, '.', ',') : '—';
    }
}
