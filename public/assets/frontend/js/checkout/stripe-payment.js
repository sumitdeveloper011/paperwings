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
        this.retryInProgress = false;
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
            
            // Add event listeners for payment element
            this.paymentElement.on('ready', () => {
                console.log('[StripePayment] Payment element ready');
            });
            
            this.paymentElement.on('change', (event) => {
                if (event.error) {
                    console.warn('[StripePayment] Payment element change error:', event.error);
                    this.showError(event.error.message);
                } else {
                    this.clearError();
                }
            });
            
            this.paymentElement.on('loaderror', (event) => {
                console.error('[StripePayment] Payment element load error:', event);
                console.error('[StripePayment] Error details:', {
                    elementType: event.elementType,
                    error: event.error,
                    errorType: event.error?.type,
                    errorCode: event.error?.code,
                    errorMessage: event.error?.message,
                    errorDeclineCode: event.error?.decline_code,
                    errorParam: event.error?.param,
                    clientSecret: clientSecret ? clientSecret.substring(0, 20) + '...' : 'missing'
                });
                
                let errorMessage = 'Failed to load payment form. ';
                if (event.error?.message) {
                    errorMessage += event.error.message;
                } else if (event.error?.type) {
                    errorMessage += 'Error type: ' + event.error.type;
                } else {
                    errorMessage += 'Please refresh the page and try again.';
                }
                
                this.showError(errorMessage);
                this.setPaymentReady(false);
            });
            
            this.paymentElement.on('loaderstart', () => {
                console.log('[StripePayment] Payment element loader started');
            });
            
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
        console.log('[StripePayment] processPayment called');
        
        if (this.isProcessing) {
            console.warn('[StripePayment] Payment already processing, ignoring duplicate call');
            return;
        }

        const submitButton = document.getElementById('placeOrderBtn');
        const buttonText = document.getElementById('placeOrderBtnText');
        const spinner = document.getElementById('placeOrderSpinner');

        console.log('[StripePayment] Starting payment processing', {
            submitButton: !!submitButton,
            buttonText: !!buttonText,
            spinner: !!spinner
        });

        this.isProcessing = true;
        this.setButtonLoading(submitButton, buttonText, spinner, true);
        this.clearError();

        try {
            console.log('[StripePayment] Confirming payment with Stripe');
            const { error, paymentIntent } = await this.stripe.confirmPayment({
                elements: this.elements,
                confirmParams: {
                    return_url: window.location.origin + '/checkout/success'
                },
                redirect: 'if_required'
            });

            console.log('[StripePayment] Payment confirmation response', {
                error: error ? error.message : null,
                paymentIntent: paymentIntent ? {
                    id: paymentIntent.id,
                    status: paymentIntent.status
                } : null
            });

            if (error) {
                console.error('[StripePayment] Payment error', {
                    error: error.message,
                    code: error.code,
                    type: error.type
                });
                this.showError(error.message || 'Payment failed. Please try again.');
                this.setButtonLoading(submitButton, buttonText, spinner, false);
                this.isProcessing = false;
            } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                console.log('[StripePayment] Payment succeeded, submitting order form', {
                    paymentIntentId: paymentIntent.id,
                    status: paymentIntent.status
                });
                await this.submitOrderForm(paymentIntent.id, submitButton, buttonText, spinner);
            } else {
                console.warn('[StripePayment] Unexpected payment status', {
                    paymentIntent: paymentIntent,
                    status: paymentIntent?.status
                });
                this.showError('Payment status is unexpected. Please try again.');
                this.setButtonLoading(submitButton, buttonText, spinner, false);
                this.isProcessing = false;
            }
        } catch (err) {
            console.error('[StripePayment] Exception during payment processing', {
                error: err.message,
                stack: err.stack
            });
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
        console.log('[StripePayment] submitOrderForm called', {
            paymentIntentId: paymentIntentId
        });

        const form = document.getElementById('checkoutForm');
        if (!form || !paymentIntentId) {
            console.error('[StripePayment] Missing form or payment intent ID', {
                form: !!form,
                paymentIntentId: paymentIntentId
            });
            this.showError('Payment succeeded but payment intent ID is missing. Please contact support.');
            this.setButtonLoading(submitButton, buttonText, spinner, false);
            this.isProcessing = false;
            return;
        }

        try {
            console.log('[StripePayment] Creating form data and submitting order', {
                formAction: form.action,
                paymentIntentId: paymentIntentId
            });

            const formData = new FormData(form);
            formData.append('payment_intent_id', paymentIntentId);

            console.log('[StripePayment] Sending order request to server');

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            console.log('[StripePayment] Order response received', {
                status: response.status,
                statusText: response.statusText,
                ok: response.ok,
                contentType: response.headers.get('content-type'),
                url: response.url
            });

            if (!response.ok) {
                let errorText = '';
                let errorData = null;
                try {
                    errorText = await response.text();
                    try {
                        errorData = JSON.parse(errorText);
                    } catch (e) {
                        // Not JSON, use as text
                    }
                } catch (e) {
                    console.error('[StripePayment] Failed to read error response', e);
                }

                console.error('[StripePayment] Order request failed', {
                    status: response.status,
                    statusText: response.statusText,
                    errorText: errorText.substring(0, 500),
                    errorData: errorData
                });

                // Handle rate limiting (429) with retry information
                if (response.status === 429) {
                    const retryAfter = errorData?.errors?.retry_after || errorData?.retry_after || 10;
                    const errorMessage = errorData?.message || 'Too many requests. Please wait a moment before trying again.';
                    
                    console.warn('[StripePayment] Rate limit exceeded, will retry', {
                        retryAfter: retryAfter,
                        paymentIntentId: paymentIntentId
                    });

                    // Prevent multiple retry attempts
                    if (this.retryInProgress) {
                        console.warn('[StripePayment] Retry already in progress, skipping');
                        this.showError('Please wait for the automatic retry to complete.');
                        return;
                    }

                    this.retryInProgress = true;
                    this.isProcessing = true; // Keep button disabled during retry

                    // Show user-friendly error with retry countdown
                    this.showRateLimitError(errorMessage, retryAfter);
                    
                    // Auto-retry after the specified time
                    setTimeout(async () => {
                        console.log('[StripePayment] Retrying order submission after rate limit', {
                            paymentIntentId: paymentIntentId
                        });
                        this.retryInProgress = false;
                        this.clearError();
                        await this.submitOrderForm(paymentIntentId, submitButton, buttonText, spinner);
                    }, (retryAfter + 1) * 1000); // Add 1 second buffer
                    
                    return; // Exit early, retry will happen
                }

                // For other errors, show generic message
                const errorMessage = errorData?.message || 'Failed to process order. Please try again.';
                throw new Error(errorMessage);
            }

            let data;
            try {
                const responseText = await response.text();
                console.log('[StripePayment] Raw response text', {
                    length: responseText.length,
                    preview: responseText.substring(0, 200)
                });
                
                data = JSON.parse(responseText);
                console.log('[StripePayment] Parsed JSON response', {
                    success: data.success,
                    message: data.message,
                    hasData: !!data.data,
                    hasRedirectUrl: !!(data.data?.redirect_url || data.redirect_url),
                    fullData: data
                });
            } catch (parseError) {
                console.error('[StripePayment] Failed to parse JSON response', {
                    error: parseError.message,
                    stack: parseError.stack
                });
                throw new Error('Invalid response from server. Please try again.');
            }

            console.log('[StripePayment] Order response data', {
                success: data.success,
                message: data.message,
                hasRedirectUrl: !!(data.data?.redirect_url || data.redirect_url),
                redirectUrl: data.data?.redirect_url || data.redirect_url
            });

            const redirectUrl = data.data?.redirect_url || data.redirect_url;

            console.log('[StripePayment] Processing response', {
                success: data.success,
                redirectUrl: redirectUrl,
                hasRedirectUrl: !!redirectUrl
            });

            if (data.success && redirectUrl) {
                console.log('[StripePayment] Order placed successfully, preparing redirect', {
                    redirectUrl: redirectUrl,
                    currentUrl: window.location.href
                });
                
                this.isProcessing = false;
                this.setButtonLoading(submitButton, buttonText, spinner, false);
                
                window.isRedirecting = true;
                
                console.log('[StripePayment] Setting redirect flag and initiating redirect');
                
                setTimeout(() => {
                    console.log('[StripePayment] Executing redirect now', {
                        redirectUrl: redirectUrl
                    });
                    try {
                        window.location.replace(redirectUrl);
                        console.log('[StripePayment] Redirect initiated successfully');
                    } catch (redirectError) {
                        console.error('[StripePayment] Error during redirect, trying alternative method', {
                            error: redirectError.message,
                            stack: redirectError.stack
                        });
                        try {
                            window.location.href = redirectUrl;
                        } catch (fallbackError) {
                            console.error('[StripePayment] Fallback redirect also failed', {
                                error: fallbackError.message
                            });
                            this.showError('Order placed but redirect failed. Please navigate manually.');
                        }
                    }
                }, 100);
            } else {
                console.error('[StripePayment] Order placement failed or missing redirect URL', {
                    success: data.success,
                    message: data.message,
                    redirectUrl: redirectUrl,
                    hasRedirectUrl: !!redirectUrl,
                    fullData: data
                });
                this.showError(data.message || 'Failed to place order. Please try again.');
                this.setButtonLoading(submitButton, buttonText, spinner, false);
                this.isProcessing = false;
            }
        } catch (error) {
            console.error('[StripePayment] Exception during order submission', {
                error: error.message,
                stack: error.stack,
                name: error.name
            });
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
            errorElement.className = 'alert alert-danger';
            
            // Scroll to error if needed
            errorElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    showRateLimitError(message, retryAfter) {
        const errorElement = document.getElementById('payment-errors');
        if (!errorElement) return;

        let countdown = retryAfter;
        errorElement.innerHTML = `
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-clock" style="font-size: 1.2rem;"></i>
                <div>
                    <strong>${message}</strong>
                    <div style="margin-top: 0.25rem; font-size: 0.9rem;">
                        Retrying automatically in <span id="rate-limit-countdown" style="font-weight: bold;">${countdown}</span> second${countdown !== 1 ? 's' : ''}...
                    </div>
                </div>
            </div>
        `;
        errorElement.style.display = 'block';
        errorElement.className = 'alert alert-warning';
        errorElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // Update countdown
        const countdownElement = document.getElementById('rate-limit-countdown');
        if (countdownElement) {
            const countdownInterval = setInterval(() => {
                countdown--;
                if (countdownElement) {
                    countdownElement.textContent = countdown;
                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        errorElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Retrying...';
                    }
                }
            }, 1000);
        }
    }

    clearError() {
        const errorElement = document.getElementById('payment-errors');
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.innerHTML = '';
            errorElement.style.display = 'none';
            errorElement.className = '';
        }
    }
}
