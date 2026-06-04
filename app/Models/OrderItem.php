<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'product_sku',
        'grade', 'quantity', 'unit_price', 'notes',
    ];

    protected $casts = ['unit_price' => 'integer', 'quantity' => 'integer'];

    public function order()   { return $this->belongsTo(Order::class); }
    public function product() { return $this->belongsTo(Product::class); }

    public function getLineTotalAttribute(): int  { return $this->unit_price * $this->quantity; }
    public function getLineTotalInrAttribute(): string
    {
        return '₹' . number_format($this->line_total / 100, 0, '.', ',');
    }
}
