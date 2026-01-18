/**
 * Cart Module
 * Handles all cart functionality including add, remove, update, and sidebar management
 * 
 * @module CartModule
 */
(function() {
    'use strict';

    const Cart = {
        csrfToken: null,

        /**
         * Initialize cart module
         */
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

        /**
         * Check if user is authenticated
         * @returns {boolean} Whether user is authenticated
         */
        isAuthenticated: function() {
            const body = document.body;
            if (body && body.hasAttribute('data-authenticated')) {
                return body.getAttribute('data-authenticated') === 'true';
            }
            const userDropdown = document.getElementById('userDropdown');
            const loginLink = document.getElementById('loginLink');
            return userDropdown !== null && loginLink === null;
        },

        /**
         * Attach event listeners for cart interactions
         */
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

        /**
         * Add product to cart
         * @param {string} productUuid - Product UUID
         * @param {number} quantity - Quantity to add (default: 1)
         * @param {HTMLElement} button - Button element for loading state
         * @returns {Promise} Promise that resolves with cart data
         */
        addToCart: function(productUuid, quantity = 1, button = null) {
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

            const requestPromise = window.AjaxUtils && typeof window.AjaxUtils.post === 'function'
                ? window.AjaxUtils.post('/cart/add', {
                    product_uuid: productUuid,
                    quantity: quantity
                }, { silentAuth: true })
                : fetch('/cart/add', {
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
                }).then(response => {
                    if (response.status === 401) {
                        if (window.AppUtils) {
                            window.AppUtils.setButtonLoading(button, false, 'fa-shopping-cart');
                        }
                        if (window.AppUtils && typeof window.AppUtils.redirectToLogin === 'function') {
                            window.AppUtils.redirectToLogin();
                        } else {
                            window.location.href = '/login?intended=' + encodeURIComponent(window.location.href);
                        }
                        return Promise.reject({ success: false, auth_required: true });
                    }
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
                });

            return requestPromise
                .then(data => {
                    if (data && data.auth_required) {
                        return;
                    }

                    const cartCount = data.data?.cart_count ?? data.cart_count;
                    const productData = data.data?.product ?? data.product;

                    if (data && data.success) {
                        if (cartCount !== undefined) {
                            this.updateCount(cartCount);
                        }
                    
                    if (button) {
                        if (window.AppUtils) {
                            window.AppUtils.setButtonLoading(button, false, 'fa-shopping-cart');
                        }
                        
                        button.classList.add('in-cart');
                        button.setAttribute('title', 'Remove from Cart');
                        button.setAttribute('aria-label', 'Remove from Cart');
                        const icon = button.querySelector('i');
                        if (icon) {
                            icon.classList.remove('fa-shopping-cart', 'fa-spinner', 'fa-spin', 'far', 'fal', 'fab');
                            icon.classList.add('fas', 'fa-check');
                            button.disabled = false;
                        }
                    }
                    this.checkCartStatus();

                    if (productData && window.Analytics) {
                        window.Analytics.trackAddToCart({
                            id: productData.id || productUuid,
                            name: productData.name || '',
                            category: productData.category || '',
                            brand: productData.brand || '',
                            price: productData.price || 0
                        }, productData.quantity || quantity);
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
                    if (window.AppUtils) {
                        window.AppUtils.setButtonLoading(button, false, 'fa-shopping-cart');
                    }
                    
                    if (error && (error.isAuthError || error.auth_required || error.message === 'Not authenticated')) {
                        return Promise.resolve({
                            success: false,
                            message: 'Not authenticated',
                            isApiError: false,
                            auth_required: true
                        });
                    }
                    
                    if (error && error.name === 'TypeError' && error.message && error.message.includes('fetch')) {
                        if (button && typeof showToast !== 'undefined') {
                            showToast('Network error. Please check your connection and try again.', 'error');
                        } else if (button && window.AppUtils) {
                            window.AppUtils.showNotification('Network error. Please check your connection and try again.', 'error');
                        }
                        return Promise.resolve({
                            success: false,
                            message: 'Network error',
                            isApiError: false
                        });
                    }
                    
                    const errorMessage = error.message || 'Failed to add product to cart.';

                    if (error && error.isValidationError) {
                        if (button && typeof showToast !== 'undefined') {
                            showToast(errorMessage, 'error');
                        } else if (button && window.AppUtils) {
                            window.AppUtils.showNotification(errorMessage, 'error');
                        }
                        return Promise.resolve({
                            success: false,
                            message: errorMessage,
                            isApiError: true
                        });
                    }

                    if (button && typeof showToast !== 'undefined') {
                        showToast(errorMessage, 'error');
                    } else if (button && window.AppUtils) {
                        window.AppUtils.showNotification(errorMessage, 'error');
                    }
                    return Promise.resolve({
                        success: false,
                        message: errorMessage,
                        isApiError: true
                    });
                });
        },

        updateCartItem: function(cartItemId, quantity) {
            const requestPromise = window.AjaxUtils && typeof window.AjaxUtils.put === 'function'
                ? window.AjaxUtils.put('/cart/update', {
                    cart_item_id: cartItemId,
                    quantity: quantity
                }, { silentAuth: true })
                : fetch('/cart/update', {
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
                }).then(response => {
                    if (response.status === 401) {
                        if (window.AppUtils && typeof window.AppUtils.redirectToLogin === 'function') {
                            window.AppUtils.redirectToLogin();
                        } else {
                            window.location.href = '/login';
                        }
                        return Promise.reject('Not authenticated');
                    }
                    return response.json();
                });

            requestPromise
                .then(data => {
                    const cartCount = data.data?.cart_count ?? data.cart_count;
                    if (data.success) {
                        this.loadSidebar();
                        if (cartCount !== undefined) {
                            this.updateCount(cartCount);
                        }
                    }
                })
                .catch(error => {
                    if (error && (error.isAuthError || error.message === 'Not authenticated')) {
                        return;
                    }
                    if (window.AppUtils) {
                        window.AppUtils.error('Error updating cart:', error);
                    }
                });
        },

        removeFromCart: function(cartItemId, button) {
            const requestPromise = window.AjaxUtils && typeof window.AjaxUtils.post === 'function'
                ? window.AjaxUtils.post('/cart/remove', {
                    cart_item_id: cartItemId
                }, { silentAuth: true })
                : fetch('/cart/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ cart_item_id: cartItemId })
                }).then(response => {
                    if (response.status === 401) {
                        if (window.AppUtils && typeof window.AppUtils.redirectToLogin === 'function') {
                            window.AppUtils.redirectToLogin();
                        } else {
                            window.location.href = '/login';
                        }
                        return Promise.reject('Not authenticated');
                    }
                    return response.json();
                });

            requestPromise
                .then(data => {
                    const productData = data.data?.product ?? data.product;
                    const cartCount = data.data?.cart_count ?? data.cart_count;

                    if (data.success) {
                        if (productData && window.Analytics) {
                            window.Analytics.trackRemoveFromCart({
                                id: productData.id,
                                name: productData.name,
                                category: productData.category || '',
                                brand: productData.brand || '',
                                price: productData.price || 0
                            }, productData.quantity || 1);
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
                    if (cartCount !== undefined) {
                        this.updateCount(cartCount);
                    }
                    // Update button state immediately using product UUID from response
                    if (productData && productData.uuid) {
                        const productUuid = productData.uuid;
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
                    if (error && (error.isAuthError || error.message === 'Not authenticated')) {
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

            const requestPromise = window.AjaxUtils && typeof window.AjaxUtils.get === 'function'
                ? window.AjaxUtils.get('/cart/render', { silentAuth: true })
                : fetch('/cart/render', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                }).then(response => {
                    if (!response.ok && response.status === 401) {
                        this.updateSidebarEmpty();
                        return Promise.reject('Not authenticated');
                    }
                    if (!response.ok) {
                        throw new Error('Failed to load cart: ' + response.status);
                    }
                    return response.json();
                });

            requestPromise
                .then(data => {
                    if (data.success) {
                        const html = data.data?.html ?? data.html ?? '';
                        const total = data.data?.total ?? data.total ?? 0;
                        const count = data.data?.count ?? data.count ?? 0;
                        this.updateSidebarFromHtml(html, total, count);
                    } else {
                        this.updateSidebarEmpty();
                    }
                })
                .catch(error => {
                    if (error && (error.isAuthError || error.message === 'Not authenticated')) {
                        this.updateSidebarEmpty();
                        return;
                    }
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
            count = count !== null && count !== undefined ? Number(count) : 0;
            if (isNaN(count)) count = 0;
            
            const headerBadge = document.getElementById('cart-header-badge');
            const headerBadgeMobile = document.getElementById('cart-header-badge-mobile');
            
            if (headerBadge) {
                headerBadge.textContent = count;
                headerBadge.style.display = 'flex';
            }
            
            if (headerBadgeMobile) {
                headerBadgeMobile.textContent = count;
                headerBadgeMobile.style.display = 'flex';
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

            const requestPromise = window.AjaxUtils && typeof window.AjaxUtils.post === 'function'
                ? window.AjaxUtils.post('/cart/check', {
                    product_uuids: productUuids
                }, { silentAuth: true })
                : fetch('/cart/check', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_uuids: productUuids })
                }).then(response => {
                    if (response.status === 401) {
                        return Promise.reject('Not authenticated');
                    }
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                });

            requestPromise
                .then(data => {
                    const statusData = data.data?.status ?? data.status;
                    if (data.success && statusData) {
                    cartButtons.forEach(button => {
                        const productUuid = button.getAttribute('data-product-uuid');
                        if (!productUuid) return;
                        
                        const icon = button.querySelector('i');

                        if (statusData[productUuid]) {
                            button.classList.add('in-cart');
                            button.setAttribute('title', 'Remove from Cart');
                            button.setAttribute('aria-label', 'Remove from Cart');
                            button.disabled = false;
                            if (icon) {
                                icon.classList.remove('fa-shopping-cart', 'fa-spinner', 'fa-spin', 'far', 'fal', 'fab');
                                icon.classList.add('fas', 'fa-check');
                            }
                        } else {
                            button.classList.remove('in-cart');
                            button.setAttribute('title', 'Add to Cart');
                            button.setAttribute('aria-label', 'Add to Cart');
                            button.disabled = false;
                            if (icon) {
                                icon.classList.remove('fa-check', 'fa-spinner', 'fa-spin', 'far', 'fal', 'fab');
                                icon.classList.add('fas', 'fa-shopping-cart');
                            }
                        }
                    });
                }
            })
                .catch(error => {
                    if (error && (error.isAuthError || error.message === 'Not authenticated')) {
                        return;
                    }
                    if (error && error.errors && error.message) {
                        return;
                    }
                    if (window.AppUtils) {
                        window.AppUtils.error('Error checking cart status:', error);
                    }
                });
        },

        loadCartCount: function() {
            if (!this.isAuthenticated()) {
                this.updateCount(0);
                return;
            }

            const requestPromise = window.AjaxUtils && typeof window.AjaxUtils.get === 'function'
                ? window.AjaxUtils.get('/cart/count', { silentAuth: true })
                : fetch('/cart/count', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                }).then(response => {
                    if (response.status === 401) {
                        return response.json().then(data => {
                            this.updateCount(0);
                            return null;
                        }).catch(() => {
                            this.updateCount(0);
                            return null;
                        });
                    }
                    if (!response.ok) {
                        throw new Error('Failed to load cart count: ' + response.status);
                    }
                    return response.json();
                });

            requestPromise
                .then(data => {
                    if (!data) {
                        return;
                    }
                    if (data.success) {
                        const count = data.data?.count ?? data.count ?? 0;
                        this.updateCount(count);
                    } else if (data.error === 'Unauthenticated' || data.message === 'Unauthenticated.' || data.message === 'Please login to continue.') {
                        this.updateCount(0);
                    } else {
                        this.updateCount(0);
                    }
                })
                .catch(error => {
                    if (error && (error.isAuthError || error.message === 'Not authenticated')) {
                        this.updateCount(0);
                        return;
                    }
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

