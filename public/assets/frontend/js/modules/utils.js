/**
 * Utility Functions Module
 * Shared utilities for wishlist, cart, and other modules
 */
(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        isDevelopment: false, // Set to true for development
        notificationDuration: 3000,
        fadeOutDuration: 300
    };

    // Utility Functions
    window.AppUtils = {
        // Get CSRF Token
        getCsrfToken: function() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        // Log function (only in development)
        log: function(...args) {
            if (CONFIG.isDevelopment) {
                console.log(...args);
            }
        },

        // Error log (always show)
        error: function(...args) {
            console.error(...args);
        },

        // Handle authentication redirect
        redirectToLogin: function() {
            const currentUrl = window.location.href;
            window.location.href = '/login?intended=' + encodeURIComponent(currentUrl);
        },

        // Update button loading state
        setButtonLoading: function(button, isLoading, iconClass = 'fa-heart') {
            if (!button) return;
            const icon = button.querySelector('i');
            if (icon) {
                if (isLoading) {
                    icon.classList.remove('fas', iconClass);
                    icon.classList.add('fas', 'fa-spinner', 'fa-spin');
                } else {
                    icon.classList.remove('fa-spinner', 'fa-spin');
                    icon.classList.add('fas', iconClass);
                }
            }
            button.disabled = isLoading;
        },

        // Show notification
        showNotification: function(message, type = 'success', container = 'body') {
            const notification = document.createElement('div');
            notification.className = `app-notification app-notification--${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                padding: 15px 20px;
                background: ${type === 'success' ? '#10b981' : '#ef4444'};
                color: white;
                border-radius: 5px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                opacity: 0;
                transition: opacity 0.3s;
            `;

            const target = container === 'body' ? document.body : document.querySelector(container);
            if (target) {
                target.appendChild(notification);

                setTimeout(() => {
                    notification.style.opacity = '1';
                }, 10);

                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (target.contains(notification)) {
                            target.removeChild(notification);
                        }
                    }, CONFIG.fadeOutDuration);
                }, CONFIG.notificationDuration);
            }
        },

        // Handle API response
        handleApiResponse: function(response) {
            if (response.status === 401) {
                window.AppUtils.redirectToLogin();
                return Promise.reject('Not authenticated');
            }

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                window.AppUtils.redirectToLogin();
                return Promise.reject('Not authenticated');
            }
        }
    };
})();

