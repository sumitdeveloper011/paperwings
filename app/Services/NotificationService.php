<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    const TYPE_ORDER = 'order';
    const TYPE_CONTACT = 'contact';
    const TYPE_REVIEW = 'review';
    const TYPE_STOCK = 'stock';
    const TYPE_SYSTEM = 'system';

    const PRIORITY_HIGH = 'high';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_LOW = 'low';

    public function create(
        string $type,
        string $title,
        string $message,
        $notifiable,
        string $priority = self::PRIORITY_MEDIUM,
        array $data = []
    ): Notification {
        $notification = Notification::create([
            'type' => $type,
            'priority' => $priority,
            'title' => $title,
            'message' => $message,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'data' => $data,
        ]);

        $this->clearCache();

        return $notification;
    }

    public function createOrderNotification($order): Notification
    {
        $customerName = $order->user 
            ? $order->user->name 
            : ($order->billing_first_name . ' ' . $order->billing_last_name);

        $notification = $this->create(
            self::TYPE_ORDER,
            'New Order Received',
            "Order #{$order->order_number} from {$customerName} - $" . number_format($order->total, 2),
            $order,
            self::PRIORITY_MEDIUM,
            [
                'order_number' => $order->order_number,
                'customer_name' => $customerName,
                'total' => $order->total,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'url' => route('admin.orders.show', $order->uuid),
            ]
        );

        // Send email notification to admin if enabled
        if ($this->shouldSendEmail(self::TYPE_ORDER)) {
            $this->sendAdminOrderEmail($order);
        }

        return $notification;
    }

    public function createContactNotification($contactMessage): Notification
    {
        $notification = $this->create(
            self::TYPE_CONTACT,
            'New Contact Form Submission',
            "Contact form submitted by {$contactMessage->name} ({$contactMessage->email})",
            $contactMessage,
            self::PRIORITY_MEDIUM,
            [
                'name' => $contactMessage->name,
                'email' => $contactMessage->email,
                'subject' => $contactMessage->subject,
                'url' => route('admin.contact.show', $contactMessage->uuid),
            ]
        );

        // Send email notification to admin if enabled
        if ($this->shouldSendEmail(self::TYPE_CONTACT)) {
            $this->sendAdminContactEmail($contactMessage);
        }

        return $notification;
    }

    public function createReviewNotification($review): Notification
    {
        $productName = $review->product ? $review->product->name : 'Unknown Product';
        $reviewerName = $review->user ? $review->user->name : $review->name;

        $notification = $this->create(
            self::TYPE_REVIEW,
            'New Product Review',
            "{$reviewerName} reviewed {$productName} with {$review->rating} stars",
            $review,
            self::PRIORITY_LOW,
            [
                'product_name' => $productName,
                'reviewer_name' => $reviewerName,
                'rating' => $review->rating,
                'url' => route('admin.reviews.index'),
            ]
        );

        // Send email notification to admin if enabled
        if ($this->shouldSendEmail(self::TYPE_REVIEW)) {
            $this->sendAdminReviewEmail($review);
        }

        return $notification;
    }

    public function getUnreadCount(): int
    {
        return Cache::remember('admin_notifications_unread_count', 60, function () {
            return Notification::unread()->count();
        });
    }

    public function getUnreadByType(string $type): int
    {
        return Cache::remember("admin_notifications_unread_count_{$type}", 60, function () use ($type) {
            return Notification::unread()->byType($type)->count();
        });
    }

    public function markAsRead(Notification $notification): bool
    {
        $result = $notification->markAsRead();
        $this->clearCache();
        return $result;
    }

    public function markAllAsRead(): int
    {
        $count = Notification::unread()->update(['read_at' => now()]);
        $this->clearCache();
        return $count;
    }

    public function markAsReadByNotifiable($notifiable): int
    {
        $count = Notification::where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        $this->clearCache();
        return $count;
    }

    public function shouldSendEmail(string $type): bool
    {
        $preferences = json_decode(\App\Models\Setting::get('notification_email_preferences', '{}'), true);
        
        if (empty($preferences)) {
            return true;
        }

        return $preferences[$type] ?? true;
    }

    public function getEmailRecipients(): array
    {
        $recipients = json_decode(\App\Models\Setting::get('notification_email_recipients', '[]'), true);
        
        if (empty($recipients)) {
            $settings = \App\Helpers\SettingHelper::all();
            $adminEmail = \App\Helpers\SettingHelper::getFirstFromArraySetting($settings, 'emails');
            return $adminEmail ? [$adminEmail] : [];
        }

        return $recipients;
    }

    protected function sendAdminOrderEmail($order): void
    {
        try {
            $recipients = $this->getEmailRecipients();
            if (empty($recipients)) {
                return;
            }

            foreach ($recipients as $email) {
                Mail::to($email)->queue(new \App\Mail\OrderAdminNotificationMail($order));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin order email notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function sendAdminContactEmail($contactMessage): void
    {
        try {
            $recipients = $this->getEmailRecipients();
            if (empty($recipients)) {
                return;
            }

            foreach ($recipients as $email) {
                Mail::to($email)->queue(new \App\Mail\ContactAdminNotificationMail($contactMessage));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin contact email notification', [
                'contact_id' => $contactMessage->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function sendAdminReviewEmail($review): void
    {
        try {
            $recipients = $this->getEmailRecipients();
            if (empty($recipients)) {
                return;
            }

            foreach ($recipients as $email) {
                Mail::to($email)->queue(new \App\Mail\ReviewAdminNotificationMail($review));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin review email notification', [
                'review_id' => $review->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function clearCache(): void
    {
        Cache::forget('admin_notifications_unread_count');
        Cache::forget('admin_notifications_unread_count_order');
        Cache::forget('admin_notifications_unread_count_contact');
        Cache::forget('admin_notifications_unread_count_review');
    }
}
