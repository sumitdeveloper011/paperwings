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
    }

    setTotals(subtotal, discount) {
        this.subtotal = subtotal;
        this.discount = discount;
    }

    async calculate(regionId) {
        if (!regionId) {
            this.updateDisplay(0, false);
            return 0;
        }

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

            const data = await response.json();

            if (data.success) {
                const shipping = parseFloat(data.shipping) || 0;
                this.updateDisplay(shipping, data.is_free_shipping || false);
                this.updateTotal(shipping);
                return shipping;
            }

            return 0;
        } catch (error) {
            return 0;
        }
    }

    updateDisplay(shipping, isFreeShipping = false) {
        const shippingRow = document.getElementById('shippingRow');
        const shippingValue = document.getElementById('checkoutShipping');
        const freeShippingMessage = document.getElementById('freeShippingMessage');

        if (shippingValue) {
            shippingValue.textContent = '$' + shipping.toFixed(2);
        }

        if (freeShippingMessage) {
            freeShippingMessage.style.display = isFreeShipping ? 'inline' : 'none';
        }

        if (shippingRow) {
            shippingRow.style.display = shipping > 0 || isFreeShipping ? 'flex' : 'none';
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

window.updateShipping = function(regionId) {
    if (window.shippingCalculator) {
        window.shippingCalculator.calculate(regionId);
    }
};

document.addEventListener('DOMContentLoaded', function() {
    window.shippingCalculator = new ShippingCalculator();

    const subtotalEl = document.getElementById('checkoutSubtotal');
    const discountEl = document.getElementById('checkoutDiscount');

    if (subtotalEl) {
        const subtotal = parseFloat(subtotalEl.textContent.replace('$', '').replace(',', '')) || 0;
        const discount = discountEl ? parseFloat(discountEl.textContent.replace('-$', '').replace(',', '')) || 0 : 0;
        window.shippingCalculator.setTotals(subtotal, discount);
    }

    const regionSelect = document.getElementById('shippingRegion');
    if (regionSelect) {
        regionSelect.addEventListener('change', function() {
            if (this.value) {
                window.shippingCalculator.calculate(parseInt(this.value));
            }
        });

        if (regionSelect.value) {
            window.shippingCalculator.calculate(parseInt(regionSelect.value));
        }
    }
});
