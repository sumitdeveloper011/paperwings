/**
 * Order Actions Module
 * Handles reorder and cancel order functionality
 * 
 * @module OrderActions
 */
(function() {
    'use strict';

    const OrderActions = {
        csrfToken: null,
        reorderUrl: null,
        cancelUrl: null,

        /**
         * Initialize order actions module
         */
        init: function() {
            this.csrfToken = window.AppUtils ? window.AppUtils.getCsrfToken() : '';
            this.reorderUrl = '/account/orders/{orderNumber}/reorder';
            this.cancelUrl = '/account/orders/{orderNumber}/cancel';
            this.attachEventListeners();
        },

        /**
         * Attach event listeners for order action buttons
         */
        attachEventListeners: function() {
            document.addEventListener('click', (e) => {
                const actionBtn = e.target.closest('.order-action-btn');
                if (actionBtn) {
                    e.preventDefault();
                    const action = actionBtn.getAttribute('data-action');
                    const orderNumber = actionBtn.getAttribute('data-order-number');

                    if (!orderNumber) {
                        this.showError('Order number is missing. Please refresh the page.');
                        return;
                    }

                    if (action === 'reorder') {
                        this.handleReorder(orderNumber, actionBtn);
                    } else if (action === 'cancel') {
                        this.handleCancel(orderNumber, actionBtn);
                    }
                }
            });
        },

        /**
         * Handle reorder action
         * @param {string} orderNumber - Order number
         * @param {HTMLElement} button - Button element
         */
        handleReorder: async function(orderNumber, button) {
            if (!this.confirmAction('Are you sure you want to add all items from this order to your cart?')) {
                return;
            }

            this.setButtonLoading(button, true);

            try {
                const url = this.reorderUrl.replace('{orderNumber}', orderNumber);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to reorder items.');
                }

                if (data.success) {
                    this.showSuccess(data.message || 'Items added to cart successfully!');
                    
                    // Update cart count if available
                    if (data.data && data.data.cart_count !== undefined) {
                        this.updateCartCount(data.data.cart_count);
                    }

                    // Redirect to cart after short delay
                    if (data.data && data.data.redirect_url) {
                        setTimeout(() => {
                            window.location.href = data.data.redirect_url;
                        }, 1500);
                    } else {
                        setTimeout(() => {
                            window.location.href = '/cart';
                        }, 1500);
                    }
                } else {
                    throw new Error(data.message || 'Failed to reorder items.');
                }

            } catch (error) {
                console.error('Reorder error:', error);
                this.showError(error.message || 'Failed to reorder items. Please try again.');
                this.setButtonLoading(button, false);
            }
        },

        /**
         * Handle cancel order action
         * @param {string} orderNumber - Order number
         * @param {HTMLElement} button - Button element
         */
        handleCancel: async function(orderNumber, button) {
            const orderStatus = button.getAttribute('data-order-status') || 'pending';
            
            if (!this.confirmAction('Are you sure you want to cancel this order? This action cannot be undone.')) {
                return;
            }

            this.setButtonLoading(button, true);

            try {
                const url = this.cancelUrl.replace('{orderNumber}', orderNumber);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to cancel order.');
                }

                if (data.success) {
                    this.showSuccess(data.message || 'Order cancelled successfully!');
                    
                    // Reload page after short delay to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Failed to cancel order.');
                }

            } catch (error) {
                console.error('Cancel order error:', error);
                this.showError(error.message || 'Failed to cancel order. Please try again.');
                this.setButtonLoading(button, false);
            }
        },

        /**
         * Show confirmation dialog
         * @param {string} message - Confirmation message
         * @returns {boolean} Whether user confirmed
         */
        confirmAction: function(message) {
            if (window.customConfirm) {
                return window.customConfirm(message);
            }
            return window.confirm(message);
        },

        /**
         * Show success message
         * @param {string} message - Success message
         */
        showSuccess: function(message) {
            if (typeof showToast !== 'undefined') {
                showToast(message, 'success');
            } else if (window.AppUtils && typeof window.AppUtils.showNotification === 'function') {
                window.AppUtils.showNotification(message, 'success');
            } else if (window.customAlert) {
                window.customAlert(message);
            } else {
                alert(message);
            }
        },

        /**
         * Show error message
         * @param {string} message - Error message
         */
        showError: function(message) {
            if (typeof showToast !== 'undefined') {
                showToast(message, 'error');
            } else if (window.AppUtils && typeof window.AppUtils.showNotification === 'function') {
                window.AppUtils.showNotification(message, 'error');
            } else if (window.customAlert) {
                window.customAlert(message);
            } else {
                alert(message);
            }
        },

        /**
         * Set button loading state
         * @param {HTMLElement} button - Button element
         * @param {boolean} loading - Whether button is loading
         */
        setButtonLoading: function(button, loading) {
            if (!button) return;

            if (loading) {
                button.disabled = true;
                button.setAttribute('data-original-text', button.innerHTML);
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            } else {
                button.disabled = false;
                const originalText = button.getAttribute('data-original-text');
                if (originalText) {
                    button.innerHTML = originalText;
                    button.removeAttribute('data-original-text');
                }
            }
        },

        /**
         * Update cart count in header
         * @param {number} count - Cart count
         */
        updateCartCount: function(count) {
            const cartBadge = document.getElementById('cart-header-badge');
            if (cartBadge) {
                cartBadge.textContent = count;
                cartBadge.style.display = count > 0 ? 'inline-block' : 'none';
            }

            // Also update via cart module if available
            if (window.Cart && typeof window.Cart.updateCount === 'function') {
                window.Cart.updateCount(count);
            }
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            OrderActions.init();
        });
    } else {
        OrderActions.init();
    }

    // Export to global scope
    window.OrderActions = OrderActions;
})();
