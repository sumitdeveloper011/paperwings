/**
 * Wishlist Functionality
 * Handles add to wishlist, remove from wishlist, and wishlist sidebar
 */

(function() {
    'use strict';

    // CSRF Token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    /**
     * Add product to wishlist
     */
    function addToWishlist(productId, button) {
        // Show loading state
        if (button) {
            const icon = button.querySelector('i');
            if (icon) {
                icon.classList.remove('fas', 'fa-heart');
                icon.classList.add('fas', 'fa-spinner', 'fa-spin');
                button.disabled = true;
            }
        }

        fetch('/wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => {
            // Handle 401 Unauthorized responses
            if (response.status === 401) {
                const currentUrl = window.location.href;
                window.location.href = '/login?intended=' + encodeURIComponent(currentUrl);
                return Promise.reject('Not authenticated');
            }

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // If not JSON (likely a redirect), redirect to login
                const currentUrl = window.location.href;
                window.location.href = '/login?intended=' + encodeURIComponent(currentUrl);
                return Promise.reject('Not authenticated');
            }
        })
        .then(data => {
            if (data.requires_login || (data.success === false && data.message && data.message.toLowerCase().includes('login'))) {
                // User not logged in, redirect to login with intended URL
                const currentUrl = window.location.href;
                const redirectUrl = data.redirect_url || '/login';
                window.location.href = redirectUrl + (redirectUrl.includes('?') ? '&' : '?') + 'intended=' + encodeURIComponent(currentUrl);
                return;
            }

            if (data.success) {
                // Update button state
                if (button) {
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-spinner', 'fa-spin');
                        icon.classList.add('fa-heart');
                        button.classList.add('in-wishlist');
                        button.disabled = false;
                    }
                }

                // Update wishlist count in header and sidebar
                if (data.wishlist_count !== undefined) {
                    updateWishlistCount(data.wishlist_count);
                }

                // Show success message
                showNotification('Product added to wishlist!', 'success');

                // Load and show wishlist sidebar
                loadWishlistSidebar();
                toggleWishlistSidebar();
            } else {
                // Show error message
                showNotification(data.message || 'Failed to add product to wishlist.', 'error');

                // Reset button state
                if (button) {
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-spinner', 'fa-spin');
                        icon.classList.add('fa-heart');
                        button.disabled = false;
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);

            // If error is "Not authenticated", redirect already happened
            if (error === 'Not authenticated') {
                return;
            }

            // For other errors, try to redirect to login as fallback
            const currentUrl = window.location.href;
            window.location.href = '/login?intended=' + encodeURIComponent(currentUrl);
        });
    }

    /**
     * Remove product from wishlist
     */
    function removeFromWishlist(productId, button) {
        // Show loading state
        if (button) {
            const icon = button.querySelector('i');
            if (icon) {
                icon.classList.add('fa-spinner', 'fa-spin');
                button.disabled = true;
            }
        }

        fetch('/wishlist/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update button state
                if (button) {
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-spinner', 'fa-spin');
                        icon.classList.add('fa-heart');
                        button.classList.remove('in-wishlist');
                        button.disabled = false;
                    }
                }

                // Update wishlist count in header and sidebar
                if (data.wishlist_count !== undefined) {
                    updateWishlistCount(data.wishlist_count);
                }

                // Show success message
                showNotification('Product removed from wishlist!', 'success');

                // Reload wishlist sidebar
                loadWishlistSidebar();
            } else {
                showNotification(data.message || 'Failed to remove product from wishlist.', 'error');

                // Reset button state
                if (button) {
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-spinner', 'fa-spin');
                        icon.classList.add('fa-heart');
                        button.disabled = false;
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');

            // Reset button state
            if (button) {
                const icon = button.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-spinner', 'fa-spin');
                    icon.classList.add('fa-heart');
                    button.disabled = false;
                }
            }
        });
    }

    /**
     * Toggle wishlist (add or remove)
     */
    function toggleWishlist(productId, button) {
        if (button && button.classList.contains('in-wishlist')) {
            removeFromWishlist(productId, button);
        } else {
            addToWishlist(productId, button);
        }
    }

    /**
     * Load wishlist sidebar content
     */
    function loadWishlistSidebar() {
        const sidebarItems = document.getElementById('wishlist-sidebar-items');
        const sidebarEmpty = document.getElementById('wishlist-sidebar-empty');

        // Show loading state
        if (sidebarItems) {
            sidebarItems.innerHTML = '<div class="wishlist-sidebar__loading" style="text-align: center; padding: 2rem;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #6c757d;"></i><p style="margin-top: 1rem; color: #6c757d;">Loading wishlist...</p></div>';
            sidebarItems.style.display = 'block';
        }
        if (sidebarEmpty) {
            sidebarEmpty.style.display = 'none';
        }

        fetch('/wishlist/list', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateWishlistSidebar(data.items);
                updateWishlistCount(data.count);
            }
        })
        .catch(error => {
            console.error('Error loading wishlist:', error);
            if (sidebarItems) {
                sidebarItems.innerHTML = '<div class="wishlist-sidebar__error" style="text-align: center; padding: 2rem; color: #dc3545;"><p>Error loading wishlist. Please try again.</p></div>';
            }
        });
    }

    /**
     * Update wishlist sidebar HTML
     */
    function updateWishlistSidebar(items) {
        const sidebarItems = document.getElementById('wishlist-sidebar-items');
        const sidebarEmpty = document.getElementById('wishlist-sidebar-empty');

        if (!sidebarItems || !sidebarEmpty) return;

        if (items.length === 0) {
            sidebarItems.style.display = 'none';
            sidebarEmpty.style.display = 'flex';
            return;
        }

        sidebarEmpty.style.display = 'none';
        sidebarItems.style.display = 'flex';

        let html = '';
        items.forEach(item => {
            const price = item.discount_price || item.product_price;
            const oldPrice = item.discount_price ? item.product_price : null;

            html += `
                <div class="wishlist-sidebar-item" data-product-id="${item.product_id}">
                    <div class="wishlist-sidebar-item__checkbox">
                        <label class="checkbox-label">
                            <input type="checkbox" class="wishlist-item-checkbox" data-product-id="${item.product_id}">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="wishlist-sidebar-item__image">
                        <a href="${item.product_url}">
                            <img src="${item.product_image}" alt="${item.product_name}">
                        </a>
                    </div>
                    <div class="wishlist-sidebar-item__info">
                        <h4 class="wishlist-sidebar-item__name">
                            <a href="${item.product_url}">${item.product_name}</a>
                        </h4>
                        <div class="wishlist-sidebar-item__price-row">
                            ${oldPrice ? `<span class="wishlist-sidebar-item__price" style="text-decoration: line-through; color: #6c757d; font-size: 0.85rem; margin-right: 0.5rem;">$${oldPrice}</span>` : ''}
                            <span class="wishlist-sidebar-item__price">$${price}</span>
                        </div>
                        <button class="wishlist-sidebar-item__add-cart btn btn-primary btn-sm" data-product-id="${item.product_id}" title="Add to Cart" style="margin-top: 0.5rem; width: 100%;">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                    <button class="wishlist-sidebar-item__remove" data-product-id="${item.product_id}" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });

        sidebarItems.innerHTML = html;

        // Attach remove event listeners
        document.querySelectorAll('.wishlist-sidebar-item__remove').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const item = this.closest('.wishlist-sidebar-item');
                removeFromWishlist(productId, this);
                // Remove item from DOM
                if (item) {
                    $(item).fadeOut(300, function() {
                        $(this).remove();
                        // Check if wishlist is empty
                        const remainingItems = document.querySelectorAll('.wishlist-sidebar-item');
                        if (remainingItems.length === 0) {
                            updateWishlistSidebar([]);
                        }
                    });
                }
            });
        });

        // Attach add to cart event listeners
        document.querySelectorAll('.wishlist-sidebar-item__add-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                if (window.CartFunctions && typeof window.CartFunctions.addToCart === 'function') {
                    window.CartFunctions.addToCart(productId, 1, this);
                } else {
                    console.error('CartFunctions.addToCart is not available');
                }
            });
        });
    }

    /**
     * Update wishlist count badge
     */
    function updateWishlistCount(count) {
        // Update sidebar count badge
        const countBadge = document.getElementById('wishlist-count');
        if (countBadge) {
            if (count > 0) {
                countBadge.textContent = count;
                countBadge.style.display = 'inline';
            } else {
                countBadge.style.display = 'inline';
            }
        }

        // Update header badge
        const headerBadge = document.getElementById('wishlist-header-badge');
        if (headerBadge) {
            // Show badge if count > 0, hide if 0
            if (count > 0) {
                headerBadge.textContent = count;
                headerBadge.style.display = 'absolute';
            } else {
                headerBadge.style.display = 'absolute';
            }
        }
    }

    /**
     * Check wishlist status for products on page load
     */
    function checkWishlistStatus() {
        const wishlistButtons = document.querySelectorAll('.wishlist-btn[data-product-id]');
        if (wishlistButtons.length === 0) return;

        const productIds = Array.from(wishlistButtons).map(btn => btn.getAttribute('data-product-id'));

        fetch('/wishlist/check', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_ids: productIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.status) {
                wishlistButtons.forEach(button => {
                    const productId = button.getAttribute('data-product-id');
                    if (data.status[productId]) {
                        button.classList.add('in-wishlist');
                        const icon = button.querySelector('i');
                        if (icon) {
                            icon.classList.add('fas', 'fa-heart');
                        }
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error checking wishlist status:', error);
        });
    }

    /**
     * Show notification message
     */
    function showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `wishlist-notification wishlist-notification--${type}`;
        notification.textContent = message;

        // Add to body
        document.body.appendChild(notification);

        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    /**
     * Toggle wishlist sidebar
     */
    function toggleWishlistSidebar() {
        const sidebar = document.getElementById('wishlist-sidebar');
        const overlay = document.getElementById('wishlist-sidebar-overlay');
        if (!sidebar) return;

        const isOpening = !sidebar.classList.contains('active');
        sidebar.classList.toggle('active');
        if (overlay) {
            overlay.classList.toggle('active');
        }

        // Prevent body scroll when sidebar is open
        if (sidebar.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
            // Load wishlist content when opening
            if (isOpening) {
                loadWishlistSidebar();
            }
        } else {
            document.body.style.overflow = '';
        }
    }

    /**
     * Close wishlist sidebar
     */
    function closeWishlistSidebar() {
        const sidebar = document.getElementById('wishlist-sidebar');
        const overlay = document.getElementById('wishlist-sidebar-overlay');
        if (sidebar) {
            sidebar.classList.remove('active');
        }
        if (overlay) {
            overlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Attach click handlers to wishlist buttons
        document.addEventListener('click', function(e) {
            const wishlistBtn = e.target.closest('.wishlist-btn');
            if (wishlistBtn) {
                e.preventDefault();
                const productId = wishlistBtn.getAttribute('data-product-id');
                if (productId) {
                    toggleWishlist(productId, wishlistBtn);
                }
            }

            // Open wishlist sidebar when wishlist trigger is clicked
            const wishlistTrigger = e.target.closest('#wishlist-trigger') || e.target.closest('.wishlist-trigger');
            if (wishlistTrigger) {
                e.preventDefault();
                toggleWishlistSidebar();
            }

            // Close sidebar when close button is clicked
            if (e.target.closest('#wishlist-sidebar-close') || e.target.closest('.wishlist-sidebar__close')) {
                e.preventDefault();
                closeWishlistSidebar();
            }

            // Close sidebar when overlay is clicked
            if (e.target.id === 'wishlist-sidebar-overlay' || e.target.classList.contains('wishlist-sidebar-overlay')) {
                closeWishlistSidebar();
            }
        });

        // Close sidebar on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeWishlistSidebar();
            }
        });

        // Check wishlist status on page load
        checkWishlistStatus();

        // Load wishlist count on page load
        fetch('/wishlist/count', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateWishlistCount(data.count);
            }
        })
        .catch(error => {
            console.error('Error loading wishlist count:', error);
        });
    });

    // Export functions to global scope
    window.WishlistFunctions = {
        addToWishlist,
        removeFromWishlist,
        toggleWishlist,
        loadWishlistSidebar,
        toggleWishlistSidebar,
        closeWishlistSidebar
    };

})();

/**
 * Cart Functionality
 * Handles add to cart, remove from cart, and cart sidebar
 */
(function() {
    'use strict';

    // CSRF Token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    /**
     * Add product to cart
     */
    function addToCart(productId, quantity = 1, button = null) {
        // Show loading state
        if (button) {
            const icon = button.querySelector('i');
            if (icon) {
                const originalClass = icon.className;
                icon.className = 'fas fa-spinner fa-spin';
                button.disabled = true;
            }
        }

        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count
                if (data.cart_count !== undefined) {
                    updateCartCount(data.cart_count);
                }

                // Show success message
                showCartNotification('Product added to cart successfully!', 'success');

                // Load and open cart sidebar
                loadCartSidebar();
                toggleCartSidebar();

                // Reset button state
                if (button) {
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.className = 'fas fa-shopping-cart';
                    }
                    button.disabled = false;
                }
            } else {
                showCartNotification(data.message || 'Failed to add product to cart.', 'error');

                // Reset button state
                if (button) {
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.className = 'fas fa-shopping-cart';
                    }
                    button.disabled = false;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showCartNotification('An error occurred. Please try again.', 'error');

            // Reset button state
            if (button) {
                const icon = button.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-shopping-cart';
                }
                button.disabled = false;
            }
        });
    }

    /**
     * Update cart item quantity
     */
    function updateCartItem(cartItemId, quantity) {
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
                // Reload cart sidebar
                loadCartSidebar();
                if (data.cart_count !== undefined) {
                    updateCartCount(data.cart_count);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    /**
     * Remove product from cart
     */
    function removeFromCart(cartItemId, button) {
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
                // Remove item from DOM
                if (button) {
                    const cartItem = button.closest('.cart-sidebar-item');
                    if (cartItem) {
                        cartItem.style.transition = 'opacity 0.3s';
                        cartItem.style.opacity = '0';
                        setTimeout(() => {
                            cartItem.remove();
                            // Check if cart is empty
                            const cartItems = document.querySelectorAll('.cart-sidebar-item');
                            if (cartItems.length === 0) {
                                updateCartSidebarEmpty();
                            } else {
                                loadCartSidebar();
                            }
                        }, 300);
                    }
                }

                // Update cart count
                if (data.cart_count !== undefined) {
                    updateCartCount(data.cart_count);
                }

                showCartNotification('Product removed from cart.', 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showCartNotification('An error occurred. Please try again.', 'error');
        });
    }

    /**
     * Load cart sidebar content
     */
    function loadCartSidebar() {
        const sidebarItems = document.getElementById('cart-sidebar-items');
        const sidebarEmpty = document.getElementById('cart-sidebar-empty');
        const sidebarSummary = document.getElementById('cart-sidebar-summary');

        // Show loading state
        if (sidebarItems) {
            sidebarItems.innerHTML = '<div class="cart-sidebar__loading" style="text-align: center; padding: 2rem;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #6c757d;"></i><p style="margin-top: 1rem; color: #6c757d;">Loading cart...</p></div>';
            sidebarItems.style.display = 'block';
        }
        if (sidebarEmpty) {
            sidebarEmpty.style.display = 'none';
        }
        if (sidebarSummary) {
            sidebarSummary.style.display = 'none';
        }

        fetch('/cart/api/list', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartSidebar(data.items, data.total);
                updateCartCount(data.count);
            }
        })
        .catch(error => {
            console.error('Error loading cart:', error);
            if (sidebarItems) {
                sidebarItems.innerHTML = '<div class="cart-sidebar__error" style="text-align: center; padding: 2rem; color: #dc3545;"><p>Error loading cart. Please try again.</p></div>';
            }
        });
    }

    /**
     * Update cart sidebar HTML
     */
    function updateCartSidebar(items, total = 0) {
        const sidebarItems = document.getElementById('cart-sidebar-items');
        const sidebarEmpty = document.getElementById('cart-sidebar-empty');
        const sidebarSummary = document.getElementById('cart-sidebar-summary');

        if (!sidebarItems || !sidebarEmpty) return;

        if (items.length === 0) {
            sidebarItems.style.display = 'none';
            sidebarEmpty.style.display = 'flex';
            if (sidebarSummary) {
                sidebarSummary.style.display = 'none';
            }
            return;
        }

        sidebarEmpty.style.display = 'none';
        sidebarItems.style.display = 'block';
        if (sidebarSummary) {
            sidebarSummary.style.display = 'block';
        }

        // Clear existing items
        sidebarItems.innerHTML = '';

        // Add items
        items.forEach(item => {
            const itemHtml = `
                <div class="cart-sidebar-item" data-cart-item-id="${item.id}">
                    <div class="cart-sidebar-item__image">
                        <a href="${item.product_url}">
                            <img src="${item.product_image}" alt="${item.product_name}">
                        </a>
                    </div>
                    <div class="cart-sidebar-item__info">
                        <h4 class="cart-sidebar-item__name">
                            <a href="${item.product_url}" class="cart-sidebar-item__name-link">${item.product_name}</a>
                        </h4>
                        <div class="cart-sidebar-item__price-row">
                            <span class="cart-sidebar-item__price">$${item.price} x ${item.quantity}</span>
                            <span class="cart-sidebar-item__total">= $${item.subtotal.toFixed(2)}</span>
                        </div>
                    </div>
                    <button class="cart-sidebar-item__remove" data-cart-item-id="${item.id}" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            sidebarItems.insertAdjacentHTML('beforeend', itemHtml);
        });

        // Update total
        if (sidebarSummary) {
            const totalElement = sidebarSummary.querySelector('.cart-sidebar__summary-value');
            if (totalElement) {
                totalElement.textContent = '$' + total.toFixed(2);
            }
        }

        // Attach remove event listeners
        attachCartRemoveListeners();
    }

    /**
     * Update cart sidebar empty state
     */
    function updateCartSidebarEmpty() {
        const sidebarItems = document.getElementById('cart-sidebar-items');
        const sidebarEmpty = document.getElementById('cart-sidebar-empty');
        const sidebarSummary = document.getElementById('cart-sidebar-summary');

        if (sidebarItems) sidebarItems.style.display = 'none';
        if (sidebarEmpty) sidebarEmpty.style.display = 'flex';
        if (sidebarSummary) sidebarSummary.style.display = 'none';
    }

    /**
     * Attach remove event listeners to cart items
     */
    function attachCartRemoveListeners() {
        document.querySelectorAll('.cart-sidebar-item__remove').forEach(button => {
            button.addEventListener('click', function() {
                const cartItemId = this.getAttribute('data-cart-item-id');
                if (cartItemId) {
                    removeFromCart(cartItemId, this);
                }
            });
        });
    }

    /**
     * Update cart count in header
     */
    function updateCartCount(count) {
        const headerBadge = document.getElementById('cart-header-badge');
        if (headerBadge) {
            headerBadge.textContent = count;
            if (count > 0) {
                headerBadge.style.display = 'absolute';
            } else {
                headerBadge.textContent = '0';
                headerBadge.style.display = 'absolute';
            }
        }
    }

    /**
     * Toggle cart sidebar
     */
    function toggleCartSidebar() {
        const sidebar = document.getElementById('cart-sidebar');
        const overlay = document.getElementById('cart-sidebar-overlay');
        if (!sidebar) return;

        const isOpening = !sidebar.classList.contains('active');
        sidebar.classList.toggle('active');
        if (overlay) {
            overlay.classList.toggle('active');
        }

        // Prevent body scroll when sidebar is open
        if (sidebar.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
            // Load cart content when opening
            if (isOpening) {
                loadCartSidebar();
            }
        } else {
            document.body.style.overflow = '';
        }
    }

    /**
     * Close cart sidebar
     */
    function closeCartSidebar() {
        const sidebar = document.getElementById('cart-sidebar');
        const overlay = document.getElementById('cart-sidebar-overlay');
        if (sidebar) {
            sidebar.classList.remove('active');
        }
        if (overlay) {
            overlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }

    /**
     * Show cart notification
     */
    function showCartNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `cart-notification cart-notification--${type}`;
        notification.textContent = message;
        notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px 20px; background: ' + (type === 'success' ? '#10b981' : '#ef4444') + '; color: white; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Attach click handlers to add to cart buttons
        document.addEventListener('click', function(e) {
            const addToCartBtn = e.target.closest('.add-to-cart, .product__add-cart');
            if (addToCartBtn && addToCartBtn.hasAttribute('data-product-id')) {
                e.preventDefault();
                const productId = addToCartBtn.getAttribute('data-product-id');
                const quantity = 1; // Default quantity, can be enhanced later
                addToCart(productId, quantity, addToCartBtn);
            }

            // Open cart sidebar when cart trigger is clicked
            // Check if clicked element or its parent is the cart trigger
            let cartTrigger = e.target.closest('.cart-trigger') ||
                             e.target.closest('#cart-trigger') ||
                             (e.target.closest('a.header__icon') && e.target.closest('a.header__icon').classList.contains('cart-trigger'));

            // Also check if clicking directly on the icon or badge inside cart trigger
            if (!cartTrigger && (e.target.closest('i.fa-shopping-cart') || e.target.id === 'cart-header-badge')) {
                cartTrigger = e.target.closest('a.header__icon.cart-trigger') ||
                             document.getElementById('cart-trigger');
            }

            if (cartTrigger) {
                e.preventDefault();
                e.stopPropagation();
                toggleCartSidebar();
            }

            // Close sidebar when close button is clicked
            if (e.target.closest('#cart-sidebar-close, .cart-sidebar__close')) {
                e.preventDefault();
                closeCartSidebar();
            }

            // Close sidebar when overlay is clicked
            if (e.target.id === 'cart-sidebar-overlay' || e.target.classList.contains('cart-sidebar-overlay')) {
                closeCartSidebar();
            }
        });

        // Close sidebar on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCartSidebar();
            }
        });

        // Load cart count on page load
        fetch('/cart/count', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.count);
            }
        })
        .catch(error => {
            console.error('Error loading cart count:', error);
        });
    });

    // Export functions to global scope
    window.CartFunctions = {
        addToCart,
        updateCartItem,
        removeFromCart,
        loadCartSidebar,
        toggleCartSidebar,
        closeCartSidebar,
        updateCartCount
    };

})();

