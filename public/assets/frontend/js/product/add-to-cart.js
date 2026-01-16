/**
 * Product Add to Cart Module
 * Handles add to cart functionality on product detail page
 */
(function() {
    'use strict';

    function initAddToCart() {
        const addToCartBtn = document.getElementById('addToCartBtn');
        const quantityInput = document.getElementById('quantity');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        if (!addToCartBtn) {
            return;
        }

        // Show notification function
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `cart-notification cart-notification--${type}`;
            notification.textContent = message;
            notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px 20px; background: ' + (type === 'success' ? '#10b981' : '#ef4444') + '; color: white; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.3s';
                setTimeout(() => {
                    if (notification.parentNode) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        addToCartBtn.addEventListener('click', function() {
            const productUuid = this.getAttribute('data-product-uuid');
            const quantity = parseInt(quantityInput?.value) || 1;
            const btnText = this.querySelector('.btn-text');
            const btnIcon = this.querySelector('i');

            // Validate product UUID
            if (!productUuid) {
                showNotification('Product UUID is missing. Please refresh the page.', 'error');
                return;
            }

            // Disable button and show loading state
            this.disabled = true;
            if (btnText) btnText.textContent = 'Adding...';
            if (btnIcon) {
                btnIcon.classList.remove('fa-shopping-cart');
                btnIcon.classList.add('fa-spinner', 'fa-spin');
            }

            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_uuid: productUuid,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Product added to cart successfully!', 'success');

                    // Update cart count in header if function exists
                    if (window.CartFunctions && window.CartFunctions.loadCartCount) {
                        window.CartFunctions.loadCartCount();
                    } else if (window.updateCartCount) {
                        window.updateCartCount(data.cart_count);
                    }

                    // Reset button state
                    this.disabled = false;
                    if (btnText) btnText.textContent = 'Add to Cart';
                    if (btnIcon) {
                        btnIcon.classList.remove('fa-spinner', 'fa-spin');
                        btnIcon.classList.add('fa-shopping-cart');
                    }
                } else {
                    showNotification(data.message || 'Failed to add product to cart.', 'error');

                    // Reset button state
                    this.disabled = false;
                    if (btnText) btnText.textContent = 'Add to Cart';
                    if (btnIcon) {
                        btnIcon.classList.remove('fa-spinner', 'fa-spin');
                        btnIcon.classList.add('fa-shopping-cart');
                    }
                }
            })
            .catch(error => {
                showNotification('An error occurred. Please try again.', 'error');

                // Reset button state
                this.disabled = false;
                if (btnText) btnText.textContent = 'Add to Cart';
                if (btnIcon) {
                    btnIcon.classList.remove('fa-spinner', 'fa-spin');
                    btnIcon.classList.add('fa-shopping-cart');
                }
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAddToCart);
    } else {
        initAddToCart();
    }
})();

