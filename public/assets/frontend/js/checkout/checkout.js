/**
 * Main Checkout Module
 * Coordinates all checkout functionality
 */
(function() {
    'use strict';

    // Checkout configuration (will be set from Blade template)
    let checkoutConfig = null;

    /**
     * Initialize checkout functionality
     */
    function initCheckout() {
        if (!checkoutConfig) {
            console.error('Checkout configuration not found');
            return;
        }

        // Initialize modules
        const shippingCalculator = new ShippingCalculator({
            csrfToken: checkoutConfig.csrfToken,
            calculateShippingUrl: checkoutConfig.calculateShippingUrl,
            subtotal: checkoutConfig.subtotal,
            discount: checkoutConfig.discount,
            onShippingUpdated: (shipping) => {
                checkoutConfig.shipping = shipping;
                updateTotals();
            }
        });

        const paymentHandler = new PaymentHandler({
            stripeKey: checkoutConfig.stripeKey,
            csrfToken: checkoutConfig.csrfToken,
            createPaymentIntentUrl: checkoutConfig.createPaymentIntentUrl,
            processOrderUrl: checkoutConfig.processOrderUrl,
            onPaymentReady: () => {
                console.log('Payment system ready');
            }
        });

        const formHandler = new CheckoutFormHandler({
            onRegionChange: () => {
                const regionId = formHandler.getShippingRegionId();
                if (regionId) {
                    shippingCalculator.calculate(regionId);
                }
            }
        });

        // Initialize form handlers
        formHandler.init();

        // Initialize Stripe
        if (!paymentHandler.init()) {
            return;
        }

        // Store payment handler reference globally for updateTotals
        window.checkoutPaymentHandler = paymentHandler;

        // Initialize Checkout Modal
        const checkoutModal = new CheckoutModal();
        checkoutModal.init(checkoutConfig, paymentHandler, checkoutForm);
        window.checkoutModal = checkoutModal;

        // Calculate shipping (don't create payment intent automatically - wait for modal)
        const initialRegionId = formHandler.getShippingRegionId();
        if (initialRegionId) {
            shippingCalculator.calculate(initialRegionId).then((shipping) => {
                checkoutConfig.shipping = shipping;
                updateTotals();
            });
        }
    }

    /**
     * Update totals display
     */
    function updateTotals() {
        const newTotal = Math.round((checkoutConfig.subtotal - checkoutConfig.discount + checkoutConfig.shipping) * 100) / 100;

        if (Math.abs(checkoutConfig.total - newTotal) > 0.01) {
            checkoutConfig.total = newTotal;
            const totalElement = document.getElementById('checkoutTotal');
            if (totalElement) {
                totalElement.textContent = '$' + checkoutConfig.total.toFixed(2);
            }

            console.log('Total updated:', {
                newTotal: newTotal,
                subtotal: checkoutConfig.subtotal,
                discount: checkoutConfig.discount,
                shipping: checkoutConfig.shipping
            });

            // Update modal totals if modal is open
            if (window.checkoutModal && window.checkoutModal.isOpen()) {
                window.checkoutModal.updateModalTotals();
            }

            // Recreate payment intent if it exists (but don't auto-create - wait for modal)
            if (window.checkoutPaymentHandler && window.checkoutPaymentHandler.paymentIntentClientSecret) {
                console.log('Total changed, recreating payment intent');
                window.checkoutPaymentHandler.clearPaymentIntent();
                // Don't auto-create - wait for user to proceed to payment in modal
            }
        } else {
            checkoutConfig.total = newTotal;
            const totalElement = document.getElementById('checkoutTotal');
            if (totalElement) {
                totalElement.textContent = '$' + checkoutConfig.total.toFixed(2);
            }
        }
    }

    // Expose configuration function
    window.initCheckoutConfig = function(config) {
        checkoutConfig = config;

        // Store payment handler reference for updateTotals
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCheckout);
        } else {
            initCheckout();
        }
    };

})();

