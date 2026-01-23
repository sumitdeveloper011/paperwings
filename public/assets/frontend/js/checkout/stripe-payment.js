/**
 * Stripe Payment Handler
 * Handles Stripe payment initialization and processing
 */
class StripePaymentHandler {
    constructor(config) {
        this.stripeKey = config.stripeKey;
        this.clientSecret = config.clientSecret || null;
        this.paymentIntentId = config.paymentIntentId || null;
        this.createPaymentIntentUrl = config.createPaymentIntentUrl;
        this.csrfToken = config.csrfToken;
        this.total = config.total;
        this.stripe = null;
        this.elements = null;
        this.paymentElement = null;
        this.isProcessing = false;
        this.init();
    }

    async init() {
        console.log('[StripePayment] Initializing StripePaymentHandler');
        console.log('[StripePayment] Stripe library available:', typeof Stripe !== 'undefined');
        
        if (typeof Stripe === 'undefined') {
            console.error('[StripePayment] Stripe library not loaded');
            this.showLoading(false);
            this.showError('Payment system is not available. Please refresh the page.');
            return;
        }

        try {
            console.log('[StripePayment] Creating Stripe instance with key:', this.stripeKey ? this.stripeKey.substring(0, 20) + '...' : 'MISSING');
            this.stripe = Stripe(this.stripeKey);
            console.log('[StripePayment] Stripe instance created:', !!this.stripe);
            await this.initializePayment();
        } catch (error) {
            console.error('[StripePayment] Error in init:', error);
            console.error('[StripePayment] Error stack:', error.stack);
            this.showLoading(false);
            this.setPaymentReady(false);
            this.showError('Failed to initialize payment. Please refresh the page.');
        }
    }

    async initializePayment() {
        try {
            console.log('[StripePayment] Starting payment initialization');
            console.log('[StripePayment] Total amount:', this.total);
            console.log('[StripePayment] Stripe key exists:', !!this.stripeKey);
            console.log('[StripePayment] Stripe instance:', !!this.stripe);
            console.log('[StripePayment] Pre-created clientSecret:', !!this.clientSecret);
            
            // Show loading state
            this.showLoading(true);
            
            let clientSecret = this.clientSecret;
            
            // If clientSecret is not provided, create payment intent via API (fallback)
            if (!clientSecret) {
                console.log('[StripePayment] No pre-created clientSecret, creating payment intent via API...');
                const response = await fetch(this.createPaymentIntentUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ amount: this.total })
                });

                console.log('[StripePayment] Payment intent response status:', response.status);

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    console.error('[StripePayment] Payment intent creation failed:', errorData);
                    
                    // Handle rate limiting (429)
                    if (response.status === 429) {
                        const retryAfter = errorData.errors?.retry_after || errorData.data?.retry_after || errorData.retry_after || response.headers.get('Retry-After') || 60;
                        const waitTime = parseInt(retryAfter);
                        throw new Error(`Too many requests. Please wait ${waitTime} second${waitTime !== 1 ? 's' : ''} before trying again.`);
                    }
                    
                    throw new Error(errorData.message || 'Failed to create payment intent. Please try again.');
                }

                const data = await response.json();
                console.log('[StripePayment] Payment intent response data:', data);

                if (!data.success) {
                    console.error('[StripePayment] Payment intent creation failed:', data.message);
                    throw new Error(data.message || 'Failed to create payment intent. Please try again.');
                }

                // Extract clientSecret from data.data (jsonSuccess wraps it)
                clientSecret = data.data?.clientSecret || data.clientSecret;
                console.log('[StripePayment] Client secret extracted from API:', clientSecret ? 'Yes' : 'No');
                
                if (!clientSecret) {
                    console.error('[StripePayment] Missing client secret in response');
                    console.error('[StripePayment] Response structure:', JSON.stringify(data, null, 2));
                    throw new Error('Payment intent created but client secret is missing. Please try again.');
                }
            } else {
                console.log('[StripePayment] Using pre-created clientSecret from server');
            }

            console.log('[StripePayment] Client secret ready, creating Stripe Elements...');
            this.elements = this.stripe.elements({
                clientSecret: clientSecret,
                appearance: { theme: 'stripe' }
            });
            console.log('[StripePayment] Stripe Elements created:', !!this.elements);

            const paymentElementContainer = document.getElementById('payment-element');
            console.log('[StripePayment] Payment element container found:', !!paymentElementContainer);
            if (paymentElementContainer) {
                console.log('[StripePayment] Container current display:', window.getComputedStyle(paymentElementContainer).display);
                console.log('[StripePayment] Container current visibility:', window.getComputedStyle(paymentElementContainer).visibility);
            }

            if (!paymentElementContainer) {
                throw new Error('Payment element container not found');
            }

            console.log('[StripePayment] Creating payment element...');
            this.paymentElement = this.elements.create('payment');
            console.log('[StripePayment] Payment element created:', !!this.paymentElement);
            
            console.log('[StripePayment] Mounting payment element to #payment-element...');
            this.paymentElement.mount('#payment-element');
            console.log('[StripePayment] Payment element mounted');

            // Wait for Stripe to render the payment element
            // Stripe Elements need a moment to initialize and render
            setTimeout(() => {
                console.log('[StripePayment] Timeout callback - showing payment element');
                const loadingElement = document.getElementById('payment-loading');
                const paymentElement = document.getElementById('payment-element');
                
                console.log('[StripePayment] Loading element found:', !!loadingElement);
                console.log('[StripePayment] Payment element found:', !!paymentElement);
                
                if (loadingElement) {
                    console.log('[StripePayment] Hiding loading element');
                    loadingElement.style.display = 'none';
                }
                
                if (paymentElement) {
                    console.log('[StripePayment] Payment element before style change:');
                    console.log('  - display:', window.getComputedStyle(paymentElement).display);
                    console.log('  - visibility:', window.getComputedStyle(paymentElement).visibility);
                    console.log('  - opacity:', window.getComputedStyle(paymentElement).opacity);
                    console.log('  - height:', window.getComputedStyle(paymentElement).height);
                    console.log('  - innerHTML length:', paymentElement.innerHTML.length);
                    
                    // Force display - remove any inline styles that might hide it
                    paymentElement.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; min-height: 200px;';
                    
                    console.log('[StripePayment] Payment element after style change:');
                    console.log('  - display:', window.getComputedStyle(paymentElement).display);
                    console.log('  - visibility:', window.getComputedStyle(paymentElement).visibility);
                    console.log('  - opacity:', window.getComputedStyle(paymentElement).opacity);
                    console.log('  - height:', window.getComputedStyle(paymentElement).height);
                }
                
                // Enable submit button
                console.log('[StripePayment] Enabling submit button');
                this.setPaymentReady(true);
            }, 500);
            
            this.attachFormHandler();
        } catch (error) {
            console.error('[StripePayment] Error during initialization:', error);
            console.error('[StripePayment] Error stack:', error.stack);
            this.showLoading(false);
            this.setPaymentReady(false);
            this.showError(error.message || 'Failed to initialize payment. Please refresh the page.');
        }
    }
    
    showLoading(show) {
        const loadingElement = document.getElementById('payment-loading');
        const paymentElement = document.getElementById('payment-element');
        const paymentElementContainer = document.getElementById('payment-element-container');
        
        if (loadingElement) {
            loadingElement.style.display = show ? 'block' : 'none';
        }
        
        if (paymentElement) {
            paymentElement.style.display = show ? 'none' : 'block';
            if (!show) {
                paymentElement.style.visibility = 'visible';
                paymentElement.style.opacity = '1';
            }
        }
        
        if (paymentElementContainer && !show) {
            paymentElementContainer.style.display = 'block';
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
            
            // Prevent double submission
            if (this.isProcessing) {
                return;
            }
            
            await this.processPayment();
        });
    }

    async processPayment() {
        if (this.isProcessing) {
            return;
        }

        const submitButton = document.getElementById('placeOrderBtn');
        const buttonText = document.getElementById('placeOrderBtnText');
        const spinner = document.getElementById('placeOrderSpinner');

        this.isProcessing = true;
        this.setButtonLoading(submitButton, buttonText, spinner, true);
        this.clearError();

        try {
            const { error, paymentIntent } = await this.stripe.confirmPayment({
                elements: this.elements,
                confirmParams: {
                    return_url: window.location.origin + '/checkout/success'
                },
                redirect: 'if_required'
            });

            if (error) {
                this.showError(error.message || 'Payment failed. Please try again.');
                this.setButtonLoading(submitButton, buttonText, spinner, false);
                this.isProcessing = false;
            } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                // Submit form via AJAX to handle JSON response and redirect
                await this.submitOrderForm(paymentIntent.id, submitButton, buttonText, spinner);
            } else {
                this.showError('Payment status is unexpected. Please try again.');
                this.setButtonLoading(submitButton, buttonText, spinner, false);
                this.isProcessing = false;
            }
        } catch (err) {
            this.showError('An error occurred while processing payment. Please try again.');
            this.setButtonLoading(submitButton, buttonText, spinner, false);
            this.isProcessing = false;
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
            this.isProcessing = false;
            return;
        }

        try {
            // Create FormData from form
            const formData = new FormData(form);
            formData.append('payment_intent_id', paymentIntentId);

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error('Failed to process order. Please try again.');
            }

            const data = await response.json();

            // Extract redirect_url from data.data (jsonSuccess wraps it)
            const redirectUrl = data.data?.redirect_url || data.redirect_url;

            if (data.success && redirectUrl) {
                // Redirect to success page
                window.location.href = redirectUrl;
            } else {
                this.showError(data.message || 'Failed to place order. Please try again.');
                this.setButtonLoading(submitButton, buttonText, spinner, false);
                this.isProcessing = false;
            }
        } catch (error) {
            this.showError('An error occurred while placing your order. Please try again.');
            this.setButtonLoading(submitButton, buttonText, spinner, false);
            this.isProcessing = false;
        }
    }

    showError(message) {
        const errorElement = document.getElementById('payment-errors');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            
            // Scroll to error if needed
            errorElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    clearError() {
        const errorElement = document.getElementById('payment-errors');
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }
}
