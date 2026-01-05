/**
 * Shipping Calculation Module
 * Handles shipping calculation and display updates
 */
class ShippingCalculator {
    constructor(config) {
        this.csrfToken = config.csrfToken;
        this.calculateShippingUrl = config.calculateShippingUrl;
        this.subtotal = config.subtotal;
        this.discount = config.discount;
        this.onShippingUpdated = config.onShippingUpdated || null;
    }

    /**
     * Calculate shipping cost based on region
     * @param {number} regionId - The shipping region ID
     * @returns {Promise<number>} - The shipping cost
     */
    async calculate(regionId) {
        return new Promise((resolve) => {
            if (!regionId) {
                resolve(0);
                return;
            }

            fetch(this.calculateShippingUrl, {
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
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const shipping = parseFloat(data.shipping) || 0;
                    this.updateDisplay(shipping, data.is_free_shipping);

                    if (this.onShippingUpdated) {
                        this.onShippingUpdated(shipping);
                    }

                    resolve(shipping);
                } else {
                    resolve(0);
                }
            })
            .catch(error => {
                console.error('Error calculating shipping:', error);
                resolve(0);
            });
        });
    }

    /**
     * Update shipping display in the UI
     * @param {number} shipping - The shipping cost
     * @param {boolean} isFreeShipping - Whether shipping is free
     */
    updateDisplay(shipping, isFreeShipping = false) {
        const shippingRow = document.getElementById('shippingRow');
        const shippingValue = document.getElementById('checkoutShipping');
        const freeShippingMessage = document.getElementById('freeShippingMessage');

        if (shippingValue) {
            if (isFreeShipping) {
                shippingValue.textContent = '$0.00';
                if (freeShippingMessage) {
                    freeShippingMessage.style.display = 'inline';
                }
            } else {
                shippingValue.textContent = '$' + shipping.toFixed(2);
                if (freeShippingMessage) {
                    freeShippingMessage.style.display = 'none';
                }
            }
        }

        if (shippingRow) {
            shippingRow.style.display = 'flex';
        }
    }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ShippingCalculator;
}

