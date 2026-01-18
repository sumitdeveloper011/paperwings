/**
 * Product Add to Cart Module
 * Handles add to cart functionality on product detail page
 * 
 * @module AddToCart
 */
(function() {
    'use strict';

    /**
     * Initialize add to cart functionality
     */
    function initAddToCart() {
        const addToCartBtn = document.getElementById('addToCartBtn');
        const quantityInput = document.getElementById('quantity');

        if (!addToCartBtn) {
            return;
        }

        /**
         * Reset button to normal state
         * @param {HTMLElement} button - Button element
         * @param {HTMLElement} btnText - Button text element
         * @param {HTMLElement} btnIcon - Button icon element
         */
        function resetButton(button, btnText, btnIcon) {
            button.disabled = false;
            if (btnText) btnText.textContent = 'Add to Cart';
            if (btnIcon) {
                btnIcon.classList.remove('fa-spinner', 'fa-spin');
                btnIcon.classList.add('fa-shopping-cart');
            }
        }

        /**
         * Set button to loading state
         * @param {HTMLElement} button - Button element
         * @param {HTMLElement} btnText - Button text element
         * @param {HTMLElement} btnIcon - Button icon element
         */
        function setButtonLoading(button, btnText, btnIcon) {
            button.disabled = true;
            if (btnText) btnText.textContent = 'Adding...';
            if (btnIcon) {
                btnIcon.classList.remove('fa-shopping-cart');
                btnIcon.classList.add('fa-spinner', 'fa-spin');
            }
        }

        addToCartBtn.addEventListener('click', async function() {
            const productUuid = this.getAttribute('data-product-uuid');
            const quantity = parseInt(quantityInput?.value) || 1;
            const btnText = this.querySelector('.btn-text');
            const btnIcon = this.querySelector('i');

            // Validate product UUID
            if (!productUuid) {
                if (window.AjaxUtils) {
                    window.AjaxUtils.showMessage('Product UUID is missing. Please refresh the page.', 'error');
                } else if (window.AppUtils) {
                    window.AppUtils.showNotification('Product UUID is missing. Please refresh the page.', 'error');
                }
                return;
            }

            // Set loading state
            setButtonLoading(this, btnText, btnIcon);

            try {
                // Use AjaxUtils if available, fallback to direct fetch
                const data = window.AjaxUtils 
                    ? await window.AjaxUtils.post('/cart/add', {
                        product_uuid: productUuid,
                        quantity: quantity
                    }, { showMessage: false }) // We'll show custom messages
                    : await fetch('/cart/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_uuid: productUuid,
                            quantity: quantity
                        })
                    }).then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                return Promise.reject({
                                    message: errorData.message || 'Failed to add product to cart.',
                                    isApiError: true,
                                    status: response.status
                                });
                            });
                        }
                        return response.json();
                    });

                if (data.success) {
                    // Show success message
                    if (window.AjaxUtils) {
                        window.AjaxUtils.showMessage('Product added to cart successfully!', 'success');
                    } else if (window.AppUtils) {
                        window.AppUtils.showNotification('Product added to cart successfully!', 'success');
                    }

                    // Update cart count in header
                    const cartCount = data.data?.cart_count ?? data.cart_count;
                    if (window.CartModule && typeof window.CartModule.loadCartCount === 'function') {
                        window.CartModule.loadCartCount();
                    } else if (window.CartFunctions && window.CartFunctions.loadCartCount) {
                        window.CartFunctions.loadCartCount();
                    } else if (window.updateCartCount && cartCount !== undefined) {
                        window.updateCartCount(cartCount);
                    }

                    resetButton(this, btnText, btnIcon);
                } else {
                    // Show error message
                    const errorMsg = data.message || 'Failed to add product to cart.';
                    if (window.AjaxUtils) {
                        window.AjaxUtils.showMessage(errorMsg, 'error');
                    } else if (window.AppUtils) {
                        window.AppUtils.showNotification(errorMsg, 'error');
                    }
                    resetButton(this, btnText, btnIcon);
                }
            } catch (error) {
                // Error handling is done by AjaxUtils, but we still need to reset button
                resetButton(this, btnText, btnIcon);
                
                // Show error if not already shown by AjaxUtils
                if (!window.AjaxUtils || (error && !error.isAuthError)) {
                    const errorMsg = error && error.message ? error.message : 'An error occurred. Please try again.';
                    if (window.AjaxUtils) {
                        window.AjaxUtils.showMessage(errorMsg, 'error');
                    } else if (window.AppUtils) {
                        window.AppUtils.showNotification(errorMsg, 'error');
                    }
                }
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAddToCart);
    } else {
        initAddToCart();
    }
})();

