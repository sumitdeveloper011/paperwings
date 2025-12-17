@extends('layouts.frontend.main')
@section('content')
    @include('include.frontend.breadcrumb')
    <section class="cart-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="cart-table-wrapper">
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cartTableBody">
                                @if($cartItems && $cartItems->count() > 0)
                                    @foreach($cartItems as $cartItem)
                                    <tr class="cart-item" data-cart-item-id="{{ $cartItem->id }}">
                                        <td class="cart-item__product">
                                            <div class="cart-item__image">
                                                <a href="{{ route('product.detail', $cartItem->product->slug) }}">
                                                    <img src="{{ $cartItem->product->main_image }}" alt="{{ $cartItem->product->name }}">
                                                </a>
                                            </div>
                                            <div class="cart-item__info">
                                                <h3 class="cart-item__name">
                                                    <a href="{{ route('product.detail', $cartItem->product->slug) }}" class="cart-item__name-link">{{ $cartItem->product->name }}</a>
                                                </h3>
                                                @if($cartItem->product->barcode)
                                                <span class="cart-item__sku">SKU: {{ $cartItem->product->barcode }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="cart-item__price">
                                            <span class="price-current">${{ number_format($cartItem->price, 2) }}</span>
                                            @if($cartItem->product->total_price > $cartItem->price)
                                            <span class="price-old">${{ number_format($cartItem->product->total_price, 2) }}</span>
                                            @endif
                                        </td>
                                        <td class="cart-item__quantity">
                                            <div class="quantity-selector">
                                                <button class="qty-btn qty-decrease" type="button" data-cart-item-id="{{ $cartItem->id }}">-</button>
                                                <input type="number" value="{{ $cartItem->quantity }}" min="1" max="99" class="qty-input" data-cart-item-id="{{ $cartItem->id }}" data-original-quantity="{{ $cartItem->quantity }}">
                                                <button class="qty-btn qty-increase" type="button" data-cart-item-id="{{ $cartItem->id }}">+</button>
                                            </div>
                                        </td>
                                        <td class="cart-item__subtotal">
                                            <span class="subtotal-price" data-cart-item-id="{{ $cartItem->id }}">${{ number_format($cartItem->subtotal, 2) }}</span>
                                        </td>
                                        <td class="cart-item__remove">
                                            <button class="remove-btn remove-cart-item" type="button" title="Remove item" data-cart-item-id="{{ $cartItem->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center" style="padding: 3rem;">
                                            <div style="text-align: center;">
                                                <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                                                <p style="font-size: 1.2rem; color: #666;">Your cart is empty</p>
                                                <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top: 1rem;">Continue Shopping</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="cart-actions">
                        <a href="{{ route('home') }}" class="continue-shopping-btn">
                            <i class="fas fa-arrow-left"></i>
                            Continue Shopping
                        </a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h3 class="cart-summary__title">Cart Summary</h3>

                        <div class="cart-summary__item">
                            <span class="summary-label">Subtotal</span>
                            <span class="summary-value" id="cartSubtotal">${{ number_format($subtotal, 2) }}</span>
                        </div>

                        {{-- <div class="cart-summary__item">
                            <span class="summary-label">Shipping</span>
                            <span class="summary-value" id="cartShipping">${{ number_format($shipping, 2) }}</span>
                        </div> --}}

                        <div class="cart-summary__item cart-summary__item--total">
                            <span class="summary-label">Total</span>
                            <span class="summary-value" id="cartTotal">${{ number_format($total, 2) }}</span>
                        </div>

                        <div class="cart-summary__coupon">
                            <h4 class="coupon-title">Have a coupon code?</h4>
                            <div class="coupon-form">
                                <input type="text" class="coupon-input" placeholder="Enter coupon code">
                                <button class="coupon-btn" type="button">Apply</button>
                            </div>
                        </div>

                        <div class="cart-summary__checkout">
                            <a href="{{ route('checkout.index') }}" class="checkout-btn">
                                Proceed to Checkout
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            // Quantity controls
            document.querySelectorAll('.qty-decrease, .qty-increase').forEach(btn => {
                btn.addEventListener('click', function() {
                    const cartItemId = this.getAttribute('data-cart-item-id');
                    const input = document.querySelector(`.qty-input[data-cart-item-id="${cartItemId}"]`);
                    const quantitySelector = this.closest('.quantity-selector');
                    if (!input || !quantitySelector) return;

                    let currentValue = parseInt(input.value) || 1;
                    let newValue = currentValue;

                    if (this.classList.contains('qty-decrease')) {
                        if (currentValue > 1) {
                            newValue = currentValue - 1;
                        } else {
                            return; // Can't go below 1
                        }
                    } else {
                        if (currentValue < 99) {
                            newValue = currentValue + 1;
                        } else {
                            return; // Can't go above 99
                        }
                    }

                    // Show loader and disable controls
                    showQuantityLoader(quantitySelector, true);
                    input.value = newValue;
                    updateCartItem(cartItemId, newValue, quantitySelector);
                });
            });

            // Quantity input change
            document.querySelectorAll('.qty-input').forEach(input => {
                input.addEventListener('change', function() {
                    const cartItemId = this.getAttribute('data-cart-item-id');
                    const quantitySelector = this.closest('.quantity-selector');
                    const quantity = parseInt(this.value) || 1;

                    if (quantity < 1) {
                        this.value = 1;
                    } else if (quantity > 99) {
                        this.value = 99;
                    }

                    if (quantitySelector) {
                        showQuantityLoader(quantitySelector, true);
                    }
                    updateCartItem(cartItemId, this.value, quantitySelector);
                });
            });

            // Remove item
            document.querySelectorAll('.remove-cart-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    const cartItemId = this.getAttribute('data-cart-item-id');
                    if (confirm('Are you sure you want to remove this item from your cart?')) {
                        removeCartItem(cartItemId);
                    }
                });
            });

            // Show/hide quantity loader
            function showQuantityLoader(quantitySelector, show) {
                if (!quantitySelector) return;

                const buttons = quantitySelector.querySelectorAll('.qty-btn');
                const input = quantitySelector.querySelector('.qty-input');

                if (show) {
                    quantitySelector.classList.add('loading');
                    buttons.forEach(btn => {
                        btn.disabled = true;
                        btn.style.opacity = '0.5';
                        btn.style.cursor = 'not-allowed';
                    });
                    if (input) {
                        input.disabled = true;
                        input.style.opacity = '0.5';
                    }
                } else {
                    quantitySelector.classList.remove('loading');
                    buttons.forEach(btn => {
                        btn.disabled = false;
                        btn.style.opacity = '1';
                        btn.style.cursor = 'pointer';
                    });
                    if (input) {
                        input.disabled = false;
                        input.style.opacity = '1';
                    }
                }
            }

            // Update cart item quantity
            function updateCartItem(cartItemId, quantity, quantitySelector) {
                fetch('/cart/update', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        cart_item_id: cartItemId,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update subtotal for this item
                        const subtotalElement = document.querySelector(`.subtotal-price[data-cart-item-id="${cartItemId}"]`);
                        if (subtotalElement && data.subtotal !== undefined) {
                            subtotalElement.textContent = '$' + parseFloat(data.subtotal).toFixed(2);
                        }

                        // Update cart summary
                        updateCartSummary();

                        // Update cart count in header if function exists
                        if (window.CartFunctions && data.cart_count !== undefined) {
                            window.CartFunctions.updateCartCount(data.cart_count);
                        }
                    }
                    // Hide loader
                    if (quantitySelector) {
                        showQuantityLoader(quantitySelector, false);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Hide loader on error
                    if (quantitySelector) {
                        showQuantityLoader(quantitySelector, false);
                    }
                });
            }

            // Remove cart item
            function removeCartItem(cartItemId) {
                fetch('/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        cart_item_id: cartItemId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove row from table
                        const row = document.querySelector(`tr[data-cart-item-id="${cartItemId}"]`);
                        if (row) {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();

                                // Check if cart is empty
                                const cartItems = document.querySelectorAll('.cart-item[data-cart-item-id]');
                                if (cartItems.length === 0) {
                                    location.reload();
                                } else {
                                    updateCartSummary();
                                }

                                // Update cart count in header if function exists
                                if (window.CartFunctions && data.cart_count !== undefined) {
                                    window.CartFunctions.updateCartCount(data.cart_count);
                                }
                            }, 300);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }

            // Update cart summary
            function updateCartSummary() {
                let subtotal = 0;
                document.querySelectorAll('.subtotal-price[data-cart-item-id]').forEach(element => {
                    const value = parseFloat(element.textContent.replace('$', ''));
                    if (!isNaN(value)) {
                        subtotal += value;
                    }
                });

                // const shipping = 5.00; // Shipping commented out for now
                const shipping = 0.00;
                const total = subtotal + shipping;

                const subtotalElement = document.getElementById('cartSubtotal');
                // const shippingElement = document.getElementById('cartShipping'); // Shipping commented out
                const totalElement = document.getElementById('cartTotal');

                if (subtotalElement) subtotalElement.textContent = '$' + subtotal.toFixed(2);
                // if (shippingElement) shippingElement.textContent = '$' + shipping.toFixed(2); // Shipping commented out
                if (totalElement) totalElement.textContent = '$' + total.toFixed(2);
            }
        });
    </script>
@endsection
