<?php
namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        return $this->listing($request, null);
    }

    public function category(Request $request, string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        return $this->listing($request, $category);
    }

    private function listing(Request $request, ?Category $category)
    {
        $query = Product::with(['brand', 'category', 'media'])
            ->active()
            ->where('stock_status', '!=', 'out_of_stock');

        // Category filter
        if ($category) {
            $ids = collect([$category->id])->merge($category->children->pluck('id'));
            $query->whereIn('category_id', $ids);
        } elseif ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Brand filter
        if ($request->filled('brand_id')) $query->where('brand_id', $request->brand_id);

        // Grade filter
        if ($request->filled('grade')) $query->where('grade', strtoupper($request->grade));

        // Search
        if ($request->filled('q')) {
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhere('sku',  'like', "%{$request->q}%")
                  ->orWhere('quick_specs', 'like', "%{$request->q}%")
            );
        }

        if ($request->filled('featured')) $query->where('is_featured', true);

        // Price range (values in ₹, stored in paise)
        if ($request->filled('min_price')) $query->where('price', '>=', (int)($request->min_price * 100));
        if ($request->filled('max_price') && $request->max_price < 200000)
            $query->where('price', '<=', (int)($request->max_price * 100));

        // ── Spec filters ──────────────────────────────────────
        if ($request->filled('ram')) {
            $ram = preg_replace('/[^0-9A-Za-z]/', '', $request->ram);
            $query->where('quick_specs', 'LIKE', "%RAM: {$ram}%");
        }
        if ($request->filled('storage')) {
            $storage = preg_replace('/[^0-9A-Za-z]/', '', $request->storage);
            $query->where('quick_specs', 'LIKE', "%Storage: {$storage}%");
        }
        if ($request->filled('processor')) {
            $proc = strtoupper(trim($request->processor));
            if (in_array($proc, ['I3', 'I5', 'I7', 'I9'])) {
                $query->where('quick_specs', 'LIKE', "%\"{$proc}-%\"");
            } elseif (preg_match('/^CORE [357]$/', $proc)) {
                $query->where('quick_specs', 'LIKE', "%{$proc}%");
            }
        }

        // Sort
        match ($request->get('sort', 'newest')) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name'       => $query->orderBy('name'),
            'discount'   => $query->orderByRaw('(mrp - price) / NULLIF(mrp,0) DESC'),
            default      => $query->orderByDesc('is_featured')->latest(),
        };

        $products = $query->paginate(24)->withQueryString();

        // ── Sidebar data ──────────────────────────────────────
        $brands = Brand::active()
            ->withCount(['products' => fn($q) => $q->active()])
            ->orderBy('name')
            ->get();

        $categories = Category::whereNull('parent_id')
            ->visible()
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        // Build spec options from all products in the current category/brand scope
        $specOptions = $this->extractSpecOptions($category, $request);

        // Active filter count
        $activeFilters = collect(['brand_id', 'grade', 'min_price', 'q', 'ram', 'storage', 'processor'])
            ->filter(fn($k) => $request->filled($k)
                && !($k === 'min_price' && $request->min_price == 0))
            ->count();

        return view('pages.shop', compact(
            'products', 'brands', 'categories', 'category',
            'activeFilters', 'specOptions'
        ));
    }

    private function extractSpecOptions(?Category $category, Request $request): array
    {
        $q = Product::active()
            ->where('stock_status', '!=', 'out_of_stock')
            ->whereNotNull('quick_specs');

        if ($category) {
            $ids = collect([$category->id])->merge($category->children->pluck('id'));
            $q->whereIn('category_id', $ids);
        }
        if ($request->filled('brand_id')) {
            $q->where('brand_id', $request->brand_id);
        }

        $rawSpecs = $q->pluck('quick_specs');

        $ram = [];
        $storage = [];
        $processors = [];

        foreach ($rawSpecs as $raw) {
            $arr = is_array($raw) ? $raw : json_decode($raw, true);
            if (!is_array($arr)) continue;

            foreach ($arr as $chip) {
                $chip = trim($chip);

                // RAM: e.g. "RAM: 8GB"
                if (preg_match('/^RAM:\s*(\d+\s*GB)$/i', $chip, $m)) {
                    $val = preg_replace('/\s+/', '', strtoupper($m[1]));
                    $ram[$val] = ($ram[$val] ?? 0) + 1;
                }

                // Storage: e.g. "Storage: 512GB" or "Storage: 1TB"
                if (preg_match('/^Storage:\s*(\d+\s*[GT]B)\b/i', $chip, $m)) {
                    $val = preg_replace('/\s+/', '', strtoupper($m[1]));
                    $storage[$val] = ($storage[$val] ?? 0) + 1;
                }

                // Processor family: i3/i5/i7/i9 or Core 3/5/7
                if (preg_match('/^(i[3579])\s*-/i', $chip, $m)) {
                    $key = strtoupper($m[1]);
                    $processors[$key] = ($processors[$key] ?? 0) + 1;
                } elseif (preg_match('/^(CORE\s*[357])\s/i', $chip, $m)) {
                    $key = preg_replace('/\s+/', ' ', strtoupper(trim($m[1])));
                    $processors[$key] = ($processors[$key] ?? 0) + 1;
                }
            }
        }

        // Sort RAM by numeric size
        uksort($ram, fn($a, $b) => (int)$a - (int)$b);

        // Sort storage logically
        $sOrder = ['256GB' => 1, '512GB' => 2, '1TB' => 3, '2TB' => 4, '4TB' => 5];
        uksort($storage, fn($a, $b) => ($sOrder[$a] ?? 9) - ($sOrder[$b] ?? 9));

        // Sort processors
        $pOrder = ['I3' => 1, 'I5' => 2, 'I7' => 3, 'I9' => 4, 'CORE 3' => 5, 'CORE 5' => 6, 'CORE 7' => 7];
        uksort($processors, fn($a, $b) => ($pOrder[$a] ?? 9) - ($pOrder[$b] ?? 9));

        return compact('ram', 'storage', 'processors');
    }
}
