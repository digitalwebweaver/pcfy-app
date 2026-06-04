<?php
namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductPageController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::with(['brand', 'category', 'specs_rows', 'media'])
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Product::with(['brand', 'media'])
            ->active()
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->where('stock_status', '!=', 'out_of_stock')
            ->limit(4)
            ->get();

        // Group specs by group name
        $specGroups = $product->specs_rows
            ->groupBy(fn($s) => $s->group ?: 'General');

        return view('pages.product', compact('product', 'related', 'specGroups'));
    }
}
