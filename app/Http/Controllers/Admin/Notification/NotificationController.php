<?php

namespace App\Http\Controllers\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get unread order notifications
     */
    public function index(Request $request): JsonResponse
    {
        // Get unread orders (orders that haven't been viewed by admin)
        $unreadOrders = Order::whereNull('admin_viewed_at')
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $notifications = $unreadOrders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->user ? $order->user->name : ($order->billing_first_name . ' ' . $order->billing_last_name),
                'total' => number_format($order->total, 2),
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'created_at' => $order->created_at->format('M d, Y H:i'),
                'time_ago' => $order->created_at->diffForHumans(),
                'url' => route('admin.orders.show', $order),
            ];
        });

        $unreadCount = Order::whereNull('admin_viewed_at')->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, Order $order): JsonResponse
    {
        if (!$order->admin_viewed_at) {
            $order->update([
                'admin_viewed_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        Order::whereNull('admin_viewed_at')->update([
            'admin_viewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }
}
