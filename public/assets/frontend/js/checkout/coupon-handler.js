/**
 * Coupon Handler Module
 * Handles coupon apply/remove functionality via AJAX
 * Single button toggles between Apply and Remove
 */
class CouponHandler {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        this.applyUrl = '/checkout/apply-coupon';
        this.removeUrl = '/checkout/remove-coupon';
        this.init();
    }

    init() {
        this.initCouponToggle();
    }

    initCouponToggle() {
        // Try multiple ways to find elements - prioritize ID first
        let couponForm = document.getElementById('couponForm');
        const couponInput = document.getElementById('orderCoupon');
        const couponBtn = document.getElementById('couponApplyBtn');
        
        // Helper function to verify if a form is the coupon form
        const isCouponForm = (form) => {
            if (!form) return false;
            const action = form.action || form.getAttribute('action') || '';
            const formId = form.id || '';
            const hasCouponClass = form.classList && form.classList.contains('coupon-form-inline');
            
            return formId === 'couponForm' || 
                   action.includes('apply-coupon') || 
                   action.includes('remove-coupon') ||
                   hasCouponClass;
        };
        
        // If form not found by ID, try finding by action attribute (more specific)
        if (!couponForm || !isCouponForm(couponForm)) {
            couponForm = document.querySelector('form[action*="apply-coupon"]');
        }
        
        // If still not found, try by class (but verify it's the coupon form)
        if (!couponForm || !isCouponForm(couponForm)) {
            const formsWithClass = document.querySelectorAll('form.coupon-form-inline');
            // Find the one that has the coupon input/button inside it AND is the coupon form
            for (let form of formsWithClass) {
                if (isCouponForm(form) && (form.contains(couponInput) || form.contains(couponBtn))) {
                    couponForm = form;
                    break;
                }
            }
        }
        
        // Since HTML doesn't allow nested forms, the coupon form might not be properly nested
        // Try to find it by ID directly and verify it contains the elements
        if ((!couponForm || !isCouponForm(couponForm)) && couponInput && couponBtn) {
            // First, try to find form by ID directly
            const formById = document.getElementById('couponForm');
            if (formById) {
                const containsInput = formById.contains(couponInput);
                const containsButton = formById.contains(couponBtn);
                
                if (containsInput && containsButton) {
                    couponForm = formById;
                }
            }
            
            // If still not found, search all forms for one that contains both elements
            if (!couponForm || !isCouponForm(couponForm)) {
                const allForms = document.querySelectorAll('form');
                
                for (let form of allForms) {
                    const containsInput = form.contains(couponInput);
                    const containsButton = form.contains(couponBtn);
                    const matchesCoupon = isCouponForm(form);
                    
                    if (containsInput && containsButton && matchesCoupon) {
                        couponForm = form;
                        break;
                    }
                }
            }
        }
        
        // We need at least the input and button - form is optional (might be nested/invalid HTML)
        if (!couponInput || !couponBtn) {
            return;
        }
        
        // If form not found, create a virtual form reference or use null
        // We'll handle events directly on button/input instead
        if (!couponForm) {
            // Create a dummy form object for compatibility
            couponForm = {
                id: 'couponForm',
                action: this.applyUrl,
                contains: (el) => el === couponInput || el === couponBtn
            };
        } else {
            // Ensure form has ID for consistency
            if (!couponForm.id || couponForm.id !== 'couponForm') {
                couponForm.id = 'couponForm';
            }
        }

        // Check if coupon is currently applied
        const isCouponApplied = couponInput.readOnly && couponInput.value.trim().length > 0;
        const currentAction = couponBtn.getAttribute('data-action') || (isCouponApplied ? 'remove' : 'apply');

        // Function to update button state based on input
        const updateButtonState = () => {
            const hasValue = couponInput.value.trim().length > 0;
            const isReadonly = couponInput.readOnly;
            
            // If input is readonly (coupon applied), button should always be enabled
            // If input is not readonly, button should be enabled only if input has value
            couponBtn.disabled = !isReadonly && !hasValue;
            
            // Update button text and action based on readonly state
            if (isReadonly && hasValue) {
                couponBtn.textContent = 'Remove';
                couponBtn.setAttribute('data-action', 'remove');
            } else if (hasValue) {
                couponBtn.textContent = 'Apply';
                couponBtn.setAttribute('data-action', 'apply');
            } else {
                couponBtn.textContent = 'Apply';
                couponBtn.setAttribute('data-action', 'apply');
            }
        };

        // Initial state update
        updateButtonState();

        // Always add input listeners - they will check readonly state inside updateButtonState
        couponInput.addEventListener('input', updateButtonState);
        couponInput.addEventListener('paste', () => {
            setTimeout(updateButtonState, 10);
        });
        couponInput.addEventListener('keyup', updateButtonState);
        couponInput.addEventListener('change', updateButtonState);

        // Unified handler for both apply and remove
        const handleCouponAction = async () => {
            const action = couponBtn.getAttribute('data-action');
            const isRemove = action === 'remove';

            if (isRemove) {
                // Handle remove coupon
                await this.handleRemoveCoupon(couponBtn);
            } else {
                // Handle apply coupon
                const code = couponInput.value.trim();
                if (!code || couponBtn.disabled) {
                    return;
                }
                await this.handleApplyCoupon(code, couponBtn, couponInput);
            }
        };

        // Handle form submission - only if form is a real DOM element
        if (couponForm && couponForm.addEventListener) {
            couponForm.addEventListener('submit', async (e) => {
                // Check if this submit was triggered by the coupon button
                const submitter = e.submitter || document.activeElement;
                
                const isCouponButton = submitter && (
                    submitter === couponBtn || 
                    submitter.id === 'couponApplyBtn' ||
                    (submitter.closest && submitter.closest('#couponForm') === couponForm)
                );
                
                // Also verify the form itself
                const isCouponFormCheck = (e.target === couponForm || e.currentTarget === couponForm);
                const formId = e.target?.id || e.currentTarget?.id;
                const isCouponFormById = formId === 'couponForm';
                
                // Only handle if it's definitely the coupon form AND coupon button
                if (!isCouponFormCheck && !isCouponFormById) {
                    return; // Not our form, let it continue - DON'T prevent default
                }
                
                if (!isCouponButton) {
                    return; // Not triggered by coupon button, let checkout form submit - DON'T prevent default
                }
                
                e.preventDefault();
                e.stopPropagation();
                await handleCouponAction();
            });
        }

        // Handle Enter key
        couponInput.addEventListener('keypress', async (e) => {
            if (e.key === 'Enter') {
                if (!couponInput.readOnly && couponInput.value.trim() && !couponBtn.disabled) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    await handleCouponAction();
                }
            }
        });

        // Handle button click - only for coupon button
        couponBtn.addEventListener('click', async (e) => {
            // Only handle if this is actually the coupon button
            if (e.target !== couponBtn && !couponBtn.contains(e.target)) {
                return; // Not our button, let event continue
            }
            
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Check if button is disabled
            if (couponBtn.disabled) {
                return;
            }
            
            await handleCouponAction();
        });
    }

    async handleApplyCoupon(code, button, input) {
        button.disabled = true;
        const originalText = button.textContent;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        console.log('[CouponHandler] Starting coupon apply process', { code });

        try {
            const checkoutForm = document.getElementById('checkoutForm');
            let formData = {};

            if (checkoutForm) {
                console.log('[CouponHandler] Saving form data before applying coupon');
                const formDataObj = new FormData(checkoutForm);
                
                for (let [key, value] of formDataObj.entries()) {
                    formData[key] = value;
                }

                try {
                    const saveResponse = await fetch('/checkout/save-form-data', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(formData),
                        redirect: 'manual'
                    });

                    if (saveResponse.ok) {
                        const saveData = await saveResponse.json();
                        console.log('[CouponHandler] Form data saved successfully', saveData);
                    } else {
                        console.warn('[CouponHandler] Failed to save form data, continuing with coupon apply');
                    }
                } catch (saveError) {
                    console.warn('[CouponHandler] Error saving form data:', saveError);
                }
            }

            console.log('[CouponHandler] Applying coupon code:', code);
            const response = await fetch(this.applyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ code: code }),
                redirect: 'manual'
            });

            if (response.type === 'opaqueredirect' || response.redirected) {
                this.showMessage('An error occurred. Please try again.', 'error');
                button.disabled = false;
                button.textContent = originalText;
                return;
            }

            const contentType = response.headers.get('content-type');
            
            if (!contentType || !contentType.includes('application/json')) {
                this.showMessage('An error occurred. Please try again.', 'error');
                button.disabled = false;
                button.textContent = originalText;
                return;
            }

            let data;
            try {
                data = await response.json();
            } catch (parseError) {
                this.showMessage('An error occurred. Please try again.', 'error');
                button.disabled = false;
                button.textContent = originalText;
                return;
            }

            if (response.ok && data.success) {
                console.log('[CouponHandler] Coupon applied successfully', data);
                this.showMessage('Coupon applied successfully!', 'success');
                
                input.readOnly = true;
                button.textContent = 'Remove';
                button.setAttribute('data-action', 'remove');
                button.disabled = false;
                
                this.updateOrderSummary(data);
            } else {
                console.error('[CouponHandler] Coupon apply failed', data);
                const errorMsg = data.message || data.error || data.data?.message || 'Failed to apply coupon.';
                this.showMessage(errorMsg, 'error');
                button.disabled = false;
                button.textContent = originalText;
            }
        } catch (error) {
            this.showMessage('An error occurred. Please try again.', 'error');
            button.disabled = false;
            button.textContent = originalText;
        }
    }

    async handleRemoveCoupon(button) {
        button.disabled = true;
        const originalText = button.textContent;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const response = await fetch(this.removeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                redirect: 'manual'
            });

            if (response.type === 'opaqueredirect' || response.redirected) {
                this.showMessage('An error occurred. Please try again.', 'error');
                button.disabled = false;
                button.textContent = originalText;
                return;
            }

            const contentType = response.headers.get('content-type');
            
            if (!contentType || !contentType.includes('application/json')) {
                this.showMessage('An error occurred. Please try again.', 'error');
                button.disabled = false;
                button.textContent = originalText;
                return;
            }

            let data;
            try {
                data = await response.json();
            } catch (parseError) {
                this.showMessage('An error occurred. Please try again.', 'error');
                button.disabled = false;
                button.textContent = originalText;
                return;
            }

            if (response.ok && data.success) {
                console.log('[CouponHandler] Coupon removed successfully', data);
                this.showMessage('Coupon removed successfully!', 'success');
                
                const couponInput = document.getElementById('orderCoupon');
                const couponBtn = document.getElementById('couponApplyBtn');
                
                if (couponInput) {
                    couponInput.readOnly = false;
                    couponInput.value = '';
                }
                
                if (couponBtn) {
                    couponBtn.textContent = 'Apply';
                    couponBtn.setAttribute('data-action', 'apply');
                    couponBtn.disabled = false;
                }

                const discountRow = document.getElementById('couponDiscountRow');
                if (discountRow) {
                    discountRow.style.display = 'none';
                }

                const totalEl = document.getElementById('checkoutTotal');
                if (totalEl && data.data && data.data.total) {
                    totalEl.textContent = `$${parseFloat(data.data.total).toFixed(2)}`;
                }
            } else {
                console.error('[CouponHandler] Coupon remove failed', data);
                const errorMsg = data.message || data.error || data.data?.message || 'Failed to remove coupon.';
                this.showMessage(errorMsg, 'error');
                button.disabled = false;
                button.textContent = originalText;
            }
        } catch (error) {
            this.showMessage('An error occurred. Please try again.', 'error');
            button.disabled = false;
            button.textContent = originalText;
        }
    }

    updateOrderSummary(data) {
        console.log('[CouponHandler] Updating order summary', data);
        
        try {
            const discount = data.discount || data.data?.discount || 0;
            const total = data.total || data.data?.total || 0;
            const coupon = data.coupon || data.data?.coupon;

            const subtotalEl = document.getElementById('checkoutSubtotal');
            const discountRow = document.getElementById('couponDiscountRow');
            const discountEl = document.getElementById('checkoutDiscount');
            const totalEl = document.getElementById('checkoutTotal');

            if (discount > 0 && coupon) {
                if (!discountRow) {
                    const orderTotals = document.querySelector('.order-totals');
                    if (orderTotals && subtotalEl) {
                        const newRow = document.createElement('div');
                        newRow.className = 'order-totals__item';
                        newRow.id = 'couponDiscountRow';
                        newRow.innerHTML = `
                            <span class="order-totals__label">Discount (${coupon.code || coupon.name || ''})</span>
                            <span class="order-totals__value" style="color: var(--coral-red);" id="checkoutDiscount">-$${discount.toFixed(2)}</span>
                        `;
                        subtotalEl.closest('.order-totals__item').after(newRow);
                    }
                } else {
                    discountRow.style.display = '';
                    if (discountEl) {
                        discountEl.textContent = `-$${discount.toFixed(2)}`;
                    }
                }
            } else {
                if (discountRow) {
                    discountRow.style.display = 'none';
                }
            }

            if (totalEl) {
                const currentTotal = parseFloat(totalEl.textContent.replace('$', '').replace(',', ''));
                totalEl.textContent = `$${total.toFixed(2)}`;
                console.log('[CouponHandler] Updated total:', total);
            }

            console.log('[CouponHandler] Order summary updated successfully');
        } catch (error) {
            console.error('[CouponHandler] Error updating order summary:', error);
        }
    }

    showMessage(message, type) {
        // Remove existing messages
        const existingMessages = document.querySelectorAll('.coupon-message');
        existingMessages.forEach(msg => msg.remove());

        // Create new message
        const messageDiv = document.createElement('div');
        messageDiv.className = 'coupon-message';
        messageDiv.style.cssText = type === 'success' 
            ? 'color: #28a745; margin-bottom: 0.5rem; padding: 0.5rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px;'
            : 'color: #dc3545; margin-bottom: 0.5rem; padding: 0.5rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;';
        messageDiv.textContent = message;

        // Insert message before coupon form
        const couponSection = document.querySelector('.order-coupon');
        if (couponSection) {
            const label = couponSection.querySelector('label');
            if (label && label.nextSibling) {
                couponSection.insertBefore(messageDiv, label.nextSibling);
            } else {
                couponSection.insertBefore(messageDiv, couponSection.firstChild.nextSibling);
            }
        }

        // Auto-remove after 5 seconds
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
}

// Initialize when DOM is ready
function initializeCouponHandler() {
    // Only initialize if we're on a checkout page
    const checkoutForm = document.getElementById('checkoutForm');
    const orderCoupon = document.querySelector('.order-coupon');
    
    if (checkoutForm || orderCoupon) {
        const tryInitialize = () => {
            const couponInputCheck = document.getElementById('orderCoupon');
            const couponBtnCheck = document.getElementById('couponApplyBtn');
            
            if (couponInputCheck && couponBtnCheck) {
                try {
                    new CouponHandler();
                } catch (error) {
                    // Silently fail initialization
                }
                return true;
            }
            return false;
        };

        if (tryInitialize()) {
            return;
        }

        setTimeout(() => {
            if (!tryInitialize()) {
                setTimeout(tryInitialize, 500);
            }
        }, 100);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCouponHandler);
} else {
    initializeCouponHandler();
}
