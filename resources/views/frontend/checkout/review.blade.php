@extends('layouts.frontend.main')

@section('content')
    @include('frontend.partials.page-header', [
        'title' => 'Checkout - Review',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Cart', 'url' => route('cart.index')],
            ['label' => 'Checkout', 'url' => route('checkout.details')],
            ['label' => 'Review', 'url' => route('checkout.review')]
        ]
    ])

    <section class="checkout-section">
        <div class="container">
            <form method="POST" action="{{ route('checkout.confirm-review') }}">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="checkout-review-block">
                            <h2 class="checkout-review-block__title">Review Your Order</h2>

                            <div class="checkout-review-block__addresses" style="margin-bottom: 2rem;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="checkout-address-card">
                                            <div class="checkout-address-card__header">
                                                <i class="fas fa-file-invoice"></i>
                                                <h3 class="checkout-address-card__title">Billing Address</h3>
                                            </div>
                                            <div class="checkout-address-card__body">
                                                <p class="checkout-address-card__name">
                                                    {{ ($sessionData['billing_different'] ?? false) ? ($sessionData['billing']['first_name'] ?? '') . ' ' . ($sessionData['billing']['last_name'] ?? '') : ($sessionData['shipping']['first_name'] ?? '') . ' ' . ($sessionData['shipping']['last_name'] ?? '') }}
                                                </p>
                                                <p class="checkout-address-card__email">
                                                    {{ ($sessionData['billing_different'] ?? false) ? ($sessionData['billing']['email'] ?? '') : ($sessionData['shipping']['email'] ?? '') }}
                                                </p>
                                                <p class="checkout-address-card__phone">
                                                    {{ ($sessionData['billing_different'] ?? false) ? ($sessionData['billing']['phone'] ?? '') : ($sessionData['shipping']['phone'] ?? '') }}
                                                </p>
                                                <p class="checkout-address-card__address">
                                                    @php
                                                        $billing = ($sessionData['billing_different'] ?? false) ? ($sessionData['billing'] ?? []) : ($sessionData['shipping'] ?? []);
                                                        $billingRegion = $regions[$billing['region_id'] ?? ''] ?? null;
                                                    @endphp
                                                    {{ $billing['street_address'] ?? '' }}
                                                    @if(!empty($billing['suburb'])), {{ $billing['suburb'] }}@endif
                                                    , {{ $billing['city'] ?? '' }}
                                                    , {{ $billingRegion->name ?? '' }}
                                                    {{ $billing['zip_code'] ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="checkout-address-card">
                                            <div class="checkout-address-card__header">
                                                <i class="fas fa-truck"></i>
                                                <h3 class="checkout-address-card__title">Shipping Address</h3>
                                            </div>
                                            <div class="checkout-address-card__body">
                                                <p class="checkout-address-card__name">
                                                    {{ ($sessionData['shipping']['first_name'] ?? '') . ' ' . ($sessionData['shipping']['last_name'] ?? '') }}
                                                </p>
                                                <p class="checkout-address-card__email">
                                                    {{ $sessionData['shipping']['email'] ?? '' }}
                                                </p>
                                                <p class="checkout-address-card__phone">
                                                    {{ $sessionData['shipping']['phone'] ?? '' }}
                                                </p>
                                                <p class="checkout-address-card__address">
                                                    @php
                                                        $shippingAddress = $sessionData['shipping'] ?? [];
                                                        $shippingRegion = $regions[$shippingAddress['region_id'] ?? ''] ?? null;
                                                    @endphp
                                                    {{ $shippingAddress['street_address'] ?? '' }}
                                                    @if(!empty($shippingAddress['suburb'])), {{ $shippingAddress['suburb'] }}@endif
                                                    , {{ $shippingAddress['city'] ?? '' }}
                                                    , {{ $shippingRegion->name ?? '' }}
                                                    {{ $shippingAddress['zip_code'] ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="checkout-review-block__summary">
                                <h3 class="checkout-review-block__section-title">Order Items</h3>
                                <div class="checkout-review-table-wrapper">
                                    <table class="checkout-review-table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cartItems as $cartItem)
                                            <tr class="checkout-review-table__row">
                                                <td class="checkout-review-table__product">
                                                    <div class="checkout-review-table__product-content">
                                                        <div class="checkout-review-table__product-image">
                                                            <img src="{{ $cartItem->product->main_thumbnail_url }}" alt="{{ $cartItem->product->name }}" onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                                        </div>
                                                        <div class="checkout-review-table__product-info">
                                                            <h4 class="checkout-review-table__product-name">{{ $cartItem->product->name }}</h4>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="checkout-review-table__price">
                                                    ${{ number_format($cartItem->price, 2) }}
                                                </td>
                                                <td class="checkout-review-table__quantity">
                                                    {{ $cartItem->quantity }}
                                                </td>
                                                <td class="checkout-review-table__subtotal">
                                                    <strong>${{ number_format($cartItem->subtotal, 2) }}</strong>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if(!empty($sessionData['notes']))
                                <div class="checkout-review-block__notes" style="margin-top: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                                    <h4 style="font-size: 0.9rem; font-weight: 600; color: var(--dark-blue); margin-bottom: 0.5rem;">
                                        <i class="fas fa-sticky-note"></i> Order Notes
                                    </h4>
                                    <p style="font-size: 0.9rem; color: #6c757d; margin: 0;">{{ $sessionData['notes'] }}</p>
                                </div>
                                @endif
                            </div>

                            <div class="checkout-review-block__actions">
                                <a href="{{ route('checkout.details') }}" class="checkout-review-block__btn checkout-review-block__btn--secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Details
                                </a>
                                <button type="submit" class="checkout-review-block__btn checkout-review-block__btn--primary">
                                    Proceed to Payment <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="checkout-review-totals">
                            <h3 class="checkout-review-totals__title">Order Summary</h3>
                            <div class="checkout-review-totals__content">
                                <div class="checkout-review-totals__row">
                                    <span>Subtotal</span>
                                    <span>${{ number_format($subtotal, 2) }}</span>
                                </div>
                                @php
                                    // Ensure discount is always a float - controller should already ensure this, but be extra safe
                                    $safeDiscount = 0.0;
                                    if (isset($discount) && is_numeric($discount) && !is_array($discount)) {
                                        $safeDiscount = (float)$discount;
                                    } elseif (isset($appliedCoupon) && is_array($appliedCoupon) && isset($appliedCoupon['discount']) && is_numeric($appliedCoupon['discount']) && !is_array($appliedCoupon['discount'])) {
                                        $safeDiscount = (float)$appliedCoupon['discount'];
                                    }
                                @endphp
                                @if($appliedCoupon && is_array($appliedCoupon) && isset($appliedCoupon['code']) && $safeDiscount > 0)
                                <div class="checkout-review-totals__row checkout-review-totals__row--discount">
                                    <span>Discount ({{ $appliedCoupon['code'] }})</span>
                                    <span>-${{ number_format($safeDiscount, 2) }}</span>
                                </div>
                                @endif
                                @php
                                    // Ensure shipping and total are always floats
                                    $safeShipping = (is_numeric($shipping) && !is_array($shipping)) ? (float)$shipping : 0.0;
                                    $safeTotal = (is_numeric($total) && !is_array($total)) ? (float)$total : 0.0;
                                @endphp
                                <div class="checkout-review-totals__row">
                                    <span>Shipping</span>
                                    <span>${{ number_format($safeShipping, 2) }}</span>
                                </div>
                                <div class="checkout-review-totals__row checkout-review-totals__row--final">
                                    <span>Total</span>
                                    <span>${{ number_format($safeTotal, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Analytics && window.Analytics.isEnabled()) {
                window.Analytics.trackCheckoutStep('review', 2);
            }

            if (window.FormSubmissionHandler) {
                const reviewForm = document.querySelector('form[action*="confirm-review"]');
                if (reviewForm) {
                    window.FormSubmissionHandler.init(reviewForm, {
                        loadingText: 'Processing...',
                        timeout: 15000
                    });
                }
            }
        });
    </script>
@endsection
