/**
 * Wishlist and Cart Functionality - Optimized
 * Handles wishlist and cart operations with better structure and error handling
 */
(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        isDevelopment: false, // Set to true for development
        notificationDuration: 3000,
        fadeOutDuration: 300
    };

    // Utility Functions
    const Utils = {
        // Get CSRF Token
        getCsrfToken: function() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        // Log function (only in development)
        log: function(...args) {
            if (CONFIG.isDevelopment) {
                console.log(...args);
            }
        },

        // Error log (always show)
        error: function(...args) {
            console.error(...args);
        },

        // Handle authentication redirect
        redirectToLogin: function() {
            const currentUrl = window.location.href;
            window.location.href = '/login?intended=' + encodeURIComponent(currentUrl);
        },

        // Update button loading state
        setButtonLoading: function(button, isLoading, iconClass = 'fa-heart') {
            if (!button) return;
            const icon = button.querySelector('i');
            if (icon) {
                if (isLoading) {
                    icon.classList.remove('fas', iconClass);
                    icon.classList.add('fas', 'fa-spinner', 'fa-spin');
                } else {
                    icon.classList.remove('fa-spinner', 'fa-spin');
                    icon.classList.add('fas', iconClass);
                }
            }
            button.disabled = isLoading;
        },

        // Show notification
        showNotification: function(message, type = 'success', container = 'body') {
            const notification = document.createElement('div');
            notification.className = `app-notification app-notification--${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                padding: 15px 20px;
                background: ${type === 'success' ? '#10b981' : '#ef4444'};
                color: white;
                border-radius: 5px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                opacity: 0;
                transition: opacity 0.3s;
            `;

            const target = container === 'body' ? document.body : document.querySelector(container);
            if (target) {
                target.appendChild(notification);

                setTimeout(() => {
                    notification.style.opacity = '1';
                }, 10);

                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (target.contains(notification)) {
                            target.removeChild(notification);
                        }
                    }, CONFIG.fadeOutDuration);
                }, CONFIG.notificationDuration);
            }
        },

        // Handle API response
        handleApiResponse: function(response) {
            if (response.status === 401) {
                Utils.redirectToLogin();
                return Promise.reject('Not authenticated');
            }

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                Utils.redirectToLogin();
                return Promise.reject('Not authenticated');
            }
        }
    };

    // Wishlist Module
    const Wishlist = {
        csrfToken: Utils.getCsrfToken(),

        isAuthenticated: function() {
            // Check if user is authenticated by checking body data attribute
            const body = document.body;
            if (body && body.hasAttribute('data-authenticated')) {
                return body.getAttribute('data-authenticated') === 'true';
            }
            // Fallback: check if user dropdown exists (authenticated) or login link exists (not authenticated)
            const userDropdown = document.getElementById('userDropdown');
            const loginLink = document.getElementById('loginLink');
            return userDropdown !== null && loginLink === null;
        },

        init: function() {
            this.attachEventListeners();
            if (this.isAuthenticated()) {
                this.checkWishlistStatus();
                this.loadWishlistCount();
            }
        },

        attachEventListeners: function() {
            // Wishlist button clicks
            document.addEventListener('click', (e) => {
                const wishlistBtn = e.target.closest('.wishlist-btn');
                if (wishlistBtn) {
                    e.preventDefault();
                    const productId = wishlistBtn.getAttribute('data-product-id');
                    if (productId) {
                        this.toggleWishlist(productId, wishlistBtn);
                    }
                }

                // Open wishlist sidebar
                const wishlistTrigger = e.target.closest('#wishlist-trigger') || e.target.closest('.wishlist-trigger');
                if (wishlistTrigger) {
                    e.preventDefault();
                    this.toggleSidebar();
                }

                // Close sidebar
                if (e.target.closest('#wishlist-sidebar-close') || e.target.closest('.wishlist-sidebar__close')) {
                    e.preventDefault();
                    this.closeSidebar();
                }

                if (e.target.id === 'wishlist-sidebar-overlay' || e.target.classList.contains('wishlist-sidebar-overlay')) {
                    this.closeSidebar();
                }
            });

            // Close sidebar on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeSidebar();
                }
            });
        },

        addToWishlist: function(productId, button) {
            Utils.setButtonLoading(button, true, 'fa-heart');

            fetch('/wishlist/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(Utils.handleApiResponse)
            .then(data => {
                if (data.requires_login || (data.success === false && data.message && data.message.toLowerCase().includes('login'))) {
                    const redirectUrl = data.redirect_url || '/login';
                    const currentUrl = window.location.href;
                    window.location.href = redirectUrl + (redirectUrl.includes('?') ? '&' : '?') + 'intended=' + encodeURIComponent(currentUrl);
                    return;
                }

                if (data.success) {
                    Utils.setButtonLoading(button, false, 'fa-heart');
                    if (button) {
                        button.classList.add('in-wishlist');
                    }
                    if (data.wishlist_count !== undefined) {
                        this.updateCount(data.wishlist_count);
                    }
                    Utils.showNotification('Product added to wishlist!', 'success');
                    this.loadSidebar();
                    this.toggleSidebar();
                } else {
                    Utils.setButtonLoading(button, false, 'fa-heart');
                    Utils.showNotification(data.message || 'Failed to add product to wishlist.', 'error');
                }
            })
            .catch(error => {
                Utils.error('Wishlist error:', error);
                if (error !== 'Not authenticated') {
                    Utils.redirectToLogin();
                }
                Utils.setButtonLoading(button, false, 'fa-heart');
            });
        },

        removeFromWishlist: function(productId, button) {
            Utils.setButtonLoading(button, true, 'fa-heart');

            fetch('/wishlist/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Utils.setButtonLoading(button, false, 'fa-heart');
                    if (button) {
                        button.classList.remove('in-wishlist');
                    }
                    if (data.wishlist_count !== undefined) {
                        this.updateCount(data.wishlist_count);
                    }
                    Utils.showNotification('Product removed from wishlist!', 'success');
                    this.loadSidebar();
                } else {
                    Utils.setButtonLoading(button, false, 'fa-heart');
                    Utils.showNotification(data.message || 'Failed to remove product from wishlist.', 'error');
                }
            })
            .catch(error => {
                Utils.error('Wishlist error:', error);
                Utils.setButtonLoading(button, false, 'fa-heart');
                Utils.showNotification('An error occurred. Please try again.', 'error');
            });
        },

        toggleWishlist: function(productId, button) {
            if (button && button.classList.contains('in-wishlist')) {
                this.removeFromWishlist(productId, button);
            } else {
                this.addToWishlist(productId, button);
            }
        },

        loadSidebar: function() {
            const sidebarItems = document.getElementById('wishlist-sidebar-items');
            const sidebarEmpty = document.getElementById('wishlist-sidebar-empty');

            if (sidebarItems) {
                sidebarItems.innerHTML = '<div class="wishlist-sidebar__loading" style="text-align: center; padding: 2rem;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #6c757d;"></i><p style="margin-top: 1rem; color: #6c757d;">Loading wishlist...</p></div>';
                sidebarItems.style.display = 'block';
            }
            if (sidebarEmpty) {
                sidebarEmpty.style.display = 'none';
            }

            fetch('/wishlist/render', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateSidebarFromHtml(data.html, data.count);
                } else if (data.requires_login) {
                    Utils.redirectToLogin();
                }
            })
            .catch(error => {
                Utils.error('Error loading wishlist:', error);
                if (sidebarItems) {
                    sidebarItems.innerHTML = '<div class="wishlist-sidebar__error" style="text-align: center; padding: 2rem; color: #dc3545;"><p>Error loading wishlist. Please try again.</p></div>';
                }
            });
        },

        updateSidebarFromHtml: function(html, count) {
            const sidebarItems = document.getElementById('wishlist-sidebar-items');
            const sidebarEmpty = document.getElementById('wishlist-sidebar-empty');

            if (!sidebarItems || !sidebarEmpty) return;

            this.updateCount(count);

            if (!html || html.trim() === '' || count === 0) {
                sidebarItems.style.display = 'none';
                sidebarEmpty.style.display = 'flex';
                sidebarItems.innerHTML = '';
                return;
            }

            sidebarEmpty.style.display = 'none';
            sidebarItems.style.display = 'flex';
            sidebarItems.innerHTML = html;

            // Attach event listeners
            document.querySelectorAll('.wishlist-sidebar-item__remove').forEach(button => {
                button.addEventListener('click', () => {
                    const productId = button.getAttribute('data-product-id');
                    const item = button.closest('.wishlist-sidebar-item');
                    this.removeFromWishlist(productId, button);
                    if (item && typeof $ !== 'undefined') {
                        $(item).fadeOut(300, function() {
                            $(this).remove();
                            const remainingItems = document.querySelectorAll('.wishlist-sidebar-item');
                            if (remainingItems.length === 0) {
                                sidebarItems.style.display = 'none';
                                sidebarEmpty.style.display = 'flex';
                                Wishlist.updateCount(0);
                            }
                        });
                    }
                });
            });

            document.querySelectorAll('.wishlist-sidebar-item__add-cart').forEach(button => {
                button.addEventListener('click', () => {
                    const productId = button.getAttribute('data-product-id');
                    if (window.CartFunctions && typeof window.CartFunctions.addToCart === 'function') {
                        window.CartFunctions.addToCart(productId, 1, button);
                    } else {
                        Utils.error('CartFunctions.addToCart is not available');
                    }
                });
            });
        },

        updateCount: function(count) {
            const countBadge = document.getElementById('wishlist-count');
            if (countBadge) {
                countBadge.textContent = count;
                countBadge.style.display = 'inline';
            }

            const headerBadge = document.getElementById('wishlist-header-badge');
            if (headerBadge) {
                headerBadge.textContent = count;
                headerBadge.style.display = 'absolute';
            }
        },

        checkWishlistStatus: function() {
            const wishlistButtons = document.querySelectorAll('.wishlist-btn[data-product-id]');
            if (wishlistButtons.length === 0) return;

            // Only check if user is authenticated
            if (!this.isAuthenticated()) {
                return;
            }

            const productIds = Array.from(wishlistButtons).map(btn => btn.getAttribute('data-product-id'));

            fetch('/wishlist/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_ids: productIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.status) {
                    wishlistButtons.forEach(button => {
                        const productId = button.getAttribute('data-product-id');
                        const icon = button.querySelector('i');
                        
                        if (data.status[productId]) {
                            // Product is in wishlist
                            button.classList.add('in-wishlist');
                            if (icon) {
                                // Remove outline heart, add filled heart
                                icon.classList.remove('far', 'fa-heart');
                                icon.classList.add('fas', 'fa-heart');
                            }
                        } else {
                            // Product is not in wishlist
                            button.classList.remove('in-wishlist');
                            if (icon) {
                                // Remove filled heart, add outline heart
                                icon.classList.remove('fas', 'fa-heart');
                                icon.classList.add('far', 'fa-heart');
                            }
                        }
                    });
                }
            })
            .catch(error => {
                Utils.error('Error checking wishlist status:', error);
            });
        },

        loadWishlistCount: function() {
            fetch('/wishlist/count', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateCount(data.count);
                }
            })
            .catch(error => {
                Utils.error('Error loading wishlist count:', error);
            });
        },

        toggleSidebar: function() {
            const sidebar = document.getElementById('wishlist-sidebar');
            const overlay = document.getElementById('wishlist-sidebar-overlay');
            if (!sidebar) return;

            const isOpening = !sidebar.classList.contains('active');
            sidebar.classList.toggle('active');
            if (overlay) {
                overlay.classList.toggle('active');
            }

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
            const sidebar = document.getElementById('wishlist-sidebar');
            const overlay = document.getElementById('wishlist-sidebar-overlay');
            if (sidebar) sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    // Cart Module
    const Cart = {
        csrfToken: Utils.getCsrfToken(),

        init: function() {
            this.attachEventListeners();
            // Only load cart count if user is authenticated
            if (this.isAuthenticated()) {
                this.loadCartCount();
            } else {
                // Set count to 0 for unauthenticated users
                this.updateCount(0);
            }
        },

        isAuthenticated: function() {
            // Check if user is authenticated by checking body data attribute
            const body = document.body;
            if (body && body.hasAttribute('data-authenticated')) {
                return body.getAttribute('data-authenticated') === 'true';
            }
            // Fallback: check if user dropdown exists (authenticated) or login link exists (not authenticated)
            const userDropdown = document.getElementById('userDropdown');
            const loginLink = document.getElementById('loginLink');
            return userDropdown !== null && loginLink === null;
        },

        attachEventListeners: function() {
            document.addEventListener('click', (e) => {
                const addToCartBtn = e.target.closest('.add-to-cart, .product__add-cart');
                if (addToCartBtn && addToCartBtn.hasAttribute('data-product-id')) {
                    e.preventDefault();
                    const productId = addToCartBtn.getAttribute('data-product-id');
                    this.addToCart(productId, 1, addToCartBtn);
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

        addToCart: function(productId, quantity = 1, button = null) {
            Utils.setButtonLoading(button, true, 'fa-shopping-cart');

            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
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
                    if (data.cart_count !== undefined) {
                        this.updateCount(data.cart_count);
                    }
                    // Update cart status for all buttons
                    this.checkCartStatus();
                    Utils.showNotification('Product added to cart successfully!', 'success');
                    this.loadSidebar();
                    this.toggleSidebar();
                } else {
                    Utils.showNotification(data.message || 'Failed to add product to cart.', 'error');
                }
                Utils.setButtonLoading(button, false, 'fa-shopping-cart');
            })
            .catch(error => {
                Utils.error('Cart error:', error);
                Utils.showNotification('An error occurred. Please try again.', 'error');
                Utils.setButtonLoading(button, false, 'fa-shopping-cart');
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.loadSidebar();
                    if (data.cart_count !== undefined) {
                        this.updateCount(data.cart_count);
                    }
                }
            })
            .catch(error => {
                Utils.error('Error updating cart:', error);
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
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
                    // Update cart status for all buttons
                    this.checkCartStatus();
                    Utils.showNotification('Product removed from cart.', 'success');
                }
            })
            .catch(error => {
                Utils.error('Error removing from cart:', error);
                Utils.showNotification('An error occurred. Please try again.', 'error');
            });
        },

        loadSidebar: function() {
            const sidebarItems = document.getElementById('cart-sidebar-items');
            const sidebarEmpty = document.getElementById('cart-sidebar-empty');
            const sidebarSummary = document.getElementById('cart-sidebar-summary');

            if (sidebarItems) {
                sidebarItems.innerHTML = '<div class="cart-sidebar__loading" style="text-align: center; padding: 2rem;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #6c757d;"></i><p style="margin-top: 1rem; color: #6c757d;">Loading cart...</p></div>';
                sidebarItems.style.display = 'block';
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateSidebarFromHtml(data.html, data.total, data.count);
                }
            })
            .catch(error => {
                Utils.error('Error loading cart:', error);
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
            sidebarItems.style.display = 'block';
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
                headerBadge.style.display = 'absolute';
            }
        },

        checkCartStatus: function() {
            const cartButtons = document.querySelectorAll('.add-to-cart[data-product-id], .product__add-cart[data-product-id]');
            if (cartButtons.length === 0) return;

            // Only check if user is authenticated
            if (!this.isAuthenticated()) {
                return;
            }

            const productIds = Array.from(cartButtons).map(btn => btn.getAttribute('data-product-id'));

            fetch('/cart/check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_ids: productIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.status) {
                    cartButtons.forEach(button => {
                        const productId = button.getAttribute('data-product-id');
                        const icon = button.querySelector('i');
                        
                        if (data.status[productId]) {
                            // Product is in cart
                            button.classList.add('in-cart');
                            if (icon) {
                                // Change icon to checkmark or keep cart icon with different style
                                icon.classList.remove('fa-shopping-cart');
                                icon.classList.add('fa-check');
                            }
                        } else {
                            // Product is not in cart
                            button.classList.remove('in-cart');
                            if (icon) {
                                // Restore cart icon
                                icon.classList.remove('fa-check');
                                icon.classList.add('fa-shopping-cart');
                            }
                        }
                    });
                }
            })
            .catch(error => {
                Utils.error('Error checking cart status:', error);
            });
        },

        loadCartCount: function() {
            fetch('/cart/count', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // If user is not authenticated (401), set count to 0 silently
                if (response.status === 401) {
                    this.updateCount(0);
                    return null;
                }
                
                // Check if response is ok
                if (!response.ok) {
                    throw new Error('Failed to load cart count: ' + response.status);
                }
                
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    this.updateCount(data.count);
                } else if (data && data.message === 'Unauthenticated.') {
                    // Handle unauthenticated response
                    this.updateCount(0);
                } else if (!data) {
                    // Response was null (401 handled above)
                    return;
                }
            })
            .catch(error => {
                // Silently set count to 0 for any errors (including unauthenticated)
                // Don't log errors to console for unauthenticated users
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

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            Wishlist.init();
            Cart.init();
        });
    } else {
        Wishlist.init();
        Cart.init();
    }

    // Export to global scope
    window.WishlistFunctions = {
        addToWishlist: (productId, button) => Wishlist.addToWishlist(productId, button),
        removeFromWishlist: (productId, button) => Wishlist.removeFromWishlist(productId, button),
        toggleWishlist: (productId, button) => Wishlist.toggleWishlist(productId, button),
        loadWishlistSidebar: () => Wishlist.loadSidebar(),
        toggleWishlistSidebar: () => Wishlist.toggleSidebar(),
        closeWishlistSidebar: () => Wishlist.closeSidebar()
    };

    window.CartFunctions = {
        addToCart: (productId, quantity, button) => Cart.addToCart(productId, quantity, button),
        updateCartItem: (cartItemId, quantity) => Cart.updateCartItem(cartItemId, quantity),
        removeFromCart: (cartItemId, button) => Cart.removeFromCart(cartItemId, button),
        loadCartSidebar: () => Cart.loadSidebar(),
        toggleCartSidebar: () => Cart.toggleSidebar(),
        closeCartSidebar: () => Cart.closeSidebar(),
        updateCartCount: (count) => Cart.updateCount(count)
    };

})();
