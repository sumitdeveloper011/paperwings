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

                if (!this.elements) {
                    console.log('Creating Stripe elements...');
                    this.elements = this.stripe.elements({
                        clientSecret: this.paymentIntentClientSecret,
                        appearance: { theme: 'stripe' }
                    });
                } else {
                    console.log('Updating existing elements with new client secret...');
                    this.elements.update({ clientSecret: this.paymentIntentClientSecret });
                }

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
            const modalContainer = document.getElementById('modal-payment-element');
            const regularContainer = document.getElementById('payment-element');
            const mountTarget = modalContainer ? '#modal-payment-element' : '#payment-element';
            
            this.paymentElement = this.elements.create('payment');
            this.paymentElement.mount(mountTarget);
            await new Promise(resolve => setTimeout(resolve, 500));
        }

        // Verify element is still mounted
        const modalPaymentContainer = document.getElementById('modal-payment-element');
        const regularPaymentContainer = document.getElementById('payment-element');
        const paymentElementContainer = modalPaymentContainer || regularPaymentContainer;
        
        if (!paymentElementContainer || !paymentElementContainer.children.length) {
            console.warn('Payment element container empty, remounting...');
            const mountTarget = modalPaymentContainer ? '#modal-payment-element' : '#payment-element';
            this.paymentElement = this.elements.create('payment');
            this.paymentElement.mount(mountTarget);
            await new Promise(resolve => setTimeout(resolve, 500));
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
                throw new Error('PAYMENT_INTENT_RECREATE');
            } else {
                throw new Error(stripeError.message || 'Payment failed. Please try again.');
            }
        }

        console.log('Payment confirmed, processing order...', paymentIntent);

        // Process order
        const formData = new FormData(form);
        const orderData = {};
        formData.forEach((value, key) => {
            orderData[key] = value;
        });
        orderData.payment_intent_id = paymentIntent.id;

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

