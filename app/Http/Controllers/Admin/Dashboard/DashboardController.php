<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $path = 'admin.dashboard.';

    // Display dashboard with statistics and charts
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return [
                'total_products' => Product::count(),
                'active_products' => Product::where('status', 1)->count(),
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'total_users' => User::role('User')->count(),
                'active_users' => User::role('User')->where('status', 1)->count(),
                'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
                'total_subscriptions' => Subscription::where('status', 1)->count(),
            ];
        });

        $growthData = $this->getGrowthData($startDate, $endDate);

        $recentOrders = Order::with(['user'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $topProducts = DB::table('order_items')
            ->select('products.id', 'products.name', DB::raw('SUM(order_items.quantity) as total_sold'), DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->take(3)
            ->get();

        $orderStatusBreakdown = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $revenueByMonth = $this->getRevenueByMonth($startDate, $endDate);

        $data = [
            'title' => 'Dashboard',
            'pageTitle' => 'Dashboard',
            'pageSubtitle' => '',
            'pageIcon' => 'fas fa-chart-line',
            'stats' => $stats,
            'growthData' => $growthData,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'orderStatusBreakdown' => $orderStatusBreakdown,
            'revenueByMonth' => $revenueByMonth,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
        
        return view($this->path . 'index', $data);
    }

    // Get growth data for the specified date range
    private function getGrowthData($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $data = [];
        $current = $start->copy();

        while ($current <= $end) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            $products = Product::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            
            $orders = Order::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            
            $users = User::role('User')->whereBetween('created_at', [$monthStart, $monthEnd])->count();
            
            $revenue = Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total');

            $data[] = [
                'month' => $current->format('M Y'),
                'month_key' => $current->format('Y-m'),
                'products' => $products,
                'orders' => $orders,
                'users' => $users,
                'revenue' => round($revenue, 2),
            ];

            $current->addMonth();
        }

        return $data;
    }

    // Get revenue by month
    private function getRevenueByMonth($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $revenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        return $revenue;
    }

    // Get chart data via AJAX
    public function getChartData(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $growthData = $this->getGrowthData($startDate, $endDate);
        $revenueByMonth = $this->getRevenueByMonth($startDate, $endDate);

        return response()->json([
            'growthData' => $growthData,
            'revenueByMonth' => $revenueByMonth,
        ]);
    }
}
