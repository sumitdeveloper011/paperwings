<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        // Overview stats
        $totalViews = ProductView::count();
        $totalProducts = Product::active()->count();
        $totalOrders = Order::where('status', '!=', 'cancelled')->count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Most viewed products
        $mostViewed = ProductView::select('product_id', DB::raw('count(*) as views'))
            ->groupBy('product_id')
            ->orderBy('views', 'desc')
            ->with('product')
            ->limit(10)
            ->get();

        // Recent views
        $recentViews = ProductView::with(['product', 'user'])
            ->orderBy('viewed_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.analytics.index', compact(
            'totalViews',
            'totalProducts',
            'totalOrders',
            'totalRevenue',
            'mostViewed',
            'recentViews'
        ));
    }

    public function productViews(Request $request): View
    {
        $productId = $request->get('product_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = ProductView::with(['product', 'user']);

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($dateFrom) {
            $query->where('viewed_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('viewed_at', '<=', $dateTo);
        }

        $views = $query->orderBy('viewed_at', 'desc')->paginate(50);

        // Most viewed products
        $mostViewed = ProductView::select('product_id', DB::raw('count(*) as views'))
            ->groupBy('product_id')
            ->orderBy('views', 'desc')
            ->with('product')
            ->limit(20)
            ->get();

        $products = Product::active()->orderBy('name')->get();

        return view('admin.analytics.product-views', compact('views', 'mostViewed', 'products', 'productId', 'dateFrom', 'dateTo'));
    }

    public function conversion(): View
    {
        // Products with views and orders
        $conversionData = DB::table('product_views')
            ->select('product_views.product_id', DB::raw('count(*) as views'))
            ->groupBy('product_views.product_id')
            ->get();

        $products = Product::withCount(['views', 'cartItems'])
            ->having('views_count', '>', 0)
            ->orderBy('views_count', 'desc')
            ->limit(50)
            ->get();

        return view('admin.analytics.conversion', compact('products'));
    }

    public function sales(Request $request): View
    {
        $period = $request->get('period', 'month'); // day, week, month, year

        $salesData = Order::where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as orders'),
                DB::raw('sum(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        $totalRevenue = Order::where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $totalOrders = Order::where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->count();

        return view('admin.analytics.sales', compact('salesData', 'totalRevenue', 'totalOrders', 'period'));
    }
}
