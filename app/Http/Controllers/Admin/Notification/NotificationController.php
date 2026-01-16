<?php

namespace App\Http\Controllers\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $priority = $request->get('priority');
        
        $query = Notification::unread()
            ->with('notifiable')
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('created_at', 'desc')
            ->take(20);

        if ($type) {
            $query->byType($type);
        }

        if ($priority) {
            $query->byPriority($priority);
        }

        $notifications = $query->get()->map(function ($notification) {
            $data = $notification->data ?? [];
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'priority' => $notification->priority,
                'title' => $notification->title,
                'message' => $notification->message,
                'url' => $data['url'] ?? '#',
                'created_at' => $notification->created_at->format('M d, Y H:i'),
                'time_ago' => $notification->created_at->diffForHumans(),
                'data' => $data,
            ];
        });

        $unreadCount = $this->notificationService->getUnreadCount();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function render(Request $request): JsonResponse
    {
        $notifications = Notification::unread()
            ->with('notifiable')
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $unreadCount = $this->notificationService->getUnreadCount();

        $html = view('admin.notifications.partials.items', [
            'notifications' => $notifications
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        $this->notificationService->markAsRead($notification);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $count = $this->notificationService->markAllAsRead();

        return response()->json([
            'success' => true,
            'message' => "{$count} notifications marked as read",
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $priority = $request->get('priority');
        $perPage = $request->get('per_page', 20);

        $query = Notification::with('notifiable')
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('created_at', 'desc');

        if ($type) {
            $query->byType($type);
        }

        if ($priority) {
            $query->byPriority($priority);
        }

        $notifications = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
        ]);
    }
}
