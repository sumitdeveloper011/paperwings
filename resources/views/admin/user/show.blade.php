@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-user"></i>
                    {{ $user->name }}
                </h1>
                <p class="page-header__subtitle">User details and information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit User</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Users</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Profile Card -->
        <div class="col-lg-4">
            <div class="modern-card modern-card--glass">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-user-circle"></i>
                        User Profile
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="user-profile-header">
                        <div class="user-profile-avatar">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="user-profile-avatar__img">
                            @else
                                <div class="user-profile-avatar__placeholder">
                                    {{ strtoupper(substr($user->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <h3 class="user-profile-name">{{ $user->name }}</h3>
                        <p class="user-profile-email">
                            <i class="fas fa-envelope"></i>
                            {{ $user->email }}
                        </p>
                        <div class="user-profile-status">
                            <span class="badge badge-{{ $user->status == 1 ? 'success' : 'danger' }}">
                                {{ $user->status == 1 ? 'Active' : 'Inactive' }}
                            </span>
                            @if($user->hasVerifiedEmail())
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Verified
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <i class="fas fa-exclamation-circle"></i> Unverified
                                </span>
                            @endif
                        </div>
                        @if($user->roles && $user->roles->count() > 0)
                        <div class="user-profile-roles" style="margin-top: 1rem;">
                            <strong style="display: block; margin-bottom: 0.5rem; color: #2c3e50;">Roles:</strong>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; justify-content: center;">
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                                        <i class="fas fa-user-tag"></i> {{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Quick Stats -->
                    <div class="user-quick-stats">
                        <div class="user-quick-stat">
                            <div class="user-quick-stat__icon">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="user-quick-stat__content">
                                <div class="user-quick-stat__value">{{ $orders->total() }}</div>
                                <div class="user-quick-stat__label">Orders</div>
                            </div>
                        </div>
                        <div class="user-quick-stat">
                            <div class="user-quick-stat__icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="user-quick-stat__content">
                                <div class="user-quick-stat__value">{{ $user->wishlists_count ?? 0 }}</div>
                                <div class="user-quick-stat__label">Wishlist</div>
                            </div>
                        </div>
                        <div class="user-quick-stat">
                            <div class="user-quick-stat__icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="user-quick-stat__content">
                                <div class="user-quick-stat__value">{{ $user->addresses_count ?? 0 }}</div>
                                <div class="user-quick-stat__label">Addresses</div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Update Form -->
                    <div class="user-status-form">
                        <form method="POST" action="{{ route('admin.users.updateStatus', $user) }}">
                            @csrf
                            @method('PATCH')
                            <label class="form-label">
                                <i class="fas fa-toggle-on"></i>
                                Change Status
                            </label>
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- User Details Card -->
            <div class="modern-card modern-card--glass" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Personal Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-grid">
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-phone"></i> Phone
                            </label>
                            <div class="info-value">
                                {{ $user->userDetail->phone ?? ($user->phone ?? 'N/A') }}
                            </div>
                        </div>
                        @if($user->userDetail && $user->userDetail->date_of_birth)
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-birthday-cake"></i> Date of Birth
                            </label>
                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($user->userDetail->date_of_birth)->format('M d, Y') }}
                            </div>
                        </div>
                        @endif
                        @if($user->userDetail && $user->userDetail->gender)
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-venus-mars"></i> Gender
                            </label>
                            <div class="info-value">
                                {{ ucfirst($user->userDetail->gender) }}
                            </div>
                        </div>
                        @endif
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-calendar-alt"></i> Member Since
                            </label>
                            <div class="info-value">
                                {{ $user->created_at->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="info-item">
                            <label class="info-label">
                                <i class="fas fa-id-card"></i> User ID
                            </label>
                            <div class="info-value">
                                #{{ $user->id }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Addresses Card -->
            @if($user->addresses->count() > 0)
            <div class="modern-card modern-card--glass" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-map-marker-alt"></i>
                        Addresses ({{ $user->addresses->count() }})
                    </h3>
                </div>
                <div class="modern-card__body">
                    @foreach($user->addresses as $address)
                    <div class="address-card">
                        <div class="address-card__header">
                            <span class="address-card__type">
                                <i class="fas fa-{{ $address->type === 'billing' ? 'credit-card' : 'truck' }}"></i>
                                {{ ucfirst($address->type) }} Address
                            </span>
                            @if($address->is_default)
                                <span class="badge badge-success">Default</span>
                            @endif
                        </div>
                        <div class="address-card__body">
                            <p class="address-card__name">
                                <strong>{{ $address->first_name }} {{ $address->last_name }}</strong>
                            </p>
                            <p class="address-card__address">
                                {{ $address->street_address }}<br>
                                @if($address->suburb){{ $address->suburb }}, @endif
                                {{ $address->city }}<br>
                                @if($address->region){{ $address->region->name }}, @endif
                                {{ $address->zip_code }}<br>
                                {{ $address->country }}
                            </p>
                            @if($address->phone)
                            <p class="address-card__phone">
                                <i class="fas fa-phone"></i> {{ $address->phone }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @if(!$loop->last)
                        <hr class="address-divider">
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Orders Section -->
        <div class="col-lg-8">
            <div class="modern-card modern-card--glass">
                <div class="modern-card__header">
                    <div class="modern-card__header-content">
                        <h3 class="modern-card__title">
                            <i class="fas fa-shopping-bag"></i>
                            Order History
                        </h3>
                        <p class="modern-card__subtitle">{{ $orders->total() }} total orders</p>
                    </div>
                </div>
                <div class="modern-card__body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="modern-table modern-table--enhanced">
                                <thead class="modern-table__head">
                                    <tr>
                                        <th class="modern-table__th">Order #</th>
                                        <th class="modern-table__th">Date</th>
                                        <th class="modern-table__th">Items</th>
                                        <th class="modern-table__th">Total</th>
                                        <th class="modern-table__th modern-table__th--actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="modern-table__body">
                                    @foreach($orders as $index => $order)
                                    <tr class="modern-table__row modern-table__row--animated" style="animation-delay: {{ $index * 0.05 }}s;">
                                        <td class="modern-table__td">
                                            <strong>#{{ $order->order_number }}</strong>
                                        </td>
                                        <td class="modern-table__td">
                                            {{ $order->created_at->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td class="modern-table__td">
                                            <span class="badge badge-info">{{ $order->items->count() }} items</span>
                                        </td>
                                        <td class="modern-table__td">
                                            <strong>${{ number_format($order->total, 2) }}</strong>
                                        </td>
                                        <td class="modern-table__td modern-table__td--actions">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info" title="View Order">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($orders->hasPages())
                        <div class="pagination-wrapper mt-4">
                            {{ $orders->links() }}
                        </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <div class="empty-state__icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3 class="empty-state__title">No Orders Found</h3>
                            <p class="empty-state__text">This user hasn't placed any orders yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* User Profile Styles */
.user-profile-header {
    text-align: center;
    padding: 1.5rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    margin-bottom: 1.5rem;
}

.user-profile-avatar {
    width: 120px;
    height: 120px;
    margin: 0 auto 1.5rem;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #f0f0f0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.user-profile-avatar__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-profile-avatar__placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 2.5rem;
}

.user-profile-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.user-profile-email {
    color: #6c757d;
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

.user-profile-email i {
    margin-right: 0.5rem;
    color: #667eea;
}

.user-profile-status {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

/* Quick Stats */
.user-quick-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin: 1.5rem 0;
    padding: 1.5rem 0;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.user-quick-stat {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 12px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.user-quick-stat:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.user-quick-stat__icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.user-quick-stat__content {
    flex: 1;
}

.user-quick-stat__value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1.2;
}

.user-quick-stat__label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

/* Status Form */
.user-status-form {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.user-status-form .form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.user-status-form .form-label i {
    color: #667eea;
}

.user-status-form .form-select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.user-status-form .form-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Info Grid */
.info-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.info-item:hover {
    background: #e9ecef;
}

.info-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-label i {
    color: #667eea;
    width: 20px;
}

.info-value {
    font-size: 1rem;
    color: #2c3e50;
    font-weight: 500;
}

/* Address Card */
.address-card {
    padding: 1.25rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 12px;
    border: 1px solid #e9ecef;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.address-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.address-card__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e9ecef;
}

.address-card__type {
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.address-card__type i {
    color: #667eea;
}

.address-card__body {
    color: #6c757d;
    line-height: 1.8;
}

.address-card__name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.address-card__address {
    margin-bottom: 0.5rem;
}

.address-card__phone {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e9ecef;
    color: #667eea;
    font-weight: 500;
}

.address-card__phone i {
    margin-right: 0.5rem;
}

.address-divider {
    margin: 1.5rem 0;
    border: none;
    border-top: 1px solid #e9ecef;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
}

.empty-state__icon {
    font-size: 4rem;
    color: #cbd5e0;
    margin-bottom: 1.5rem;
}

.empty-state__title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.empty-state__text {
    color: #6c757d;
    margin: 0;
}

/* Responsive */
@media (max-width: 992px) {
    .user-quick-stats {
        grid-template-columns: 1fr;
    }

    .user-quick-stat {
        justify-content: flex-start;
    }
}

@media (max-width: 768px) {
    .user-profile-avatar {
        width: 100px;
        height: 100px;
    }

    .user-profile-avatar__placeholder {
        font-size: 2rem;
    }

    .user-profile-name {
        font-size: 1.25rem;
    }
}
</style>
@endsection
