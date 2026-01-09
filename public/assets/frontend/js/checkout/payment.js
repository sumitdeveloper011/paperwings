/**
 * Payment Processing Module
 * Handles Stripe payment initialization and processing
 */
class PaymentHandler {
    constructor(config) {
        this.stripeKey = config.stripeKey;
        this.csrfToken = config.csrfToken;
        this.createPaymentIntentUrl = config.createPaymentIntentUrl;
        this.processOrderUrl = config.processOrderUrl;
        this.stripe = null;
        this.elements = null;
        this.paymentElement = null;
        this.paymentIntentClientSecret = null;
        this.onPaymentReady = config.onPaymentReady || null;
    }

    /**
     * Initialize Stripe
     * @returns {boolean} - True if initialized successfully
     */
    init() {
        if (!this.stripeKey || typeof this.stripeKey !== 'string' ||
            !this.stripeKey.trim() || !this.stripeKey.startsWith('pk_')) {
            console.warn('Stripe key not configured');
            this.showError('Payment system is not configured. Please add STRIPE_KEY and STRIPE_SECRET to your .env file.');
            return false;
        }

        try {
            this.stripe = Stripe(this.stripeKey);
            console.log('Stripe initialized successfully');
            return true;
        } catch (error) {
            console.error('Failed to initialize Stripe:', error);
            this.showError('Failed to initialize payment system: ' + error.message);
            return false;
        }
    }

    /**
     * Create a payment intent
     * @param {number} amount - The total amount
     * @returns {Promise<boolean>} - True if successful
     */
    async createPaymentIntent(amount) {
        const roundedTotal = Math.round(amount * 100) / 100;

        console.log('createPaymentIntent called', {
            total: roundedTotal
        });

        if (roundedTotal <= 0) {
            console.warn('Total is 0 or negative, skipping payment intent creation');
            return false;
        }

        // Unmount existing payment element if present
        if (this.paymentElement) {
            try {
                this.paymentElement.unmount();
                this.paymentElement = null;
            } catch(e) {
                console.warn('Error unmounting payment element:', e);
            }
        }

        // Clear elements instance to allow fresh creation
        this.elements = null;

        try {
            const response = await fetch(this.createPaymentIntentUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ amount: roundedTotal })
            });

            const data = await response.json();
            console.log('Payment intent response:', data);

            const clientSecret = data.clientSecret || data.client_secret;

            if (data.success && clientSecret) {
                this.paymentIntentClientSecret = clientSecret;
                console.log('Payment intent created, client secret received');

                // Always create fresh elements instance
                console.log('Creating Stripe elements...');
                this.elements = this.stripe.elements({
                    clientSecret: this.paymentIntentClientSecret,
                    appearance: { theme: 'stripe' }
                });

                // Create payment element (don't mount yet - will mount in modal when user proceeds to payment)
                // The element will be created and mounted by the modal when needed
                console.log('Payment intent created, ready for modal mounting');

                if (this.onPaymentReady) {
                    this.onPaymentReady();
                }

                return true;
            } else {
                console.error('Payment intent creation failed:', data);
                this.showError(data.message || 'Failed to initialize payment.');
                return false;
            }
        } catch (error) {
            console.error('Error creating payment intent:', error);
            this.showError('Failed to initialize payment. Please refresh the page.');
            return false;
        }
    }

    /**
     * Confirm payment and process order
     * @param {HTMLFormElement} form - The checkout form
     * @returns {Promise<Object>} - Result object with success status and redirect URL or error message
     */
    async confirmPayment(form) {
        if (!this.stripe || !this.elements || !this.paymentIntentClientSecret) {
            throw new Error('Payment system not ready. Please wait for payment form to load and try again.');
        }

        // Verify payment element is mounted
        if (!this.paymentElement) {
            console.warn('Payment element not found, recreating...');
            if (!this.elements) {
                this.elements = this.stripe.elements({
                    clientSecret: this.paymentIntentClientSecret,
                    appearance: { theme: 'stripe' }
                });
            }

            // Check if modal container exists, otherwise use regular container
            const mountTarget = '#payment-element';

            this.paymentElement = this.elements.create('payment');
            this.paymentElement.mount(mountTarget);
            await new Promise(resolve => setTimeout(resolve, 500));
        }

        // Verify element is still mounted
        const paymentElementContainer = document.getElementById('payment-element');

        if (!paymentElementContainer || !paymentElementContainer.children.length) {
            console.warn('Payment element container empty, checking if element exists...');
            // Only create if element doesn't exist
            if (!this.paymentElement) {
                try {
                    this.paymentElement = this.elements.create('payment');
                    this.paymentElement.mount('#payment-element');
                    await new Promise(resolve => setTimeout(resolve, 500));
                } catch (error) {
                    console.error('Error creating payment element:', error);
                    if (error.message && error.message.includes('one Element')) {
                        console.log('Payment element already exists, skipping creation');
                    } else {
                        throw error;
                    }
                }
            } else {
                // Element exists but not mounted, try to remount
                try {
                    this.paymentElement.mount('#payment-element');
                    await new Promise(resolve => setTimeout(resolve, 500));
                } catch (error) {
                    console.error('Error remounting payment element:', error);
                }
            }
        }

        const {error: stripeError, paymentIntent} = await this.stripe.confirmPayment({
            elements: this.elements,
            confirmParams: {
                return_url: window.location.origin + '/checkout/success',
            },
            redirect: 'if_required'
        });

        if (stripeError) {
            console.error('Stripe payment error:', stripeError);

            // Handle payment intent unexpected state
            if (stripeError.code === 'payment_intent_unexpected_state') {
                console.log('Payment intent in unexpected state, recreating...');
                this.paymentIntentClientSecret = null;
                if (this.paymentElement) {
                    try {
                        this.paymentElement.unmount();
                        this.paymentElement = null;
                    } catch(e) {
                        console.warn('Error unmounting payment element:', e);
                    }
                }
                // Clear elements instance to allow recreation
                this.elements = null;
                throw new Error('PAYMENT_INTENT_RECREATE');
            } else {
                throw new Error(stripeError.message || 'Payment failed. Please try again.');
            }
        }

        console.log('Payment confirmed, processing order...', paymentIntent);

        // Process order
        // If "same as billing" is checked, copy billing values to shipping fields before submission
        const sameAsBillingCheckbox = document.getElementById('sameAsBilling');
        const shippingFieldsToCopy = [
            { billing: 'billing_first_name', shipping: 'shipping_first_name' },
            { billing: 'billing_last_name', shipping: 'shipping_last_name' },
            { billing: 'billing_email', shipping: 'shipping_email' },
            { billing: 'billing_phone', shipping: 'shipping_phone' },
            { billing: 'billing_street_address', shipping: 'shipping_street_address' },
            { billing: 'billing_city', shipping: 'shipping_city' },
            { billing: 'billing_suburb', shipping: 'shipping_suburb' },
            { billing: 'billing_region_id', shipping: 'shipping_region_id' },
            { billing: 'billing_zip_code', shipping: 'shipping_zip_code' }
        ];

        if (sameAsBillingCheckbox && sameAsBillingCheckbox.checked) {
            // Enable shipping fields and copy billing values
            shippingFieldsToCopy.forEach(({ billing, shipping }) => {
                const billingField = form.querySelector(`[name="${billing}"]`);
                const shippingField = form.querySelector(`[name="${shipping}"]`);
                if (billingField && shippingField) {
                    // Enable shipping field temporarily
                    shippingField.disabled = false;
                    // Copy value from billing to shipping
                    shippingField.value = billingField.value;
                }
            });
        }

        const formData = new FormData(form);
        const orderData = {};
        formData.forEach((value, key) => {
            orderData[key] = value;
        });
        orderData.payment_intent_id = paymentIntent.id;

        // Restore disabled state of shipping fields after form data collection
        if (sameAsBillingCheckbox && sameAsBillingCheckbox.checked) {
            shippingFieldsToCopy.forEach(({ shipping }) => {
                const shippingField = form.querySelector(`[name="${shipping}"]`);
                if (shippingField) {
                    shippingField.disabled = true;
                }
            });
        }

        const response = await fetch(this.processOrderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(orderData)
        });

        const result = await response.json();
        console.log('Order processing result:', result);

        if (result.success) {
            return {
                success: true,
                redirectUrl: result.redirect_url
            };
        } else {
            throw new Error(result.message || 'Failed to process order.');
        }
    }

    /**
     * Show error message
     * @param {string} message - Error message
     */
    showError(message) {
        // Try modal error element first, then regular
        const modalErrorElement = document.getElementById('modal-payment-errors');
        const errorElement = document.getElementById('payment-errors');

        if (modalErrorElement) {
            modalErrorElement.innerHTML = message;
            modalErrorElement.style.display = 'block';
        } else if (errorElement) {
            errorElement.innerHTML = message;
            errorElement.style.display = 'block';
        }
    }

    /**
     * Clear payment intent (for recreation)
     */
    clearPaymentIntent() {
        this.paymentIntentClientSecret = null;
        if (this.paymentElement) {
            try {
                this.paymentElement.unmount();
            } catch(e) {
                console.warn('Error unmounting payment element:', e);
            }
            this.paymentElement = null;
        }
    }

    /**
     * Check if payment is ready
     * @returns {boolean}
     */
    isReady() {
        return !!(this.stripe && this.elements && this.paymentIntentClientSecret);
    }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PaymentHandler;
}

