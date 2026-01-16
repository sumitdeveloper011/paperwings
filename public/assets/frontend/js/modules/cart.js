/**
 * Cart Module
 * Handles all cart functionality
 */
(function() {
    'use strict';

    const Cart = {
        csrfToken: null,

        init: function() {
            this.csrfToken = window.AppUtils ? window.AppUtils.getCsrfToken() : '';
            this.attachEventListeners();
            if (this.isAuthenticated()) {
                this.loadCartCount();
                this.checkCartStatus();
            } else {
                this.updateCount(0);
            }
        },

        isAuthenticated: function() {
            const body = document.body;
            if (body && body.hasAttribute('data-authenticated')) {
                return body.getAttribute('data-authenticated') === 'true';
            }
            const userDropdown = document.getElementById('userDropdown');
            const loginLink = document.getElementById('loginLink');
            return userDropdown !== null && loginLink === null;
        },

        attachEventListeners: function() {
            document.addEventListener('click', (e) => {
                const addToCartBtn = e.target.closest('.add-to-cart, .product__add-cart, .cute-stationery__add-cart, .cute-stationery__add-cart-mobile, .wishlist-sidebar-item__add-cart');
                if (addToCartBtn && addToCartBtn.hasAttribute('data-product-uuid')) {
                    e.preventDefault();
                    const productUuid = addToCartBtn.getAttribute('data-product-uuid');
                    this.addToCart(productUuid, 1, addToCartBtn);
                }

                let cartTrigger = e.target.closest('.cart-trigger') ||
                                 e.target.closest('#cart-trigger') ||
                                 (e.target.closest('a.header__icon') && e.target.closest('a.header__icon').classList.contains('cart-trigger'));

                if (!cartTrigger && (e.target.closest('i.fa-shopping-cart') || e.target.id === 'cart-header-badge')) {
                    cartTrigger = e.target.closest('a.header__icon.cart-trigger') || document.getElementById('cart-trigger');
                }

                if (cartTrigger) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.toggleSidebar();
                }

                if (e.target.closest('#cart-sidebar-close, .cart-sidebar__close')) {
                    e.preventDefault();
                    this.closeSidebar();
                }

                if (e.target.id === 'cart-sidebar-overlay' || e.target.classList.contains('cart-sidebar-overlay')) {
                    this.closeSidebar();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeSidebar();
                }
            });
        },

        addToCart: function(productUuid, quantity = 1, button = null) {
            // Validate product UUID
            if (!productUuid) {
                if (typeof showToast !== 'undefined') {
                    showToast('Product UUID is missing. Please refresh the page.', 'error');
                } else if (window.AppUtils) {
                    window.AppUtils.showNotification('Product UUID is missing. Please refresh the page.', 'error');
                }
                return Promise.reject('Product UUID is missing');
            }

            if (window.AppUtils) {
                window.AppUtils.setButtonLoading(button, true, 'fa-shopping-cart');
            }

            return fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_uuid: productUuid,
                    quantity: quantity
                })
            })
            .then(response => {
                // Handle authentication errors
                if (response.status === 401) {
                    // Stop button loading
                    if (window.AppUtils) {
                        window.AppUtils.setButtonLoading(button, false, 'fa-shopping-cart');
                    }
                    // Redirect to login (don't continue promise chain)
                    if (window.AppUtils && typeof window.AppUtils.redirectToLogin === 'function') {
                        window.AppUtils.redirectToLogin();
                    } else {
                        const currentUrl = window.location.href;
                        window.location.href = '/login?intended=' + encodeURIComponent(currentUrl);
                    }
                    // Return a resolved promise to stop the chain
                    return Promise.resolve({ success: false, auth_required: true });
                }
                
                // Handle validation errors (400)
                if (response.status === 400) {
                    return response.json().then(data => {
                        return Promise.reject({ 
                            isValidationError: true, 
                            message: data.message || 'Invalid request. Please check your input.',
                            errors: data.errors || {}
                        });
                    });
                }
                
                if (!response.ok) {
                    return response.json().then(data => {
                        return Promise.reject({ 
                            message: data.message || 'An error occurred. Please try again.',
                            status: response.status
                        });
                    }).catch(() => {
                        return Promise.reject({ 
                            message: 'An error occurred. Please try again.',
                            status: response.status
                        });
                    });
                }
                
                return response.json();
            })
            .then(data => {
                // Skip processing if authentication was required
                if (data && data.auth_required) {
                    return;
                }

                if (data && data.success) {
                    if (data.cart_count !== undefined) {
                        this.updateCount(data.cart_count);
                    }
                    // Update button state immediately
                    if (button) {
                        button.classList.add('in-cart');
                        button.setAttribute('title', 'Remove from Cart');
                        button.setAttribute('aria-label', 'Remove from Cart');
                        const icon = button.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-shopping-cart');
                            icon.classList.add('fa-check');
                        }
                    }
                    this.checkCartStatus();

                    if (data.product && window.Analytics) {
                        window.Analytics.trackAddToCart({
                            id: data.product.id || productUuid,
                            name: data.product.name || '',
                            category: data.product.category || '',
                            brand: data.product.brand || '',
                            price: data.product.price || 0
                        }, data.product.quantity || quantity);
                    }

                    const wishlistItem = button?.closest('.wishlist-sidebar-item');
                    if (wishlistItem) {
                        const productUuidFromWishlist = wishlistItem.getAttribute('data-product-uuid');
                        if (productUuidFromWishlist && window.WishlistFunctions && typeof window.WishlistFunctions.removeFromWishlist === 'function') {
                            window.WishlistFunctions.removeFromWishlist(productUuidFromWishlist, null, true);
                        }
                    }

                    setTimeout(() => {
                        if (window.WishlistFunctions) {
                            if (typeof window.WishlistFunctions.loadWishlistSidebar === 'function') {
                                window.WishlistFunctions.loadWishlistSidebar();
                            }
                            if (typeof window.WishlistFunctions.checkWishlistStatus === 'function') {
                                window.WishlistFunctions.checkWishlistStatus();
                            }
                            if (typeof window.WishlistFunctions.loadWishlistCount === 'function') {
                                window.WishlistFunctions.loadWishlistCount();
                            }
                        }
                    }, 300);

                    if (typeof showToast !== 'undefined') {
                        showToast('Product added to cart successfully!', 'success');
                    } else if (window.AppUtils) {
                        window.AppUtils.showNotification('Product added to cart successfully!', 'success');
                    }
                    if (button) {
                        this.loadSidebar();
                        this.toggleSidebar();
                    }
                    return data;
                } else if (data) {
                    // Don't show toast here if button is null (bulk operation)
                    if (button && typeof showToast !== 'undefined') {
                        showToast(data.message || 'Failed to add product to cart.', 'error');
                    } else if (button && window.AppUtils) {
                        window.AppUtils.showNotification(data.message || 'Failed to add product to cart.', 'error');
                    }
                    if (window.AppUtils) {
                        window.AppUtils.setButtonLoading(button, false, 'fa-shopping-cart');
                    }
                    // Reject with error object containing message
                    return Promise.reject({
                        message: data.message || 'Failed to add product to cart.',
                        isApiError: true
                    });
                }
                if (window.AppUtils) {
                    window.AppUtils.setButtonLoading(button, false, 'fa-shopping-cart');
                }
                return Promise.reject({
                    message: 'Unknown error occurred.',
                    isApiError: false
                });
            })
            .catch(error => {
                // Stop button loading
                if (window.AppUtils) {
                    window.AppUtils.setButtonLoading(button, false, 'fa-shopping-cart');
                }
                
                // Don't show errors for authentication failures (they're expected)
                if (error === 'Not authenticated' || (error && typeof error === 'object' && error.message && error.message.includes('authenticated'))) {
                    return Promise.reject({
                        message: 'Not authenticated',
                        isApiError: false
                    });
                }
                
                // Handle validation errors - show user-friendly message
                if (error && error.isValidationError) {
                    const errorMessage = error.message || 'Invalid request. Please check your input.';
                    // Don't show toast if button is null (bulk operation)
                    if (button && typeof showToast !== 'undefined') {
                        showToast(errorMessage, 'error');
                    } else if (button && window.AppUtils) {
                        window.AppUtils.showNotification(errorMessage, 'error');
                    }
                    return Promise.reject({
                        message: errorMessage,
                        isApiError: true
                    });
                }

                // Handle API errors (400, 422, etc.) - extract message from response
                if (error && error.status && (error.status >= 400 && error.status < 500)) {
                    // Extract message from error object
                    const errorMessage = error.message || 'Failed to add product to cart.';
                    // Don't show toast if button is null (bulk operation)
                    if (button && typeof showToast !== 'undefined') {
                        showToast(errorMessage, 'error');
                    } else if (button && window.AppUtils) {
                        window.AppUtils.showNotification(errorMessage, 'error');
                    }
                    return Promise.reject({
                        message: errorMessage,
                        isApiError: true
                    });
                }

                // Only log/show errors for unexpected failures (network errors, 500, etc.)
                if (window.AppUtils) {
                    window.AppUtils.error('Cart error:', error);
                    // Don't show toast if button is null (bulk operation)
                    if (button && typeof showToast !== 'undefined') {
                        showToast(error.message || 'An error occurred. Please try again.', 'error');
                    } else if (button) {
                        window.AppUtils.showNotification(error.message || 'An error occurred. Please try again.', 'error');
                    }
                }
                return Promise.reject({
                    message: error.message || 'An error occurred. Please try again.',
                    isApiError: false
                });
            });
        },

        updateCartItem: function(cartItemId, quantity) {
            fetch('/cart/update', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    cart_item_id: cartItemId,
                    quantity: quantity
                })
            })
            .then(response => {
                if (response.status === 401) {
                    if (window.AppUtils && typeof window.AppUtils.redirectToLogin === 'function') {
                        window.AppUtils.redirectToLogin();
                    } else {
                        window.location.href = '/login';
                    }
                    return Promise.reject('Not authenticated');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.loadSidebar();
                    if (data.cart_count !== undefined) {
                        this.updateCount(data.cart_count);
                    }
                }
            })
            .catch(error => {
                // Don't log authentication errors (expected behavior)
                if (error === 'Not authenticated' || (error && error.message && error.message.includes('authenticated'))) {
                    return;
                }
                if (window.AppUtils) {
                    window.AppUtils.error('Error updating cart:', error);
                }
            });
        },

        removeFromCart: function(cartItemId, button) {
            fetch('/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ cart_item_id: cartItemId })
            })
            .then(response => {
                if (response.status === 401) {
                    if (window.AppUtils && typeof window.AppUtils.redirectToLogin === 'function') {
                        window.AppUtils.redirectToLogin();
                    } else {
                        window.location.href = '/login';
                    }
                    return Promise.reject('Not authenticated');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (data.product && window.Analytics) {
                        window.Analytics.trackRemoveFromCart({
                            id: data.product.id,
                            name: data.product.name,
                            category: data.product.category || '',
                            brand: data.product.brand || '',
                            price: data.product.price || 0
                        }, data.product.quantity || 1);
                    }

                    if (button) {
                        const cartItem = button.closest('.cart-sidebar-item');
                        if (cartItem) {
                            cartItem.style.transition = 'opacity 0.3s';
                            cartItem.style.opacity = '0';
                            setTimeout(() => {
                                cartItem.remove();
                                const cartItems = document.querySelectorAll('.cart-sidebar-item');
                                if (cartItems.length === 0) {
                                    this.updateSidebarEmpty();
                                } else {
                                    this.loadSidebar();
                                }
                            }, 300);
                        }
                    }
                    if (data.cart_count !== undefined) {
                        this.updateCount(data.cart_count);
                    }
                    // Update button state immediately using product UUID from response
                    if (data.product && data.product.uuid) {
                        const productUuid = data.product.uuid;
                        const allCartButtons = document.querySelectorAll(`.add-to-cart[data-product-uuid="${productUuid}"], .product__add-cart[data-product-uuid="${productUuid}"], .cute-stationery__add-cart[data-product-uuid="${productUuid}"], .cute-stationery__add-cart-mobile[data-product-uuid="${productUuid}"]`);
                        allCartButtons.forEach(btn => {
                            btn.classList.remove('in-cart');
                            btn.setAttribute('title', 'Add to Cart');
                            btn.setAttribute('aria-label', 'Add to Cart');
                            const btnIcon = btn.querySelector('i');
                            if (btnIcon) {
                                btnIcon.classList.remove('fa-check');
                                btnIcon.classList.add('fa-shopping-cart');
                            }
                        });
                    }
                    this.checkCartStatus();
                    if (typeof showToast !== 'undefined') {
                        showToast('Product removed from cart.', 'success');
                    } else if (window.AppUtils) {
                        window.AppUtils.showNotification('Product removed from cart.', 'success');
                    }
                }
            })
            .catch(error => {
                // Don't log authentication errors (expected behavior)
                if (error === 'Not authenticated' || (error && error.message && error.message.includes('authenticated'))) {
                    return;
                }
                if (window.AppUtils) {
                    window.AppUtils.error('Error removing from cart:', error);
                    if (typeof showToast !== 'undefined') {
                        showToast('An error occurred. Please try again.', 'error');
                    } else {
                        window.AppUtils.showNotification('An error occurred. Please try again.', 'error');
                    }
                }
            });
        },

        loadSidebar: function() {
            const sidebarItems = document.getElementById('cart-sidebar-items');
            const sidebarEmpty = document.getElementById('cart-sidebar-empty');
            const sidebarSummary = document.getElementById('cart-sidebar-summary');

            // Check authentication first - cart requires login
            if (!this.isAuthenticated()) {
                this.updateSidebarEmpty();
                // Optionally redirect to login or show message
                if (window.AppUtils && typeof window.AppUtils.redirectToLogin === 'function') {
                    // Don't auto-redirect, just show empty cart
                }
                return;
            }

            if (sidebarItems) {
                sidebarItems.innerHTML = '<div class="cart-sidebar__loading" style="text-align: center; padding: 2rem;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #6c757d;"></i><p style="margin-top: 1rem; color: #6c757d;">Loading cart...</p></div>';
                sidebarItems.style.display = 'flex';
            }
            if (sidebarEmpty) sidebarEmpty.style.display = 'none';
            if (sidebarSummary) sidebarSummary.style.display = 'none';

            fetch('/cart/render', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok && response.status === 401) {
                    // Handle 401 gracefully - show empty cart
                    this.updateSidebarEmpty();
                    return Promise.reject('Not authenticated');
                }
                if (!response.ok) {
                    throw new Error('Failed to load cart: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.updateSidebarFromHtml(data.html, data.total, data.count);
                } else {
                    // Fallback to empty state
                    this.updateSidebarEmpty();
                }
            })
            .catch(error => {
                // Silently handle authentication errors
                if (error === 'Not authenticated' || (error && error.message && error.message.includes('authenticated'))) {
                    this.updateSidebarEmpty();
                    return;
                }
                // Only show error for unexpected errors
                if (window.AppUtils) {
                    window.AppUtils.error('Error loading cart:', error);
                }
                if (sidebarItems) {
                    sidebarItems.innerHTML = '<div class="cart-sidebar__error" style="text-align: center; padding: 2rem; color: #dc3545;"><p>Error loading cart. Please try again.</p></div>';
                }
            });
        },

        updateSidebarFromHtml: function(html, total = 0, count = 0) {
            const sidebarItems = document.getElementById('cart-sidebar-items');
            const sidebarEmpty = document.getElementById('cart-sidebar-empty');
            const sidebarSummary = document.getElementById('cart-sidebar-summary');

            if (!sidebarItems || !sidebarEmpty) return;

            this.updateCount(count);

            if (!html || html.trim() === '' || count === 0) {
                sidebarItems.style.display = 'none';
                sidebarEmpty.style.display = 'flex';
                if (sidebarSummary) sidebarSummary.style.display = 'none';
                sidebarItems.innerHTML = '';
                return;
            }

            sidebarEmpty.style.display = 'none';
            sidebarItems.style.display = 'flex';
            if (sidebarSummary) sidebarSummary.style.display = 'block';
            sidebarItems.innerHTML = html;

            if (sidebarSummary) {
                const totalElement = sidebarSummary.querySelector('.cart-sidebar__summary-value');
                if (totalElement) {
                    totalElement.textContent = '$' + total.toFixed(2);
                }
            }

            document.querySelectorAll('.cart-sidebar-item__remove').forEach(button => {
                button.addEventListener('click', () => {
                    const cartItemId = button.getAttribute('data-cart-item-id');
                    if (cartItemId) {
                        this.removeFromCart(cartItemId, button);
                    }
                });
            });
        },

        updateSidebarEmpty: function() {
            const sidebarItems = document.getElementById('cart-sidebar-items');
            const sidebarEmpty = document.getElementById('cart-sidebar-empty');
            const sidebarSummary = document.getElementById('cart-sidebar-summary');
            if (sidebarItems) sidebarItems.style.display = 'none';
            if (sidebarEmpty) sidebarEmpty.style.display = 'flex';
            if (sidebarSummary) sidebarSummary.style.display = 'none';
        },

        updateCount: function(count) {
            const headerBadge = document.getElementById('cart-header-badge');
            if (headerBadge) {
                headerBadge.textContent = count;
                // Hide badge when count is 0, show when count > 0
                headerBadge.style.display = count > 0 ? 'absolute' : 'absolute';
            }
        },

        checkCartStatus: function() {
            const cartButtons = document.querySelectorAll('.add-to-cart[data-product-uuid], .product__add-cart[data-product-uuid], .cute-stationery__add-cart[data-product-uuid], .cute-stationery__add-cart-mobile[data-product-uuid]');
            if (cartButtons.length === 0) return;

            if (!this.isAuthenticated()) {
                return;
            }

            const productUuids = Array.from(cartButtons)
                .map(btn => btn.getAttribute('data-product-uuid'))
                .filter(uuid => uuid && uuid.trim() !== '');

            if (productUuids.length === 0) {
                return;
            }

            fetch('/cart/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_uuids: productUuids })
            })
            .then(response => {
                if (response.status === 401) {
                    // User not authenticated, don't show cart status
                    return Promise.reject('Not authenticated');
                }
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.status) {
                    cartButtons.forEach(button => {
                        const productUuid = button.getAttribute('data-product-uuid');
                        if (!productUuid) return; // Skip if no UUID
                        
                        const icon = button.querySelector('i');

                        if (data.status[productUuid]) {
                            button.classList.add('in-cart');
                            button.setAttribute('title', 'Remove from Cart');
                            button.setAttribute('aria-label', 'Remove from Cart');
                            if (icon) {
                                icon.classList.remove('fa-shopping-cart');
                                icon.classList.add('fa-check');
                            }
                        } else {
                            button.classList.remove('in-cart');
                            button.setAttribute('title', 'Add to Cart');
                            button.setAttribute('aria-label', 'Add to Cart');
                            if (icon) {
                                icon.classList.remove('fa-check');
                                icon.classList.add('fa-shopping-cart');
                            }
                        }
                    });
                }
            })
            .catch(error => {
                // Don't log validation errors (422) - they're expected if UUIDs are missing
                if (error && error.errors && error.message) {
                    // Validation error - silently fail
                    return;
                }
                if (window.AppUtils && error !== 'Not authenticated') {
                    window.AppUtils.error('Error checking cart status:', error);
                }
            });
        },

        loadCartCount: function() {
            // Don't call API if not authenticated
            if (!this.isAuthenticated()) {
                this.updateCount(0);
                return;
            }

            fetch('/cart/count', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // Handle 401 Unauthorized - user not authenticated
                if (response.status === 401) {
                    // Try to parse JSON response (backend returns JSON even for 401)
                    return response.json().then(data => {
                        // Set count to 0 and return null to stop promise chain
                        this.updateCount(0);
                        return null;
                    }).catch(() => {
                        // If JSON parsing fails, just set count to 0
                        this.updateCount(0);
                        return null;
                    });
                }

                if (!response.ok) {
                    throw new Error('Failed to load cart count: ' + response.status);
                }

                return response.json();
            })
            .then(data => {
                // Skip if data is null (401 was handled)
                if (!data) {
                    return;
                }

                if (data.success) {
                    this.updateCount(data.count);
                } else if (data.error === 'Unauthenticated' || data.message === 'Unauthenticated.' || data.message === 'Please login to continue.') {
                    // Handle unauthenticated response
                    this.updateCount(0);
                } else {
                    // Other response, set count to 0
                    this.updateCount(0);
                }
            })
            .catch(error => {
                // Silently handle errors - don't log authentication errors
                if (error === 'Not authenticated' || (error && error.message && error.message.includes('authenticated'))) {
                    this.updateCount(0);
                    return;
                }
                // Only log unexpected errors
                if (window.AppUtils) {
                    window.AppUtils.error('Error loading cart count:', error);
                }
                this.updateCount(0);
            });
        },

        toggleSidebar: function() {
            const sidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('cart-sidebar-overlay');
            if (!sidebar) return;

            const isOpening = !sidebar.classList.contains('active');
            sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');

            if (sidebar.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
                if (isOpening) {
                    this.loadSidebar();
                }
            } else {
                document.body.style.overflow = '';
            }
        },

        closeSidebar: function() {
            const sidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('cart-sidebar-overlay');
            if (sidebar) sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    // Export to global scope
    window.CartModule = Cart;

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            Cart.init();
        });
    } else {
        Cart.init();
    }
})();

