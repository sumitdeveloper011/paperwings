{{-- Notifications Collection Partial --}}
@forelse($notifications as $notification)
    @include('admin.notifications.partials.item', ['notification' => $notification])
@empty
    <div class="notification-empty">
        <i class="fas fa-bell-slash"></i>
        <p>No new notifications</p>
    </div>
@endforelse

