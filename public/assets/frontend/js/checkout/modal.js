/**
 * Checkout Modal Module
 * Handles the checkout review and payment modal flow
 */
(function() {
    'use strict';

    class CheckoutModal {
        constructor() {
            this.modal = null;
            this.overlay = null;
            this.currentStep = 'review'; // 'review' or 'payment'
            this.paymentHandler = null;
            this.checkoutConfig = null;
            this.checkoutForm = null;
        }

        init(config, paymentHandler, checkoutForm) {
            this.checkoutConfig = config;
            this.paymentHandler = paymentHandler;
            this.checkoutForm = checkoutForm;
            
            this.modal = document.getElementById('checkoutModal');
            this.overlay = document.getElementById('checkoutModalOverlay');
            
            if (!this.modal) {
                console.error('Checkout modal not found');
                return;
            }

            this.attachEventListeners();
        }

        attachEventListeners() {
            // Review Order Button
            const reviewBtn = document.getElementById('reviewOrderBtn');
            if (reviewBtn) {
                reviewBtn.addEventListener('click', () => this.openReview());
            }

            // Close Modal
            const closeBtn = document.getElementById('checkoutModalClose');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => this.close());
            }

            if (this.overlay) {
                this.overlay.addEventListener('click', () => this.close());
            }

            const backToCheckoutBtn = document.getElementById('closeModalBtn');
            if (backToCheckoutBtn) {
                backToCheckoutBtn.addEventListener('click', () => this.close());
            }

            // Proceed to Payment
            const proceedBtn = document.getElementById('proceedToPaymentBtn');
            if (proceedBtn) {
                proceedBtn.addEventListener('click', () => this.showPayment());
            }

            // Back to Review
            const backToReviewBtn = document.getElementById('backToReviewBtn');
            if (backToReviewBtn) {
                backToReviewBtn.addEventListener('click', () => this.showReview());
            }

            // Place Order
            const placeOrderBtn = document.getElementById('placeOrderBtn');
            if (placeOrderBtn) {
                placeOrderBtn.addEventListener('click', () => this.placeOrder());
            }

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen()) {
                    this.close();
                }
            });
        }

        openReview() {
            if (!this.modal) return;
            
            // Validate form first
            if (!this.validateForm()) {
                return;
            }

            // Update modal totals
            this.updateModalTotals();
            
            // Show review step
            this.showReview();
            
            // Open modal
            this.modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        showReview() {
            this.currentStep = 'review';
            const reviewStep = document.getElementById('reviewStep');
            const paymentStep = document.getElementById('paymentStep');
            const modalTitle = document.getElementById('modalTitle');

            if (reviewStep) reviewStep.style.display = 'block';
            if (paymentStep) paymentStep.style.display = 'none';
            if (modalTitle) modalTitle.textContent = 'Review Your Order';
        }

        async showPayment() {
            this.currentStep = 'payment';
            const reviewStep = document.getElementById('reviewStep');
            const paymentStep = document.getElementById('paymentStep');
            const modalTitle = document.getElementById('modalTitle');

            if (reviewStep) reviewStep.style.display = 'none';
            if (paymentStep) paymentStep.style.display = 'block';
            if (modalTitle) modalTitle.textContent = 'Complete Payment';

            // Initialize payment in modal
            await this.initPaymentInModal();
        }

        async initPaymentInModal() {
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
                        // Mount payment element in modal
                        this.mountPaymentElementInModal();
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
                this.mountPaymentElementInModal();
            }
        }

        mountPaymentElementInModal() {
            if (!this.paymentHandler || !this.paymentHandler.elements) {
                console.error('Payment elements not available');
                return;
            }

            // Unmount existing payment element if present
            if (this.paymentHandler.paymentElement) {
                try {
                    this.paymentHandler.paymentElement.unmount();
                } catch (e) {
                    console.warn('Error unmounting payment element:', e);
                }
            }

            // Create and mount payment element in modal
            const modalPaymentContainer = document.getElementById('modal-payment-element');
            if (modalPaymentContainer) {
                this.paymentHandler.paymentElement = this.paymentHandler.elements.create('payment');
                this.paymentHandler.paymentElement.mount('#modal-payment-element');
                
                const container = document.getElementById('modal-payment-element-container');
                if (container) {
                    container.style.display = 'block';
                }
                console.log('Payment element mounted in modal');
            } else {
                console.error('Modal payment container not found');
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
                    await this.paymentHandler.createPaymentIntent(this.checkoutConfig.total);
                    this.mountPaymentElementInModal();
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
            const errorElement = document.getElementById('modal-payment-errors');
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
        }

        updateModalTotals() {
            const subtotal = document.getElementById('checkoutSubtotal');
            const discount = document.getElementById('checkoutDiscount');
            const shipping = document.getElementById('checkoutShipping');
            const total = document.getElementById('checkoutTotal');

            const modalSubtotal = document.getElementById('modalSubtotal');
            const modalDiscount = document.getElementById('modalDiscount');
            const modalShipping = document.getElementById('modalShipping');
            const modalTotal = document.getElementById('modalFinalTotal');
            const modalShippingRow = document.getElementById('modalShippingRow');

            if (subtotal && modalSubtotal) {
                modalSubtotal.textContent = subtotal.textContent;
            }

            if (discount) {
                const discountRow = modalDiscount ? modalDiscount.closest('.checkout-modal__total-row') : null;
                if (discountRow) {
                    discountRow.style.display = 'flex';
                    if (modalDiscount) {
                        modalDiscount.textContent = discount.textContent;
                    }
                }
            } else {
                // Hide discount row if no discount
                const discountRow = modalDiscount ? modalDiscount.closest('.checkout-modal__total-row') : null;
                if (discountRow) {
                    discountRow.style.display = 'none';
                }
            }

            if (shipping && modalShipping) {
                modalShipping.textContent = shipping.textContent;
                if (modalShippingRow) {
                    const shippingValue = parseFloat(shipping.textContent.replace('$', '').replace(',', ''));
                    modalShippingRow.style.display = shippingValue > 0 ? 'flex' : 'none';
                }
            }

            if (total && modalTotal) {
                modalTotal.textContent = total.textContent;
            }
        }

        validateForm() {
            const form = this.checkoutForm;
            if (!form) return false;

            // Check required fields
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });

            if (!isValid) {
                alert('Please fill in all required fields before reviewing your order.');
                return false;
            }

            return true;
        }

        close() {
            if (this.modal) {
                this.modal.classList.remove('active');
                document.body.style.overflow = '';
                this.showReview(); // Reset to review step
            }
        }

        isOpen() {
            return this.modal && this.modal.classList.contains('active');
        }
    }

    // Export globally
    window.CheckoutModal = CheckoutModal;
})();

