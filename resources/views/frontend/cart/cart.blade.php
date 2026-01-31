@extends('layouts.frontend.main')
@section('content')
    @include('frontend.partials.page-header', [
        'title' => 'Shopping Cart',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Cart', 'url' => null]
        ]
    ])
    <section class="cart-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <!-- Mobile Header (Hidden on Desktop) -->
                    <div class="cart-mobile-header d-md-none">
                        <h2 class="cart-mobile-header__title">Shopping Cart</h2>
                        <div class="cart-mobile-header__count">
                            <span id="cartMobileCount">{{ $cartItems ? $cartItems->count() : 0 }}</span> item(s)
                        </div>
                    </div>
                    <div class="cart-table-wrapper">
                        <!-- Desktop: Scrollable Table Container -->
                        <div class="cart-table-container d-none d-md-block">
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
                                        <td class="cart-item__product" data-label="">
                                            <div class="cart-item__image">
                                                <a href="{{ route('product.detail', $cartItem->product->slug) }}">
                                                    <div class="image-wrapper skeleton-image-wrapper">
                                                        <div class="skeleton-small-image">
                                                            <div class="skeleton-shimmer"></div>
                                                        </div>
                                                        <img src="{{ $cartItem->product->main_thumbnail_url }}" alt="{{ $cartItem->product->name }}" width="100" height="100" loading="lazy">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="cart-item__info">
                                                <h3 class="cart-item__name">
                                                    <a href="{{ route('product.detail', $cartItem->product->slug) }}" class="cart-item__name-link">{{ $cartItem->product->name }}</a>
                                                </h3>
                                                @if($cartItem->product->sku || $cartItem->product->barcode)
                                                <span class="cart-item__sku">SKU: {{ $cartItem->product->sku ?? $cartItem->product->barcode }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="cart-item__price" data-label="Price">
                                            <span class="price-current">${{ number_format($cartItem->price, 2) }}</span>
                                            @if($cartItem->product->total_price > $cartItem->price)
                                            <span class="price-old">${{ number_format($cartItem->product->total_price, 2) }}</span>
                                            @endif
                                        </td>
                                        <td class="cart-item__quantity" data-label="Quantity">
                                            <div class="quantity-selector">
                                                <button class="qty-btn qty-decrease" type="button" data-cart-item-id="{{ $cartItem->id }}">-</button>
                                                <input type="number" value="{{ $cartItem->quantity }}" min="1" max="99" class="qty-input" data-cart-item-id="{{ $cartItem->id }}" data-original-quantity="{{ $cartItem->quantity }}">
                                                <button class="qty-btn qty-increase" type="button" data-cart-item-id="{{ $cartItem->id }}">+</button>
                                            </div>
                                        </td>
                                        <td class="cart-item__subtotal" data-label="Subtotal">
                                            <span class="subtotal-price" data-cart-item-id="{{ $cartItem->id }}">${{ number_format($cartItem->subtotal, 2) }}</span>
                                        </td>
                                        <td class="cart-item__remove" data-label="">
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
                        <!-- Mobile: Scrollable Cards Container -->
                        <div class="cart-items-container d-md-none">
                            <table class="cart-table">
                                <tbody id="cartTableBodyMobile">
                                    @if($cartItems && $cartItems->count() > 0)
                                        @foreach($cartItems as $cartItem)
                                        <tr class="cart-item" data-cart-item-id="{{ $cartItem->id }}">
                                            <td class="cart-item__product" data-label="">
                                                <div class="cart-item__image">
                                                    <a href="{{ route('product.detail', $cartItem->product->slug) }}">
                                                        <div class="image-wrapper skeleton-image-wrapper">
                                                            <div class="skeleton-small-image">
                                                                <div class="skeleton-shimmer"></div>
                                                            </div>
                                                            <img src="{{ $cartItem->product->main_thumbnail_url }}" alt="{{ $cartItem->product->name }}" width="100" height="100" loading="lazy">
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="cart-item__info">
                                                    <h3 class="cart-item__name">
                                                        <a href="{{ route('product.detail', $cartItem->product->slug) }}" class="cart-item__name-link">{{ $cartItem->product->name }}</a>
                                                    </h3>
                                                    @if($cartItem->product->sku || $cartItem->product->barcode)
                                                    <span class="cart-item__sku">SKU: {{ $cartItem->product->sku ?? $cartItem->product->barcode }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="cart-item__pricing-group" data-label="">
                                                <div class="cart-item__pricing-group-inner">
                                                    <div class="cart-item__price">
                                                        <span class="price-current">${{ number_format($cartItem->price, 2) }}</span>
                                                        @if($cartItem->product->total_price > $cartItem->price)
                                                        <span class="price-old">${{ number_format($cartItem->product->total_price, 2) }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="cart-item__quantity">
                                                        <span class="cart-item__quantity-label">Qty:</span>
                                                        <div class="quantity-selector">
                                                            <button class="qty-btn qty-decrease" type="button" data-cart-item-id="{{ $cartItem->id }}">-</button>
                                                            <input type="number" value="{{ $cartItem->quantity }}" min="1" max="99" class="qty-input" data-cart-item-id="{{ $cartItem->id }}" data-original-quantity="{{ $cartItem->quantity }}">
                                                            <button class="qty-btn qty-increase" type="button" data-cart-item-id="{{ $cartItem->id }}">+</button>
                                                        </div>
                                                    </div>
                                                    <div class="cart-item__subtotal">
                                                        <span class="cart-item__subtotal-label">Total:</span>
                                                        <span class="subtotal-price" data-cart-item-id="{{ $cartItem->id }}">${{ number_format($cartItem->subtotal, 2) }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="cart-item__remove" data-label="">
                                                <button class="remove-btn remove-cart-item" type="button" title="Remove item" data-cart-item-id="{{ $cartItem->id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Desktop Totals -->
                        <div class="cart-totals-wrapper d-none d-md-block">
                            <table class="cart-table">
                                @if($cartItems && $cartItems->count() > 0)
                                <tfoot>
                                    <tr class="cart-totals-row">
                                        <td colspan="2"></td>
                                        <td class="cart-totals-quantity">
                                            <strong>Total Quantity: <span id="cartTotalQuantity">{{ $cartItems->sum('quantity') }}</span></strong>
                                        </td>
                                        <td class="cart-totals-total">
                                            <strong>Total: <span id="cartTotal" class="cart-total-price">${{ number_format($total, 2) }}</span></strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>

                        <!-- Mobile Totals (Sticky) -->
                        @if($cartItems && $cartItems->count() > 0)
                        <div class="cart-totals-mobile d-md-none">
                            <div class="cart-totals-mobile__row">
                                <span>Total Quantity:</span>
                                <strong id="cartTotalQuantityMobile">{{ $cartItems->sum('quantity') }}</strong>
                            </div>
                            <div class="cart-totals-mobile__row">
                                <span>Total:</span>
                                <strong class="cart-total-price" id="cartTotalMobile">${{ number_format($total, 2) }}</strong>
                            </div>
                        </div>
                        @endif

                        <!-- Desktop Actions -->
                        <div class="cart-actions d-none d-md-flex">
                            <a href="{{ route('home') }}" class="continue-shopping-btn">
                                <i class="fas fa-arrow-left"></i>
                                Continue Shopping
                            </a>
                            @if($cartItems && $cartItems->count() > 0)
                            <a href="{{ route('checkout.index') }}" class="checkout-btn">
                                Proceed to Checkout
                                <i class="fas fa-arrow-right"></i>
                            </a>
                            @endif
                        </div>

                        <!-- Mobile Actions (Sticky) -->
                        <div class="cart-actions-mobile d-md-none">
                            <a href="{{ route('home') }}" class="continue-shopping-btn">
                                <i class="fas fa-arrow-left"></i>
                                Continue Shopping
                            </a>
                            @if($cartItems && $cartItems->count() > 0)
                            <a href="{{ route('checkout.index') }}" class="checkout-btn">
                                Proceed to Checkout
                                <i class="fas fa-arrow-right"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            // Sync mobile table with desktop table on load
            function syncMobileTable() {
                const desktopBody = document.getElementById('cartTableBody');
                const mobileBody = document.getElementById('cartTableBodyMobile');
                if (desktopBody && mobileBody) {
                    // Mobile table is already rendered, just ensure they stay in sync
                    // Updates will be handled by the same event listeners
                }
            }

            // Quantity controls (works for both desktop and mobile)
            document.querySelectorAll('.qty-decrease, .qty-increase').forEach(btn => {
                btn.addEventListener('click', function() {
                    const cartItemId = this.getAttribute('data-cart-item-id');
                    const inputs = document.querySelectorAll(`.qty-input[data-cart-item-id="${cartItemId}"]`);
                    const quantitySelector = this.closest('.quantity-selector');
                    if (!inputs.length || !quantitySelector) return;

                    // Get value from first input (they should all have same value)
                    let currentValue = parseInt(inputs[0].value) || 1;
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

                    // Show loader and disable controls (all selectors for this item)
                    const allSelectors = document.querySelectorAll(`.quantity-selector`);
                    allSelectors.forEach(selector => {
                        const selectorInput = selector.querySelector(`.qty-input[data-cart-item-id="${cartItemId}"]`);
                        if (selectorInput) {
                            showQuantityLoader(selector, true);
                        }
                    });

                    // Update all inputs (desktop and mobile)
                    inputs.forEach(input => {
                        input.value = newValue;
                    });

                    updateCartItem(cartItemId, newValue, quantitySelector);
                });
            });

            // Initialize sync
            syncMobileTable();

            // Quantity input change (works for both desktop and mobile)
            document.querySelectorAll('.qty-input').forEach(input => {
                input.addEventListener('change', function() {
                    const cartItemId = this.getAttribute('data-cart-item-id');
                    const quantity = parseInt(this.value) || 1;
                    let finalQuantity = quantity;

                    if (quantity < 1) {
                        finalQuantity = 1;
                    } else if (quantity > 99) {
                        finalQuantity = 99;
                    }

                    // Update all inputs for this item (desktop and mobile)
                    const allInputs = document.querySelectorAll(`.qty-input[data-cart-item-id="${cartItemId}"]`);
                    allInputs.forEach(inp => {
                        inp.value = finalQuantity;
                    });

                    // Show loader for all quantity selectors for this item
                    const allSelectors = document.querySelectorAll('.quantity-selector');
                    allSelectors.forEach(selector => {
                        const selectorInput = selector.querySelector(`.qty-input[data-cart-item-id="${cartItemId}"]`);
                        if (selectorInput) {
                            showQuantityLoader(selector, true);
                        }
                    });

                    updateCartItem(cartItemId, finalQuantity, this.closest('.quantity-selector'));
                });
            });

            // Remove item
            document.querySelectorAll('.remove-cart-item').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const cartItemId = this.getAttribute('data-cart-item-id');
                    if (window.customConfirm) {
                        const confirmed = await window.customConfirm(
                            'Are you sure you want to remove this item from your cart?',
                            'Remove Item',
                            'question'
                        );
                        if (confirmed) {
                            removeCartItem(cartItemId);
                        }
                    } else {
                        if (confirm('Are you sure you want to remove this item from your cart?')) {
                            removeCartItem(cartItemId);
                        }
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
                        const responseData = data.data || {};
                        
                        // Update subtotal for this item (both desktop and mobile)
                        const subtotalElements = document.querySelectorAll(`.subtotal-price[data-cart-item-id="${cartItemId}"]`);
                        if (subtotalElements.length > 0 && responseData.subtotal !== undefined) {
                            subtotalElements.forEach(element => {
                                element.textContent = '$' + parseFloat(responseData.subtotal).toFixed(2);
                            });
                        }

                        // Update cart summary with backend total if available
                        updateCartSummary(responseData.cart_total);

                        // Update cart count in header if function exists
                        if (window.Cart && responseData.cart_count !== undefined) {
                            window.Cart.updateCount(responseData.cart_count);
                        } else if (window.CartFunctions && responseData.cart_count !== undefined) {
                            window.CartFunctions.updateCartCount(responseData.cart_count);
                        }
                    }
                    // Hide loader
                    if (quantitySelector) {
                        showQuantityLoader(quantitySelector, false);
                    }
                })
                .catch(error => {
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
                        const responseData = data.data || {};
                        
                        // Remove row from both desktop and mobile tables
                        const rows = document.querySelectorAll(`tr[data-cart-item-id="${cartItemId}"]`);
                        rows.forEach(row => {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                        });
                        setTimeout(() => {
                            rows.forEach(row => row.remove());

                            // Check if cart is empty
                            const cartItems = document.querySelectorAll('.cart-item[data-cart-item-id]');
                            if (cartItems.length === 0) {
                                location.reload();
                            } else {
                                updateCartSummary(responseData.cart_total);
                            }

                            // Update cart count in header if function exists
                            if (window.Cart && responseData.cart_count !== undefined) {
                                window.Cart.updateCount(responseData.cart_count);
                            } else if (window.CartFunctions && responseData.cart_count !== undefined) {
                                window.CartFunctions.updateCartCount(responseData.cart_count);
                            }
                        }, 300);
                    }
                })
                .catch(error => {
                    // Error handled silently
                });
            }

            // Update cart totals (total quantity and total price)
            function updateCartSummary(cartTotal = null) {
                let subtotal = 0;
                let totalQuantity = 0;
                const processedItems = new Set();
                
                // Calculate subtotal (only count each item once - use desktop table)
                document.querySelectorAll('#cartTableBody .subtotal-price[data-cart-item-id]').forEach(element => {
                    const cartItemId = element.getAttribute('data-cart-item-id');
                    if (!processedItems.has(cartItemId)) {
                        const value = parseFloat(element.textContent.replace('$', '').replace(',', ''));
                        if (!isNaN(value)) {
                            subtotal += value;
                            processedItems.add(cartItemId);
                        }
                    }
                });

                // If no desktop table, use mobile table
                if (subtotal === 0) {
                    document.querySelectorAll('#cartTableBodyMobile .subtotal-price[data-cart-item-id]').forEach(element => {
                        const cartItemId = element.getAttribute('data-cart-item-id');
                        if (!processedItems.has(cartItemId)) {
                            const value = parseFloat(element.textContent.replace('$', '').replace(',', ''));
                            if (!isNaN(value)) {
                                subtotal += value;
                                processedItems.add(cartItemId);
                            }
                        }
                    });
                }

                // Calculate total quantity (only count each item once - use desktop table)
                processedItems.clear();
                document.querySelectorAll('#cartTableBody .qty-input[data-cart-item-id]').forEach(input => {
                    const cartItemId = input.getAttribute('data-cart-item-id');
                    if (!processedItems.has(cartItemId)) {
                        const quantity = parseInt(input.value) || 0;
                        totalQuantity += quantity;
                        processedItems.add(cartItemId);
                    }
                });

                // If no desktop table, use mobile table
                if (totalQuantity === 0) {
                    processedItems.clear();
                    document.querySelectorAll('#cartTableBodyMobile .qty-input[data-cart-item-id]').forEach(input => {
                        const cartItemId = input.getAttribute('data-cart-item-id');
                        if (!processedItems.has(cartItemId)) {
                            const quantity = parseInt(input.value) || 0;
                            totalQuantity += quantity;
                            processedItems.add(cartItemId);
                        }
                    });
                }

                const shipping = 0.00;
                const total = cartTotal !== null ? cartTotal : (subtotal + shipping);

                // Update desktop totals
                const totalQuantityElement = document.getElementById('cartTotalQuantity');
                const totalElement = document.getElementById('cartTotal');

                if (totalQuantityElement) totalQuantityElement.textContent = totalQuantity;
                if (totalElement) {
                    const parent = totalElement.parentElement;
                    if (parent) {
                        parent.innerHTML = 'Total: <span id="cartTotal" class="cart-total-price">$' + total.toFixed(2) + '</span>';
                    }
                }

                // Update mobile totals
                const totalQuantityMobile = document.getElementById('cartTotalQuantityMobile');
                const totalMobile = document.getElementById('cartTotalMobile');

                if (totalQuantityMobile) totalQuantityMobile.textContent = totalQuantity;
                if (totalMobile) {
                    totalMobile.textContent = '$' + total.toFixed(2);
                }

                // Update mobile count (count unique items)
                processedItems.clear();
                document.querySelectorAll('.cart-item[data-cart-item-id]').forEach(item => {
                    const cartItemId = item.getAttribute('data-cart-item-id');
                    if (cartItemId && !processedItems.has(cartItemId)) {
                        processedItems.add(cartItemId);
                    }
                });
                const mobileCount = document.getElementById('cartMobileCount');
                if (mobileCount) {
                    mobileCount.textContent = processedItems.size;
                }
            }
        });
    </script>
@endsection
