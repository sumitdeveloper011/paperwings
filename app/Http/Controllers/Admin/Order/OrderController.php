<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderDeliveredMail;
use App\Mail\OrderCancelledMail;

class OrderController extends Controller
{
    // Display a listing of orders
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $paymentStatus = $request->get('payment_status');

        $query = Order::with(['user', 'items.product'])
            ->withCount('items');

        if ($search) {
            $searchTerm = trim($search);
            // Optimized search - start with prefix match for better index usage
            $query->where(function($q) use ($searchTerm) {
                $q->where('order_number', 'like', "{$searchTerm}%")
                  ->orWhere('billing_email', 'like', "{$searchTerm}%")
                  ->orWhere('billing_first_name', 'like', "{$searchTerm}%")
                  ->orWhere('billing_last_name', 'like', "{$searchTerm}%")
                  ->orWhereRaw("CONCAT(billing_first_name, ' ', billing_last_name) LIKE ?", ["%{$searchTerm}%"]);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($paymentStatus) {
            $query->where('payment_status', $paymentStatus);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics with caching
        $cacheKey = 'admin_order_stats';
        $stats = cache()->remember($cacheKey, 300, function () {
            return [
                'total' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'shipped' => Order::where('status', 'shipped')->count(),
                'delivered' => Order::where('status', 'delivered')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
                'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            ];
        });

        // Return JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.order.partials.table', compact('orders'))->render(),
                'pagination' => view('admin.order.partials.pagination', compact('orders'))->render()
            ]);
        }

        return view('admin.order.index', compact('orders', 'search', 'status', 'paymentStatus', 'stats'));
    }

    // Display the specified order
    public function show(Order $order): View
    {
        $order->load([
            'user',
            'items.product.images',
            'billingRegion',
            'shippingRegion'
        ]);

        // Use ProductImageService for efficient image loading
        $productIds = $order->items->pluck('product_id')->unique()->filter();
        if ($productIds->isNotEmpty()) {
            $images = \App\Services\ProductImageService::getFirstImagesForProducts($productIds);

            $order->items->each(function($item) use ($images) {
                if ($item->product) {
                    $image = $images->get($item->product_id);
                    $item->product->setAttribute('main_image',
                        $image ? $image->image_url : asset('assets/images/placeholder.jpg')
                    );
                }
            });
        }

        return view('admin.order.show', compact('order'));
    }

    // Update order status
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_id' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|url|max:500',
        ]);

        $oldStatus = $order->status;
        $order->update([
            'status' => $request->status,
            'tracking_id' => $request->tracking_id,
            'tracking_url' => $request->tracking_url,
        ]);

        // Refresh order to get updated data
        $order->refresh();

        Log::info('Order status updated', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'updated_by' => Auth::id()
        ]);

        // Send email notification based on status (queue it to avoid blocking redirect)
        try {
            if ($request->status === 'delivered') {
                // Use OrderDeliveredMail for delivered status
                Mail::to($order->billing_email)->queue(new OrderDeliveredMail($order));
            } elseif ($request->status === 'cancelled') {
                // Use OrderCancelledMail for cancelled status
                Mail::to($order->billing_email)->queue(new OrderCancelledMail($order));
            } elseif ($request->status === 'shipped') {
                // Use OrderConfirmationMail for shipped status
                Mail::to($order->billing_email)->queue(new OrderConfirmationMail($order));
            }
        } catch (\Exception $e) {
            Log::error('Failed to queue order status email', [
                'order_id' => $order->id,
                'status' => $request->status,
                'error' => $e->getMessage()
            ]);
            // Don't fail the redirect if email fails
        }

        // Use order UUID for redirect to ensure proper route resolution
        return redirect()->route('admin.orders.show', $order->uuid)
            ->with('success', 'Order status updated successfully.');
    }

    // Update payment status
    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded'
        ]);

        $oldStatus = $order->payment_status;
        $order->update(['payment_status' => $request->payment_status]);

        Log::info('Order payment status updated', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'old_status' => $oldStatus,
            'new_status' => $request->payment_status,
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Payment status updated successfully.');
    }

    // Delete order
    public function destroy(Order $order): RedirectResponse
    {
        $orderNumber = $order->order_number;

        // Delete order items first
        $order->items()->delete();

        // Delete order
        $order->delete();

        Log::info('Order deleted', [
            'order_number' => $orderNumber,
            'deleted_by' => Auth::id()
        ]);

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}

