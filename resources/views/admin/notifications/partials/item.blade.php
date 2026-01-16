{{-- Notification Item Partial --}}
@php
    $typeIcons = [
        'order' => 'fa-shopping-cart',
        'contact' => 'fa-envelope',
        'review' => 'fa-star',
        'stock' => 'fa-exclamation-triangle',
        'system' => 'fa-cog',
    ];
    $priorityClasses = [
        'high' => 'notification-priority--high',
        'medium' => 'notification-priority--medium',
        'low' => 'notification-priority--low',
    ];
    $icon = $typeIcons[$notification->type ?? 'order'] ?? 'fa-bell';
    $priorityClass = $priorityClasses[$notification->priority ?? 'medium'] ?? '';
    $data = $notification->data ?? [];
    $url = $data['url'] ?? '#';
    $timeAgo = $notification->created_at ? $notification->created_at->diffForHumans() : '';
@endphp

<div class="notification-item {{ $priorityClass }}" data-notification-id="{{ $notification->id }}">
    <div class="notification-item__icon">
        <i class="fas {{ $icon }}"></i>
    </div>
    <div class="notification-item__content">
        <div class="notification-item__header">
            <strong>{{ $notification->title ?? 'Notification' }}</strong>
            @if($timeAgo)
                <span class="notification-time">{{ $timeAgo }}</span>
            @endif
        </div>
        <div class="notification-item__body">
            <p>{{ $notification->message ?? '' }}</p>
            @if(isset($data['status']))
                <span class="status-badge status-badge--{{ $data['status'] === 'pending' ? 'warning' : ($data['status'] === 'delivered' ? 'success' : 'info') }}">
                    {{ ucfirst($data['status']) }}
                </span>
            @endif
        </div>
    </div>
    <div class="notification-item__action">
        <a href="{{ $url }}" 
           class="notification-view-btn" 
           onclick="markNotificationAsRead({{ $notification->id }})">
            <i class="fas fa-eye"></i>
        </a>
    </div>
</div>
