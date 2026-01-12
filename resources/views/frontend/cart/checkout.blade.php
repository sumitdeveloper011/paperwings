@extends('layouts.frontend.main')
@section('content')
    @include('frontend.partials.page-header', [
        'title' => 'Checkout',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Cart', 'url' => route('cart.index')],
            ['label' => 'Checkout', 'url' => null]
        ]
    ])
    <section class="checkout-section">
        <div class="container">
            <form class="checkout-form" id="checkoutForm">
                <div class="row">
                    <!-- Left Column - Forms -->
                    <div class="col-lg-8">
                        <!-- Billing Details -->
                        <div class="checkout-block">
                            <h2 class="checkout-block__title">Billing Details</h2>
                            <div class="checkout-form__grid">
                                <div class="form-group">
                                    <label for="billingFirstName" class="form-label">First Name <span class="required">*</span></label>
                                    <input type="text" id="billingFirstName" class="form-input" placeholder="Enter your first name" required>
                                </div>
                                <div class="form-group">
                                    <label for="billingLastName" class="form-label">Last Name <span class="required">*</span></label>
                                    <input type="text" id="billingLastName" class="form-input" placeholder="Enter your last name" required>
                                </div>
                                <div class="form-group">
                                    <label for="billingEmail" class="form-label">Email Address <span class="required">*</span></label>
                                    <input type="email" id="billingEmail" class="form-input" placeholder="Enter your email" required>
                                </div>
                                <div class="form-group">
                                    <label for="billingPhone" class="form-label">Phone Number <span class="required">*</span></label>
                                    <input type="tel" id="billingPhone" class="form-input" placeholder="Enter your phone number" required>
                                </div>
                                <div class="form-group form-group--full">
                                    <label for="billingAddress" class="form-label">Street Address <span class="required">*</span></label>
                                    <input type="text" id="billingAddress" class="form-input" placeholder="Enter your street address" required>
                                </div>
                                <div class="form-group form-group--full">
                                    <label for="billingAddress2" class="form-label">Apartment, suite, etc. (optional)</label>
                                    <input type="text" id="billingAddress2" class="form-input" placeholder="Apartment, suite, unit, building, floor, etc.">
                                </div>
                                <div class="form-group">
                                    <label for="billingCity" class="form-label">City <span class="required">*</span></label>
                                    <input type="text" id="billingCity" class="form-input" placeholder="Enter your city" required>
                                </div>
                                <div class="form-group">
                                    <label for="billingState" class="form-label">State / Province <span class="required">*</span></label>
                                    <input type="text" id="billingState" class="form-input" placeholder="Enter your state" required>
                                </div>
                                <div class="form-group">
                                    <label for="billingZip" class="form-label">ZIP / Postal Code <span class="required">*</span></label>
                                    <input type="text" id="billingZip" class="form-input" placeholder="Enter your ZIP code" required>
                                </div>
                                <div class="form-group">
                                    <label for="billingCountry" class="form-label">Country <span class="required">*</span></label>
                                    <select id="billingCountry" class="form-input" required>
                                        <option value="">Select Country</option>
                                        <option value="US">United States</option>
                                        <option value="UK">United Kingdom</option>
                                        <option value="CA">Canada</option>
                                        <option value="AU">Australia</option>
                                        <option value="DE">Germany</option>
                                        <option value="FR">France</option>
                                        <option value="IT">Italy</option>
                                        <option value="ES">Spain</option>
                                    </select>
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
                                        <input type="text" id="shippingFirstName" class="form-input" placeholder="Enter your first name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingLastName" class="form-label">Last Name <span class="required">*</span></label>
                                        <input type="text" id="shippingLastName" class="form-input" placeholder="Enter your last name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingEmail" class="form-label">Email Address <span class="required">*</span></label>
                                        <input type="email" id="shippingEmail" class="form-input" placeholder="Enter your email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingPhone" class="form-label">Phone Number <span class="required">*</span></label>
                                        <input type="tel" id="shippingPhone" class="form-input" placeholder="Enter your phone number" required>
                                    </div>
                                    <div class="form-group form-group--full">
                                        <label for="shippingAddress" class="form-label">Street Address <span class="required">*</span></label>
                                        <input type="text" id="shippingAddress" class="form-input" placeholder="Enter your street address" required>
                                    </div>
                                    <div class="form-group form-group--full">
                                        <label for="shippingAddress2" class="form-label">Apartment, suite, etc. (optional)</label>
                                        <input type="text" id="shippingAddress2" class="form-input" placeholder="Apartment, suite, unit, building, floor, etc.">
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingCity" class="form-label">City <span class="required">*</span></label>
                                        <input type="text" id="shippingCity" class="form-input" placeholder="Enter your city" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingState" class="form-label">State / Province <span class="required">*</span></label>
                                        <input type="text" id="shippingState" class="form-input" placeholder="Enter your state" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingZip" class="form-label">ZIP / Postal Code <span class="required">*</span></label>
                                        <input type="text" id="shippingZip" class="form-input" placeholder="Enter your ZIP code" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="shippingCountry" class="form-label">Country <span class="required">*</span></label>
                                        <select id="shippingCountry" class="form-input" required>
                                            <option value="">Select Country</option>
                                            <option value="US">United States</option>
                                            <option value="UK">United Kingdom</option>
                                            <option value="CA">Canada</option>
                                            <option value="AU">Australia</option>
                                            <option value="DE">Germany</option>
                                            <option value="FR">France</option>
                                            <option value="IT">Italy</option>
                                            <option value="ES">Spain</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Order Summary -->
                    <div class="col-lg-4">
                        <div class="order-summary">
                            <h2 class="order-summary__title">Order Summary</h2>
                            
                            <!-- Order Items -->
                            <div class="order-items">
                                <div class="order-item">
                                    <div class="order-item__image">
                                        <img src="assets/images/product-1.jpg" alt="Premium Notebook">
                                    </div>
                                    <div class="order-item__info">
                                        <h3 class="order-item__name">Premium Notebook</h3>
                                    </div>
                                    <div class="order-item__price-row">
                                        <span class="order-item__price">$24.99 x 2</span>
                                        <span class="order-item__price-total">= $49.98</span>
                                    </div>
                                </div>
                                
                                <div class="order-item">
                                    <div class="order-item__image">
                                        <img src="assets/images/product-2.jpg" alt="Professional Pen Set">
                                    </div>
                                    <div class="order-item__info">
                                        <h3 class="order-item__name">Professional Pen Set</h3>
                                    </div>
                                    <div class="order-item__price-row">
                                        <span class="order-item__price">$19.99 x 1</span>
                                        <span class="order-item__price-total">= $19.99</span>
                                    </div>
                                </div>
                                
                                <div class="order-item">
                                    <div class="order-item__image">
                                        <img src="assets/images/product-3.jpg" alt="Art Supply Kit">
                                    </div>
                                    <div class="order-item__info">
                                        <h3 class="order-item__name">Art Supply Kit</h3>
                                    </div>
                                    <div class="order-item__price-row">
                                        <span class="order-item__price">$45.99 x 1</span>
                                        <span class="order-item__price-total">= $45.99</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Totals -->
                            <div class="order-totals">
                                <div class="order-totals__item">
                                    <span class="order-totals__label">Subtotal</span>
                                    <span class="order-totals__value">$115.96</span>
                                </div>
                                <div class="order-totals__item">
                                    <span class="order-totals__label">Shipping</span>
                                    <span class="order-totals__value">$5.00</span>
                                </div>
                                <div class="order-totals__item">
                                    <span class="order-totals__label">Tax</span>
                                    <span class="order-totals__value">$10.40</span>
                                </div>
                                <div class="order-totals__item order-totals__item--total">
                                    <span class="order-totals__label">Total</span>
                                    <span class="order-totals__value">$131.36</span>
                                </div>
                            </div>

                            <!-- Coupon Code -->
                            <div class="order-coupon">
                                <label for="orderCoupon" class="form-label">Have a coupon code?</label>
                                <div class="coupon-form">
                                    <input type="text" id="orderCoupon" class="coupon-input" placeholder="Enter coupon code">
                                    <button type="button" class="coupon-btn">Apply</button>
                                </div>
                            </div>

                            <!-- Place Order Button -->
                            <button type="submit" class="place-order-btn">
                                Place Order
                                <i class="fas fa-arrow-right"></i>
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
@endsection