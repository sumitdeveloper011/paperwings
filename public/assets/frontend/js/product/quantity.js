/**
 * Product Quantity Controls Module
 * Handles quantity increase/decrease functionality
 */
(function() {
    'use strict';

    function initQuantityControls() {
        const quantityInput = document.getElementById('quantity');
        const decreaseQty = document.getElementById('decreaseQty');
        const increaseQty = document.getElementById('increaseQty');
        const minQty = parseInt(quantityInput?.getAttribute('min') || '1');
        const maxQty = parseInt(quantityInput?.getAttribute('max') || '999');

        if (!quantityInput) {
            return;
        }

        // Decrease quantity
        if (decreaseQty) {
            decreaseQty.addEventListener('click', function(e) {
                e.preventDefault();
                let currentQty = parseInt(quantityInput.value) || minQty;
                if (currentQty > minQty) {
                    quantityInput.value = currentQty - 1;
                }
            });
        }

        // Increase quantity
        if (increaseQty) {
            increaseQty.addEventListener('click', function(e) {
                e.preventDefault();
                let currentQty = parseInt(quantityInput.value) || minQty;
                if (currentQty < maxQty) {
                    quantityInput.value = currentQty + 1;
                }
            });
        }

        // Validate quantity on input
        quantityInput.addEventListener('input', function() {
            let value = parseInt(this.value) || minQty;
            if (value < minQty) {
                this.value = minQty;
            } else if (value > maxQty) {
                this.value = maxQty;
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQuantityControls);
    } else {
        initQuantityControls();
    }
})();

