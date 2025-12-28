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

                            <!-- Stripe Payment Element -->
                            <div id="payment-element-container" style="margin: 20px 0; display: none;">
                                <div id="payment-element">
                                    <!-- Stripe Elements will create form elements here -->
                                </div>
                                <div id="payment-errors" role="alert" style="color: #dc3545; margin-top: 10px; font-size: 0.9rem; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; display: none;"></div>
                            </div>

                            <!-- Place Order Button -->
                            <button type="submit" class="place-order-btn" id="submitBtn">
                                <span id="button-text">Place Order</span>
                                <span id="spinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            let total = {{ $total }};
            const subtotal = {{ $subtotal }};
            let discount = {{ $discount }};
            let shipping = {{ $shipping }};
            const stripeKey = @json($stripePublishableKey ?? config('services.stripe.key'));
            let stripe;
            let elements;
            let paymentElement;
            let paymentIntentClientSecret = null;

            // Same as billing checkbox - simple functionality
            const sameAsBillingCheckbox = document.getElementById('sameAsBilling');
            const shippingDetails = document.getElementById('shippingDetails');
            const shippingInputs = shippingDetails ? shippingDetails.querySelectorAll('input, select') : [];

            // Function to calculate shipping
            function calculateShipping() {
                const shippingRegion = document.getElementById('shippingRegion');
                const billingRegion = document.getElementById('billingRegion');
                const regionId = shippingRegion ? shippingRegion.value : (billingRegion ? billingRegion.value : null);
                
                if (!regionId) {
                    shipping = 0;
                    updateTotals();
                    return;
                }

                const orderAmount = subtotal - discount;
                
                fetch('{{ route("checkout.calculate-shipping") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        region_id: regionId,
                        subtotal: subtotal,
                        discount: discount
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        shipping = parseFloat(data.shipping) || 0;
                        updateTotals();
                        
                        // Update shipping display
                        const shippingRow = document.getElementById('shippingRow');
                        const shippingValue = document.getElementById('checkoutShipping');
                        const freeShippingMessage = document.getElementById('freeShippingMessage');
                        
                        if (shippingValue) {
                            if (data.is_free_shipping) {
                                shippingValue.textContent = '$0.00';
                                if (freeShippingMessage) freeShippingMessage.style.display = 'inline';
                            } else {
                                shippingValue.textContent = '$' + shipping.toFixed(2);
                                if (freeShippingMessage) freeShippingMessage.style.display = 'none';
                            }
                        }
                        
                        if (shippingRow) {
                            shippingRow.style.display = 'flex';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error calculating shipping:', error);
                });
            }

            // Function to update totals
            function updateTotals() {
                total = subtotal - discount + shipping;
                const totalElement = document.getElementById('checkoutTotal');
                if (totalElement) {
                    totalElement.textContent = '$' + total.toFixed(2);
                }
                
                // Recreate payment intent with new total
                if (paymentIntentClientSecret) {
                    createPaymentIntent();
                }
            }

            if (sameAsBillingCheckbox && shippingInputs.length > 0) {
                sameAsBillingCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        document.getElementById('shippingFirstName').value = document.getElementById('billingFirstName').value;
                        document.getElementById('shippingLastName').value = document.getElementById('billingLastName').value;
                        document.getElementById('shippingEmail').value = document.getElementById('billingEmail').value;
                        document.getElementById('shippingPhone').value = document.getElementById('billingPhone').value;
                        document.getElementById('shippingAddress').value = document.getElementById('billingAddress').value;
                        document.getElementById('shippingCity').value = document.getElementById('billingCity').value;
                        document.getElementById('shippingSuburb').value = document.getElementById('billingSuburb').value;
                        document.getElementById('shippingRegion').value = document.getElementById('billingRegion').value;
                        document.getElementById('shippingZip').value = document.getElementById('billingZip').value;
                        shippingInputs.forEach(input => input.disabled = true);
                        calculateShipping();
                    } else {
                        shippingInputs.forEach(input => input.disabled = false);
                    }
                });
            }

            // Calculate shipping when shipping region changes
            const shippingRegionSelect = document.getElementById('shippingRegion');
            if (shippingRegionSelect) {
                shippingRegionSelect.addEventListener('change', function() {
                    calculateShipping();
                });
            }

            // Calculate shipping when billing region changes (if same as billing is checked)
            const billingRegionSelect = document.getElementById('billingRegion');
            if (billingRegionSelect && sameAsBillingCheckbox) {
                billingRegionSelect.addEventListener('change', function() {
                    if (sameAsBillingCheckbox.checked) {
                        calculateShipping();
                    }
                });
            }

            // Calculate shipping on page load if region is selected
            const initialShippingRegion = document.getElementById('shippingRegion');
            if (initialShippingRegion && initialShippingRegion.value) {
                calculateShipping();
            }

            // Initialize Stripe - only essential code
            console.log('Initializing Stripe...', { stripeKey: stripeKey ? 'present' : 'missing' });

            if (stripeKey && typeof stripeKey === 'string' && stripeKey.trim() !== '' && stripeKey.startsWith('pk_')) {
                try {
                    stripe = Stripe(stripeKey);
                    console.log('Stripe initialized, creating payment intent...');
                    // Don't create payment intent immediately - wait for shipping calculation
                    setTimeout(() => {
                        createPaymentIntent();
                    }, 500);
                } catch (error) {
                    console.error('Failed to initialize Stripe:', error);
                    const errorElement = document.getElementById('payment-errors');
                    if (errorElement) {
                        errorElement.textContent = 'Failed to initialize payment system: ' + error.message;
                        errorElement.style.display = 'block';
                    }
                }
            } else {
                console.warn('Stripe key not configured');
                const errorElement = document.getElementById('payment-errors');
                if (errorElement) {
                    errorElement.innerHTML = '<strong>Payment system is not configured.</strong><br>Please add STRIPE_KEY and STRIPE_SECRET to your .env file.';
                    errorElement.style.display = 'block';
                }
            }

            function createPaymentIntent() {
                console.log('createPaymentIntent called, total:', total);

                if (total <= 0) {
                    console.warn('Total is 0 or negative, skipping payment intent creation');
                    return;
                }

                console.log('Fetching payment intent from server...');
                fetch('{{ route("checkout.create-payment-intent") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ amount: total })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Payment intent response:', data);
                    // Check for both camelCase and snake_case (API might return either)
                    const clientSecret = data.clientSecret || data.client_secret;

                    console.log('Client secret check:', {
                        clientSecret: clientSecret,
                        hasClientSecret: !!clientSecret,
                        success: data.success
                    });

                    if (data.success && clientSecret) {
                        paymentIntentClientSecret = clientSecret;
                        console.log('Payment intent created, client secret received:', paymentIntentClientSecret);

                        if (!elements) {
                            console.log('Creating Stripe elements...');
                            elements = stripe.elements({
                                clientSecret: paymentIntentClientSecret,
                                appearance: { theme: 'stripe' }
                            });
                            paymentElement = elements.create('payment');
                            paymentElement.mount('#payment-element');
                            const container = document.getElementById('payment-element-container');
                            if (container) {
                                container.style.display = 'block';
                                console.log('Payment element mounted and displayed');
                            }
                        } else {
                            console.log('Updating existing elements...');
                            elements.update({ clientSecret: paymentIntentClientSecret });
                        }
                    } else {
                        console.error('Payment intent creation failed:', data);
                        const errorElement = document.getElementById('payment-errors');
                        if (errorElement) {
                            errorElement.textContent = data.message || 'Failed to initialize payment.';
                            errorElement.style.display = 'block';
                        }
                    }
                })
                .catch(error => {
                    const errorElement = document.getElementById('payment-errors');
                    if (errorElement) {
                        errorElement.textContent = 'Failed to initialize payment. Please refresh the page.';
                        errorElement.style.display = 'block';
                    }
                });
            }

            // Handle form submission - simplified
            const checkoutForm = document.getElementById('checkoutForm');
            const submitBtn = document.getElementById('submitBtn');

            // Add click handler to button as well (fallback)
            if (submitBtn) {
                submitBtn.addEventListener('click', function(e) {
                    console.log('Submit button clicked');
                    // Let the form submit event handle it, but log for debugging
                });
            }

            if (checkoutForm) {
                console.log('Checkout form found, attaching submit handler');
                checkoutForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Form submit event triggered');

                    // Check if payment is ready
                    console.log('Payment check:', {
                        stripe: !!stripe,
                        elements: !!elements,
                        clientSecret: !!paymentIntentClientSecret
                    });

                    if (!stripe || !elements || !paymentIntentClientSecret) {
                        console.error('Payment not ready');
                        const errorElement = document.getElementById('payment-errors');
                        if (errorElement) {
                            errorElement.textContent = 'Payment system not ready. Please wait for payment form to load and try again.';
                            errorElement.style.display = 'block';
                        } else {
                            alert('Payment system not ready. Please refresh the page.');
                        }
                        return;
                    }

                    const submitBtn = document.getElementById('submitBtn');
                    const buttonText = document.getElementById('button-text');
                    const spinner = document.getElementById('spinner');
                    const arrowIcon = document.getElementById('arrow-icon');

                    if (!submitBtn) {
                        console.error('Submit button not found');
                        alert('Submit button not found. Please refresh the page.');
                        return;
                    }

                    submitBtn.disabled = true;
                    if (buttonText) buttonText.textContent = 'Processing...';
                    if (spinner) spinner.style.display = 'inline-block';
                    if (arrowIcon) arrowIcon.style.display = 'none';

                    console.log('Starting payment confirmation...');

                    try {
                        const {error: stripeError, paymentIntent} = await stripe.confirmPayment({
                            elements,
                            confirmParams: {
                                return_url: window.location.origin + '/checkout/success',
                            },
                            redirect: 'if_required'
                        });

                        if (stripeError) {
                            console.error('Stripe payment error:', stripeError);
                            const errorElement = document.getElementById('payment-errors');
                            if (errorElement) {
                                errorElement.textContent = stripeError.message;
                                errorElement.style.display = 'block';
                            } else {
                                alert('Payment error: ' + stripeError.message);
                            }
                            submitBtn.disabled = false;
                            if (buttonText) buttonText.textContent = 'Place Order';
                            if (spinner) spinner.style.display = 'none';
                            if (arrowIcon) arrowIcon.style.display = 'inline-block';
                            return;
                        }

                        console.log('Payment confirmed, processing order...', paymentIntent);

                        const formData = new FormData(checkoutForm);
                        const orderData = {};
                        formData.forEach((value, key) => {
                            orderData[key] = value;
                        });
                        orderData.payment_intent_id = paymentIntent.id;

                        console.log('Sending order data to server...');

                        const response = await fetch('{{ route("checkout.process-order") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(orderData)
                        });

                        const result = await response.json();
                        console.log('Order processing result:', result);

                        if (result.success) {
                            console.log('Order successful, redirecting to:', result.redirect_url);
                            window.location.href = result.redirect_url;
                        } else {
                            console.error('Order processing failed:', result.message);
                            const errorElement = document.getElementById('payment-errors');
                            if (errorElement) {
                                errorElement.textContent = result.message || 'Failed to process order.';
                                errorElement.style.display = 'block';
                            } else {
                                alert(result.message || 'Failed to process order.');
                            }
                            submitBtn.disabled = false;
                            if (buttonText) buttonText.textContent = 'Place Order';
                            if (spinner) spinner.style.display = 'none';
                            if (arrowIcon) arrowIcon.style.display = 'inline-block';
                        }
                    } catch (error) {
                        console.error('Error in form submission:', error);
                        const errorElement = document.getElementById('payment-errors');
                        if (errorElement) {
                            errorElement.textContent = 'An error occurred: ' + error.message;
                            errorElement.style.display = 'block';
                        } else {
                            alert('An error occurred: ' + error.message);
                        }
                        submitBtn.disabled = false;
                        if (buttonText) buttonText.textContent = 'Place Order';
                        if (spinner) spinner.style.display = 'none';
                        if (arrowIcon) arrowIcon.style.display = 'inline-block';
                    }
                });
            }
        });
    </script>
@endsection
