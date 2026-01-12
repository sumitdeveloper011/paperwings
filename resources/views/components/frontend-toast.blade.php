<div id="frontend-toast-container" class="frontend-toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 99999; max-width: 400px; pointer-events: none;"></div>

<script>
(function() {
    'use strict';

    // Function to show toast
    window.showToast = function(message, type = 'success', duration = 5000) {
        type = type || 'success';
        duration = duration || 5000;

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
    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif

    @if(session('warning'))
        showToast('{{ session('warning') }}', 'warning');
    @endif

    @if(session('info'))
        showToast('{{ session('info') }}', 'info');
    @endif

    // Handle validation errors
    @if($errors->any())
        @foreach($errors->all() as $error)
            showToast('{{ $error }}', 'error');
        @endforeach
    @endif
})();
</script>

<style>
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.frontend-toast-container {
    pointer-events: none;
}

.frontend-toast {
    pointer-events: auto;
}

@media (max-width: 768px) {
    #frontend-toast-container {
        left: 20px;
        right: 20px;
        max-width: calc(100% - 40px);
    }
    
    .frontend-toast {
        min-width: auto;
        max-width: 100%;
    }
}
</style>
