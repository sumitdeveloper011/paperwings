/**
 * Stripe Payment Handler
 * Handles Stripe payment initialization and processing
 */
class StripePaymentHandler {
    constructor(config) {
        this.stripeKey = config.stripeKey;
        this.createPaymentIntentUrl = config.createPaymentIntentUrl;
        this.csrfToken = config.csrfToken;
        this.total = config.total;
        this.stripe = null;
        this.elements = null;
        this.paymentElement = null;
        this.init();
    }

    async init() {
        if (typeof Stripe === 'undefined') {
            this.showLoading(false);
            this.showError('Payment system is not available. Please refresh the page.');
            return;
        }

        try {
            this.stripe = Stripe(this.stripeKey);
            await this.initializePayment();
        } catch (error) {
            this.showLoading(false);
            this.setPaymentReady(false);
            this.showError('Failed to initialize payment. Please refresh the page.');
        }
    }

    async initializePayment() {
        try {
            // Show loading state
            this.showLoading(true);
            
            const response = await fetch(this.createPaymentIntentUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                body: JSON.stringify({ amount: this.total })
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to create payment intent');
            }

            this.elements = this.stripe.elements({
                clientSecret: data.clientSecret,
                appearance: { theme: 'stripe' }
            });

            this.paymentElement = this.elements.create('payment');
            this.paymentElement.mount('#payment-element');

            // Hide loading and show payment element
            this.showLoading(false);
            
            // Enable submit button
            this.setPaymentReady(true);
            
            this.attachFormHandler();
        } catch (error) {
            this.showLoading(false);
            this.showError(error.message || 'Failed to initialize payment. Please refresh the page.');
        }
    }
    
    showLoading(show) {
        const loadingElement = document.getElementById('payment-loading');
        const paymentElement = document.getElementById('payment-element');
        
        if (loadingElement) {
            loadingElement.style.display = show ? 'block' : 'none';
        }
        
        if (paymentElement) {
            paymentElement.style.display = show ? 'none' : 'block';
        }
    }
    
    setPaymentReady(ready) {
        const submitButton = document.getElementById('placeOrderBtn');
        const buttonText = document.getElementById('placeOrderBtnText');
        
        if (submitButton) {
            submitButton.disabled = !ready;
        }
        
        if (buttonText) {
            buttonText.textContent = ready ? 'Place Order' : 'Loading Payment Options...';
        }
    }

    attachFormHandler() {
        const form = document.getElementById('checkoutForm');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.processPayment();
        });
    }

    async processPayment() {
        const submitButton = document.getElementById('placeOrderBtn');
        const buttonText = document.getElementById('placeOrderBtnText');
        const spinner = document.getElementById('placeOrderSpinner');

        this.setButtonLoading(submitButton, buttonText, spinner, true);

        try {
            const { error, paymentIntent } = await this.stripe.confirmPayment({
                elements: this.elements,
                confirmParams: {
                    return_url: window.location.origin + '/checkout/success'
                },
                redirect: 'if_required'
            });

            if (error) {
                this.showError(error.message);
                this.setButtonLoading(submitButton, buttonText, spinner, false);
            } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                // Submit form via AJAX to handle JSON response and redirect
                await this.submitOrderForm(paymentIntent.id, submitButton, buttonText, spinner);
            }
        } catch (err) {
            this.showError('An error occurred. Please try again.');
            this.setButtonLoading(submitButton, buttonText, spinner, false);
        }
    }

    setButtonLoading(button, text, spinner, loading) {
        if (button) button.disabled = loading;
        if (text) text.textContent = loading ? 'Processing...' : 'Place Order';
        if (spinner) spinner.style.display = loading ? 'inline-block' : 'none';
    }

    async submitOrderForm(paymentIntentId, submitButton, buttonText, spinner) {
        const form = document.getElementById('checkoutForm');
        if (!form || !paymentIntentId) {
            this.showError('Payment succeeded but payment intent ID is missing. Please contact support.');
            this.setButtonLoading(submitButton, buttonText, spinner, false);
            return;
        }

        try {
            // Create FormData from form
            const formData = new FormData(form);
            formData.append('payment_intent_id', paymentIntentId);

            console.log('[StripePaymentHandler] Submitting order form with payment_intent_id:', paymentIntentId);

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success && data.redirect_url) {
                console.log('[StripePaymentHandler] Order placed successfully, redirecting to:', data.redirect_url);
                // Redirect to success page
                window.location.href = data.redirect_url;
            } else {
                this.showError(data.message || 'Failed to place order. Please try again.');
                this.setButtonLoading(submitButton, buttonText, spinner, false);
            }
        } catch (error) {
            console.error('[StripePaymentHandler] Error submitting order:', error);
            this.showError('An error occurred while placing your order. Please try again.');
            this.setButtonLoading(submitButton, buttonText, spinner, false);
        }
    }

    showError(message) {
        const errorElement = document.getElementById('payment-errors');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }
}
