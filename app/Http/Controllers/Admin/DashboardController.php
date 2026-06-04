<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Core stats
        $stats = [
            'quotes_new'      => Quote::where('status', 'new')->count(),
            'quotes_total'    => Quote::count(),
            'quotes_won'      => Quote::where('status', 'won')->count(),
            'products_total'  => Product::count(),
            'products_active' => Product::where('is_active', true)->count(),
            'contacts_unread' => ContactSubmission::where('status', 'unread')->count(),
        ];

        // Quote funnel (all time)
        $funnel = Quote::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Quotes per month (last 12 months)
        $monthlyQuotes = Quote::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $chartLabels = [];
        $chartData   = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $chartLabels[] = now()->subMonths($i)->format('M Y');
            $chartData[]   = $monthlyQuotes[$key] ?? 0;
        }

        // Top 10 quoted products
        $topProducts = QuoteItem::select('product_name', DB::raw('sum(quantity) as total_qty'), DB::raw('count(*) as times'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Revenue pipeline (sum of estimated totals for in-progress quotes)
        $pipeline = Quote::whereIn('status', ['reviewing', 'quoted'])
            ->with('items')
            ->get()
            ->sum(fn($q) => $q->items->sum(fn($i) => ($i->unit_price ?? 0) * $i->quantity));

        // Recent
        $recentQuotes   = Quote::with('items')->latest()->limit(8)->get();
        $recentContacts = ContactSubmission::latest()->limit(5)->get();

        return view('admin.dashboard.index', compact(
            'stats', 'funnel', 'chartLabels', 'chartData',
            'topProducts', 'pipeline', 'recentQuotes', 'recentContacts'
        ));
    }
}
