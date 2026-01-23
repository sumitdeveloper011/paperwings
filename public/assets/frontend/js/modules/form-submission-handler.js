/**
 * Form Submission Handler Module (DRY Approach)
 * Prevents double submission and shows loading state for all forms
 */
(function() {
    'use strict';

    window.FormSubmissionHandler = {
        /**
         * Initialize form submission handler
         * @param {string|HTMLElement} formSelector - Form ID or form element
         * @param {Object} options - Configuration options
         * @param {string} options.loadingText - Text to show during submission
         * @param {number} options.timeout - Safety timeout in milliseconds (default: 10000)
         * @param {string} options.loadingIcon - Icon class for loading state (default: 'fa-spinner fa-spin')
         * @param {Function} options.onSubmit - Callback before submission
         * @param {Function} options.onComplete - Callback after submission completes
         */
        init: function(formSelector, options = {}) {
            const form = typeof formSelector === 'string' 
                ? document.getElementById(formSelector) 
                : formSelector;

            if (!form) {
                console.warn('FormSubmissionHandler: Form not found', formSelector);
                return;
            }

            const config = {
                loadingText: options.loadingText || 'Processing...',
                timeout: options.timeout || 10000,
                loadingIcon: options.loadingIcon || 'fa-spinner fa-spin',
                onSubmit: options.onSubmit || null,
                onComplete: options.onComplete || null
            };

            let isSubmitting = false;
            const submitBtn = form.querySelector('button[type="submit"]');
            let originalHTML = submitBtn ? submitBtn.innerHTML : '';
            let originalDisabled = submitBtn ? submitBtn.disabled : false;

            form.addEventListener('submit', function(e) {
                // Check form validity first
                const isValid = form.checkValidity();
                
                if (!isValid) {
                    form.classList.add('was-validated');
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                
                if (isSubmitting) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                if (config.onSubmit && typeof config.onSubmit === 'function') {
                    const result = config.onSubmit(e, form);
                    if (result === false) {
                        e.preventDefault();
                        return false;
                    }
                }
                
                isSubmitting = true;

                if (submitBtn) {
                    originalHTML = submitBtn.innerHTML;
                    originalDisabled = submitBtn.disabled;
                    submitBtn.disabled = true;
                    
                    const icon = submitBtn.querySelector('i');
                    if (icon) {
                        icon.className = 'fas ' + config.loadingIcon;
                        submitBtn.innerHTML = icon.outerHTML + ' ' + config.loadingText;
                    } else {
                        submitBtn.innerHTML = '<i class="fas ' + config.loadingIcon + '"></i> ' + config.loadingText;
                    }
                }

                const safetyTimeout = setTimeout(function() {
                    if (isSubmitting) {
                        isSubmitting = false;
                        if (submitBtn) {
                            submitBtn.disabled = originalDisabled;
                            submitBtn.innerHTML = originalHTML;
                        }
                        if (config.onComplete && typeof config.onComplete === 'function') {
                            config.onComplete(false, 'Timeout');
                        }
                    }
                }, config.timeout);

                form.dataset.submissionTimeout = safetyTimeout;
                form.dataset.isSubmitting = 'true';
            });
        },

        /**
         * Initialize multiple forms at once
         * @param {Array} forms - Array of form configurations [{id: 'formId', options: {}}]
         */
        initMultiple: function(forms) {
            if (!Array.isArray(forms)) {
                console.warn('FormSubmissionHandler.initMultiple: Expected array');
                return;
            }

            forms.forEach(function(formConfig) {
                if (formConfig.id) {
                    window.FormSubmissionHandler.init(formConfig.id, formConfig.options || {});
                }
            });
        },

        /**
         * Reset form submission state (useful for manual resets)
         * @param {string|HTMLElement} formSelector - Form ID or form element
         */
        reset: function(formSelector) {
            const form = typeof formSelector === 'string' 
                ? document.getElementById(formSelector) 
                : formSelector;

            if (!form) return;

            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && submitBtn.dataset.originalHTML) {
                submitBtn.disabled = submitBtn.dataset.originalDisabled === 'true';
                submitBtn.innerHTML = submitBtn.dataset.originalHTML;
            }
        },

        /**
         * Auto-initialize forms with data-form-submit attribute
         */
        autoInit: function() {
            const forms = document.querySelectorAll('form[data-form-submit]');
            forms.forEach(function(form) {
                const formId = form.id || form.getAttribute('id');
                const loadingText = form.dataset.loadingText || 'Processing...';
                const timeout = parseInt(form.dataset.timeout) || 10000;

                if (formId) {
                    window.FormSubmissionHandler.init(formId, {
                        loadingText: loadingText,
                        timeout: timeout
                    });
                }
            });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.FormSubmissionHandler.autoInit();
        });
    } else {
        window.FormSubmissionHandler.autoInit();
    }
})();
