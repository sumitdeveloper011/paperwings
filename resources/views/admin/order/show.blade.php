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
        <!-- Main Content Area (Left Side) -->
        <div class="col-lg-8">
            <!-- Order Items Section -->
            <div class="modern-card order-section">
                <div class="modern-card__header">
                    <div class="modern-card__header-content">
                        <h3 class="modern-card__title">
                            <i class="fas fa-box"></i>
                            Order Items
                        </h3>
                        <p class="modern-card__subtitle">{{ $order->items->count() }} item(s) in this order</p>
                    </div>
                </div>
                <div class="modern-card__body">
                    <div class="order-items-list">
                        @foreach($order->items as $index => $item)
                        <div class="order-item-card" style="animation-delay: {{ $index * 0.05 }}s;">
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
                                        <a href="{{ route('admin.products.show', $item->product->uuid ?? $item->product->id) }}" class="order-item-card__link">
                                            {{ $item->product->name ?? $item->product_name }}
                                        </a>
                                    @else
                                        {{ $item->product_name }}
                                    @endif
                                </h4>
                                <div class="order-item-card__meta">
                                    <span class="order-item-card__meta-item">
                                        <i class="fas fa-hashtag"></i>
                                        Quantity: <strong>{{ $item->quantity }}</strong>
                                    </span>
                                    <span class="order-item-card__meta-item">
                                        <i class="fas fa-dollar-sign"></i>
                                        Unit Price: <strong>${{ number_format($item->price, 2) }}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="order-item-card__total">
                                <div class="order-item-card__total-label">Subtotal</div>
                                <div class="order-item-card__total-value">${{ number_format($item->subtotal, 2) }}</div>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <div class="order-item-divider"></div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Details Section (Moved from Sidebar, Styled like Customer Information) -->
            <div class="modern-card order-section" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Order Details
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="customer-info-grid">
                        <div class="customer-info-item">
                            <div class="customer-info-item__icon">
                                <i class="fas fa-hashtag"></i>
                            </div>
                            <div class="customer-info-item__content">
                                <div class="customer-info-item__label">Order Number</div>
                                <div class="customer-info-item__value">{{ $order->order_number }}</div>
                            </div>
                        </div>
                        <div class="customer-info-item">
                            <div class="customer-info-item__icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="customer-info-item__content">
                                <div class="customer-info-item__label">Order Date</div>
                                <div class="customer-info-item__value">{{ $order->created_at->format('M d, Y') }}</div>
                                <div class="customer-info-item__subtext">{{ $order->created_at->format('h:i A') }}</div>
                            </div>
                        </div>
                        <div class="customer-info-item">
                            <div class="customer-info-item__icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="customer-info-item__content">
                                <div class="customer-info-item__label">Payment Method</div>
                                <div class="customer-info-item__value">{{ ucfirst($order->payment_method) }}</div>
                            </div>
                        </div>
                        @if($order->stripe_payment_intent_id)
                        <div class="customer-info-item">
                            <div class="customer-info-item__icon">
                                <i class="fab fa-stripe"></i>
                            </div>
                            <div class="customer-info-item__content">
                                <div class="customer-info-item__label">Stripe Payment ID</div>
                                <div class="customer-info-item__value">
                                    <code class="payment-id-code">{{ $order->stripe_payment_intent_id }}</code>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Customer Information Section -->
            <div class="modern-card order-section" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-user"></i>
                        Customer Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="customer-info-grid">
                        @if($order->user)
                        <div class="customer-info-item">
                            <div class="customer-info-item__icon">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="customer-info-item__content">
                                <div class="customer-info-item__label">Customer</div>
                                <div class="customer-info-item__value">
                                    <a href="{{ route('admin.users.show', $order->user) }}">{{ $order->user->name }}</a>
                                </div>
                                <div class="customer-info-item__subtext">{{ $order->user->email }}</div>
                            </div>
                        </div>
                        @endif
                        <div class="customer-info-item">
                            <div class="customer-info-item__icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="customer-info-item__content">
                                <div class="customer-info-item__label">Email</div>
                                <div class="customer-info-item__value">{{ $order->billing_email }}</div>
                            </div>
                        </div>
                        <div class="customer-info-item">
                            <div class="customer-info-item__icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="customer-info-item__content">
                                <div class="customer-info-item__label">Phone</div>
                                <div class="customer-info-item__value">{{ $order->billing_phone }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Addresses Section -->
            <div class="modern-card order-section" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-map-marker-alt"></i>
                        Addresses
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="addresses-grid">
                        <!-- Billing Address -->
                        <div class="address-card">
                            <div class="address-card__header">
                                <div class="address-card__icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="address-card__title">Billing Address</div>
                            </div>
                            <div class="address-card__body">
                                <div class="address-card__name">{{ $order->billing_first_name }} {{ $order->billing_last_name }}</div>
                                <div class="address-card__address">
                                    {{ $order->billing_street_address }}<br>
                                    @if($order->billing_suburb){{ $order->billing_suburb }}, @endif
                                    {{ $order->billing_city }}<br>
                                    @if($order->billingRegion){{ $order->billingRegion->name }}, @endif
                                    {{ $order->billing_zip_code }}<br>
                                    {{ $order->billing_country }}
                                </div>
                                <div class="address-card__contact">
                                    <div class="address-card__contact-item">
                                        <i class="fas fa-envelope"></i>
                                        {{ $order->billing_email }}
                                    </div>
                                    <div class="address-card__contact-item">
                                        <i class="fas fa-phone"></i>
                                        {{ $order->billing_phone }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="address-card">
                            <div class="address-card__header">
                                <div class="address-card__icon">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <div class="address-card__title">Shipping Address</div>
                            </div>
                            <div class="address-card__body">
                                <div class="address-card__name">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</div>
                                <div class="address-card__address">
                                    {{ $order->shipping_street_address }}<br>
                                    @if($order->shipping_suburb){{ $order->shipping_suburb }}, @endif
                                    {{ $order->shipping_city }}<br>
                                    @if($order->shippingRegion){{ $order->shippingRegion->name }}, @endif
                                    {{ $order->shipping_zip_code }}<br>
                                    {{ $order->shipping_country }}
                                </div>
                                <div class="address-card__contact">
                                    <div class="address-card__contact-item">
                                        <i class="fas fa-envelope"></i>
                                        {{ $order->shipping_email }}
                                    </div>
                                    <div class="address-card__contact-item">
                                        <i class="fas fa-phone"></i>
                                        {{ $order->shipping_phone }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
            <div class="modern-card order-section" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-sticky-note"></i>
                        Order Notes
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="order-notes">
                        <p>{{ $order->notes }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar: Order Management & Summary (Right Side) -->
        <div class="col-lg-4">
            <!-- Order Status & Payment Actions -->
            <div class="modern-card order-section order-section--actions">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-cog"></i>
                        Order Management
                    </h3>
                </div>
                <div class="modern-card__body">
                    <!-- Order Status -->
                    <div class="order-action-group">
                        <label class="order-action-label">
                            <i class="fas fa-truck"></i>
                            Order Status
                        </label>
                        <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" id="statusForm">
                            @csrf
                            @method('PATCH')
                            <div class="select-wrapper">
                                <select name="status" id="orderStatus" class="form-control order-status-select">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <i class="fas fa-chevron-down select-arrow"></i>
                            </div>
                            <input type="hidden" name="tracking_id" id="trackingIdInput" value="{{ $order->tracking_id ?? '' }}">
                            <input type="hidden" name="tracking_url" id="trackingUrlInput" value="{{ $order->tracking_url ?? '' }}">
                        </form>
                        @if($order->tracking_id)
                        <div class="tracking-info">
                            <div class="tracking-info__item">
                                <i class="fas fa-barcode"></i>
                                <div>
                                    <strong>Tracking ID:</strong>
                                    <span>{{ $order->tracking_id }}</span>
                                </div>
                            </div>
                            @if($order->tracking_url)
                            <div class="tracking-info__item">
                                <i class="fas fa-link"></i>
                                <div>
                                    <strong>Tracking URL:</strong>
                                    <a href="{{ $order->tracking_url }}" target="_blank" class="tracking-link">View Tracking</a>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Payment Status -->
                    <div class="order-action-group" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e9ecef;">
                        <label class="order-action-label">
                            <i class="fas fa-credit-card"></i>
                            Payment Status
                        </label>
                        <div class="select-wrapper">
                            <select name="payment_status" class="form-control payment-status-select" disabled>
                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                            <i class="fas fa-chevron-down select-arrow"></i>
                        </div>
                        <div class="payment-status-note">
                            <i class="fas fa-info-circle"></i>
                            <span>Payment status is managed automatically by the payment gateway.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary (Moved to Right Side) -->
            <div class="modern-card order-section" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-calculator"></i>
                        Order Summary
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="order-summary-list">
                        <div class="order-summary-row">
                            <span class="order-summary-label">Subtotal</span>
                            <span class="order-summary-value">${{ number_format($order->subtotal, 2) }}</span>
                        </div>

                        @if($order->discount > 0)
                        <div class="order-summary-row order-summary-row--discount">
                            <span class="order-summary-label">
                                <i class="fas fa-tag"></i>
                                Discount ({{ $order->coupon_code ?? 'Coupon' }})
                            </span>
                            <span class="order-summary-value order-summary-value--discount">-${{ number_format($order->discount, 2) }}</span>
                        </div>
                        @endif

                        @if($order->shipping > 0 || $order->shipping_price > 0)
                        <div class="order-summary-row">
                            <span class="order-summary-label">
                                <i class="fas fa-shipping-fast"></i>
                                Shipping
                            </span>
                            <span class="order-summary-value">${{ number_format($order->shipping_price ?? $order->shipping, 2) }}</span>
                        </div>
                        @endif

                        @if($order->tax > 0)
                        <div class="order-summary-row">
                            <span class="order-summary-label">
                                <i class="fas fa-receipt"></i>
                                Tax
                            </span>
                            <span class="order-summary-value">${{ number_format($order->tax, 2) }}</span>
                        </div>
                        @endif

                        <div class="order-summary-divider"></div>

                        <div class="order-summary-row order-summary-row--total">
                            <span class="order-summary-label">Total</span>
                            <span class="order-summary-value order-summary-value--total">${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tracking Modal -->
<div class="modal fade" id="trackingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tracking Information</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Tracking ID / Number</label>
                    <input type="text" class="form-control" id="trackingId"
                           placeholder="e.g., TRK123456789" value="{{ $order->tracking_id ?? '' }}">
                </div>
                <div class="form-group">
                    <label>Tracking URL (Optional)</label>
                    <input type="url" class="form-control" id="trackingUrl"
                           placeholder="https://tracking.courier.com/track/TRK123456789" value="{{ $order->tracking_url ?? '' }}">
                    <small class="form-text text-muted">Direct link to track shipment</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTrackingBtn">Save & Update Status</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('orderStatus');
    const trackingModal = document.getElementById('trackingModal');
    const trackingIdInput = document.getElementById('trackingIdInput');
    const trackingUrlInput = document.getElementById('trackingUrlInput');
    const trackingId = document.getElementById('trackingId');
    const trackingUrl = document.getElementById('trackingUrl');
    const saveBtn = document.getElementById('saveTrackingBtn');
    const cancelBtn = trackingModal.querySelector('[data-dismiss="modal"]');
    const statusForm = document.getElementById('statusForm');
    let previousStatus = statusSelect.value; // Store current status

    statusSelect.addEventListener('change', function() {
        const selectedStatus = this.value;

        // Sirf "shipped" status te popup kholo
        if (selectedStatus === 'shipped') {
            // Previous status store karo
            previousStatus = statusSelect.options[statusSelect.selectedIndex - 1]?.value || '{{ $order->status }}';

            // jQuery modal show karo
            if (typeof $ !== 'undefined') {
                $('#trackingModal').modal('show');
            } else {
                // Bootstrap 5
                const modal = new bootstrap.Modal(trackingModal);
                modal.show();
            }
        } else {
            // Baaki statuses te direct submit karo
            statusForm.submit();
        }
    });

    // Cancel button handler
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Status dropdown ko previous value par reset karo
            statusSelect.value = previousStatus;

            // Modal band karo
            if (typeof $ !== 'undefined') {
                $('#trackingModal').modal('hide');
            } else {
                const modal = bootstrap.Modal.getInstance(trackingModal);
                if (modal) modal.hide();
            }
        });
    }

    // Modal close event (agar user modal ke bahar click kare ya ESC press kare)
    if (typeof $ !== 'undefined') {
        $('#trackingModal').on('hidden.bs.modal', function() {
            // Status dropdown ko previous value par reset karo
            if (statusSelect.value === 'shipped') {
                statusSelect.value = previousStatus;
            }
        });
    } else {
        trackingModal.addEventListener('hidden.bs.modal', function() {
            // Status dropdown ko previous value par reset karo
            if (statusSelect.value === 'shipped') {
                statusSelect.value = previousStatus;
            }
        });
    }

    saveBtn.addEventListener('click', function() {
        // Tracking values hidden inputs mein daalo
        trackingIdInput.value = trackingId.value;
        trackingUrlInput.value = trackingUrl.value;

        // Modal band karo
        if (typeof $ !== 'undefined') {
            $('#trackingModal').modal('hide');
        } else {
            const modal = bootstrap.Modal.getInstance(trackingModal);
            if (modal) modal.hide();
        }

        // Form submit karo
        statusForm.submit();
    });
});
</script>
@endsection

