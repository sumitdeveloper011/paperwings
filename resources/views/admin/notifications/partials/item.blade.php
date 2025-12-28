{{-- Notification Item Partial --}}
<div class="notification-item" data-order-id="{{ $notification->id }}">
    <div class="notification-item__icon">
        <i class="fas fa-shopping-cart"></i>
    </div>
    <div class="notification-item__content">
        <div class="notification-item__header">
            <strong>New Order: {{ $notification->order_number }}</strong>
            <span class="notification-time">{{ $notification->time_ago }}</span>
        </div>
        <div class="notification-item__body">
            <p>Customer: {{ $notification->customer_name }}</p>
            <p>Total: ${{ $notification->total }}</p>
            <span class="status-badge status-badge--{{ $notification->status === 'pending' ? 'warning' : ($notification->status === 'delivered' ? 'success' : 'info') }}">
                {{ ucfirst($notification->status) }}
            </span>
        </div>
    </div>
    <div class="notification-item__action">
        <a href="{{ $notification->url }}" 
           class="notification-view-btn" 
           onclick="markNotificationAsRead({{ $notification->id }})">
            <i class="fas fa-eye"></i>
        </a>
    </div>
</div>

