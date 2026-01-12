@extends('layouts.frontend.main')
@section('content')
@include('frontend.partials.page-header', [
    'title' => 'Order Details',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'My Account', 'url' => route('account.view-profile')],
        ['label' => 'My Orders', 'url' => route('account.my-orders')],
        ['label' => 'Order Details', 'url' => null]
    ]
])

<section class="account-section">
    <div class="container">
        <div class="row">
            @include('frontend.account.partials.sidebar')

            <!-- Account Content -->
            <div class="col-lg-9">
                <div class="account-content">
                    <div class="account-block">
                        <div class="account-block__header mb-4">
                            <a href="{{ route('account.my-orders') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i>
                                Back to Orders
                            </a>
                        </div>

                        <h2 class="account-block__title">Order #{{ $order->order_number }}</h2>
                        <p class="text-muted mb-4">Placed on {{ $order->created_at->format('F d, Y h:i A') }}</p>

                        <!-- Order Status -->
                        <div class="alert alert-info mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Order Status:</strong> 
                                    <span class="badge badge-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div>
                                    <strong>Payment Status:</strong> 
                                    <span class="badge badge-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Order Items -->
                            <div class="col-lg-8">
                                <div class="order-details-section">
                                    <h3 class="order-details-section__title">Order Items</h3>
                                    <div class="order-items-list">
                                        @foreach($order->items as $item)
                                        <div class="order-item-card">
                                            <div class="order-item-card__image">
                                                @if($item->product)
                                                    <img src="{{ $item->product->main_thumbnail_url ?? asset('assets/images/placeholder.jpg') }}" alt="{{ $item->product->name ?? $item->product_name }}">
                                                @else
                                                    <img src="{{ asset('assets/images/placeholder.jpg') }}" alt="{{ $item->product_name }}">
                                                @endif
                                            </div>
                                            <div class="order-item-card__info">
                                                <h4 class="order-item-card__name">
                                                    @if($item->product)
                                                        <a href="{{ route('product.detail', $item->product->slug) }}">{{ $item->product->name ?? $item->product_name }}</a>
                                                    @else
                                                        {{ $item->product_name }}
                                                    @endif
                                                </h4>
                                                <div class="order-item-card__meta">
                                                    <span>Quantity: {{ $item->quantity }}</span>
                                                    <span>Price: ${{ number_format($item->price, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="order-item-card__total">
                                                <strong>${{ number_format($item->subtotal, 2) }}</strong>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="col-lg-4">
                                <div class="order-summary-card">
                                    <h3 class="order-summary-card__title">Order Summary</h3>
                                    
                                    <div class="order-summary-row">
                                        <span>Subtotal:</span>
                                        <span>${{ number_format($order->subtotal, 2) }}</span>
                                    </div>
                                    
                                    @if($order->discount > 0)
                                    <div class="order-summary-row">
                                        <span>Discount ({{ $order->coupon_code ?? 'Coupon' }}):</span>
                                        <span class="text-danger">-${{ number_format($order->discount, 2) }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($order->shipping > 0 || $order->shipping_price > 0)
                                    <div class="order-summary-row">
                                        <span>Shipping:</span>
                                        <span>${{ number_format($order->shipping_price ?? $order->shipping, 2) }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($order->tax > 0)
                                    <div class="order-summary-row">
                                        <span>Tax:</span>
                                        <span>${{ number_format($order->tax, 2) }}</span>
                                    </div>
                                    @endif
                                    
                                    <div class="order-summary-row order-summary-row--total">
                                        <span><strong>Total:</strong></span>
                                        <span><strong>${{ number_format($order->total, 2) }}</strong></span>
                                    </div>
                                </div>

                                <!-- Billing Address -->
                                <div class="order-address-card">
                                    <h3 class="order-address-card__title">Billing Address</h3>
                                    <div class="order-address-card__content">
                                        <p>
                                            <strong>{{ $order->billing_first_name }} {{ $order->billing_last_name }}</strong><br>
                                            {{ $order->billing_street_address }}<br>
                                            @if($order->billing_suburb){{ $order->billing_suburb }}, @endif
                                            {{ $order->billing_city }}<br>
                                            @if($order->billingRegion){{ $order->billingRegion->name }}, @endif
                                            {{ $order->billing_zip_code }}<br>
                                            {{ $order->billing_country }}<br>
                                            <strong>Email:</strong> {{ $order->billing_email }}<br>
                                            <strong>Phone:</strong> {{ $order->billing_phone }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Shipping Address -->
                                <div class="order-address-card">
                                    <h3 class="order-address-card__title">Shipping Address</h3>
                                    <div class="order-address-card__content">
                                        <p>
                                            <strong>{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</strong><br>
                                            {{ $order->shipping_street_address }}<br>
                                            @if($order->shipping_suburb){{ $order->shipping_suburb }}, @endif
                                            {{ $order->shipping_city }}<br>
                                            @if($order->shippingRegion){{ $order->shippingRegion->name }}, @endif
                                            {{ $order->shipping_zip_code }}<br>
                                            {{ $order->shipping_country }}<br>
                                            <strong>Email:</strong> {{ $order->shipping_email }}<br>
                                            <strong>Phone:</strong> {{ $order->shipping_phone }}
                                        </p>
                                    </div>
                                </div>

                                @if($order->notes)
                                <div class="order-notes-card">
                                    <h3 class="order-notes-card__title">Order Notes</h3>
                                    <p>{{ $order->notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.order-details-section {
    background: #fff;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.order-details-section__title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.order-items-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.order-item-card {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    align-items: center;
}

.order-item-card__image {
    width: 80px;
    height: 80px;
    flex-shrink: 0;
}

.order-item-card__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
}

.order-item-card__info {
    flex: 1;
}

.order-item-card__name {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.order-item-card__name a {
    color: #2c3e50;
    text-decoration: none;
}

.order-item-card__name a:hover {
    color: #007bff;
}

.order-item-card__meta {
    display: flex;
    gap: 1rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.order-item-card__total {
    font-size: 1.125rem;
    font-weight: 600;
    color: #2c3e50;
}

.order-summary-card,
.order-address-card,
.order-notes-card {
    background: #fff;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.order-summary-card__title,
.order-address-card__title,
.order-notes-card__title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #2c3e50;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

.order-summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.order-summary-row:last-child {
    border-bottom: none;
}

.order-summary-row--total {
    border-top: 2px solid #e9ecef;
    margin-top: 0.5rem;
    padding-top: 1rem;
    font-size: 1.125rem;
}

.order-address-card__content p {
    margin: 0;
    line-height: 1.8;
    color: #6c757d;
}

.badge {
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 600;
}

.badge-success {
    background-color: #28a745;
    color: #fff;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
    color: #fff;
}
</style>
@endsection

