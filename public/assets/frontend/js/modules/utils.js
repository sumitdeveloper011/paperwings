/**
 * Utility Functions Module
 * Shared utilities for wishlist, cart, and other modules
 * 
 * @module AppUtils
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
        /**
         * Get CSRF token from meta tag
         * @returns {string} CSRF token
         */
        getCsrfToken: function() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        /**
         * Log function (only in development mode)
         * @param {...*} args - Arguments to log
         */
        log: function(...args) {
            if (CONFIG.isDevelopment) {
                console.log(...args);
            }
        },

        /**
         * Error log function (only in development mode)
         * @param {...*} args - Arguments to log as error
         */
        error: function(...args) {
            if (CONFIG.isDevelopment) {
                console.error(...args);
            }
        },

        /**
         * Handle authentication redirect to login page
         * @param {string} currentUrl - Optional current URL to redirect back after login
         */
        redirectToLogin: function(currentUrl = null) {
            const url = currentUrl || window.location.href;
            window.location.href = '/login?intended=' + encodeURIComponent(url);
        },

        /**
         * Update button loading state with spinner icon
         * @param {HTMLElement} button - Button element
         * @param {boolean} isLoading - Whether button is in loading state
         * @param {string} iconClass - Icon class to restore when not loading (default: 'fa-heart')
         */
        setButtonLoading: function(button, isLoading, iconClass = 'fa-heart') {
            if (!button) return;
            
            const icon = button.querySelector('i');
            if (icon) {
                if (isLoading) {
                    icon.classList.remove('fas', iconClass, 'fa-check');
                    icon.classList.add('fas', 'fa-spinner', 'fa-spin');
                } else {
                    icon.classList.remove('fa-spinner', 'fa-spin', 'fa-check');
                    icon.classList.add('fas', iconClass);
                }
            }
            
            button.disabled = isLoading;
        },

        /**
         * Show notification message
         * @param {string} message - Notification message
         * @param {string} type - Notification type ('success', 'error', 'warning')
         * @param {string} container - Container selector (default: 'body')
         */
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

