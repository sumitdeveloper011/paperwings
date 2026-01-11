/**
 * Product Pricing Calculator
 * Handles price calculations and discount field toggling
 * Reusable across product create and edit pages
 */
(function() {
    'use strict';

    /**
     * Initialize product pricing functionality
     */
    function initProductPricing() {
        const totalPriceInput = document.getElementById('total_price');
        const priceWithoutTaxInput = document.getElementById('price_without_tax');
        const taxAmountInput = document.getElementById('tax_amount');
        const discountTypeSelect = document.getElementById('discount_type');
        const discountPriceInput = document.getElementById('discount_price');
        const discountPercentageInput = document.getElementById('discount_percentage');
        const discountDirectWrapper = document.getElementById('discount_direct_wrapper');
        const discountPercentageWrapper = document.getElementById('discount_percentage_wrapper');
        const calculatedDiscountPrice = document.getElementById('calculated_discount_price');

        if (!totalPriceInput || !discountTypeSelect) {
            return; // Elements not found, exit early
        }

        /**
         * Calculate price without tax and tax amount
         * If discount is applied, calculate from discounted price, otherwise from total price
         */
        function calculatePrices() {
            const totalPrice = parseFloat(totalPriceInput.value) || 0;
            let finalPrice = totalPrice; // Price to use for calculations
            let discountPriceValue = 0;

            // Determine if discount is applied and get the final price
            if (discountTypeSelect) {
                const discountType = discountTypeSelect.value;
                
                if (discountType === 'percentage' && discountPercentageInput) {
                    const percentage = parseFloat(discountPercentageInput.value) || 0;
                    if (percentage > 0 && totalPrice > 0) {
                        discountPriceValue = totalPrice - (totalPrice * percentage / 100);
                        finalPrice = discountPriceValue; // Use discounted price for calculations
                        
                        if (calculatedDiscountPrice) {
                            calculatedDiscountPrice.textContent = `Calculated price: $${discountPriceValue.toFixed(2)}`;
                        }
                        // Set discount_price value for form submission (even though field is disabled)
                        if (discountPriceInput) {
                            discountPriceInput.value = discountPriceValue.toFixed(2);
                        }
                    } else {
                        if (calculatedDiscountPrice) {
                            calculatedDiscountPrice.textContent = '';
                        }
                        if (discountPriceInput) {
                            discountPriceInput.value = '';
                        }
                    }
                } else if (discountType === 'direct' && discountPriceInput) {
                    discountPriceValue = parseFloat(discountPriceInput.value) || 0;
                    if (discountPriceValue > 0) {
                        finalPrice = discountPriceValue; // Use discounted price for calculations
                    }
                }
            }

            // Calculate price without tax and tax amount from final price (discounted or original)
            const priceWithoutTax = finalPrice / 1.15;
            const taxAmount = finalPrice - priceWithoutTax;

            if (priceWithoutTaxInput) {
                priceWithoutTaxInput.value = priceWithoutTax.toFixed(2);
            }
            if (taxAmountInput) {
                taxAmountInput.value = taxAmount.toFixed(2);
            }
        }

        /**
         * Toggle discount field based on discount type (single field approach)
         */
        function toggleDiscountFields() {
            if (!discountTypeSelect) return;
            
            const discountType = discountTypeSelect.value;

            if (discountType === 'none') {
                // Hide both, disable both
                if (discountDirectWrapper) {
                    discountDirectWrapper.style.display = 'none';
                }
                if (discountPercentageWrapper) {
                    discountPercentageWrapper.style.display = 'none';
                }
                if (discountPriceInput) {
                    discountPriceInput.disabled = true;
                    discountPriceInput.value = '';
                }
                if (discountPercentageInput) {
                    discountPercentageInput.disabled = true;
                    discountPercentageInput.value = '';
                }
                if (calculatedDiscountPrice) {
                    calculatedDiscountPrice.textContent = '';
                }
            } else if (discountType === 'direct') {
                // Show direct price enabled, hide percentage
                if (discountDirectWrapper) {
                    discountDirectWrapper.style.display = 'flex';
                }
                if (discountPercentageWrapper) {
                    discountPercentageWrapper.style.display = 'none';
                }
                if (discountPriceInput) {
                    discountPriceInput.disabled = false;
                }
                if (discountPercentageInput) {
                    discountPercentageInput.disabled = true;
                    discountPercentageInput.value = '';
                }
                if (calculatedDiscountPrice) {
                    calculatedDiscountPrice.textContent = '';
                }
            } else if (discountType === 'percentage') {
                // Show percentage enabled, hide direct price
                if (discountDirectWrapper) {
                    discountDirectWrapper.style.display = 'none';
                }
                if (discountPercentageWrapper) {
                    discountPercentageWrapper.style.display = 'flex';
                }
                if (discountPriceInput) {
                    discountPriceInput.disabled = true;
                    // Don't clear value here - it will be calculated by calculatePrices()
                }
                if (discountPercentageInput) {
                    discountPercentageInput.disabled = false;
                }
                calculatePrices(); // Calculate discount price from percentage
            }
        }

        // Event listeners
        if (totalPriceInput) {
            totalPriceInput.addEventListener('input', calculatePrices);
        }
        if (discountTypeSelect) {
            discountTypeSelect.addEventListener('change', toggleDiscountFields);
        }
        if (discountPercentageInput) {
            discountPercentageInput.addEventListener('input', calculatePrices);
        }
        if (discountPriceInput) {
            discountPriceInput.addEventListener('input', calculatePrices);
        }

        // Initial toggle
        toggleDiscountFields();

        // Initial calculation if there's a value
        // Use setTimeout to ensure all fields are properly initialized
        setTimeout(function() {
            if (totalPriceInput && totalPriceInput.value) {
                calculatePrices();
            }
        }, 100);
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProductPricing);
    } else {
        initProductPricing();
    }
})();
