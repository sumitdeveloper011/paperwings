@extends('layouts.frontend.main')

@push('head')
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')
    @include('frontend.partials.page-header', [
        'title' => 'Checkout - Details',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Cart', 'url' => route('cart.index')],
            ['label' => 'Checkout', 'url' => route('checkout.details')]
        ]
    ])

    <section class="checkout-section">
        <div class="container">
            <form method="POST" action="{{ route('checkout.store-details') }}" id="checkoutForm">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="checkout-block checkout-block--compact">
                            <div class="checkout-block__header-compact">
                                <h2 class="checkout-block__title">
                                    <i class="fas fa-truck"></i>
                                    Shipping Address
                                </h2>
                            </div>
                            
                            @if($user && $shippingAddresses->count() > 0)
                            <div class="form-group form-group--full" style="margin-bottom: 1rem;">
                                <label for="shippingAddressSelect" class="form-label">Select Saved Address</label>
                                <select id="shippingAddressSelect" class="form-input">
                                    <option value="">-- Use New Address --</option>
                                    @foreach($shippingAddresses as $address)
                                        <option value="{{ $address->id }}" 
                                                data-first-name="{{ $address->first_name }}"
                                                data-last-name="{{ $address->last_name }}"
                                                data-email="{{ $address->email ?? $user->email }}"
                                                data-phone="{{ $address->phone }}"
                                                data-street="{{ $address->street_address }}"
                                                data-suburb="{{ $address->suburb ?? '' }}"
                                                data-city="{{ $address->city }}"
                                                data-region-id="{{ $address->region_id }}"
                                                data-postcode="{{ $address->zip_code }}"
                                                {{ $address->is_default ? 'selected' : '' }}>
                                            {{ $address->full_name }} - {{ $address->city }}, {{ $address->region->name ?? '' }} {{ $address->zip_code }}
                                            @if($address->is_default) (Default) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="checkout-form__grid checkout-form__grid--compact" id="shippingFormFields">
                                <div class="form-group">
                                    <label for="shippingFirstName" class="form-label">First Name <span class="required">*</span></label>
                                    <input type="text" id="shippingFirstName" name="shipping_first_name" class="form-input"
                                        value="{{ $sessionData['shipping']['first_name'] ?? $shippingAddress->first_name ?? $user->first_name ?? '' }}"
                                        placeholder="Enter your first name" required>
                                </div>
                                <div class="form-group">
                                    <label for="shippingLastName" class="form-label">Last Name <span class="required">*</span></label>
                                    <input type="text" id="shippingLastName" name="shipping_last_name" class="form-input"
                                        value="{{ $sessionData['shipping']['last_name'] ?? $shippingAddress->last_name ?? $user->last_name ?? '' }}"
                                        placeholder="Enter your last name" required>
                                </div>
                                <div class="form-group">
                                    <label for="shippingEmail" class="form-label">Email Address <span class="required">*</span></label>
                                    <input type="email" id="shippingEmail" name="shipping_email" class="form-input"
                                        value="{{ $sessionData['shipping']['email'] ?? $shippingAddress->email ?? $user->email ?? '' }}"
                                        placeholder="Enter your email" required>
                                </div>
                                <div class="form-group">
                                    <label for="shippingPhone" class="form-label">Phone Number <span class="required">*</span></label>
                                    <input type="tel" id="shippingPhone" name="shipping_phone" class="form-input"
                                        value="{{ $sessionData['shipping']['phone'] ?? $shippingAddress->phone ?? $user->userDetail->phone ?? '' }}"
                                        placeholder="Enter your phone number"
                                        pattern="[\d\+\s\-]+"
                                        inputmode="numeric"
                                        required>
                                </div>
                                <div class="form-group form-group--full">
                                    <label for="shippingAddress" class="form-label">Street Address <span class="required">*</span></label>
                                    <div class="address-autocomplete-wrapper">
                                        <input type="text" id="shippingAddress" name="shipping_street_address" class="form-input address-autocomplete"
                                            value="{{ $sessionData['shipping']['street_address'] ?? $shippingAddress->street_address ?? '' }}"
                                            placeholder="Start typing your address (e.g., 123 Queen Street)"
                                            autocomplete="off"
                                            required>
                                        <div id="shippingAddressSuggestions" class="address-suggestions" style="display: none;"></div>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Start typing to search for your address
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="shippingCity" class="form-label">City <span class="required">*</span></label>
                                    <input type="text" id="shippingCity" name="shipping_city" class="form-input"
                                        value="{{ $sessionData['shipping']['city'] ?? $shippingAddress->city ?? '' }}"
                                        placeholder="Enter your city" required>
                                </div>
                                <div class="form-group">
                                    <label for="shippingSuburb" class="form-label">Suburb (optional)</label>
                                    <input type="text" id="shippingSuburb" name="shipping_suburb" class="form-input"
                                        value="{{ $sessionData['shipping']['suburb'] ?? $shippingAddress->suburb ?? '' }}"
                                        placeholder="Enter your suburb">
                                </div>
                                <div class="form-group">
                                    <label for="shippingRegion" class="form-label">Region <span class="required">*</span></label>
                                    <select id="shippingRegion" name="shipping_region_id" class="form-input" required>
                                        <option value="">Select Region</option>
                                        @foreach($regions as $region)
                                            <option value="{{ $region->id }}"
                                                {{ ($sessionData['shipping']['region_id'] ?? ($shippingAddress && $shippingAddress->region_id == $region->id)) ? 'selected' : '' }}>
                                                {{ $region->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="shippingZip" class="form-label">Postcode <span class="required">*</span></label>
                                    <input type="text" id="shippingZip" name="shipping_zip_code" class="form-input"
                                        value="{{ $sessionData['shipping']['zip_code'] ?? $shippingAddress->zip_code ?? '' }}"
                                        placeholder="Enter your postcode"
                                        pattern="\d{4}"
                                        maxlength="4"
                                        inputmode="numeric"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="shippingCountry" class="form-label">Country <span class="required">*</span></label>
                                    <input type="text" id="shippingCountry" name="shipping_country" class="form-input"
                                        value="New Zealand" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Address Checkbox - Always Visible -->
                        <div class="checkout-block checkout-block--compact" style="margin-bottom: 1rem;">
                            <div class="checkout-block__header-compact">
                                <label class="checkbox-label checkbox-label--prominent" style="margin: 0; cursor: pointer;">
                                    <input type="checkbox" id="billingDifferent" name="billing_different" value="1" {{ ($sessionData['billing_different'] ?? false) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    <span class="checkbox-label__text">Billing address is different from shipping address</span>
                                </label>
                            </div>
                        </div>

                        <!-- Billing Address Section - Hidden by default -->
                        <div class="checkout-block checkout-block--compact" id="billingAddressSection" style="display: {{ ($sessionData['billing_different'] ?? false) ? 'block' : 'none' }};">
                            <div class="checkout-block__header-compact">
                                <h2 class="checkout-block__title">
                                    <i class="fas fa-file-invoice"></i>
                                    Billing Address
                                </h2>
                            </div>

                            @if($user && $billingAddresses->count() > 0)
                            <div class="form-group form-group--full" style="margin-bottom: 1rem;">
                                <label for="billingAddressSelect" class="form-label">Select Saved Address</label>
                                <select id="billingAddressSelect" class="form-input">
                                    <option value="">-- Use New Address --</option>
                                    @foreach($billingAddresses as $address)
                                        <option value="{{ $address->id }}" 
                                                data-first-name="{{ $address->first_name }}"
                                                data-last-name="{{ $address->last_name }}"
                                                data-email="{{ $address->email ?? $user->email }}"
                                                data-phone="{{ $address->phone }}"
                                                data-street="{{ $address->street_address }}"
                                                data-suburb="{{ $address->suburb ?? '' }}"
                                                data-city="{{ $address->city }}"
                                                data-region-id="{{ $address->region_id }}"
                                                data-postcode="{{ $address->zip_code }}"
                                                {{ $address->is_default ? 'selected' : '' }}>
                                            {{ $address->full_name }} - {{ $address->city }}, {{ $address->region->name ?? '' }} {{ $address->zip_code }}
                                            @if($address->is_default) (Default) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="checkout-form__grid checkout-form__grid--compact" id="billingFormFields">
                                <div class="form-group">
                                    <label for="billingFirstName" class="form-label">First Name <span class="required">*</span></label>
                                    <input type="text" id="billingFirstName" name="billing_first_name" class="form-input"
                                        value="{{ $sessionData['billing']['first_name'] ?? $billingAddress->first_name ?? $user->first_name ?? '' }}"
                                        placeholder="Enter your first name">
                                </div>
                                <div class="form-group">
                                    <label for="billingLastName" class="form-label">Last Name <span class="required">*</span></label>
                                    <input type="text" id="billingLastName" name="billing_last_name" class="form-input"
                                        value="{{ $sessionData['billing']['last_name'] ?? $billingAddress->last_name ?? $user->last_name ?? '' }}"
                                        placeholder="Enter your last name">
                                </div>
                                <div class="form-group">
                                    <label for="billingEmail" class="form-label">Email Address <span class="required">*</span></label>
                                    <input type="email" id="billingEmail" name="billing_email" class="form-input"
                                        value="{{ $sessionData['billing']['email'] ?? $billingAddress->email ?? $user->email ?? '' }}"
                                        placeholder="Enter your email">
                                </div>
                                <div class="form-group">
                                    <label for="billingPhone" class="form-label">Phone Number <span class="required">*</span></label>
                                    <input type="tel" id="billingPhone" name="billing_phone" class="form-input"
                                        value="{{ $sessionData['billing']['phone'] ?? $billingAddress->phone ?? $user->userDetail->phone ?? '' }}"
                                        placeholder="Enter your phone number"
                                        pattern="[\d\+\s\-]+"
                                        inputmode="numeric">
                                </div>
                                <div class="form-group form-group--full">
                                    <label for="billingAddress" class="form-label">Street Address <span class="required">*</span></label>
                                    <div class="address-autocomplete-wrapper">
                                        <input type="text" id="billingAddress" name="billing_street_address" class="form-input address-autocomplete"
                                            value="{{ $sessionData['billing']['street_address'] ?? $billingAddress->street_address ?? '' }}"
                                            placeholder="Start typing your address (e.g., 123 Queen Street)"
                                            autocomplete="off">
                                        <div id="billingAddressSuggestions" class="address-suggestions" style="display: none;"></div>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Start typing to search for your address
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label for="billingCity" class="form-label">City <span class="required">*</span></label>
                                    <input type="text" id="billingCity" name="billing_city" class="form-input"
                                        value="{{ $sessionData['billing']['city'] ?? $billingAddress->city ?? '' }}"
                                        placeholder="Enter your city">
                                </div>
                                <div class="form-group">
                                    <label for="billingSuburb" class="form-label">Suburb (optional)</label>
                                    <input type="text" id="billingSuburb" name="billing_suburb" class="form-input"
                                        value="{{ $sessionData['billing']['suburb'] ?? $billingAddress->suburb ?? '' }}"
                                        placeholder="Enter your suburb">
                                </div>
                                <div class="form-group">
                                    <label for="billingRegion" class="form-label">Region <span class="required">*</span></label>
                                    <select id="billingRegion" name="billing_region_id" class="form-input">
                                        <option value="">Select Region</option>
                                        @foreach($regions as $region)
                                            <option value="{{ $region->id }}"
                                                {{ ($sessionData['billing']['region_id'] ?? ($billingAddress && $billingAddress->region_id == $region->id)) ? 'selected' : '' }}>
                                                {{ $region->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="billingZip" class="form-label">Postcode <span class="required">*</span></label>
                                    <input type="text" id="billingZip" name="billing_zip_code" class="form-input"
                                        value="{{ $sessionData['billing']['zip_code'] ?? $billingAddress->zip_code ?? '' }}"
                                        placeholder="Enter your postcode"
                                        pattern="\d{4}"
                                        maxlength="4"
                                        inputmode="numeric">
                                </div>
                                <div class="form-group">
                                    <label for="billingCountry" class="form-label">Country <span class="required">*</span></label>
                                    <input type="text" id="billingCountry" name="billing_country" class="form-input"
                                        value="New Zealand" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="checkout-block checkout-block--compact checkout-block--notes">
                            <div class="checkout-block__header-compact">
                                <h2 class="checkout-block__title">
                                    <i class="fas fa-sticky-note"></i>
                                    Order Notes <span class="checkout-block__title-optional">(Optional)</span>
                                </h2>
                            </div>
                            <div class="form-group form-group--full">
                                <label for="orderNotes" class="form-label">Special instructions for your order</label>
                                <textarea id="orderNotes" name="notes" class="form-input" rows="3" placeholder="Any special instructions or notes for your order...">{{ $sessionData['notes'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        @include('frontend.checkout.partials.order-summary')
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script src="{{ asset('assets/frontend/js/checkout/form-handler.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/checkout/address-autocomplete.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/checkout/shipping-calculator.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/checkout/coupon-handler.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize form handler
            if (typeof CheckoutFormHandler !== 'undefined') {
                new CheckoutFormHandler();
            }
            
            // CouponHandler is initialized automatically by coupon-handler.js
            // No need for duplicate code here - it handles button state and click events
            
            if (window.Analytics && window.Analytics.isEnabled()) {
                window.Analytics.trackCheckoutStep('details', 1);
            }

            const checkoutForm = document.getElementById('checkoutForm');
            const reviewButton = document.querySelector('button[form="checkoutForm"]');
            
            // Handle review button click
            if (reviewButton) {
                reviewButton.addEventListener('click', function(e) {
                    console.log('[Checkout] Review button clicked');
                    console.log('[Checkout] Form:', checkoutForm);
                    console.log('[Checkout] Form validity:', checkoutForm ? checkoutForm.checkValidity() : 'N/A');
                    
                    // If form is valid, it will submit naturally via form attribute
                    // If form is invalid, validation will show errors
                    if (checkoutForm && !checkoutForm.checkValidity()) {
                        console.warn('[Checkout] Form validation failed');
                        checkoutForm.classList.add('was-validated');
                        // Don't prevent default - let browser show validation errors
                    } else {
                        console.log('[Checkout] Form is valid, submitting...');
                    }
                });
            }
            
            if (window.FormSubmissionHandler && checkoutForm) {
                const reviewButton = document.querySelector('button[form="checkoutForm"]');
                if (reviewButton) {
                    window.FormSubmissionHandler.init('checkoutForm', {
                        loadingText: 'Processing...',
                        timeout: 15000,
                        onSubmit: function(e, form) {
                            const reviewBtn = document.querySelector('button[form="checkoutForm"]');
                            if (reviewBtn) {
                                reviewBtn.disabled = true;
                                reviewBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                            }
                        }
                    });
                }
            }
            
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', function(e) {
                    console.log('[Checkout] Form submit event triggered');
                    
                    if (window.Analytics && window.Analytics.isEnabled()) {
                        const cartData = {
                            total: parseFloat(document.getElementById('checkoutTotal')?.textContent?.replace(/[^0-9.]/g, '') || 0),
                            items: []
                        };
                        document.querySelectorAll('.order-item').forEach(item => {
                            const name = item.querySelector('.order-item__name a')?.textContent || '';
                            const priceText = item.querySelector('.order-item__price')?.textContent || '';
                            const quantityText = item.querySelector('.order-item__price')?.textContent || '';
                            const price = parseFloat(priceText.match(/\$?([0-9.]+)/)?.[1] || 0);
                            const quantity = parseInt(quantityText.match(/x\s*(\d+)/)?.[1] || 1);
                            cartData.items.push({
                                item_name: name,
                                price: price,
                                quantity: quantity
                            });
                        });
                        window.Analytics.trackBeginCheckout(cartData);
                    }
                });
            }
        });
    </script>
@endsection
