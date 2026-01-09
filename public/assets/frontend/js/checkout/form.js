/**
 * Form Handling Module
 * Handles form interactions and address copying
 */
class CheckoutFormHandler {
    constructor(config) {
        this.onRegionChange = config.onRegionChange || null;
    }

    /**
     * Initialize form handlers
     */
    init() {
        this.initSameAsBilling();
        this.initRegionChangeHandlers();
    }

    /**
     * Initialize "Same as Billing" checkbox
     */
    initSameAsBilling() {
        const sameAsBillingCheckbox = document.getElementById('sameAsBilling');
        const shippingDetails = document.getElementById('shippingDetails');
        const shippingInputs = shippingDetails ? shippingDetails.querySelectorAll('input, select') : [];

        if (sameAsBillingCheckbox && shippingInputs.length > 0) {
            sameAsBillingCheckbox.addEventListener('change', () => {
                if (sameAsBillingCheckbox.checked) {
                    this.copyBillingToShipping();
                    shippingInputs.forEach(input => input.disabled = true);

                    if (this.onRegionChange) {
                        this.onRegionChange();
                    }
                } else {
                    shippingInputs.forEach(input => input.disabled = false);
                }

                // Re-validate form when checkbox state changes
                this.revalidateForm();
            });
        }
    }

    /**
     * Re-validate form (useful when conditional fields change)
     */
    revalidateForm() {
        if (typeof jQuery !== 'undefined' && typeof window.CheckoutModal !== 'undefined') {
            // Access the validator from CheckoutModal instance if available
            const checkoutForm = document.getElementById('checkoutForm');
            if (checkoutForm && jQuery(checkoutForm).data('validator')) {
                const validator = jQuery(checkoutForm).data('validator');
                if (validator) {
                    validator.resetForm();
                    validator.form();
                }
            }
        }
    }

    /**
     * Copy billing address fields to shipping
     */
    copyBillingToShipping() {
        const fields = [
            'FirstName', 'LastName', 'Email', 'Phone',
            'Address', 'City', 'Suburb', 'Region', 'Zip'
        ];

        fields.forEach(field => {
            const billingField = document.getElementById('billing' + field);
            const shippingField = document.getElementById('shipping' + field);

            if (billingField && shippingField) {
                shippingField.value = billingField.value;
            }
        });
    }

    /**
     * Initialize region change handlers
     */
    initRegionChangeHandlers() {
        // Shipping region change
        const shippingRegionSelect = document.getElementById('shippingRegion');
        if (shippingRegionSelect) {
            shippingRegionSelect.addEventListener('change', () => {
                if (this.onRegionChange) {
                    this.onRegionChange();
                }
            });
        }

        // Billing region change (if same as billing is checked)
        const billingRegionSelect = document.getElementById('billingRegion');
        const sameAsBillingCheckbox = document.getElementById('sameAsBilling');

        if (billingRegionSelect && sameAsBillingCheckbox) {
            billingRegionSelect.addEventListener('change', () => {
                if (sameAsBillingCheckbox.checked && this.onRegionChange) {
                    this.onRegionChange();
                }
            });
        }
    }

    /**
     * Get current shipping region ID
     * @returns {string|null}
     */
    getShippingRegionId() {
        const shippingRegion = document.getElementById('shippingRegion');
        const billingRegion = document.getElementById('billingRegion');
        const sameAsBillingCheckbox = document.getElementById('sameAsBilling');

        if (sameAsBillingCheckbox && sameAsBillingCheckbox.checked && billingRegion) {
            return billingRegion.value || null;
        }

        return shippingRegion ? shippingRegion.value || null : null;
    }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CheckoutFormHandler;
}

