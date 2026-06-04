<?php
namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Testimonial;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Featured products
        $featured = Product::with(['brand', 'media'])
            ->active()
            ->where('stock_status', '!=', 'out_of_stock')
            ->orderByDesc('is_featured')
            ->latest()
            ->limit(8)
            ->get();

        // Top-level categories with child-aggregated product counts + min price
        $categories = Category::whereNull('parent_id')
            ->visible()
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $categories->load('children');

        // Collect all IDs (parent + every child)
        $allIds = $categories
            ->flatMap(fn($c) => $c->children->pluck('id')->prepend($c->id))
            ->unique()
            ->values();

        // One query: count + min price per direct category_id
        $statsByCat = DB::table('products')
            ->whereIn('category_id', $allIds)
            ->where('is_active', true)
            ->where('stock_status', '!=', 'out_of_stock')
            ->select(
                'category_id',
                DB::raw('COUNT(*) as cnt'),
                DB::raw('MIN(price) as min_price')
            )
            ->groupBy('category_id')
            ->get()
            ->keyBy('category_id');

        foreach ($categories as $cat) {
            $ids    = $cat->children->pluck('id')->prepend($cat->id)->toArray();
            $subset = $statsByCat->filter(fn($r, $id) => in_array($id, $ids));
            $cat->total_products  = (int) $subset->sum('cnt');
            $minP = $subset->min('min_price');
            $cat->min_price_inr   = $minP ? '₹' . number_format($minP / 100, 0, '.', ',') : null;
        }

        // Testimonials
        $testimonials = Testimonial::active()
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('pages.home', compact('featured', 'categories', 'testimonials'));
    }
}
