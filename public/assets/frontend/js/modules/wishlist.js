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
                    const productUuid = wishlistBtn.getAttribute('data-product-uuid');
                    if (productUuid) {
                        this.toggleWishlist(productUuid, wishlistBtn);
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

                // Bulk actions - use event delegation
                const addSelectedBtn = e.target.closest('#wishlist-add-selected-to-cart');
                if (addSelectedBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!addSelectedBtn.disabled && !this._addingToCart) {
                        this.addSelectedToCart();
                    }
                }

                const removeSelectedBtn = e.target.closest('#wishlist-remove-selected');
                if (removeSelectedBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!removeSelectedBtn.disabled && !this._removingFromWishlist) {
                        this.removeSelected();
                    }
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeSidebar();
                }
            });
        },

        addToWishlist: function(productUuid, button) {
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
                body: JSON.stringify({ product_uuid: productUuid })
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
                    if (data.product && window.Analytics) {
                        window.Analytics.trackAddToWishlist({
                            id: data.product.id,
                            name: data.product.name,
                            category: data.product.category || '',
                            brand: data.product.brand || '',
                            price: data.product.price || 0
                        });
                    }

                    if (window.AppUtils) {
                        window.AppUtils.setButtonLoading(button, false, 'fa-heart');
                    }
                    if (button) {
                        button.classList.add('in-wishlist');
                        button.setAttribute('title', 'Remove from Wishlist');
                        button.setAttribute('aria-label', 'Remove from Wishlist');
                        const icon = button.querySelector('i');
                        if (icon) {
                            icon.classList.remove('far', 'fa-heart');
                            icon.classList.add('fas', 'fa-heart');
                        }
                    }
                    if (data.wishlist_count !== undefined) {
                        this.updateCount(data.wishlist_count);
                    }
                    if (typeof showToast !== 'undefined') {
                        showToast('Product added to wishlist!', 'success');
                    } else if (window.AppUtils) {
                        window.AppUtils.showNotification('Product added to wishlist!', 'success');
                    }
                    this.loadSidebar();
                    this.toggleSidebar();
                } else {
                    if (window.AppUtils) {
                        window.AppUtils.setButtonLoading(button, false, 'fa-heart');
                    }
                    if (typeof showToast !== 'undefined') {
                        showToast(data.message || 'Failed to add product to wishlist.', 'error');
                    } else if (window.AppUtils) {
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
                    if (typeof showToast !== 'undefined') {
                        showToast('An error occurred. Please try again.', 'error');
                    } else {
                        window.AppUtils.showNotification('An error occurred. Please try again.', 'error');
                    }
                    window.AppUtils.setButtonLoading(button, false, 'fa-heart');
                }
            });
        },

        removeFromWishlist: function(productUuid, button, silent = false) {
            // Use batch endpoint even for single item (consistent approach)
            // productUuid can be a single UUID string or an array of UUIDs
            const productUuids = Array.isArray(productUuid) ? productUuid : [productUuid];
            const isSingleItem = !Array.isArray(productUuid);
            
            if (button && window.AppUtils) {
                window.AppUtils.setButtonLoading(button, true, 'fa-heart');
            }

            return fetch('/wishlist/remove-multiple', {
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
                if (data.success !== undefined) {
                    if (button && window.AppUtils) {
                        window.AppUtils.setButtonLoading(button, false, 'fa-heart');
                    }

                    const successCount = data.results?.success?.length || 0;
                    const failedCount = data.results?.failed?.length || 0;

                    // Update wishlist count
                    if (data.wishlist_count !== undefined) {
                        this.updateCount(data.wishlist_count);
                    }

                    // Update wishlist buttons for successfully removed products
                    if (successCount > 0 && data.results?.success) {
                        data.results.success.forEach(item => {
                            if (item.uuid) {
                                const allWishlistButtons = document.querySelectorAll(`.wishlist-btn[data-product-uuid="${item.uuid}"]`);
                                allWishlistButtons.forEach(btn => {
                                    btn.classList.remove('in-wishlist');
                                    btn.setAttribute('title', 'Add to Wishlist');
                                    btn.setAttribute('aria-label', 'Add to Wishlist');
                                    const btnIcon = btn.querySelector('i');
                                    if (btnIcon) {
                                        btnIcon.classList.remove('fas', 'fa-heart');
                                        btnIcon.classList.add('far', 'fa-heart');
                                    }
                                });

                                // Remove from sidebar
                                const wishlistItem = document.querySelector(`.wishlist-sidebar-item[data-product-uuid="${item.uuid}"]`);
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
                                            this.updateBulkActionsVisibility();
                                        } else {
                                            this.updateBulkActionsState();
                                        }
                                    }, 300);
                                }
                            }
                        });
                    }

                    if (!silent) {
                        if (typeof showToast !== 'undefined') {
                            if (successCount > 0) {
                                const message = isSingleItem && successCount === 1
                                    ? 'Product removed from wishlist!' 
                                    : `${successCount} item(s) removed from wishlist!`;
                                showToast(message, 'success');
                            }
                            if (failedCount > 0) {
                                const firstError = data.results.failed[0];
                                const errorMsg = firstError?.message || `${failedCount} item(s) failed to remove.`;
                                showToast(errorMsg, 'error');
                            }
                        } else if (window.AppUtils) {
                            if (successCount > 0) {
                                const message = isSingleItem && successCount === 1
                                    ? 'Product removed from wishlist!' 
                                    : `${successCount} item(s) removed from wishlist!`;
                                window.AppUtils.showNotification(message, 'success');
                            }
                        }
                    }
                    if (!silent && isSingleItem) {
                        // Only reload sidebar for single item removal (bulk removal handles it separately)
                        this.loadSidebar();
                    }
                    return data;
                } else {
                    if (button && window.AppUtils) {
                        window.AppUtils.setButtonLoading(button, false, 'fa-heart');
                    }
                    if (!silent) {
                        if (typeof showToast !== 'undefined') {
                            showToast(data.message || 'Failed to remove product from wishlist.', 'error');
                        } else if (window.AppUtils) {
                            window.AppUtils.showNotification(data.message || 'Failed to remove product from wishlist.', 'error');
                        }
                    }
                    return Promise.reject(data.message || 'Failed to remove');
                }
            })
            .catch(error => {
                if (window.AppUtils) {
                    window.AppUtils.error('Wishlist error:', error);
                }
                if (button && window.AppUtils) {
                    window.AppUtils.setButtonLoading(button, false, 'fa-heart');
                }
                if (!silent) {
                    if (typeof showToast !== 'undefined') {
                        showToast('An error occurred. Please try again.', 'error');
                    } else if (window.AppUtils) {
                        window.AppUtils.showNotification('An error occurred. Please try again.', 'error');
                    }
                }
                return Promise.reject(error);
            });
        },

        toggleWishlist: function(productUuid, button) {
            if (button && button.classList.contains('in-wishlist')) {
                this.removeFromWishlist(productUuid, button);
            } else {
                this.addToWishlist(productUuid, button);
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
                if (!response.ok && response.status === 401) {
                    // Handle 401 gracefully - show empty wishlist
                    this.updateSidebarEmpty();
                    return Promise.reject('Not authenticated');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.updateSidebarFromHtml(data.html, data.count || 0);
                } else if (data.requires_login) {
                    // Show empty state for unauthenticated users
                    this.updateSidebarEmpty();
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
                    window.AppUtils.error('Error loading wishlist:', error);
                }
                if (sidebarItems) {
                    sidebarItems.innerHTML = '<div class="wishlist-sidebar__error" style="text-align: center; padding: 2rem; color: #dc3545;"><p>Error loading wishlist. Please try again.</p></div>';
                }
            });
        },

        updateSidebarEmpty: function() {
            const sidebarItems = document.getElementById('wishlist-sidebar-items');
            const sidebarEmpty = document.getElementById('wishlist-sidebar-empty');

            if (!sidebarItems || !sidebarEmpty) return;

            this.updateCount(0);
            sidebarItems.style.display = 'none';
            sidebarEmpty.style.display = 'flex';
            sidebarItems.innerHTML = '';
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
                this.updateBulkActionsVisibility();
                return;
            }

            sidebarEmpty.style.display = 'none';
            sidebarItems.style.display = 'flex';
            sidebarItems.innerHTML = html;

            document.querySelectorAll('.wishlist-sidebar-item__remove').forEach(button => {
                button.addEventListener('click', () => {
                    const productUuid = button.getAttribute('data-product-uuid');
                    const item = button.closest('.wishlist-sidebar-item');
                    this.removeFromWishlist(productUuid, button);
                    if (item && typeof $ !== 'undefined') {
                        $(item).fadeOut(300, function() {
                            $(this).remove();
                            const remainingItems = document.querySelectorAll('.wishlist-sidebar-item');
                            if (remainingItems.length === 0) {
                                sidebarItems.style.display = 'none';
                                sidebarEmpty.style.display = 'flex';
                                Wishlist.updateCount(0);
                                Wishlist.updateBulkActionsVisibility();
                            } else {
                                Wishlist.updateBulkActionsState();
                            }
                        });
                    }
                });
            });

            document.querySelectorAll('.wishlist-sidebar-item__add-cart').forEach(button => {
                button.addEventListener('click', () => {
                    const productUuid = button.getAttribute('data-product-uuid');
                    if (window.CartFunctions && typeof window.CartFunctions.addToCart === 'function') {
                        window.CartFunctions.addToCart(productUuid, 1, button);
                    } else if (window.AppUtils) {
                        window.AppUtils.error('CartFunctions.addToCart is not available');
                    }
                });
            });

            // Initialize bulk actions
            this.initBulkActions();
            this.updateBulkActionsVisibility();
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
            const wishlistButtons = document.querySelectorAll('.wishlist-btn[data-product-uuid]');
            if (wishlistButtons.length === 0) return;

            if (!this.isAuthenticated()) {
                return;
            }

            const productUuids = Array.from(wishlistButtons)
                .map(btn => btn.getAttribute('data-product-uuid'))
                .filter(uuid => uuid && uuid.trim() !== '');

            if (productUuids.length === 0) {
                return;
            }

            fetch('/wishlist/check', {
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
                    // User not authenticated, don't show wishlist status
                    return Promise.reject('Not authenticated');
                }
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.status) {
                    wishlistButtons.forEach(button => {
                        const productUuid = button.getAttribute('data-product-uuid');
                        if (!productUuid) return; // Skip if no UUID
                        
                        const icon = button.querySelector('i');

                        if (data.status[productUuid]) {
                            button.classList.add('in-wishlist');
                            button.setAttribute('title', 'Remove from Wishlist');
                            button.setAttribute('aria-label', 'Remove from Wishlist');
                            if (icon) {
                                icon.classList.remove('far', 'fa-heart');
                                icon.classList.add('fas', 'fa-heart');
                            }
                        } else {
                            button.classList.remove('in-wishlist');
                            button.setAttribute('title', 'Add to Wishlist');
                            button.setAttribute('aria-label', 'Add to Wishlist');
                            if (icon) {
                                icon.classList.remove('fas', 'fa-heart');
                                icon.classList.add('far', 'fa-heart');
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
        },

        initBulkActions: function() {
            const selectAllCheckbox = document.getElementById('wishlist-select-all');

            // Remove old listener if it exists
            if (this._selectAllHandler && selectAllCheckbox) {
                selectAllCheckbox.removeEventListener('change', this._selectAllHandler);
            }

            // Select All checkbox
            if (selectAllCheckbox) {
                this._selectAllHandler = (e) => {
                    const isChecked = e.target.checked;
                    document.querySelectorAll('.wishlist-item-checkbox').forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                    this.updateBulkActionsState();
                };
                selectAllCheckbox.addEventListener('change', this._selectAllHandler);
            }

            // Individual checkbox change - use event delegation (only one listener)
            if (!this._checkboxChangeHandler) {
                this._checkboxChangeHandler = (e) => {
                    if (e.target.classList.contains('wishlist-item-checkbox')) {
                        this.updateBulkActionsState();
                    }
                };
                document.addEventListener('change', this._checkboxChangeHandler);
            }

            // Bulk action buttons are now handled via event delegation in attachEventListeners
            // No need to attach listeners here - they're handled globally
        },

        getSelectedItems: function() {
            const checkboxes = document.querySelectorAll('.wishlist-item-checkbox:checked');
            return Array.from(checkboxes).map(checkbox => checkbox.getAttribute('data-product-uuid'));
        },

        updateBulkActionsState: function() {
            const selectedCount = this.getSelectedItems().length;
            const addSelectedBtn = document.getElementById('wishlist-add-selected-to-cart');
            const removeSelectedBtn = document.getElementById('wishlist-remove-selected');
            const selectAllCheckbox = document.getElementById('wishlist-select-all');

            // Enable/disable buttons based on selection
            if (addSelectedBtn) {
                addSelectedBtn.disabled = selectedCount === 0;
            }
            if (removeSelectedBtn) {
                removeSelectedBtn.disabled = selectedCount === 0;
            }

            // Update select all checkbox state
            if (selectAllCheckbox) {
                const totalCheckboxes = document.querySelectorAll('.wishlist-item-checkbox').length;
                selectAllCheckbox.checked = totalCheckboxes > 0 && selectedCount === totalCheckboxes;
                selectAllCheckbox.indeterminate = selectedCount > 0 && selectedCount < totalCheckboxes;
            }
        },

        updateBulkActionsVisibility: function() {
            const footer = document.getElementById('wishlist-sidebar-footer');
            const sidebarItems = document.getElementById('wishlist-sidebar-items');
            const sidebarEmpty = document.getElementById('wishlist-sidebar-empty');

            if (footer && sidebarItems && sidebarEmpty) {
                const hasItems = sidebarItems.style.display !== 'none' && 
                                document.querySelectorAll('.wishlist-sidebar-item').length > 0;
                footer.style.display = hasItems ? 'block' : 'none';
            }
        },

        addSelectedToCart: function() {
            const addSelectedBtn = document.getElementById('wishlist-add-selected-to-cart');
            
            // Prevent multiple simultaneous calls - use a flag
            if (this._addingToCart) {
                return;
            }
            
            // Prevent multiple simultaneous calls
            if (addSelectedBtn && addSelectedBtn.disabled) {
                return;
            }
            
            const selectedUuids = this.getSelectedItems();
            if (selectedUuids.length === 0) {
                if (typeof showToast !== 'undefined') {
                    showToast('Please select items to add to cart.', 'warning');
                }
                return;
            }

            // Set flag to prevent multiple calls
            this._addingToCart = true;

            if (addSelectedBtn) {
                addSelectedBtn.disabled = true;
                addSelectedBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Adding...</span>';
            }

            // Batch approach: Send all UUIDs in a single API call
            // This is faster, cleaner, and prevents console errors
            fetch('/cart/add-multiple', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_uuids: selectedUuids,
                    quantities: selectedUuids.map(() => 1) // All quantities are 1
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
                // Clear flag
                this._addingToCart = false;
                
                if (addSelectedBtn) {
                    addSelectedBtn.disabled = false;
                    addSelectedBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> <span class="btn-text">Add to Cart</span>';
                }

                if (data.success !== undefined) {
                    const successCount = data.results?.success?.length || 0;
                    const failedCount = data.results?.failed?.length || 0;

                    // Remove successfully added items from wishlist
                    if (successCount > 0 && data.results?.success) {
                        const successUuids = data.results.success
                            .map(item => item.uuid)
                            .filter(uuid => uuid);
                        
                        // Remove from wishlist in batch
                        successUuids.forEach((uuid, index) => {
                            setTimeout(() => {
                                this.removeFromWishlist(uuid, null, true);
                            }, index * 50); // Small delay to prevent overload
                        });
                    }

                    // Update cart count
                    if (data.cart_count !== undefined && window.CartFunctions) {
                        if (typeof window.CartFunctions.updateCartCount === 'function') {
                            window.CartFunctions.updateCartCount(data.cart_count);
                        } else if (typeof window.CartFunctions.loadCartCount === 'function') {
                            window.CartFunctions.loadCartCount();
                        }
                    }

                    // Show toast messages
                    if (typeof showToast !== 'undefined') {
                        if (successCount > 0) {
                            showToast(`${successCount} item(s) added to cart successfully.`, 'success');
                        }
                        if (failedCount > 0) {
                            // Show first error message or generic message
                            const firstError = data.results.failed[0];
                            const errorMsg = firstError?.message || `${failedCount} item(s) failed to add.`;
                            showToast(errorMsg, 'error');
                        }
                    }

                    setTimeout(() => {
                        this.loadSidebar();
                        this.updateBulkActionsState();
                        // Update cart status
                        if (window.CartFunctions && typeof window.CartFunctions.checkCartStatus === 'function') {
                            window.CartFunctions.checkCartStatus();
                        }
                    }, 500);
                } else {
                    // Fallback error handling
                    if (typeof showToast !== 'undefined') {
                        showToast(data.message || 'Failed to add items to cart.', 'error');
                    }
                }
            })
            .catch(error => {
                // Clear flag on error
                this._addingToCart = false;
                
                if (addSelectedBtn) {
                    addSelectedBtn.disabled = false;
                    addSelectedBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> <span class="btn-text">Add to Cart</span>';
                }

                // Don't show errors for authentication failures
                if (error === 'Not authenticated') {
                    return;
                }

                // Show error message
                if (typeof showToast !== 'undefined') {
                    const errorMsg = (error && typeof error === 'object' && error.message) 
                        ? error.message 
                        : 'An error occurred. Please try again.';
                    showToast(errorMsg, 'error');
                }
            });
        },

        removeSelected: function() {
            const removeSelectedBtn = document.getElementById('wishlist-remove-selected');
            
            // Prevent multiple simultaneous calls - use a flag
            if (this._removingFromWishlist) {
                return;
            }
            
            // Prevent multiple simultaneous calls
            if (removeSelectedBtn && removeSelectedBtn.disabled) {
                return;
            }
            
            const selectedUuids = this.getSelectedItems();
            if (selectedUuids.length === 0) {
                if (typeof showToast !== 'undefined') {
                    showToast('Please select items to remove.', 'warning');
                }
                return;
            }

            if (!confirm(`Are you sure you want to remove ${selectedUuids.length} item(s) from your wishlist?`)) {
                return;
            }

            // Set flag to prevent multiple calls
            this._removingFromWishlist = true;

            if (removeSelectedBtn) {
                removeSelectedBtn.disabled = true;
                removeSelectedBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Removing...</span>';
            }

            // Batch approach: Send all UUIDs in a single API call
            // This is faster, cleaner, and prevents console errors
            fetch('/wishlist/remove-multiple', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_uuids: selectedUuids
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
                // Clear flag
                this._removingFromWishlist = false;
                
                if (removeSelectedBtn) {
                    removeSelectedBtn.disabled = false;
                    removeSelectedBtn.innerHTML = '<i class="fas fa-trash"></i> <span class="btn-text">Remove</span>';
                }

                if (data.success !== undefined) {
                    const successCount = data.results?.success?.length || 0;
                    const failedCount = data.results?.failed?.length || 0;

                    // Update wishlist count
                    if (data.wishlist_count !== undefined) {
                        this.updateCount(data.wishlist_count);
                    }

                    // Remove successfully removed items from sidebar
                    if (successCount > 0 && data.results?.success) {
                        data.results.success.forEach(item => {
                            if (item.uuid) {
                                const wishlistItem = document.querySelector(`.wishlist-sidebar-item[data-product-uuid="${item.uuid}"]`);
                                if (wishlistItem) {
                                    wishlistItem.style.transition = 'opacity 0.3s';
                                    wishlistItem.style.opacity = '0';
                                    setTimeout(() => {
                                        wishlistItem.remove();
                                    }, 300);
                                }

                                // Update wishlist buttons
                                const allWishlistButtons = document.querySelectorAll(`.wishlist-btn[data-product-uuid="${item.uuid}"]`);
                                allWishlistButtons.forEach(btn => {
                                    btn.classList.remove('in-wishlist');
                                    btn.setAttribute('title', 'Add to Wishlist');
                                    btn.setAttribute('aria-label', 'Add to Wishlist');
                                    const btnIcon = btn.querySelector('i');
                                    if (btnIcon) {
                                        btnIcon.classList.remove('fas', 'fa-heart');
                                        btnIcon.classList.add('far', 'fa-heart');
                                    }
                                });
                            }
                        });

                        // Check if sidebar is empty
                        setTimeout(() => {
                            const remainingItems = document.querySelectorAll('.wishlist-sidebar-item');
                            if (remainingItems.length === 0) {
                                const sidebarItems = document.getElementById('wishlist-sidebar-items');
                                const sidebarEmpty = document.getElementById('wishlist-sidebar-empty');
                                if (sidebarItems) sidebarItems.style.display = 'none';
                                if (sidebarEmpty) sidebarEmpty.style.display = 'flex';
                                this.updateBulkActionsVisibility();
                            } else {
                                this.updateBulkActionsState();
                            }
                        }, 350);
                    }

                    // Show toast messages
                    if (typeof showToast !== 'undefined') {
                        if (successCount > 0) {
                            showToast(`${successCount} item(s) removed from wishlist successfully.`, 'success');
                        }
                        if (failedCount > 0) {
                            // Show first error message or generic message
                            const firstError = data.results.failed[0];
                            const errorMsg = firstError?.message || `${failedCount} item(s) failed to remove.`;
                            showToast(errorMsg, 'error');
                        }
                    }

                    setTimeout(() => {
                        this.loadSidebar();
                        this.updateBulkActionsState();
                        // Update wishlist status
                        if (typeof this.checkWishlistStatus === 'function') {
                            this.checkWishlistStatus();
                        }
                    }, 500);
                } else {
                    // Fallback error handling
                    if (typeof showToast !== 'undefined') {
                        showToast(data.message || 'Failed to remove items from wishlist.', 'error');
                    }
                }
            })
            .catch(error => {
                // Clear flag on error
                this._removingFromWishlist = false;
                
                if (removeSelectedBtn) {
                    removeSelectedBtn.disabled = false;
                    removeSelectedBtn.innerHTML = '<i class="fas fa-trash"></i> <span class="btn-text">Remove</span>';
                }

                // Don't show errors for authentication failures
                if (error === 'Not authenticated') {
                    return;
                }

                // Show error message
                if (typeof showToast !== 'undefined') {
                    const errorMsg = (error && typeof error === 'object' && error.message) 
                        ? error.message 
                        : 'An error occurred. Please try again.';
                    showToast(errorMsg, 'error');
                }
            });
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

