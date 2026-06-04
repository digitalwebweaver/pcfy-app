<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggestions(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $products = Product::active()
            ->with('brand')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('sku', 'like', "%{$q}%")
                      ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$q}%"));
            })
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', ["{$q}%"])
            ->limit(8)
            ->get();

        return response()->json($products->map(fn ($p) => [
            'name'  => $p->name,
            'thumb' => $p->thumb,
            'price' => $p->price_inr,
            'brand' => $p->brand?->name,
            'grade' => $p->grade,
            'url'   => route('product', $p->slug),
        ]));
    }
}
