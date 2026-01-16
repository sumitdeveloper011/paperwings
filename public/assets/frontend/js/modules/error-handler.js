/**
 * Global Error Handler Module
 * Handles uncaught JavaScript errors and unhandled promise rejections
 * Conservative approach - only shows critical, user-actionable errors
 */

(function(window) {
    'use strict';

    const ErrorHandler = {
        // Rate limiting to prevent spam
        lastErrorTime: 0,
        errorCount: 0,
        errorWindow: 60000, // 1 minute window
        maxErrorsPerWindow: 2, // Max 2 errors per minute
        
        /**
         * Initialize global error handlers
         */
        init: function() {
            // Only initialize in production or if explicitly enabled
            // In development, errors are logged to console only
            if (window.location.hostname === 'localhost' || 
                window.location.hostname === '127.0.0.1' || 
                window.location.hostname.includes('localhost')) {
                // Development mode - only log to console, no toasts
                this.setupConsoleOnlyErrorHandler();
                return;
            }
            
            this.setupGlobalErrorHandler();
            this.setupUnhandledRejectionHandler();
            this.setupNetworkErrorHandler();
        },

        /**
         * Setup console-only error handler for development
         */
        setupConsoleOnlyErrorHandler: function() {
            window.addEventListener('error', function(event) {
                const error = {
                    message: event.message,
                    filename: event.filename,
                    lineno: event.lineno,
                    colno: event.colno,
                    stack: event.error ? event.error.stack : null
                };
                
                // Only log meaningful errors
                if (ErrorHandler.shouldLogError(error)) {
                    console.error('JavaScript Error:', error);
                }
            }, true);
            
            window.addEventListener('unhandledrejection', function(event) {
                if (event.reason) {
                    console.error('Unhandled Promise Rejection:', event.reason);
                }
            });
        },

        /**
         * Handle global JavaScript errors (production only)
         */
        setupGlobalErrorHandler: function() {
            window.addEventListener('error', function(event) {
                const error = {
                    message: event.message,
                    filename: event.filename,
                    lineno: event.lineno,
                    colno: event.colno,
                    stack: event.error ? event.error.stack : null,
                    timestamp: new Date().toISOString(),
                    url: window.location.href,
                    userAgent: navigator.userAgent
                };

                // Check rate limit first
                if (!ErrorHandler.checkRateLimit()) {
                    return; // Too many errors, skip
                }

                // Filter out non-critical errors that shouldn't show toasts
                const shouldShowToast = ErrorHandler.shouldShowErrorToast(error);
                
                // Always log errors (silently)
                ErrorHandler.logError(error);
                
                // Only show toast for critical, user-actionable errors
                if (shouldShowToast) {
                    ErrorHandler.showUserFriendlyError(error);
                }
            }, true);
        },

        /**
         * Handle unhandled promise rejections (production only)
         */
        setupUnhandledRejectionHandler: function() {
            window.addEventListener('unhandledrejection', function(event) {
                // Skip if reason is not an error object and is falsy/null
                if (!event.reason) {
                    event.preventDefault();
                    return;
                }

                // Skip if it's a non-error rejection (like cancelled requests)
                if (typeof event.reason === 'string' && (
                    event.reason.includes('aborted') || 
                    event.reason.includes('cancelled') ||
                    event.reason.includes('abort') ||
                    event.reason.includes('cancel')
                )) {
                    event.preventDefault();
                    return;
                }

                const error = {
                    message: event.reason?.message || (typeof event.reason === 'string' ? event.reason : null) || 'Unhandled promise rejection',
                    stack: event.reason?.stack || null,
                    timestamp: new Date().toISOString(),
                    url: window.location.href,
                    userAgent: navigator.userAgent
                };

                // Skip if no meaningful error information
                if (!error.message || error.message === 'Unhandled promise rejection') {
                    event.preventDefault();
                    return;
                }

                // Check rate limit
                if (!ErrorHandler.checkRateLimit()) {
                    event.preventDefault();
                    return;
                }

                // Filter out non-critical errors
                const shouldShowToast = ErrorHandler.shouldShowErrorToast(error);
                
                // Always log errors (silently)
                ErrorHandler.logError(error);
                
                // Only show toast for critical errors
                if (shouldShowToast) {
                    ErrorHandler.showUserFriendlyError(error);
                }
                
                event.preventDefault();
            });
        },

        /**
         * Handle network errors in fetch requests
         */
        setupNetworkErrorHandler: function() {
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                const url = typeof args[0] === 'string' ? args[0] : (args[0]?.url || '');
                
                // Skip error handling for Laravel Debugbar requests
                if (url.includes('/_debugbar/')) {
                    return originalFetch.apply(this, args);
                }
                
                return originalFetch.apply(this, args)
                    .catch(error => {
                        // Skip error handling for debugbar
                        if (url.includes('/_debugbar/')) {
                            throw error;
                        }
                        
                        if (error.name === 'TypeError' && error.message.includes('fetch')) {
                            ErrorHandler.handleNetworkError(error);
                        }
                        throw error;
                    });
            };
        },

        /**
         * Handle network errors
         */
        handleNetworkError: function(error) {
            if (typeof showToast !== 'undefined') {
                showToast('Network error. Please check your internet connection and try again.', 'error', 7000);
            }
        },

        /**
         * Log error to console and optionally to server
         */
        logError: function(error) {
            // Skip logging if error has no meaningful message
            if (!error || (!error.message && !error.stack && !error.filename)) {
                return;
            }

            // Skip Laravel Debugbar errors (development tool, not user-facing)
            const message = (error.message || '').toLowerCase();
            const filename = (error.filename || '').toLowerCase();
            const url = (error.url || window.location.href || '').toLowerCase();
            const stack = (error.stack || '').toLowerCase();
            
            if (url.includes('/_debugbar/') || message.includes('debugbar') || 
                filename.includes('debugbar') || stack.includes('debugbar')) {
                return; // Don't log debugbar errors
            }

            // Only log to console in development
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' || window.location.hostname.includes('localhost')) {
                console.error('Global Error:', error);
                return;
            }

            // Don't log errors without actual error information
            const hasErrorInfo = error.message || error.stack || error.filename || error.lineno;
            if (!hasErrorInfo) {
                return;
            }

            try {
                fetch('/api/log-client-error', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(error)
                }).catch(() => {
                });
            } catch (e) {
            }
        },

        /**
         * Show user-friendly error message
         */
        showUserFriendlyError: function(error) {
            if (typeof showToast !== 'undefined') {
                const message = ErrorHandler.getUserFriendlyMessage(error);
                showToast(message, 'error', 8000);
            }
        },

        /**
         * Check rate limit to prevent error spam
         */
        checkRateLimit: function() {
            const now = Date.now();
            
            // Reset counter if window expired
            if (now - this.lastErrorTime > this.errorWindow) {
                this.errorCount = 0;
                this.lastErrorTime = now;
            }
            
            // Check if we've exceeded the limit
            if (this.errorCount >= this.maxErrorsPerWindow) {
                return false; // Rate limit exceeded
            }
            
            // Increment counter
            this.errorCount++;
            this.lastErrorTime = now;
            
            return true;
        },

        /**
         * Check if error should be logged (for development console)
         */
        shouldLogError: function(error) {
            const message = (error.message || '').toLowerCase();
            const filename = (error.filename || '').toLowerCase();
            
            // Don't log extension errors
            if (filename.includes('extension://') || filename.includes('moz-extension://') || filename.includes('chrome-extension://')) {
                return false;
            }
            
            // Don't log script errors without details
            if (message.includes('script error') && !error.stack) {
                return false;
            }
            
            return true;
        },

        /**
         * Determine if error toast should be shown (VERY CONSERVATIVE)
         * Only show truly critical, user-actionable errors
         */
        shouldShowErrorToast: function(error) {
            const message = (error.message || '').toLowerCase();
            const filename = (error.filename || '').toLowerCase();
            const url = (error.url || window.location.href || '').toLowerCase();
            const stack = (error.stack || '').toLowerCase();
            
            // Don't show toast for:
            // 1. Script loading errors (external resources, CDNs, chunks)
            if (message.includes('script error') || 
                message.includes('loading chunk') ||
                message.includes('chunk load') ||
                message.includes('failed to load script') ||
                message.includes('script load')) {
                return false;
            }
            
            // 2. Extension-related errors (browser extensions)
            if (filename.includes('extension://') || 
                filename.includes('moz-extension://') || 
                filename.includes('chrome-extension://') ||
                filename.includes('safari-extension://')) {
                return false;
            }
            
            // 3. Network errors (handled by application code or network handler)
            if (message.includes('networkerror') || 
                message.includes('network error') ||
                message.includes('fetch') ||
                message.includes('failed to fetch')) {
                return false;
            }
            
            // 4. Resource loading errors (images, fonts, CSS, etc.)
            if (message.includes('failed to load resource') || 
                message.includes('failed to load') ||
                filename.includes('.woff') || 
                filename.includes('.woff2') || 
                filename.includes('.png') || 
                filename.includes('.jpg') ||
                filename.includes('.jpeg') ||
                filename.includes('.gif') ||
                filename.includes('.svg') ||
                filename.includes('.css') ||
                filename.includes('.ico')) {
                return false;
            }
            
            // 5. CORS errors (not user-actionable)
            if (message.includes('cors') || 
                message.includes('cross-origin') ||
                message.includes('access-control')) {
                return false;
            }
            
            // 6. API errors (400, 401, 403, 404, 422, 500, etc.) - handled by application code
            if (message.includes('400') || 
                message.includes('401') || 
                message.includes('403') ||
                message.includes('404') ||
                message.includes('422') ||
                message.includes('500') ||
                message.includes('bad request') || 
                message.includes('unauthorized') || 
                message.includes('forbidden') ||
                message.includes('not found') ||
                message.includes('unprocessable') ||
                message.includes('internal server')) {
                return false;
            }
            
            // 7. Fetch API errors for known endpoints (handled by application code)
            if (url.includes('/cart/') || 
                url.includes('/wishlist/') || 
                url.includes('/api/') ||
                url.includes('/checkout/') ||
                url.includes('/auth/') ||
                url.includes('/login') ||
                url.includes('/register')) {
                return false;
            }
            
            // 8. Laravel Debugbar errors (development tool)
            if (url.includes('/_debugbar/') || 
                message.includes('debugbar') || 
                filename.includes('debugbar') || 
                stack.includes('debugbar')) {
                return false;
            }
            
            // 9. Third-party script errors (analytics, ads, etc.)
            if (filename.includes('google') ||
                filename.includes('analytics') ||
                filename.includes('gtag') ||
                filename.includes('facebook') ||
                filename.includes('ads') ||
                filename.includes('advertisement') ||
                filename.includes('doubleclick') ||
                filename.includes('googletagmanager')) {
                return false;
            }
            
            // 10. Type errors that are likely handled by application code
            if (message.includes('cannot read property') ||
                message.includes('cannot read') ||
                message.includes('is not a function') ||
                message.includes('is undefined') ||
                message.includes('is null')) {
                // Only show if it's in our own code (not third-party)
                if (!filename.includes(window.location.hostname.replace('www.', '')) &&
                    !filename.includes('assets/frontend') &&
                    !filename.includes('assets/js')) {
                    return false;
                }
            }
            
            // 11. Promise rejection errors (usually handled)
            if (message.includes('promise') && 
                (message.includes('rejected') || message.includes('unhandled'))) {
                return false;
            }
            
            // 12. Syntax errors (shouldn't happen in production, but if they do, don't show to user)
            if (message.includes('syntax error') ||
                message.includes('unexpected token') ||
                message.includes('unexpected identifier')) {
                return false;
            }
            
            // VERY CONSERVATIVE: Only show errors that are:
            // - In our own code (not third-party)
            // - Not network/resource related
            // - Not API related
            // - Truly unexpected and user-actionable
            
            // Check if error is in our codebase
            const isOurCode = filename.includes(window.location.hostname.replace('www.', '')) ||
                             filename.includes('assets/frontend') ||
                             filename.includes('assets/js') ||
                             filename.includes('paperwings');
            
            // Only show if it's in our code AND it's a truly unexpected error
            // Most errors should be filtered out by now
            return isOurCode && false; // Default to false - be very conservative
        },

        /**
         * Get user-friendly error message
         */
        getUserFriendlyMessage: function(error) {
            const message = error.message || 'An unexpected error occurred';

            // Don't show technical error messages to users
            // Only show generic, actionable messages
            if (message.includes('NetworkError') || message.includes('fetch') || message.includes('network')) {
                return 'Network error. Please check your internet connection and try again.';
            }

            if (message.includes('timeout')) {
                return 'Request timed out. Please try again.';
            }

            if (message.includes('Failed to load')) {
                return 'Failed to load resource. Please refresh the page.';
            }

            // Generic user-friendly message for all other errors
            return 'Something went wrong. Please refresh the page or try again later.';
        }
    };

    // Configuration: Set to false to completely disable error handler
    // You can also set this via: window.DISABLE_ERROR_HANDLER = true;
    const DISABLE_ERROR_HANDLER = window.DISABLE_ERROR_HANDLER || false;
    
    if (!DISABLE_ERROR_HANDLER) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                ErrorHandler.init();
            });
        } else {
            ErrorHandler.init();
        }
    } else {
        console.log('[ErrorHandler] Error handler is disabled');
    }

    window.ErrorHandler = ErrorHandler;
})(window);
