@php
    $successMessage = session('success', null);
    $errorMessage = session('error', null);
    $warningMessage = session('warning', null);
    $infoMessage = session('info', null);
    
    $successJson = $successMessage ? json_encode($successMessage) : 'null';
    $errorJson = $errorMessage ? json_encode($errorMessage) : 'null';
    $warningJson = $warningMessage ? json_encode($warningMessage) : 'null';
    $infoJson = $infoMessage ? json_encode($infoMessage) : 'null';
@endphp

<div id="frontend-toast-container" class="frontend-toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 99999; max-width: 400px; pointer-events: none;"></div>

<script>
(function() {
    'use strict';

    // Toast deduplication - prevent same message showing multiple times
    const activeToasts = new Map();
    const TOAST_DEDUP_WINDOW = 2000; // 2 seconds

    // Function to show toast
    window.showToast = function(message, type = 'success', duration = 5000) {
        type = type || 'success';
        duration = duration || 5000;

        // Deduplication: Check if same message was shown recently
        const toastKey = `${type}:${message}`;
        const now = Date.now();
        if (activeToasts.has(toastKey)) {
            const lastShown = activeToasts.get(toastKey);
            if (now - lastShown < TOAST_DEDUP_WINDOW) {
                return; // Skip duplicate toast
            }
        }
        activeToasts.set(toastKey, now);
        
        // Clean up old entries
        setTimeout(() => {
            activeToasts.delete(toastKey);
        }, TOAST_DEDUP_WINDOW);

        let container = document.getElementById('frontend-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'frontend-toast-container';
            container.className = 'frontend-toast-container';
            container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 99999; max-width: 400px; pointer-events: none;';
            document.body.appendChild(container);
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `frontend-toast frontend-toast-${type}`;
        toast.style.cssText = `
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 16px 20px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            max-width: 400px;
            pointer-events: auto;
            animation: slideInRight 0.3s ease-out;
            border-left: 4px solid;
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
            warning: '<svg class="toast-icon" style="width: 20px; height: 20px; flex-shrink: 0; color: #f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
            info: '<svg class="toast-icon" style="width: 20px; height: 20px; flex-shrink: 0; color: #3b82f6;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        };

        // Message content
        const messageDiv = document.createElement('div');
        messageDiv.style.cssText = 'flex: 1;';
        messageDiv.innerHTML = `
            <div style="font-weight: 500; color: #1f2937; margin-bottom: 4px;">${getTypeTitle(type)}</div>
            <div style="font-size: 14px; color: #6b7280; line-height: 1.5;">${message}</div>
        `;

        // Close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = `
            background: none;
            border: none;
            font-size: 24px;
            color: #9ca3af;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            line-height: 1;
        `;
        closeBtn.onmouseover = function() {
            this.style.color = '#374151';
        };
        closeBtn.onmouseout = function() {
            this.style.color = '#9ca3af';
        };

        // Assemble toast
        toast.innerHTML = icons[type] || icons.info;
        toast.appendChild(messageDiv);
        toast.appendChild(closeBtn);

        // Add to container
        container.appendChild(toast);

        // Auto remove
        const removeToast = () => {
            toast.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
                // Remove from active toasts when removed
                activeToasts.delete(toastKey);
            }, 300);
        };

        closeBtn.onclick = removeToast;

        if (duration > 0) {
            setTimeout(removeToast, duration);
        }

        return toast;
    };

    function getTypeTitle(type) {
        const titles = {
            success: 'Success',
            error: 'Error',
            warning: 'Warning',
            info: 'Information'
        };
        return titles[type] || 'Notification';
    }

    // Handle Laravel session messages
    var sessionMessages = {
        success: {!! $successJson !!},
        error: {!! $errorJson !!},
        warning: {!! $warningJson !!},
        info: {!! $infoJson !!}
    };

    if (sessionMessages.success) {
        showToast(sessionMessages.success, 'success');
    }
    if (sessionMessages.error) {
        showToast(sessionMessages.error, 'error');
    }
    if (sessionMessages.warning) {
        showToast(sessionMessages.warning, 'warning');
    }
    if (sessionMessages.info) {
        showToast(sessionMessages.info, 'info');
    }
})();
</script>
