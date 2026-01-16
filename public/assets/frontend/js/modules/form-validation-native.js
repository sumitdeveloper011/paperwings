/**
 * Native Form Validation Module
 * Provides reusable validation functions with NZ-specific rules
 * No jQuery dependency - pure native JavaScript
 */
(function() {
    'use strict';

    /**
     * Validation Rules Object
     */
    const ValidationRules = {
        /**
         * NZ Phone Number Validation
         * Formats: 0211234567, 091234567, +64211234567
         */
        nzPhone: function(value) {
            if (!value) return true; // Optional field
            const cleanedValue = value.replace(/[\s\-]/g, '');
            const phoneRegex = /^(\+64|0)[2-9]\d{7,9}$/;
            return phoneRegex.test(cleanedValue);
        },

        /**
         * NZ Postcode Validation
         * 4 digits numeric only
         */
        nzPostcode: function(value) {
            if (!value) return true; // Optional field
            const postcodeRegex = /^\d{4}$/;
            return postcodeRegex.test(value.trim());
        },

        /**
         * Email Validation
         */
        email: function(value) {
            if (!value) return true;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(value);
        },

        /**
         * Required Field
         */
        required: function(value) {
            return value !== null && value !== undefined && String(value).trim().length > 0;
        },

        /**
         * Minimum Length
         */
        minlength: function(value, min) {
            if (!value) return true;
            return String(value).trim().length >= min;
        },

        /**
         * Maximum Length
         */
        maxlength: function(value, max) {
            if (!value) return true;
            return String(value).trim().length <= max;
        },

        /**
         * Regex Pattern
         */
        regex: function(value, pattern) {
            if (!value) return true;
            const regex = new RegExp(pattern);
            return regex.test(value);
        },

        /**
         * Numeric Range
         */
        min: function(value, min) {
            if (!value) return true;
            const num = parseFloat(value);
            return !isNaN(num) && num >= min;
        },

        /**
         * Numeric Range
         */
        max: function(value, max) {
            if (!value) return true;
            const num = parseFloat(value);
            return !isNaN(num) && num <= max;
        }
    };

    /**
     * Default Error Messages
     */
    const DefaultMessages = {
        nzPhone: 'Please enter a valid New Zealand phone number (numbers only, e.g., 0211234567 or 091234567)',
        nzPostcode: 'Please enter a valid 4-digit New Zealand postcode (numbers only)',
        email: 'Please enter a valid email address',
        required: 'This field is required',
        minlength: 'Please enter at least {0} characters',
        maxlength: 'Please enter no more than {0} characters',
        regex: 'Please enter a valid value',
        min: 'Please enter a value greater than or equal to {0}',
        max: 'Please enter a value less than or equal to {0}'
    };

    /**
     * Form Validator Class
     */
    class FormValidator {
        constructor(form, options = {}) {
            this.form = typeof form === 'string' ? document.querySelector(form) : form;
            if (!this.form) {
                // Form not found
                return;
            }

            this.rules = options.rules || {};
            this.messages = options.messages || {};
            this.errorClass = options.errorClass || 'is-invalid';
            this.validClass = options.validClass || 'is-valid';
            this.errorElement = options.errorElement || 'div';
            this.onSubmit = options.onSubmit || null;
            this.onInvalid = options.onInvalid || null;

            this.init();
        }

        init() {
            // Restrict input for phone and postcode fields
            this.initNumericInputRestrictions();

            // Add submit handler
            this.form.addEventListener('submit', (e) => {
                if (!this.validate()) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                if (this.onSubmit) {
                    const result = this.onSubmit(this.form);
                    if (result === false) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                }
            });

            // Real-time validation on blur
            this.form.querySelectorAll('input, textarea, select').forEach(field => {
                field.addEventListener('blur', () => {
                    this.validateField(field);
                });
            });
        }

        /**
         * Restrict input to numeric only for phone and postcode fields
         */
        initNumericInputRestrictions() {
            const phoneFields = this.form.querySelectorAll(
                'input[name="billing_phone"], input[name="shipping_phone"], input[name="phone"], input[id="editPhone"], input[id="addressPhone"]'
            );

            phoneFields.forEach(field => {
                field.addEventListener('input', (e) => {
                    let value = e.target.value;
                    value = value.replace(/[^\d\+\s\-]/g, '');
                    e.target.value = value;
                });
            });

            const postcodeFields = this.form.querySelectorAll(
                'input[name="billing_zip_code"], input[name="shipping_zip_code"], input[name="postcode"], input[id="editPostcode"], input[id="addressPostcode"]'
            );

            postcodeFields.forEach(field => {
                field.addEventListener('input', (e) => {
                    let value = e.target.value;
                    value = value.replace(/[^\d]/g, '');
                    e.target.value = value;
                });
            });
        }

        /**
         * Validate entire form
         */
        validate() {
            let isValid = true;
            const errors = {};

            // Clear previous errors
            this.clearErrors();

            // Validate each field
            Object.keys(this.rules).forEach(fieldName => {
                const field = this.form.querySelector(`[name="${fieldName}"]`);
                if (!field) return;

                const fieldErrors = this.validateField(field);
                if (fieldErrors.length > 0) {
                    isValid = false;
                    errors[fieldName] = fieldErrors;
                }
            });

            if (!isValid && this.onInvalid) {
                this.onInvalid(errors, this);
            }

            return isValid;
        }

        /**
         * Validate single field
         */
        validateField(field) {
            const fieldName = field.name;
            if (!fieldName || !this.rules[fieldName]) {
                return [];
            }

            const value = field.value;
            const fieldRules = this.rules[fieldName];
            const errors = [];

            // Check each rule
            Object.keys(fieldRules).forEach(ruleName => {
                const ruleValue = fieldRules[ruleName];
                let isValid = true;

                if (ruleName === 'required') {
                    isValid = ValidationRules.required(value);
                } else if (ruleName === 'email') {
                    isValid = ValidationRules.email(value);
                } else if (ruleName === 'minlength') {
                    isValid = ValidationRules.minlength(value, ruleValue);
                } else if (ruleName === 'maxlength') {
                    isValid = ValidationRules.maxlength(value, ruleValue);
                } else if (ruleName === 'min') {
                    isValid = ValidationRules.min(value, ruleValue);
                } else if (ruleName === 'max') {
                    isValid = ValidationRules.max(value, ruleValue);
                } else if (ruleName === 'regex') {
                    isValid = ValidationRules.regex(value, ruleValue);
                } else if (ruleName === 'nzPhone') {
                    isValid = ValidationRules.nzPhone(value);
                } else if (ruleName === 'nzPostcode') {
                    isValid = ValidationRules.nzPostcode(value);
                }

                if (!isValid) {
                    const message = this.getMessage(fieldName, ruleName, ruleValue);
                    errors.push(message);
                }
            });

            // Display errors
            if (errors.length > 0) {
                this.showFieldError(field, errors[0]);
            } else {
                this.showFieldValid(field);
            }

            return errors;
        }

        /**
         * Get error message for field and rule
         */
        getMessage(fieldName, ruleName, ruleValue) {
            // Check custom message first
            if (this.messages[fieldName] && this.messages[fieldName][ruleName]) {
                let message = this.messages[fieldName][ruleName];
                if (typeof ruleValue === 'number') {
                    message = message.replace('{0}', ruleValue);
                }
                return message;
            }

            // Use default message
            let message = DefaultMessages[ruleName] || 'Invalid value';
            if (typeof ruleValue === 'number') {
                message = message.replace('{0}', ruleValue);
            }
            return message;
        }

        /**
         * Show field error
         */
        showFieldError(field, message) {
            field.classList.remove(this.validClass);
            field.classList.add(this.errorClass);

            // Remove existing error message
            const formGroup = field.closest('.form-group, .review-form__field, .review-form__col');
            if (formGroup) {
                const existingError = formGroup.querySelector('.invalid-feedback');
                if (existingError) {
                    existingError.remove();
                }

                // Add new error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = message;
                formGroup.appendChild(errorDiv);
            }
        }

        /**
         * Show field valid
         */
        showFieldValid(field) {
            field.classList.remove(this.errorClass);
            field.classList.add(this.validClass);

            // Remove error message
            const formGroup = field.closest('.form-group, .review-form__field, .review-form__col');
            if (formGroup) {
                const existingError = formGroup.querySelector('.invalid-feedback');
                if (existingError) {
                    existingError.remove();
                }
            }
        }

        /**
         * Clear all errors
         */
        clearErrors() {
            this.form.querySelectorAll(`.${this.errorClass}`).forEach(field => {
                field.classList.remove(this.errorClass);
            });

            this.form.querySelectorAll('.invalid-feedback').forEach(error => {
                error.remove();
            });
        }

        /**
         * Scroll to first error
         */
        scrollToFirstError() {
            const firstError = this.form.querySelector(`.${this.errorClass}`);
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    }

    /**
     * Global initialization function (similar to jQuery Validation API)
     */
    window.initFormValidationNative = function(formSelector, options = {}) {
        const form = typeof formSelector === 'string' 
            ? document.querySelector(formSelector) 
            : formSelector;

        if (!form) {
            // Form not found
            return null;
        }

        return new FormValidator(form, options);
    };

    /**
     * Auto-initialize forms with data-validate attribute
     */
    function autoInit() {
        document.querySelectorAll('form[data-validate]').forEach(form => {
            const rulesJson = form.getAttribute('data-rules');
            const messagesJson = form.getAttribute('data-messages');
            
            const options = {
                rules: rulesJson ? JSON.parse(rulesJson) : {},
                messages: messagesJson ? JSON.parse(messagesJson) : {}
            };

            new FormValidator(form, options);
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoInit);
    } else {
        autoInit();
    }
})();
