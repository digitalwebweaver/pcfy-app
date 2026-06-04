<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSpec extends Model
{
    protected $fillable = ['product_id', 'group', 'label', 'value', 'sort_order'];

    public function product() { return $this->belongsTo(Product::class); }
}
