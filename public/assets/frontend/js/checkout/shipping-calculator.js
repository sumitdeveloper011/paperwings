/**
 * Shipping Calculator Module
 * Handles dynamic shipping calculation and display updates
 */
class ShippingCalculator {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        this.calculateUrl = '/checkout/calculate-shipping';
        this.subtotal = 0;
        this.discount = 0;
        this.isCalculating = false;
        this.calculateTimeout = null;
    }

    setTotals(subtotal, discount) {
        this.subtotal = subtotal;
        this.discount = discount;
    }

    async calculate(regionId) {
        if (!regionId) {
            const shippingRow = document.getElementById('shippingRow');
            if (shippingRow) {
                shippingRow.style.display = 'none';
            }
            this.updateDisplay(0, false);
            this.updateTotal(0);
            return 0;
        }

        // Debounce: Clear previous timeout
        if (this.calculateTimeout) {
            clearTimeout(this.calculateTimeout);
        }

        // Prevent multiple simultaneous calculations
        if (this.isCalculating) {
            return new Promise((resolve) => {
                this.calculateTimeout = setTimeout(() => {
                    resolve(this.calculate(regionId));
                }, 100);
            });
        }

        this.isCalculating = true;
        this.showLoading(true);

        try {
            const response = await fetch(this.calculateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    region_id: regionId,
                    subtotal: this.subtotal,
                    discount: this.discount
                })
            });

            if (!response.ok) {
                throw new Error('Failed to calculate shipping');
            }

            const data = await response.json();

            if (data.success) {
                const responseData = data.data || {};
                const shipping = parseFloat(responseData.shipping || responseData.shipping_price || data.shipping || 0) || 0;
                const isFreeShipping = responseData.is_free_shipping || data.is_free_shipping || false;
                this.updateDisplay(shipping, isFreeShipping);
                this.updateTotal(shipping);
                this.isCalculating = false;
                this.showLoading(false);
                return shipping;
            }

            this.isCalculating = false;
            this.showLoading(false);
            return 0;
        } catch (error) {
            this.isCalculating = false;
            this.showLoading(false);
            return 0;
        }
    }

    showLoading(show) {
        const shippingValue = document.getElementById('checkoutShipping');
        if (shippingValue) {
            if (show) {
                shippingValue.textContent = 'Calculating...';
            }
        }
    }

    updateDisplay(shipping, isFreeShipping = false) {
        const shippingRow = document.getElementById('shippingRow');
        const shippingValue = document.getElementById('checkoutShipping');
        const freeShippingMessage = document.getElementById('freeShippingMessage');

        if (shippingRow) {
            shippingRow.style.display = 'flex';
        }

        if (shippingValue) {
            shippingValue.textContent = '$' + shipping.toFixed(2);
        }

        if (freeShippingMessage) {
            freeShippingMessage.style.display = isFreeShipping ? 'inline' : 'none';
        }
    }

    updateTotal(shipping) {
        const subtotalEl = document.getElementById('checkoutSubtotal');
        const discountEl = document.getElementById('checkoutDiscount');
        const totalEl = document.getElementById('checkoutTotal');

        if (!subtotalEl || !totalEl) return;

        const subtotal = parseFloat(subtotalEl.textContent.replace('$', '').replace(',', '')) || 0;
        const discount = discountEl ? parseFloat(discountEl.textContent.replace('-$', '').replace(',', '')) || 0 : 0;
        const total = subtotal - discount + shipping;

        if (totalEl) {
            totalEl.textContent = '$' + total.toFixed(2);
        }
    }
}

(function() {
    'use strict';

    let shippingCalculator = null;

    function initializeShippingCalculator() {
        if (shippingCalculator) {
            return shippingCalculator;
        }

        shippingCalculator = new ShippingCalculator();

        const subtotalEl = document.getElementById('checkoutSubtotal');
        const discountEl = document.getElementById('checkoutDiscount');

        if (subtotalEl) {
            const subtotal = parseFloat(subtotalEl.textContent.replace('$', '').replace(',', '')) || 0;
            const discount = discountEl ? parseFloat(discountEl.textContent.replace('-$', '').replace(',', '')) || 0 : 0;
            shippingCalculator.setTotals(subtotal, discount);
        }

        const regionSelect = document.getElementById('shippingRegion');
        if (regionSelect) {
            regionSelect.addEventListener('change', function() {
                if (this.value) {
                    clearTimeout(shippingCalculator.calculateTimeout);
                    shippingCalculator.calculateTimeout = setTimeout(() => {
                        shippingCalculator.calculate(parseInt(this.value));
                    }, 300);
                } else {
                    const shippingRow = document.getElementById('shippingRow');
                    if (shippingRow) {
                        shippingRow.style.display = 'none';
                    }
                    shippingCalculator.updateDisplay(0, false);
                    shippingCalculator.updateTotal(0);
                }
            });

            if (regionSelect.value) {
                shippingCalculator.calculate(parseInt(regionSelect.value));
            } else {
                const shippingRow = document.getElementById('shippingRow');
                if (shippingRow) {
                    shippingRow.style.display = 'none';
                }
            }
        }

        return shippingCalculator;
    }

    window.updateShipping = function(regionId) {
        const calculator = initializeShippingCalculator();
        if (calculator) {
            calculator.calculate(regionId);
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        initializeShippingCalculator();
    });
})();
