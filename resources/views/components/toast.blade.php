<!-- Toast Notification Container -->
<div id="toast-container" class="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;"></div>

<script>
(function() {
    'use strict';

    let container = null;
    let isInitialized = false;

    // Function to show toast
    function displayToast(title, message, type = 'info', duration = 5000) {
        // Get container (retry if not found)
        if (!container) {
            container = document.getElementById('toast-container');
            if (!container) {
                console.warn('Toast container not found');
                return;
            }
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.cssText = `
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 16px;
            margin-bottom: 12px;
            border-left: 4px solid;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            max-width: 100%;
        `;

        // Set border color based on type
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        toast.style.borderLeftColor = colors[type] || colors.info;

        // Icons
        const icons = {
            success: '<svg class="toast-icon" style="width: 20px; height: 20px; flex-shrink: 0; color: #10b981;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
            error: '<svg class="toast-icon" style="width: 20px; height: 20px; flex-shrink: 0; color: #ef4444;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
            warning: '<svg class="toast-icon" style="width: 20px; height: 20px; flex-shrink: 0; color: #f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
            info: '<svg class="toast-icon" style="width: 20px; height: 20px; flex-shrink: 0; color: #3b82f6;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        };

        // Toast content
        toast.innerHTML = `
            <div style="flex-shrink: 0;">
                ${icons[type] || icons.info}
            </div>
            <div style="flex: 1; min-width: 0;">
                <p style="margin: 0; font-weight: 600; font-size: 14px; color: #111827; margin-bottom: 4px;">${escapeHtml(title)}</p>
                <p style="margin: 0; font-size: 13px; color: #6b7280;">${escapeHtml(message)}</p>
            </div>
            <button class="toast-close" style="background: none; border: none; cursor: pointer; padding: 0; margin-left: 8px; color: #9ca3af; flex-shrink: 0;" onclick="this.parentElement.remove()">
                <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;

        // Append to container
        container.appendChild(toast);

        // Trigger animation
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        }, 10);

        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                hideToast(toast);
            }, duration);
        }
    }

    function hideToast(toast) {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initialize toast system
    function initToast() {
        container = document.getElementById('toast-container');
        if (!container) {
            setTimeout(initToast, 100);
            return;
        }

        isInitialized = true;

        // Listen for show-toast event (from common-scripts)
        window.addEventListener('show-toast', function(event) {
            const { title, message, type = 'info', duration = 5000 } = event.detail;
            displayToast(title, message, type, duration);
        });
    }

    // Override showToast function - this runs AFTER common-scripts loads
    function setupShowToast() {
        // Override the showToast function from common-scripts
        window.showToast = function(title, message, type = 'info', duration = 5000) {
            if (!isInitialized) {
                initToast();
            }

            if (container) {
                displayToast(title, message, type, duration);
            } else {
                // Fallback: dispatch event
                window.dispatchEvent(new CustomEvent('show-toast', {
                    detail: { title, message, type, duration }
                }));
            }
        };
    }

    // Initialize when DOM is ready
    function initialize() {
        initToast();
        // Setup override after common-scripts has loaded
        setTimeout(setupShowToast, 100);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();
</script>
