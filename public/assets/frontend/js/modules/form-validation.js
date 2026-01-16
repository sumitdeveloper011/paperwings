/**
 * Common Form Validation Module
 * Provides reusable validation functions with NZ-specific rules
 * Uses jQuery Validation Plugin
 */
(function() {
    'use strict';

    // Wait for jQuery and jQuery Validation to be available
    function initValidation() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.validate === 'undefined') {
            setTimeout(initValidation, 100);
            return;
        }

        const $ = jQuery;

        /**
         * NZ-specific validation rules
         */
        $.validator.addMethod('nzPhone', function(value, element) {
            // NZ phone number formats (numeric only):
            // Mobile: 0211234567, 021 123 4567
            // Landline: 091234567, 09 123 4567
            // International: +64211234567, +64 21 123 4567
            // Only allow numbers, spaces, +, and hyphens
            const cleanedValue = value.replace(/[\s\-]/g, '');
            const phoneRegex = /^(\+64|0)[2-9]\d{7,9}$/;
            return this.optional(element) || phoneRegex.test(cleanedValue);
        }, 'Please enter a valid New Zealand phone number (numbers only, e.g., 0211234567 or 091234567)');

        $.validator.addMethod('nzPostcode', function(value, element) {
            // NZ postcodes are 4 digits (numeric only)
            const postcodeRegex = /^\d{4}$/;
            return this.optional(element) || postcodeRegex.test(value.trim());
        }, 'Please enter a valid 4-digit New Zealand postcode (numbers only)');

        /**
         * Restrict input to numeric only for phone and postcode fields
         */
        function initNumericInputRestrictions() {
            // Phone number fields - allow numbers, spaces, +, and hyphens
            $(document).on('input', 'input[name="billing_phone"], input[name="shipping_phone"], input[name="phone"], input[id="editPhone"], input[id="addressPhone"]', function(e) {
                let value = $(this).val();
                // Remove any non-numeric characters except +, spaces, and hyphens
                value = value.replace(/[^\d\+\s\-]/g, '');
                $(this).val(value);
            });

            // Postcode fields - allow numbers only
            $(document).on('input', 'input[name="billing_zip_code"], input[name="shipping_zip_code"], input[name="zip_code"], input[id="addressPostcode"]', function(e) {
                let value = $(this).val();
                // Remove any non-numeric characters
                value = value.replace(/\D/g, '');
                // Limit to 4 digits
                if (value.length > 4) {
                    value = value.substring(0, 4);
                }
                $(this).val(value);
            });

            // Prevent paste of non-numeric characters
            $(document).on('paste', 'input[name="billing_phone"], input[name="shipping_phone"], input[name="phone"], input[id="editPhone"], input[id="addressPhone"]', function(e) {
                const paste = (e.originalEvent || e).clipboardData.getData('text');
                if (!/^[\d\+\s\-]*$/.test(paste)) {
                    e.preventDefault();
                }
            });

            $(document).on('paste', 'input[name="billing_zip_code"], input[name="shipping_zip_code"], input[name="zip_code"], input[id="addressPostcode"]', function(e) {
                const paste = (e.originalEvent || e).clipboardData.getData('text');
                if (!/^\d*$/.test(paste)) {
                    e.preventDefault();
                }
            });
        }

        // Initialize numeric restrictions when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initNumericInputRestrictions);
        } else {
            initNumericInputRestrictions();
        }

        $.validator.addMethod('nzAddress', function(value, element) {
            // Basic address validation - at least 5 characters
            return this.optional(element) || value.trim().length >= 5;
        }, 'Please enter a valid street address (at least 5 characters)');

        /**
         * Common validation setup function
         * @param {string|jQuery} formSelector - Form selector or jQuery object
         * @param {object} options - Validation options
         */
        window.initFormValidation = function(formSelector, options = {}) {
            const $form = $(formSelector);
            if (!$form.length) {
                // Form not found
                return null;
            }

            const defaultOptions = {
                errorClass: 'is-invalid',
                validClass: 'is-valid',
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    // Place error after the input field
                    const formGroup = element.closest('.form-group');
                    if (formGroup.length) {
                        error.addClass('invalid-feedback');
                        formGroup.append(error);
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                submitHandler: function(form) {
                    // Default submit handler - can be overridden
                    return true;
                },
                invalidHandler: function(event, validator) {
                    // Scroll to first error
                    const firstError = validator.errorList[0];
                    if (firstError) {
                        const $element = $(firstError.element);
                        $('html, body').animate({
                            scrollTop: $element.offset().top - 100
                        }, 500);
                        $element.focus();
                    }

                    // Show custom error notification if callback provided
                    if (options.onValidationError) {
                        const errors = validator.errorList.map(err => {
                            const label = $(err.element).closest('.form-group').find('label').text().trim() ||
                                         $(err.element).attr('placeholder') ||
                                         $(err.element).attr('name') ||
                                         'Field';
                            return label.replace('*', '').trim();
                        });
                        options.onValidationError(errors);
                    }
                }
            };

            // Merge user options with defaults
            const validationOptions = $.extend(true, {}, defaultOptions, options);

            // Initialize validation
            const validator = $form.validate(validationOptions);

            return validator;
        };

        /**
         * Get reusable NZ address form validation rules (DRY approach)
         * Can be used for checkout, user addresses, profile forms, etc.
         */
        window.getNZAddressFormRules = function(prefix = '') {
            const fieldPrefix = prefix ? prefix + '_' : '';
            return {
                [fieldPrefix + 'first_name']: {
                    required: true,
                    minlength: 2,
                    maxlength: 50
                },
                [fieldPrefix + 'last_name']: {
                    required: true,
                    minlength: 2,
                    maxlength: 50
                },
                [fieldPrefix + 'phone']: {
                    required: true,
                    nzPhone: true
                },
                [fieldPrefix + 'email']: {
                    email: true,
                    maxlength: 255
                },
                [fieldPrefix + 'street_address']: {
                    required: true,
                    nzAddress: true,
                    maxlength: 255
                },
                [fieldPrefix + 'city']: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                [fieldPrefix + 'zip_code']: {
                    required: true,
                    nzPostcode: true
                },
                [fieldPrefix + 'postcode']: {
                    required: true,
                    nzPostcode: true
                },
                [fieldPrefix + 'region_id']: {
                    required: true
                }
            };
        };

        /**
         * Get reusable NZ address form validation messages (DRY approach)
         */
        window.getNZAddressFormMessages = function(prefix = '') {
            const fieldPrefix = prefix ? prefix + '_' : '';
            return {
                [fieldPrefix + 'first_name']: {
                    required: 'Please enter your first name.',
                    minlength: 'First name must be at least 2 characters.',
                    maxlength: 'First name cannot exceed 50 characters.'
                },
                [fieldPrefix + 'last_name']: {
                    required: 'Please enter your last name.',
                    minlength: 'Last name must be at least 2 characters.',
                    maxlength: 'Last name cannot exceed 50 characters.'
                },
                [fieldPrefix + 'phone']: {
                    required: 'Please enter your phone number.',
                    nzPhone: 'Please enter a valid New Zealand phone number (numbers only, e.g., 0211234567 or 091234567).'
                },
                [fieldPrefix + 'email']: {
                    email: 'Please enter a valid email address.',
                    maxlength: 'Email cannot exceed 255 characters.'
                },
                [fieldPrefix + 'street_address']: {
                    required: 'Please enter your street address.',
                    nzAddress: 'Please enter a valid street address (at least 5 characters).',
                    maxlength: 'Street address cannot exceed 255 characters.'
                },
                [fieldPrefix + 'city']: {
                    required: 'Please enter your city.',
                    minlength: 'City must be at least 2 characters.',
                    maxlength: 'City cannot exceed 100 characters.'
                },
                [fieldPrefix + 'zip_code']: {
                    required: 'Please enter your postcode.',
                    nzPostcode: 'Please enter a valid 4-digit New Zealand postcode (numbers only).'
                },
                [fieldPrefix + 'postcode']: {
                    required: 'Please enter your postcode.',
                    nzPostcode: 'Please enter a valid 4-digit New Zealand postcode (numbers only).'
                },
                [fieldPrefix + 'region_id']: {
                    required: 'Please select a region.'
                }
            };
        };

        /**
         * Get default NZ checkout form validation rules
         */
        window.getNZCheckoutRules = function() {
            return {
                'billing_first_name': {
                    required: true,
                    minlength: 2,
                    maxlength: 50
                },
                'billing_last_name': {
                    required: true,
                    minlength: 2,
                    maxlength: 50
                },
                'billing_email': {
                    required: true,
                    email: true,
                    maxlength: 255
                },
                'billing_phone': {
                    required: true,
                    nzPhone: true
                },
                'billing_street_address': {
                    required: true,
                    nzAddress: true,
                    maxlength: 255
                },
                'billing_city': {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                'billing_region_id': {
                    required: true
                },
                'billing_zip_code': {
                    required: true,
                    nzPostcode: true
                },
                'billing_country': {
                    required: true
                },
                'shipping_first_name': {
                    required: function() {
                        return $('#sameAsBilling').length && !$('#sameAsBilling').is(':checked');
                    },
                    minlength: 2,
                    maxlength: 50
                },
                'shipping_last_name': {
                    required: function() {
                        return $('#sameAsBilling').length && !$('#sameAsBilling').is(':checked');
                    },
                    minlength: 2,
                    maxlength: 50
                },
                'shipping_email': {
                    required: function() {
                        return $('#sameAsBilling').length && !$('#sameAsBilling').is(':checked');
                    },
                    email: true,
                    maxlength: 255
                },
                'shipping_phone': {
                    required: function() {
                        return $('#sameAsBilling').length && !$('#sameAsBilling').is(':checked');
                    },
                    nzPhone: true
                },
                'shipping_street_address': {
                    required: function() {
                        return $('#sameAsBilling').length && !$('#sameAsBilling').is(':checked');
                    },
                    nzAddress: true,
                    maxlength: 255
                },
                'shipping_city': {
                    required: function() {
                        return $('#sameAsBilling').length && !$('#sameAsBilling').is(':checked');
                    },
                    minlength: 2,
                    maxlength: 100
                },
                'shipping_region_id': {
                    required: function() {
                        return $('#sameAsBilling').length && !$('#sameAsBilling').is(':checked');
                    }
                },
                'shipping_zip_code': {
                    required: function() {
                        return $('#sameAsBilling').length && !$('#sameAsBilling').is(':checked');
                    },
                    nzPostcode: true
                }
            };
        };

        /**
         * Get default NZ checkout form messages
         */
        window.getNZCheckoutMessages = function() {
            return {
                'billing_first_name': {
                    required: 'Please enter your first name',
                    minlength: 'First name must be at least 2 characters',
                    maxlength: 'First name cannot exceed 50 characters'
                },
                'billing_last_name': {
                    required: 'Please enter your last name',
                    minlength: 'Last name must be at least 2 characters',
                    maxlength: 'Last name cannot exceed 50 characters'
                },
                'billing_email': {
                    required: 'Please enter your email address',
                    email: 'Please enter a valid email address',
                    maxlength: 'Email cannot exceed 255 characters'
                },
                'billing_phone': {
                    required: 'Please enter your phone number',
                    nzPhone: 'Please enter a valid New Zealand phone number'
                },
                'billing_street_address': {
                    required: 'Please enter your street address',
                    nzAddress: 'Please enter a valid street address',
                    maxlength: 'Address cannot exceed 255 characters'
                },
                'billing_city': {
                    required: 'Please enter your city',
                    minlength: 'City must be at least 2 characters',
                    maxlength: 'City cannot exceed 100 characters'
                },
                'billing_region_id': {
                    required: 'Please select a region'
                },
                'billing_zip_code': {
                    required: 'Please enter your postcode',
                    nzPostcode: 'Please enter a valid 4-digit postcode'
                },
                'billing_country': {
                    required: 'Country is required'
                },
                'shipping_first_name': {
                    required: 'Please enter shipping first name',
                    minlength: 'First name must be at least 2 characters',
                    maxlength: 'First name cannot exceed 50 characters'
                },
                'shipping_last_name': {
                    required: 'Please enter shipping last name',
                    minlength: 'Last name must be at least 2 characters',
                    maxlength: 'Last name cannot exceed 50 characters'
                },
                'shipping_email': {
                    required: 'Please enter shipping email address',
                    email: 'Please enter a valid email address',
                    maxlength: 'Email cannot exceed 255 characters'
                },
                'shipping_phone': {
                    required: 'Please enter shipping phone number',
                    nzPhone: 'Please enter a valid New Zealand phone number'
                },
                'shipping_street_address': {
                    required: 'Please enter shipping street address',
                    nzAddress: 'Please enter a valid street address',
                    maxlength: 'Address cannot exceed 255 characters'
                },
                'shipping_city': {
                    required: 'Please enter shipping city',
                    minlength: 'City must be at least 2 characters',
                    maxlength: 'City cannot exceed 100 characters'
                },
                'shipping_region_id': {
                    required: 'Please select a shipping region'
                },
                'shipping_zip_code': {
                    required: 'Please enter shipping postcode',
                    nzPostcode: 'Please enter a valid 4-digit postcode'
                }
            };
        };

        /**
         * Show custom validation error notification
         */
        window.showValidationErrorNotification = function(emptyFields, options = {}) {
            const defaultOptions = {
                position: 'top-right',
                duration: 5000,
                className: 'validation-error-notification'
            };

            const opts = $.extend({}, defaultOptions, options);

            // Remove existing notification
            const existing = document.querySelector('.' + opts.className);
            if (existing) {
                existing.remove();
            }

            // Create notification element
            const errorDiv = document.createElement('div');
            errorDiv.className = opts.className;

            const positionStyles = {
                'top-right': 'top: 20px; right: 20px;',
                'top-left': 'top: 20px; left: 20px;',
                'top-center': 'top: 20px; left: 50%; transform: translateX(-50%);'
            };

            errorDiv.style.cssText = `
                position: fixed;
                ${positionStyles[opts.position] || positionStyles['top-right']}
                background: #dc3545;
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                max-width: 400px;
                animation: slideInRight 0.3s ease;
            `;

            const errorMessage = emptyFields.length > 0
                ? `Please fill in: ${emptyFields.slice(0, 3).join(', ')}${emptyFields.length > 3 ? ' and more...' : ''}`
                : 'Please fill in all required fields.';

            errorDiv.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-exclamation-circle" style="font-size: 1.2rem;"></i>
                    <div>
                        <strong>Required Fields Missing</strong>
                        <div style="margin-top: 0.25rem; font-size: 0.9rem;">${errorMessage}</div>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; margin-left: auto;">&times;</button>
                </div>
            `;

            document.body.appendChild(errorDiv);

            // Auto remove after duration
            setTimeout(() => {
                if (errorDiv.parentElement) {
                    errorDiv.style.animation = 'slideOutRight 0.3s ease';
                    setTimeout(() => errorDiv.remove(), 300);
                }
            }, opts.duration);
        };

        // Form validation module initialized
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initValidation);
    } else {
        initValidation();
    }
})();
