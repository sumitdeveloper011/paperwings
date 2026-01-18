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
            return form.id === 'couponForm' || 
                   form.action.includes('apply-coupon') || 
                   form.action.includes('remove-coupon');
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
        
        // Last resort: find via input/button parent, but verify it's the coupon form
        if ((!couponForm || !isCouponForm(couponForm)) && couponInput) {
            const parentForm = couponInput.closest('form');
            // Only use if it's verified as the coupon form
            if (isCouponForm(parentForm)) {
                couponForm = parentForm;
            }
        }
        if ((!couponForm || !isCouponForm(couponForm)) && couponBtn) {
            const parentForm = couponBtn.closest('form');
            // Only use if it's verified as the coupon form
            if (isCouponForm(parentForm)) {
                couponForm = parentForm;
            }
        }
        
        if (!couponForm || !couponInput || !couponBtn) {
            return;
        }
        
        // Ensure form has ID for consistency
        if (!couponForm.id || couponForm.id !== 'couponForm') {
            couponForm.id = 'couponForm';
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

        // Only add input listeners if input is not readonly (coupon not applied)
        if (!couponInput.readOnly) {
            couponInput.addEventListener('input', updateButtonState);
            couponInput.addEventListener('paste', () => {
                setTimeout(updateButtonState, 10);
            });
            couponInput.addEventListener('keyup', updateButtonState);
            couponInput.addEventListener('change', updateButtonState);
        }

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

        // Handle form submission - only for coupon form, not checkout form
        couponForm.addEventListener('submit', async (e) => {
            // Check if this submit was triggered by the coupon button
            const submitter = e.submitter || document.activeElement;
            
            const isCouponButton = submitter && (
                submitter === couponBtn || 
                submitter.id === 'couponApplyBtn' ||
                (submitter.closest && submitter.closest('#couponForm') === couponForm)
            );
            
            // Also verify the form itself
            const isCouponForm = (e.target === couponForm || e.currentTarget === couponForm);
            const formId = e.target?.id || e.currentTarget?.id;
            const isCouponFormById = formId === 'couponForm';
            
            // Only handle if it's definitely the coupon form AND coupon button
            if (!isCouponForm && !isCouponFormById) {
                return; // Not our form, let it continue - DON'T prevent default
            }
            
            if (!isCouponButton) {
                return; // Not triggered by coupon button, let checkout form submit - DON'T prevent default
            }
            
            e.preventDefault();
            e.stopPropagation();
            await handleCouponAction();
        });

        // Handle Enter key (only if not readonly)
        if (!couponInput.readOnly) {
            couponInput.addEventListener('keypress', async (e) => {
                if (e.key === 'Enter' && couponInput.value.trim() && !couponBtn.disabled) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    await handleCouponAction();
                }
            });
        }

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

        try {
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
                this.showMessage('Coupon applied successfully!', 'success');
                
                // Reload page to update order summary
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                const errorMsg = data.message || data.error || 'Failed to apply coupon.';
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
                this.showMessage('Coupon removed successfully!', 'success');
                
                // Reload page to update order summary
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                const errorMsg = data.message || data.error || 'Failed to remove coupon.';
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
        try {
            // Wait to ensure all elements are rendered
            setTimeout(() => {
                // Try multiple ways to find elements
                let couponFormCheck = document.getElementById('couponForm');
                const couponInputCheck = document.getElementById('orderCoupon');
                const couponBtnCheck = document.getElementById('couponApplyBtn');
                
                // If form not found, try finding via input/button
                if (!couponFormCheck && couponInputCheck) {
                    couponFormCheck = couponInputCheck.closest('form');
                }
                if (!couponFormCheck && couponBtnCheck) {
                    couponFormCheck = couponBtnCheck.closest('form');
                }
                if (!couponFormCheck) {
                    couponFormCheck = document.querySelector('form[action*="apply-coupon"], form.coupon-form-inline');
                }
                
                // Only need input and button - form can be found via them
                if (couponInputCheck && couponBtnCheck) {
                    new CouponHandler();
                }
            }, 500);
        } catch (error) {
            // Silent fail - initialization error
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCouponHandler);
} else {
    // DOM already loaded
    initializeCouponHandler();
}
