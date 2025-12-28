@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-shopping-cart"></i>
                    Order Details
                </h1>
                <p class="page-header__subtitle">Order #{{ $order->order_number }}</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Orders
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Order Items</h3>
                </div>
                <div class="modern-card__body">
                    <div class="order-items-list">
                        @foreach($order->items as $item)
                        <div class="order-item-card">
                            <div class="order-item-card__image">
                                @if($item->product)
                                    <img src="{{ $item->product->main_image ?? asset('assets/images/placeholder.jpg') }}" alt="{{ $item->product->name ?? $item->product_name }}">
                                @else
                                    <img src="{{ asset('assets/images/placeholder.jpg') }}" alt="{{ $item->product_name }}">
                                @endif
                            </div>
                            <div class="order-item-card__info">
                                <h4 class="order-item-card__name">
                                    @if($item->product)
                                        {{ $item->product->name ?? $item->product_name }}
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
        </div>

        <!-- Order Summary & Actions -->
        <div class="col-lg-4">
            <!-- Order Status -->
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Order Status</h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label>Order Status</label>
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.orders.updatePaymentStatus', $order) }}" class="mt-3">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label>Payment Status</label>
                            <select name="payment_status" class="form-control" onchange="this.form.submit()">
                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="modern-card mt-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Order Summary</h3>
                </div>
                <div class="modern-card__body">
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
            </div>

            <!-- Order Information -->
            <div class="modern-card mt-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Order Information</h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-row">
                        <strong>Order Number:</strong>
                        <span>{{ $order->order_number }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Order Date:</strong>
                        <span>{{ $order->created_at->format('F d, Y h:i A') }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Payment Method:</strong>
                        <span>{{ ucfirst($order->payment_method) }}</span>
                    </div>
                    @if($order->stripe_payment_intent_id)
                    <div class="info-row">
                        <strong>Stripe Payment ID:</strong>
                        <span class="text-muted small">{{ $order->stripe_payment_intent_id }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Billing Address -->
            <div class="modern-card mt-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Billing Address</h3>
                </div>
                <div class="modern-card__body">
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
            <div class="modern-card mt-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Shipping Address</h3>
                </div>
                <div class="modern-card__body">
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
            <div class="modern-card mt-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Order Notes</h3>
                </div>
                <div class="modern-card__body">
                    <p>{{ $order->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
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

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-row:last-child {
    border-bottom: none;
}
</style>
@endsection

