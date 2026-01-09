/**
 * Checkout Steps Module
 * Handles the checkout step-based flow (Form -> Review -> Payment)
 */
(function() {
    'use strict';

    class CheckoutSteps {
        constructor() {
            this.currentStep = 1; // 1: Form, 2: Review, 3: Payment
            this.paymentHandler = null;
            this.checkoutConfig = null;
            this.checkoutForm = null;
            this.formValidator = null;
        }

        init(config, paymentHandler, checkoutForm) {
            this.checkoutConfig = config;
            this.paymentHandler = paymentHandler;
            this.checkoutForm = checkoutForm;

            this.initFormValidation();
            this.attachEventListeners();
            this.updateStepIndicator(1);
        }

        initFormValidation() {
            // Wait for jQuery Validation to be available
            if (typeof window.initFormValidation === 'undefined' || typeof jQuery === 'undefined') {
                setTimeout(() => this.initFormValidation(), 100);
                return;
            }

            const $ = jQuery;
            const $form = $(this.checkoutForm);

            if (!$form.length) {
                console.warn('Checkout form not found for validation');
                return;
            }

            // Get NZ-specific rules and messages
            const rules = typeof window.getNZCheckoutRules === 'function'
                ? window.getNZCheckoutRules()
                : {};
            const messages = typeof window.getNZCheckoutMessages === 'function'
                ? window.getNZCheckoutMessages()
                : {};

            // Initialize validation
            this.formValidator = window.initFormValidation($form, {
                rules: rules,
                messages: messages,
                onValidationError: (errors) => {
                    if (typeof window.showValidationErrorNotification === 'function') {
                        window.showValidationErrorNotification(errors, {
                            className: 'checkout-validation-error'
                        });
                    } else {
                        // Fallback to old method
                        this.showValidationError(errors);
                    }
                }
            });
        }

        attachEventListeners() {
            // Review Order Button (Step 1 -> Step 2)
            const reviewBtn = document.getElementById('reviewOrderBtn');
            if (reviewBtn) {
                reviewBtn.addEventListener('click', () => this.showReview());
            }

            // Back to Form Button (Step 2 -> Step 1)
            const backToFormBtn = document.getElementById('backToFormBtn');
            if (backToFormBtn) {
                backToFormBtn.addEventListener('click', () => this.showForm());
            }

            // Proceed to Payment Button (Step 2 -> Step 3)
            const proceedBtn = document.getElementById('proceedToPaymentBtn');
            if (proceedBtn) {
                proceedBtn.addEventListener('click', () => this.showPayment());
            }

            // Back to Review Button (Step 3 -> Step 2)
            const backToReviewBtn = document.getElementById('backToReviewBtn');
            if (backToReviewBtn) {
                backToReviewBtn.addEventListener('click', () => this.showReview());
            }

            // Place Order Button (Step 3)
            const placeOrderBtn = document.getElementById('placeOrderBtn');
            if (placeOrderBtn) {
                placeOrderBtn.addEventListener('click', () => this.placeOrder());
            }
        }

        showForm() {
            this.currentStep = 1;
            this.hideAllSteps();
            const step1 = document.getElementById('checkoutStep1');
            if (step1) step1.style.display = 'block';
            this.updateStepIndicator(1);
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        showReview() {
            // Validate form first
            if (!this.validateForm()) {
                return;
            }

            this.currentStep = 2;
            this.hideAllSteps();
            const step2 = document.getElementById('checkoutStep2');
            if (step2) step2.style.display = 'block';
            this.updateStepIndicator(2);
            this.updateReviewTotals();
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        async showPayment() {
            this.currentStep = 3;
            this.hideAllSteps();
            const step3 = document.getElementById('checkoutStep3');
            if (step3) step3.style.display = 'block';
            this.updateStepIndicator(3);
            this.updatePaymentTotal();

            // Initialize payment
            await this.initPayment();

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        hideAllSteps() {
            const step1 = document.getElementById('checkoutStep1');
            const step2 = document.getElementById('checkoutStep2');
            const step3 = document.getElementById('checkoutStep3');

            if (step1) step1.style.display = 'none';
            if (step2) step2.style.display = 'none';
            if (step3) step3.style.display = 'none';
        }

        updateStepIndicator(step) {
            const indicators = document.querySelectorAll('.checkout-step');
            indicators.forEach((indicator, index) => {
                const stepNum = index + 1;
                if (stepNum < step) {
                    indicator.classList.add('checkout-step--completed');
                    indicator.classList.remove('checkout-step--active');
                } else if (stepNum === step) {
                    indicator.classList.add('checkout-step--active');
                    indicator.classList.remove('checkout-step--completed');
                } else {
                    indicator.classList.remove('checkout-step--active', 'checkout-step--completed');
                }
            });
        }

        async initPayment() {
            if (!this.paymentHandler || !this.checkoutConfig) {
                console.error('Payment handler or config not available');
                return;
            }

            // Create payment intent if not exists
            if (!this.paymentHandler.paymentIntentClientSecret) {
                const proceedBtn = document.getElementById('proceedToPaymentBtn');
                if (proceedBtn) {
                    proceedBtn.disabled = true;
                    proceedBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                }

                try {
                    const success = await this.paymentHandler.createPaymentIntent(this.checkoutConfig.total);
                    if (success) {
                        this.mountPaymentElement();
                    }
                } catch (error) {
                    console.error('Error creating payment intent:', error);
                    this.showPaymentError('Failed to initialize payment. Please try again.');
                } finally {
                    if (proceedBtn) {
                        proceedBtn.disabled = false;
                        proceedBtn.innerHTML = 'Proceed to Payment <i class="fas fa-arrow-right"></i>';
                    }
                }
            } else {
                // Payment intent already exists, just mount the element
                this.mountPaymentElement();
            }
        }

        mountPaymentElement() {
            if (!this.paymentHandler || !this.paymentHandler.elements) {
                console.error('Payment elements not available');
                return;
            }

            const paymentContainer = document.getElementById('payment-element');
            if (!paymentContainer) {
                console.error('Payment container not found');
                return;
            }

            // Check if element already exists and is mounted
            if (this.paymentHandler.paymentElement) {
                // Check if element is already mounted in the container
                if (paymentContainer.children.length > 0) {
                    console.log('Payment element already mounted');
                    const container = document.getElementById('payment-element-container');
                    if (container) {
                        container.style.display = 'block';
                    }
                    return;
                }

                // Element exists but not mounted, unmount it first
                try {
                    this.paymentHandler.paymentElement.unmount();
                    this.paymentHandler.paymentElement = null;
                } catch (e) {
                    console.warn('Error unmounting payment element:', e);
                    this.paymentHandler.paymentElement = null;
                }
            }

            // Create and mount payment element only if it doesn't exist
            if (!this.paymentHandler.paymentElement) {
                try {
                    this.paymentHandler.paymentElement = this.paymentHandler.elements.create('payment');
                    this.paymentHandler.paymentElement.mount('#payment-element');

                    const container = document.getElementById('payment-element-container');
                    if (container) {
                        container.style.display = 'block';
                    }
                    console.log('Payment element mounted');
                } catch (error) {
                    console.error('Error creating payment element:', error);
                    // If element already exists, try to get it from elements
                    if (error.message && error.message.includes('one Element')) {
                        console.log('Payment element already exists, using existing');
                        // Clear and recreate elements instance
                        this.paymentHandler.elements = this.paymentHandler.stripe.elements({
                            clientSecret: this.paymentHandler.paymentIntentClientSecret,
                            appearance: { theme: 'stripe' }
                        });
                        this.paymentHandler.paymentElement = this.paymentHandler.elements.create('payment');
                        this.paymentHandler.paymentElement.mount('#payment-element');
                    }
                }
            }
        }

        async placeOrder() {
            if (!this.paymentHandler || !this.checkoutForm) {
                console.error('Payment handler or form not available');
                return;
            }

            if (!this.paymentHandler.isReady()) {
                this.showPaymentError('Payment system not ready. Please wait for payment form to load and try again.');
                return;
            }

            const placeOrderBtn = document.getElementById('placeOrderBtn');
            const btnText = document.getElementById('placeOrderBtnText');
            const spinner = document.getElementById('placeOrderSpinner');

            if (placeOrderBtn) placeOrderBtn.disabled = true;
            if (btnText) btnText.textContent = 'Processing...';
            if (spinner) spinner.style.display = 'inline-block';

            try {
                const result = await this.paymentHandler.confirmPayment(this.checkoutForm);

                if (result.success) {
                    console.log('Order successful, redirecting to:', result.redirectUrl);
                    window.location.href = result.redirectUrl;
                }
            } catch (error) {
                console.error('Error in order placement:', error);

                // Handle payment intent recreation
                if (error.message === 'PAYMENT_INTENT_RECREATE') {
                    // Clear existing payment element
                    if (this.paymentHandler.paymentElement) {
                        try {
                            this.paymentHandler.paymentElement.unmount();
                        } catch (e) {
                            console.warn('Error unmounting payment element:', e);
                        }
                        this.paymentHandler.paymentElement = null;
                    }
                    // Clear elements instance
                    this.paymentHandler.elements = null;

                    await this.paymentHandler.createPaymentIntent(this.checkoutConfig.total);
                    this.mountPaymentElement();
                    this.showPaymentError('Payment form was updated. Please try again.');
                } else {
                    this.showPaymentError('An error occurred: ' + error.message);
                }

                if (placeOrderBtn) placeOrderBtn.disabled = false;
                if (btnText) btnText.textContent = 'Place Order';
                if (spinner) spinner.style.display = 'none';
            }
        }

        showPaymentError(message) {
            const errorElement = document.getElementById('payment-errors');
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
        }

        updateReviewTotals() {
            const subtotal = document.getElementById('checkoutSubtotal');
            const discount = document.getElementById('checkoutDiscount');
            const shipping = document.getElementById('checkoutShipping');
            const total = document.getElementById('checkoutTotal');

            const reviewSubtotal = document.getElementById('reviewSubtotal');
            const reviewDiscount = document.getElementById('reviewDiscount');
            const reviewShipping = document.getElementById('reviewShipping');
            const reviewTotal = document.getElementById('reviewTotal');
            const reviewShippingRow = document.getElementById('reviewShippingRow');

            if (subtotal && reviewSubtotal) {
                reviewSubtotal.textContent = subtotal.textContent;
            }

            if (discount && reviewDiscount) {
                const discountRow = reviewDiscount.closest('.checkout-review-block__total-row');
                if (discountRow) {
                    discountRow.style.display = 'flex';
                    reviewDiscount.textContent = discount.textContent;
                }
            } else {
                const discountRow = document.querySelector('.checkout-review-block__total-row--discount');
                if (discountRow) {
                    discountRow.style.display = 'none';
                }
            }

            if (shipping && reviewShipping) {
                reviewShipping.textContent = shipping.textContent;
                if (reviewShippingRow) {
                    const shippingValue = parseFloat(shipping.textContent.replace('$', '').replace(',', ''));
                    reviewShippingRow.style.display = shippingValue > 0 ? 'flex' : 'none';
                }
            }

            if (total && reviewTotal) {
                reviewTotal.textContent = total.textContent;
            }
        }

        updatePaymentTotal() {
            const total = document.getElementById('checkoutTotal');
            const paymentTotal = document.getElementById('paymentFinalTotal');

            if (total && paymentTotal) {
                paymentTotal.textContent = total.textContent;
            }
        }

        validateForm() {
            const form = this.checkoutForm;
            if (!form) return false;

            // Use jQuery Validation if available
            if (this.formValidator && typeof jQuery !== 'undefined') {
                const isValid = this.formValidator.form();
                return isValid;
            }

            // Fallback to manual validation if jQuery Validation not available
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            const emptyFields = [];

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');

                    // Get field label for better error message
                    const label = field.closest('.form-group')?.querySelector('label')?.textContent?.trim() ||
                                  field.getAttribute('placeholder') ||
                                  field.getAttribute('name') ||
                                  'Field';
                    emptyFields.push(label.replace('*', '').trim());
                } else {
                    field.classList.remove('error');
                }
            });

            if (!isValid) {
                // Scroll to first error field
                const firstError = form.querySelector('[required].error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }

                // Show better notification instead of alert
                this.showValidationError(emptyFields);
                return false;
            }

            return true;
        }

        showValidationError(emptyFields) {
            // Remove existing error message if any
            const existingError = document.querySelector('.checkout-validation-error');
            if (existingError) {
                existingError.remove();
            }

            // Create error message element
            const errorDiv = document.createElement('div');
            errorDiv.className = 'checkout-validation-error';
            errorDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
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
                : 'Please fill in all required fields before reviewing your order.';

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

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (errorDiv.parentElement) {
                    errorDiv.style.animation = 'slideOutRight 0.3s ease';
                    setTimeout(() => errorDiv.remove(), 300);
                }
            }, 5000);
        }
    }

    // Export globally (keeping same name for compatibility)
    window.CheckoutModal = CheckoutSteps;
})();
