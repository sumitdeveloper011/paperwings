/**
 * Form Handler Module
 * Handles form interactions for checkout details page
 */
class CheckoutFormHandler {
    constructor() {
        this.init();
    }

    init() {
        this.initBillingDifferent();
        this.initAddressSelection();
        this.initFormValidation();
    }

    initBillingDifferent() {
        const billingCheckbox = document.getElementById('billingDifferent');
        const billingSection = document.getElementById('billingAddressSection');

        if (billingCheckbox && billingSection) {
            billingCheckbox.addEventListener('change', () => {
                if (billingCheckbox.checked) {
                    billingSection.style.display = 'block';
                    this.setBillingFieldsRequired(true);
                } else {
                    billingSection.style.display = 'none';
                    this.setBillingFieldsRequired(false);
                    this.copyShippingToBilling();
                }
            });
        }
    }

    setBillingFieldsRequired(required) {
        const billingSection = document.getElementById('billingAddressSection');
        if (!billingSection) return;

        const fields = billingSection.querySelectorAll('input, select, textarea');
        fields.forEach(field => {
            if (field.name && field.name.startsWith('billing_')) {
                field.required = required;
            }
        });
    }

    initAddressSelection() {
        const shippingSelect = document.getElementById('shippingAddressSelect');
        if (shippingSelect) {
            shippingSelect.addEventListener('change', (e) => {
                if (e.target.value) {
                    // Saved address selected - populate fields and calculate shipping
                    this.populateFromSelect(e.target, 'shipping');
                } else {
                    // "Use New Address" selected - clear fields and remove shipping
                    this.clearAddressFields('shipping');
                    this.clearShipping();
                }
            });
        }

        const billingSelect = document.getElementById('billingAddressSelect');
        if (billingSelect) {
            billingSelect.addEventListener('change', (e) => {
                if (e.target.value) {
                    // Saved address selected - populate fields
                    this.populateFromSelect(e.target, 'billing');
                } else {
                    // "Use New Address" selected - clear all billing fields
                    this.clearAddressFields('billing');
                }
            });
        }
    }

    populateFromSelect(selectElement, prefix) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        if (!selectedOption || !selectedOption.value) return;

        const fields = {
            'first_name': selectedOption.dataset.firstName || '',
            'last_name': selectedOption.dataset.lastName || '',
            'email': selectedOption.dataset.email || '',
            'phone': selectedOption.dataset.phone || '',
            'street_address': selectedOption.dataset.street || '',
            'suburb': selectedOption.dataset.suburb || '',
            'city': selectedOption.dataset.city || '',
            'region_id': selectedOption.dataset.regionId || '',
            'zip_code': selectedOption.dataset.postcode || '',
        };

        Object.keys(fields).forEach(field => {
            const fieldId = prefix + field.charAt(0).toUpperCase() + field.slice(1).replace('_', '');
            const fieldElement = document.getElementById(fieldId) || document.querySelector(`[name="${prefix}_${field}"]`);
            
            if (fieldElement) {
                if (field === 'region_id') {
                    fieldElement.value = fields[field];
                    fieldElement.dispatchEvent(new Event('change'));
                } else {
                    fieldElement.value = fields[field];
                }
            }
        });

        // Calculate shipping when saved address is selected (only for shipping address)
        if (prefix === 'shipping' && fields.region_id) {
            // Small delay to ensure region dropdown is updated
            setTimeout(() => {
                if (typeof window.updateShipping === 'function') {
                    window.updateShipping(fields.region_id);
                } else if (window.shippingCalculator && typeof window.shippingCalculator.calculate === 'function') {
                    window.shippingCalculator.calculate(fields.region_id);
                }
            }, 100);
        }
    }

    clearAddressFields(prefix) {
        // Clear only address-specific fields, keep user basic info (name, email) if they want to reuse it
        // But for "Use New Address", we clear everything to give them a fresh start
        const addressFields = ['street_address', 'city', 'suburb', 'region_id', 'zip_code', 'phone'];
        
        addressFields.forEach(field => {
            const fieldId = prefix + field.charAt(0).toUpperCase() + field.slice(1).replace('_', '');
            const fieldElement = document.getElementById(fieldId) || document.querySelector(`[name="${prefix}_${field}"]`);

            if (fieldElement) {
                if (field === 'region_id') {
                    // Reset region dropdown to first option (empty)
                    fieldElement.value = '';
                    fieldElement.dispatchEvent(new Event('change'));
                } else {
                    // Clear address fields
                    fieldElement.value = '';
                }
            }
        });

        // Clear address autocomplete suggestions if they exist
        const suggestionsId = prefix === 'shipping' ? 'shippingAddressSuggestions' : 'billingAddressSuggestions';
        const suggestionsElement = document.getElementById(suggestionsId);
        if (suggestionsElement) {
            suggestionsElement.style.display = 'none';
            suggestionsElement.innerHTML = '';
        }

        // Note: We keep first_name, last_name, and email as they might want to reuse them
        // If you want to clear everything, uncomment the following:
        /*
        const allFields = ['first_name', 'last_name', 'email', 'phone', 'street_address', 'city', 'suburb', 'region_id', 'zip_code'];
        allFields.forEach(field => {
            const fieldId = prefix + field.charAt(0).toUpperCase() + field.slice(1).replace('_', '');
            const fieldElement = document.getElementById(fieldId) || document.querySelector(`[name="${prefix}_${field}"]`);
            if (fieldElement) {
                if (field === 'region_id') {
                    fieldElement.value = '';
                    fieldElement.dispatchEvent(new Event('change'));
                } else {
                    fieldElement.value = '';
                }
            }
        });
        */
    }

    clearShipping() {
        // Clear shipping price when "Use New Address" is selected
        if (window.shippingCalculator && typeof window.shippingCalculator.updateDisplay === 'function') {
            window.shippingCalculator.updateDisplay(0, false);
            window.shippingCalculator.updateTotal(0);
        } else if (typeof window.updateShipping === 'function') {
            // Fallback: call with null/0 to clear shipping
            window.updateShipping(null);
        }
    }

    copyShippingToBilling() {
        const fields = ['first_name', 'last_name', 'email', 'phone', 'street_address', 'city', 'suburb', 'region_id', 'zip_code'];

        fields.forEach(field => {
            const shippingField = document.querySelector(`[name="shipping_${field}"]`);
            const billingField = document.querySelector(`[name="billing_${field}"]`);

            if (shippingField && billingField) {
                billingField.value = shippingField.value;
                if (field === 'region_id') {
                    billingField.dispatchEvent(new Event('change'));
                }
            }
        });
    }

    initFormValidation() {
        const form = document.getElementById('checkoutForm');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            console.log('[CheckoutFormHandler] Form submit event triggered');
            console.log('[CheckoutFormHandler] Form validity:', form.checkValidity());
            
            if (!form.checkValidity()) {
                console.warn('[CheckoutFormHandler] Form validation failed, preventing submission');
                e.preventDefault();
                e.stopPropagation();
            } else {
                console.log('[CheckoutFormHandler] Form is valid, allowing submission');
            }
            form.classList.add('was-validated');
        }, false);
        
        // Also handle the review button click directly
        const reviewButton = document.querySelector('button[form="checkoutForm"]');
        if (reviewButton) {
            reviewButton.addEventListener('click', (e) => {
                console.log('[CheckoutFormHandler] Review button clicked');
                // Don't prevent default - let the form submit naturally
                // The button has form="checkoutForm" attribute, so it will submit the form
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    new CheckoutFormHandler();
});
