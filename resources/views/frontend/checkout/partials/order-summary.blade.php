<div class="order-summary">
    <h2 class="order-summary__title">Order Summary</h2>

    <div class="order-items">
        @foreach($cartItems as $cartItem)
        <div class="order-item">
            <div class="order-item__image">
                <a href="{{ route('product.detail', $cartItem->product->slug) }}">
                    <img src="{{ $cartItem->product->main_thumbnail_url }}" alt="{{ $cartItem->product->name }}" onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                </a>
            </div>
            <div class="order-item__info">
                <h3 class="order-item__name">
                    <a href="{{ route('product.detail', $cartItem->product->slug) }}">{{ $cartItem->product->name }}</a>
                </h3>
            </div>
            <div class="order-item__price-row">
                <span class="order-item__price">${{ number_format($cartItem->price, 2) }} x {{ $cartItem->quantity }}</span>
                <span class="order-item__price-total">= ${{ number_format($cartItem->subtotal, 2) }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <div class="order-totals">
        <div class="order-totals__item">
            <span class="order-totals__label">Subtotal</span>
            <span class="order-totals__value" id="checkoutSubtotal">${{ number_format($subtotal, 2) }}</span>
        </div>
        @if($appliedCoupon && is_array($appliedCoupon) && isset($appliedCoupon['code']) && $discount > 0)
        <div class="order-totals__item" id="couponDiscountRow">
            <span class="order-totals__label">Discount ({{ $appliedCoupon['code'] }})</span>
            <span class="order-totals__value" style="color: var(--coral-red);" id="checkoutDiscount">-${{ number_format($discount, 2) }}</span>
        </div>
        @endif
        <div class="order-totals__item" id="shippingRow" style="{{ $shipping == 0 ? 'display: none;' : '' }}">
            <span class="order-totals__label">
                Shipping
                <span id="freeShippingMessage" style="display: none; color: #28a745; font-size: 0.85rem; margin-left: 0.5rem;">
                    <i class="fas fa-check-circle"></i> Free Shipping!
                </span>
            </span>
            <span class="order-totals__value" id="checkoutShipping">${{ number_format($shipping, 2) }}</span>
        </div>
        @if(isset($platformFee) && $platformFee > 0)
        <div class="order-totals__item">
            <span class="order-totals__label">Platform Fee</span>
            <span class="order-totals__value" id="checkoutPlatformFee">${{ number_format($platformFee, 2) }}</span>
        </div>
        @endif
        @if(isset($estimatedStripeFee) && $estimatedStripeFee > 0)
        <div class="order-totals__item">
            <span class="order-totals__label">Processing Fee (Est.)</span>
            <span class="order-totals__value" id="checkoutStripeFee">${{ number_format($estimatedStripeFee, 2) }}</span>
        </div>
        @endif
        <div class="order-totals__item order-totals__item--total">
            <span class="order-totals__label">Total</span>
            <span class="order-totals__value" id="checkoutTotal">${{ number_format($finalTotal ?? $total, 2) }}</span>
        </div>
    </div>

    <div class="order-coupon" style="margin-bottom: 1.5rem;">
        <label for="orderCoupon" class="form-label">Have a coupon code?</label>
        @if(session('success'))
            <div class="coupon-message" style="color: #28a745; margin-bottom: 0.5rem; padding: 0.5rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="coupon-message" style="color: #dc3545; margin-bottom: 0.5rem; padding: 0.5rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('checkout.apply-coupon') }}" class="coupon-form-inline" id="couponForm" style="margin: 0;">
            @csrf
            <div class="coupon-input-wrapper">
                <input type="text" 
                       name="code" 
                       id="orderCoupon" 
                       class="coupon-input coupon-input--with-button" 
                       placeholder="Enter coupon code"
                       value="{{ ($appliedCoupon && is_array($appliedCoupon) && isset($appliedCoupon['code'])) ? $appliedCoupon['code'] : '' }}"
                       {{ ($appliedCoupon && is_array($appliedCoupon) && isset($appliedCoupon['code'])) ? 'readonly' : '' }}>
                <button type="button" 
                        class="coupon-btn-inline" 
                        id="couponApplyBtn" 
                        data-action="{{ ($appliedCoupon && is_array($appliedCoupon) && isset($appliedCoupon['code'])) ? 'remove' : 'apply' }}"
                        {{ ($appliedCoupon && is_array($appliedCoupon) && isset($appliedCoupon['code'])) ? '' : 'disabled' }}>
                    {{ ($appliedCoupon && is_array($appliedCoupon) && isset($appliedCoupon['code'])) ? 'Remove' : 'Apply' }}
                </button>
            </div>
        </form>
    </div>

    <div class="order-summary__action" style="margin-top: 1.5rem;">
        <button type="submit" form="checkoutForm" class="place-order-btn" style="width: 100%;">
            <span>Continue to Review</span>
            <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</div>
