@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-user"></i>
                    User Details
                </h1>
                <p class="page-header__subtitle">View and manage user information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Information -->
        <div class="col-lg-4">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">User Information</h3>
                </div>
                <div class="modern-card__body">
                    <div class="user-profile">
                        <div class="user-profile__avatar">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                            @else
                                <div class="user-profile__avatar-placeholder">
                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <h3 class="user-profile__name">{{ $user->name }}</h3>
                        <p class="user-profile__email">{{ $user->email }}</p>
                        
                        <div class="user-stats">
                            <div class="user-stat">
                                <span class="user-stat__label">Status</span>
                                <span class="user-stat__value badge badge-{{ $user->status == 1 ? 'success' : 'danger' }}">
                                    {{ $user->status == 1 ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="user-stat">
                                <span class="user-stat__label">Orders</span>
                                <span class="user-stat__value">{{ $orders->total() }}</span>
                            </div>
                            <div class="user-stat">
                                <span class="user-stat__label">Wishlist</span>
                                <span class="user-stat__value">{{ $user->wishlists_count ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="user-details">
                            <div class="user-detail-item">
                                <strong>Phone:</strong>
                                <span>{{ $user->userDetail->phone ?? 'N/A' }}</span>
                            </div>
                            <div class="user-detail-item">
                                <strong>Date of Birth:</strong>
                                <span>{{ $user->userDetail->date_of_birth ? \Carbon\Carbon::parse($user->userDetail->date_of_birth)->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="user-detail-item">
                                <strong>Gender:</strong>
                                <span>{{ ucfirst($user->userDetail->gender ?? 'N/A') }}</span>
                            </div>
                            <div class="user-detail-item">
                                <strong>Member Since:</strong>
                                <span>{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="user-detail-item">
                                <strong>Email Verified:</strong>
                                <span class="badge badge-{{ $user->hasVerifiedEmail() ? 'success' : 'warning' }}">
                                    {{ $user->hasVerifiedEmail() ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.users.updateStatus', $user) }}" class="mt-3">
                            @csrf
                            @method('PATCH')
                            <label>Change Status:</label>
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Addresses -->
            @if($user->addresses->count() > 0)
            <div class="modern-card mt-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Addresses</h3>
                </div>
                <div class="modern-card__body">
                    @foreach($user->addresses as $address)
                    <div class="address-card">
                        <div class="address-card__header">
                            <strong>{{ ucfirst($address->type) }} Address</strong>
                            @if($address->is_default)
                                <span class="badge badge-success">Default</span>
                            @endif
                        </div>
                        <div class="address-card__body">
                            <p>
                                {{ $address->first_name }} {{ $address->last_name }}<br>
                                {{ $address->street_address }}<br>
                                @if($address->suburb){{ $address->suburb }}, @endif
                                {{ $address->city }}<br>
                                @if($address->region){{ $address->region->name }}, @endif
                                {{ $address->zip_code }}<br>
                                {{ $address->country }}<br>
                                <strong>Phone:</strong> {{ $address->phone }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- User Orders -->
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Order History</h3>
                    <p class="modern-card__subtitle">{{ $orders->total() }} total orders</p>
                </div>
                <div class="modern-card__body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>{{ $order->items->count() }} items</td>
                                        <td>${{ number_format($order->total, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="pagination-wrapper mt-3">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart fa-3x"></i>
                            <h3>No orders found</h3>
                            <p>This user hasn't placed any orders yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.user-profile {
    text-align: center;
}

.user-profile__avatar {
    width: 120px;
    height: 120px;
    margin: 0 auto 1.5rem;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #f0f0f0;
}

.user-profile__avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-profile__avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 2rem;
}

.user-profile__name {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.user-profile__email {
    color: #6c757d;
    margin-bottom: 1.5rem;
}

.user-stats {
    display: flex;
    justify-content: space-around;
    margin: 1.5rem 0;
    padding: 1rem 0;
    border-top: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
}

.user-stat {
    text-align: center;
}

.user-stat__label {
    display: block;
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.user-stat__value {
    display: block;
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
}

.user-details {
    margin-top: 1.5rem;
    text-align: left;
}

.user-detail-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.user-detail-item:last-child {
    border-bottom: none;
}

.address-card {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.address-card__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    font-weight: 600;
}

.address-card__body p {
    margin: 0;
    line-height: 1.8;
    color: #6c757d;
}
</style>
@endsection

