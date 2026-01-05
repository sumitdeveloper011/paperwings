/**
 * Wishlist Module
 * Handles all wishlist functionality
 */
(function() {
    'use strict';

    const Wishlist = {
        csrfToken: null,

        init: function() {
            this.csrfToken = window.AppUtils ? window.AppUtils.getCsrfToken() : '';
            this.attachEventListeners();
            if (this.isAuthenticated()) {
                this.checkWishlistStatus();
                this.loadWishlistCount();
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
                const wishlistBtn = e.target.closest('.wishlist-btn');
                if (wishlistBtn) {
                    e.preventDefault();
                    const productId = wishlistBtn.getAttribute('data-product-id');
                    if (productId) {
                        this.toggleWishlist(productId, wishlistBtn);
                    }
                }

                const wishlistTrigger = e.target.closest('#wishlist-trigger') || e.target.closest('.wishlist-trigger');
                if (wishlistTrigger) {
                    e.preventDefault();
                    this.toggleSidebar();
                }

                if (e.target.closest('#wishlist-sidebar-close') || e.target.closest('.wishlist-sidebar__close')) {
                    e.preventDefault();
                    this.closeSidebar();
                }

                if (e.target.id === 'wishlist-sidebar-overlay' || e.target.classList.contains('wishlist-sidebar-overlay')) {
                    this.closeSidebar();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeSidebar();
                }
            });
        },

        addToWishlist: function(productId, button) {
            if (window.AppUtils) {
                window.AppUtils.setButtonLoading(button, true, 'fa-heart');
            }

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
            .then(response => {
                // Handle authentication errors
                if (response.status === 401) {
                    // Stop button loading
                    if (window.AppUtils) {
                        window.AppUtils.setButtonLoading(button, false, 'fa-heart');
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

                if (window.AppUtils) {
                    return window.AppUtils.handleApiResponse(response);
                }
                return response.json();
            })
            .then(data => {
                // Skip processing if authentication was required
                if (data && data.auth_required) {
                    return;
                }

                if (data.requires_login || (data.success === false && data.message && data.message.toLowerCase().includes('login'))) {
                    const redirectUrl = data.redirect_url || '/login';
                    const currentUrl = window.location.href;
                    window.location.href = redirectUrl + (redirectUrl.includes('?') ? '&' : '?') + 'intended=' + encodeURIComponent(currentUrl);
                    return;
                }

                if (data.success) {
                    if (window.AppUtils) {
                        window.AppUtils.setButtonLoading(button, false, 'fa-heart');
                    }
                    if (button) {
                        button.classList.add('in-wishlist');
                    }
                    if (data.wishlist_count !== undefined) {
                        this.updateCount(data.wishlist_count);
                    }
                    if (window.AppUtils) {
                        window.AppUtils.showNotification('Product added to wishlist!', 'success');
                    }
                    this.loadSidebar();
                    this.toggleSidebar();
                } else {
                    if (window.AppUtils) {
                        window.AppUtils.setButtonLoading(button, false, 'fa-heart');
                        window.AppUtils.showNotification(data.message || 'Failed to add product to wishlist.', 'error');
                    }
                }
            })
            .catch(error => {
                // Don't show errors for authentication failures (they're expected)
                if (error === 'Not authenticated' || (error && error.message && error.message.includes('authenticated'))) {
                    if (window.AppUtils) {
                        window.AppUtils.setButtonLoading(button, false, 'fa-heart');
                    }
                    return;
                }

                // Only log/show errors for unexpected failures
                if (window.AppUtils) {
                    window.AppUtils.error('Wishlist error:', error);
                    window.AppUtils.showNotification('An error occurred. Please try again.', 'error');
                    window.AppUtils.setButtonLoading(button, false, 'fa-heart');
                }
            });
        },

        removeFromWishlist: function(productId, button, silent = false) {
            if (button && window.AppUtils) {
                window.AppUtils.setButtonLoading(button, true, 'fa-heart');
            }

            fetch('/wishlist/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
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
                    if (button && window.AppUtils) {
                        window.AppUtils.setButtonLoading(button, false, 'fa-heart');
                        button.classList.remove('in-wishlist');
                    }
                    if (data.wishlist_count !== undefined) {
                        this.updateCount(data.wishlist_count);
                    }

                    const wishlistItem = document.querySelector(`.wishlist-sidebar-item[data-product-id="${productId}"]`);
                    if (wishlistItem) {
                        wishlistItem.style.transition = 'opacity 0.3s';
                        wishlistItem.style.opacity = '0';
                        setTimeout(() => {
                            wishlistItem.remove();
                            const remainingItems = document.querySelectorAll('.wishlist-sidebar-item');
                            if (remainingItems.length === 0) {
                                const sidebarItems = document.getElementById('wishlist-sidebar-items');
                                const sidebarEmpty = document.getElementById('wishlist-sidebar-empty');
                                if (sidebarItems) sidebarItems.style.display = 'none';
                                if (sidebarEmpty) sidebarEmpty.style.display = 'flex';
                            }
                        }, 300);
                    }

                    if (!silent && window.AppUtils) {
                        window.AppUtils.showNotification('Product removed from wishlist!', 'success');
                    }
                    this.loadSidebar();
                } else {
                    if (button && window.AppUtils) {
                        window.AppUtils.setButtonLoading(button, false, 'fa-heart');
                    }
                    if (!silent && window.AppUtils) {
                        window.AppUtils.showNotification(data.message || 'Failed to remove product from wishlist.', 'error');
                    }
                }
            })
            .catch(error => {
                if (window.AppUtils) {
                    window.AppUtils.error('Wishlist error:', error);
                }
                if (button && window.AppUtils) {
                    window.AppUtils.setButtonLoading(button, false, 'fa-heart');
                }
                if (!silent && window.AppUtils) {
                    window.AppUtils.showNotification('An error occurred. Please try again.', 'error');
                }
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
            .then(response => {
                if (response.status === 401) {
                    this.updateSidebarEmpty();
                    return Promise.reject('Not authenticated');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.updateSidebarFromHtml(data.html, data.count);
                } else if (data.requires_login) {
                    if (window.AppUtils) {
                        window.AppUtils.redirectToLogin();
                    }
                }
            })
            .catch(error => {
                if (window.AppUtils) {
                    window.AppUtils.error('Error loading wishlist:', error);
                }
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
                    } else if (window.AppUtils) {
                        window.AppUtils.error('CartFunctions.addToCart is not available');
                    }
                });
            });
        },

        updateCount: function(count) {
            const countBadge = document.getElementById('wishlist-count');
            if (countBadge) {
                countBadge.textContent = count;
                // Show badge even if count is 0 (for consistency)
                countBadge.style.display = count > 0 ? 'absolute' : 'absolute';
            }

            const headerBadge = document.getElementById('wishlist-header-badge');
            if (headerBadge) {
                headerBadge.textContent = count;
                // Show badge only if count > 0
                headerBadge.style.display = count > 0 ? 'absolute' : 'absolute';
            }
        },

        checkWishlistStatus: function() {
            const wishlistButtons = document.querySelectorAll('.wishlist-btn[data-product-id]');
            if (wishlistButtons.length === 0) return;

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
            .then(response => {
                if (response.status === 401) {
                    // User not authenticated, don't show wishlist status
                    return Promise.reject('Not authenticated');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.status) {
                    wishlistButtons.forEach(button => {
                        const productId = button.getAttribute('data-product-id');
                        const icon = button.querySelector('i');

                        if (data.status[productId]) {
                            button.classList.add('in-wishlist');
                            if (icon) {
                                icon.classList.remove('far', 'fa-heart');
                                icon.classList.add('fas', 'fa-heart');
                            }
                        } else {
                            button.classList.remove('in-wishlist');
                            if (icon) {
                                icon.classList.remove('fas', 'fa-heart');
                                icon.classList.add('far', 'fa-heart');
                            }
                        }
                    });
                }
            })
            .catch(error => {
                if (window.AppUtils) {
                    window.AppUtils.error('Error checking wishlist status:', error);
                }
            });
        },

        loadWishlistCount: function() {
            // Don't call API if not authenticated
            if (!this.isAuthenticated()) {
                this.updateCount(0);
                return;
            }

            fetch('/wishlist/count', {
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
                    throw new Error('Failed to load wishlist count: ' + response.status);
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
                    window.AppUtils.error('Error loading wishlist count:', error);
                }
                this.updateCount(0);
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

    // Export to global scope
    window.WishlistModule = Wishlist;

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            Wishlist.init();
        });
    } else {
        Wishlist.init();
    }
})();

