@extends('layouts.frontend.main')

@push('head')
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')
    @include('include.frontend.breadcrumb')

    <!-- Checkout Section -->
    <section class="checkout-section">
        <div class="container">
            <form class="checkout-form" id="checkoutForm">
                @csrf
                <div class="row">
                    <!-- Left Column - Forms -->
                    <div class="col-lg-8">
                        <!-- Billing Details -->
                        <div class="checkout-block">
                            <h2 class="checkout-block__title">Billing Details</h2>
                            <div class="checkout-form__grid">
                                <div class="form-group">
                                    <label for="billingFirstName" class="form-label">First Name <span class="required">*</span></label>
                                    <input type="text" id="billingFirstName" name="billing_first_name" class="form-input"
                                           value="{{ $billingAddress->first_name ?? $user->first_name ?? '' }}"
                                           placeholder="Enter your first name" required>
                                </div>
                                <div class="form-group">
                                    <label for="billingLastName" class="form-label">Last Name <span class="required">*</span></label>
                                    <input type="text" id="billingLastName" name="billing_last_name" class="form-input"
                                           value="{{ $billingAddress->last_name ?? $user->last_name ?? '' }}"
                                           placeholder="Enter your last name" required>
                                </div>
                                <div class="form-group">
                                    <label for="billingEmail" class="form-label">Email Address <span class="required">*</span></label>
                                    <input type="email" id="billingEmail" name="billing_email" class="form-input"
                                           value="{{ $billingAddress->email ?? $user->email ?? '' }}"
                                           placeholder="Enter your email" required>
                                </div>
                                <div class="form-group">
                                    <label for="billingPhone" class="form-label">Phone Number <span class="required">*</span></label>
                                    <input type="tel" id="billingPhone" name="billing_phone" class="form-input"
                                           value="{{ $billingAddress->phone ?? $user->userDetail->phone ?? '' }}"
                                           placeholder="Enter your phone number" required>
                                </div>
                                <div class="form-group form-group--full">
                                    <label for="billingAddress" class="form-label">Street Address <span class="required">*</span></label>
                                    <input type="text" id="billingAddress" name="billing_street_address" class="form-input"
                                           value="{{ $billingAddress->street_address ?? '' }}"
                                           placeholder="Enter your street address" required>
                                </div>
                                <div class="form-group">
                                    <label for="billingCity" class="form-label">City <span class="required">*</span></label>
                                    <input type="text" id="billingCity" name="billing_city" class="form-input"
                                           value="{{ $billingAddress->city ?? '' }}"
                                           placeholder="Enter your city" required>
                                </div>
                                <div class="form-group">
                                    <label for="billingSuburb" class="form-label">Suburb (optional)</label>
                                    <input type="text" id="billingSuburb" name="billing_suburb" class="form-input"
                                           value="{{ $billingAddress->suburb ?? '' }}"
                                           placeholder="Enter your suburb">
                                </div>
                                <div class="form-group">
                                    <label for="billingRegion" class="form-label">Region <span class="required">*</span></label>
                                    <select id="billingRegion" name="billing_region_id" class="form-input" required>
                                        <option value="">Select Region</option>
                                        @foreach($regions as $region)
                                            <option value="{{ $region->id }}"
                                                {{ ($billingAddress && $billingAddress->region_id == $region->id) ? 'selected' : '' }}>
                                                {{ $region->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="billingZip" class="form-label">Postcode <span class="required">*</span></label>
                                    <input type="text" id="billingZip" name="billing_zip_code" class="form-input"
                                           value="{{ $billingAddress->zip_code ?? '' }}"
                                           placeholder="Enter your postcode" required>
                                </div>
                                <div class="form-group">
                                    <label for="billingCountry" class="form-label">Country <span class="required">*</span></label>
                                    <input type="text" id="billingCountry" name="billing_country" class="form-input"
                                           value="New Zealand" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Details -->
                        <div class="checkout-block">
                            <div class="checkout-block__header">
                                <h2 class="checkout-block__title">Shipping Details</h2>
                                <label class="checkbox-label">
                                    <input type="checkbox" id="sameAsBilling">
                                    <span class="checkmark"></span>
                                    Same as billing address
                                </label>
                            </div>
                            <div class="shipping-details" id="shippingDetails">
                                <div class="checkout-form__grid">
                                    <div class="form-group">
                                        <label for="shippingFirstName" class="form-label">First Name <span class="required">*</span></label>
                                        <input type="text" id="shippingFirstName" name="shipping_first_name" class="form-input"
                                               value="{{ $shippingAddress->first_name ?? $user->first_name ?? '' }}"
                                               placeholder="Enter your first name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingLastName" class="form-label">Last Name <span class="required">*</span></label>
                                        <input type="text" id="shippingLastName" name="shipping_last_name" class="form-input"
                                               value="{{ $shippingAddress->last_name ?? $user->last_name ?? '' }}"
                                               placeholder="Enter your last name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingEmail" class="form-label">Email Address <span class="required">*</span></label>
                                        <input type="email" id="shippingEmail" name="shipping_email" class="form-input"
                                               value="{{ $shippingAddress->email ?? $user->email ?? '' }}"
                                               placeholder="Enter your email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingPhone" class="form-label">Phone Number <span class="required">*</span></label>
                                        <input type="tel" id="shippingPhone" name="shipping_phone" class="form-input"
                                               value="{{ $shippingAddress->phone ?? $user->userDetail->phone ?? '' }}"
                                               placeholder="Enter your phone number" required>
                                    </div>
                                    <div class="form-group form-group--full">
                                        <label for="shippingAddress" class="form-label">Street Address <span class="required">*</span></label>
                                        <input type="text" id="shippingAddress" name="shipping_street_address" class="form-input"
                                               value="{{ $shippingAddress->street_address ?? '' }}"
                                               placeholder="Enter your street address" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingCity" class="form-label">City <span class="required">*</span></label>
                                        <input type="text" id="shippingCity" name="shipping_city" class="form-input"
                                               value="{{ $shippingAddress->city ?? '' }}"
                                               placeholder="Enter your city" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingSuburb" class="form-label">Suburb (optional)</label>
                                        <input type="text" id="shippingSuburb" name="shipping_suburb" class="form-input"
                                               value="{{ $shippingAddress->suburb ?? '' }}"
                                               placeholder="Enter your suburb">
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingRegion" class="form-label">Region <span class="required">*</span></label>
                                        <select id="shippingRegion" name="shipping_region_id" class="form-input" required>
                                            <option value="">Select Region</option>
                                            @foreach($regions as $region)
                                                <option value="{{ $region->id }}"
                                                    {{ ($shippingAddress && $shippingAddress->region_id == $region->id) ? 'selected' : '' }}>
                                                    {{ $region->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingZip" class="form-label">Postcode <span class="required">*</span></label>
                                        <input type="text" id="shippingZip" name="shipping_zip_code" class="form-input"
                                               value="{{ $shippingAddress->zip_code ?? '' }}"
                                               placeholder="Enter your postcode" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingCountry" class="form-label">Country <span class="required">*</span></label>
                                        <input type="text" id="shippingCountry" name="shipping_country" class="form-input"
                                               value="New Zealand" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="checkout-block" style="margin-top: 2rem;">
                            <h2 class="checkout-block__title">Order Notes (Optional)</h2>
                            <div class="form-group">
                                <label for="orderNotes" class="form-label">Special instructions for your order</label>
                                <textarea id="orderNotes" name="notes" class="form-input" rows="4" placeholder="Any special instructions or notes for your order..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Order Summary -->
                    <div class="col-lg-4">
                        <div class="order-summary">
                            <h2 class="order-summary__title">Order Summary</h2>

                            <!-- Order Items -->
                            <div class="order-items">
                                @foreach($cartItems as $cartItem)
                                <div class="order-item">
                                    <div class="order-item__image">
                                        <img src="{{ $cartItem->product->main_image }}" alt="{{ $cartItem->product->name }}">
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

                            <!-- Order Totals -->
                            <div class="order-totals">
                                <div class="order-totals__item">
                                    <span class="order-totals__label">Subtotal</span>
                                    <span class="order-totals__value" id="checkoutSubtotal">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                @if($appliedCoupon && $discount > 0)
                                <div class="order-totals__item" id="couponDiscountRow">
                                    <span class="order-totals__label">
                                        Discount ({{ $appliedCoupon['code'] }})
                                        <button type="button" class="remove-coupon-btn" id="removeCouponBtn" style="background: none; border: none; color: var(--coral-red); cursor: pointer; margin-left: 0.5rem; font-size: 0.85rem;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </span>
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
                                {{-- <div class="order-totals__item">
                                    <span class="order-totals__label">Tax (15%)</span>
                                    <span class="order-totals__value">${{ number_format($tax, 2) }}</span>
                                </div> --}}
                                <div class="order-totals__item order-totals__item--total">
                                    <span class="order-totals__label">Total</span>
                                    <span class="order-totals__value" id="checkoutTotal">${{ number_format($total, 2) }}</span>
                                </div>
                            </div>

                            <!-- Coupon Code -->
                            <div class="order-coupon">
                                <label for="orderCoupon" class="form-label">Have a coupon code?</label>
                                @if(session('success'))
                                    <div class="coupon-message" style="color: #28a745; margin-bottom: 0.5rem; padding: 0.5rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px;">{{ session('success') }}</div>
                                @endif
                                @if(session('error'))
                                    <div class="coupon-message" style="color: #dc3545; margin-bottom: 0.5rem; padding: 0.5rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;">{{ session('error') }}</div>
                                @endif
                                @if($appliedCoupon)
                                    <div class="coupon-form">
                                        <input type="text" class="coupon-input" value="{{ $appliedCoupon['code'] }}" readonly>
                                        <form method="POST" action="{{ route('checkout.remove-coupon') }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="coupon-btn" style="background: var(--coral-red);">Remove</button>
                                        </form>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('checkout.apply-coupon') }}" class="coupon-form">
                                        @csrf
                                        <input type="text" name="code" id="orderCoupon" class="coupon-input"
                                               placeholder="Enter coupon code">
                                        <button type="submit" class="coupon-btn">Apply</button>
                                    </form>
                                @endif
                            </div>

                            <!-- Review Order Button -->
                            <button type="button" class="place-order-btn" id="reviewOrderBtn">
                                <span id="button-text">Review Order</span>
                                <i class="fas fa-arrow-right" id="arrow-icon"></i>
                            </button>

                            <!-- Security Info -->
                            <div class="order-security">
                                <i class="fas fa-lock"></i>
                                <span>Your payment information is secure and encrypted</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Checkout Review Modal -->
    <div class="checkout-modal" id="checkoutModal">
        <div class="checkout-modal__overlay" id="checkoutModalOverlay"></div>
        <div class="checkout-modal__content">
            <div class="checkout-modal__header">
                <h2 class="checkout-modal__title" id="modalTitle">Review Your Order</h2>
                <button type="button" class="checkout-modal__close" id="checkoutModalClose" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="checkout-modal__body">
                <!-- Step 1: Order Review -->
                <div class="checkout-modal__step" id="reviewStep">
                    <div class="checkout-modal__order-summary">
                        <h3 class="checkout-modal__section-title">Order Summary</h3>
                        
                        <!-- Order Items -->
                        <div class="checkout-modal__items">
                            @foreach($cartItems as $cartItem)
                            <div class="checkout-modal__item">
                                <div class="checkout-modal__item-image">
                                    <img src="{{ $cartItem->product->main_image }}" alt="{{ $cartItem->product->name }}">
                                </div>
                                <div class="checkout-modal__item-info">
                                    <h4 class="checkout-modal__item-name">{{ $cartItem->product->name }}</h4>
                                    <div class="checkout-modal__item-details">
                                        <span>${{ number_format($cartItem->price, 2) }} x {{ $cartItem->quantity }}</span>
                                        <span class="checkout-modal__item-total">= ${{ number_format($cartItem->subtotal, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Order Totals -->
                        <div class="checkout-modal__totals">
                            <div class="checkout-modal__total-row">
                                <span>Subtotal</span>
                                <span id="modalSubtotal">${{ number_format($subtotal, 2) }}</span>
                            </div>
                            @if($appliedCoupon && $discount > 0)
                            <div class="checkout-modal__total-row checkout-modal__total-row--discount">
                                <span>Discount ({{ $appliedCoupon['code'] }})</span>
                                <span id="modalDiscount">-${{ number_format($discount, 2) }}</span>
                            </div>
                            @endif
                            <div class="checkout-modal__total-row" id="modalShippingRow" style="{{ $shipping == 0 ? 'display: none;' : '' }}">
                                <span>Shipping</span>
                                <span id="modalShipping">${{ number_format($shipping, 2) }}</span>
                            </div>
                            <div class="checkout-modal__total-row checkout-modal__total-row--final">
                                <span>Total</span>
                                <span id="modalTotal">${{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-modal__actions">
                        <button type="button" class="checkout-modal__btn checkout-modal__btn--secondary" id="closeModalBtn">
                            <i class="fas fa-arrow-left"></i> Back to Checkout
                        </button>
                        <button type="button" class="checkout-modal__btn checkout-modal__btn--primary" id="proceedToPaymentBtn">
                            Proceed to Payment <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Payment -->
                <div class="checkout-modal__step" id="paymentStep" style="display: none;">
                    <div class="checkout-modal__payment-section">
                        <h3 class="checkout-modal__section-title">Payment Details</h3>
                        
                        <!-- Final Total Display -->
                        <div class="checkout-modal__final-total">
                            <span>Total Amount</span>
                            <span class="checkout-modal__final-total-amount" id="modalFinalTotal">${{ number_format($total, 2) }}</span>
                        </div>

                        <!-- Stripe Payment Element -->
                        <div id="modal-payment-element-container">
                            <div id="modal-payment-element">
                                <!-- Stripe Elements will create form elements here -->
                            </div>
                            <div id="modal-payment-errors" role="alert" style="color: #dc3545; margin-top: 10px; font-size: 0.9rem; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; display: none;"></div>
                        </div>

                        <!-- Security Info -->
                        <div class="checkout-modal__security">
                            <i class="fas fa-lock"></i>
                            <span>Your payment information is secure and encrypted</span>
                        </div>
                    </div>

                    <div class="checkout-modal__actions">
                        <button type="button" class="checkout-modal__btn checkout-modal__btn--secondary" id="backToReviewBtn">
                            <i class="fas fa-arrow-left"></i> Back to Review
                        </button>
                        <button type="button" class="checkout-modal__btn checkout-modal__btn--primary" id="placeOrderBtn">
                            <span id="placeOrderBtnText">Place Order</span>
                            <span id="placeOrderSpinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Checkout JavaScript Modules --}}
    <script src="{{ asset('assets/frontend/js/checkout/shipping.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/checkout/payment.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/checkout/form.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/checkout/modal.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/checkout/checkout.js') }}"></script>

    <script>
        // Initialize checkout with configuration
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            window.initCheckoutConfig({
                csrfToken: csrfToken,
                total: {{ $total }},
                subtotal: {{ $subtotal }},
                discount: {{ $discount }},
                shipping: {{ $shipping }},
                stripeKey: @json($stripePublishableKey ?? config('services.stripe.key')),
                calculateShippingUrl: '{{ route("checkout.calculate-shipping") }}',
                createPaymentIntentUrl: '{{ route("checkout.create-payment-intent") }}',
                processOrderUrl: '{{ route("checkout.process-order") }}'
            });
        });
    </script>
@endsection
