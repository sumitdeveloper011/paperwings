/**
 * Global Error Handler Module
 * Handles uncaught JavaScript errors and unhandled promise rejections
 * Conservative approach - only shows critical, user-actionable errors
 */

(function (window) {
    'use strict';

    const ErrorHandler = {
        lastErrorTime: 0,
        errorCount: 0,
        errorWindow: 60000,
        maxErrorsPerWindow: 2,

        init() {
            if (
                window.location.hostname === 'localhost' ||
                window.location.hostname === '127.0.0.1' ||
                window.location.hostname.includes('localhost')
            ) {
                this.setupConsoleOnlyErrorHandler();
                return;
            }

            this.setupGlobalErrorHandler();
            this.setupUnhandledRejectionHandler();
            this.setupNetworkErrorHandler();
        },

        setupConsoleOnlyErrorHandler() {
            window.addEventListener(
                'error',
                event => {
                    if (!event?.message && !event?.filename && !event?.error) return;
                    console.error('JavaScript Error:', event.error || event.message);
                },
                true
            );

            window.addEventListener('unhandledrejection', event => {
                if (event?.reason) {
                    console.error('Unhandled Promise Rejection:', event.reason);
                }
            });
        },

        setupGlobalErrorHandler() {
            window.addEventListener(
                'error',
                event => {
                    if (!this.checkRateLimit()) return;

                    const error = {
                        message: event.message,
                        filename: event.filename,
                        lineno: event.lineno,
                        colno: event.colno,
                        stack: event.error?.stack || null,
                        timestamp: new Date().toISOString(),
                        url: window.location.href,
                        userAgent: navigator.userAgent
                    };

                    this.logError(error);

                    if (this.shouldShowErrorToast(error)) {
                        this.showUserFriendlyError(error);
                    }
                },
                true
            );
        },

        setupUnhandledRejectionHandler() {
            window.addEventListener('unhandledrejection', event => {
                if (!event.reason || event.reason?.handled === true) {
                    event.preventDefault();
                    return;
                }

                if (!this.checkRateLimit()) {
                    event.preventDefault();
                    return;
                }

                const error = {
                    message: event.reason?.message || String(event.reason),
                    stack: event.reason?.stack || null,
                    timestamp: new Date().toISOString(),
                    url: window.location.href,
                    userAgent: navigator.userAgent
                };

                this.logError(error);

                if (this.shouldShowErrorToast(error)) {
                    this.showUserFriendlyError(error);
                }

                event.preventDefault();
            });
        },

        setupNetworkErrorHandler() {
            const originalFetch = window.fetch;

            window.fetch = (...args) => {
                const url = typeof args[0] === 'string' ? args[0] : args[0]?.url || '';

                if (url.includes('/_debugbar/')) {
                    return originalFetch(...args).catch(() =>
                        Promise.reject({ handled: true, silent: true })
                    );
                }

                return originalFetch(...args).catch(error => {
                    if (error instanceof TypeError) {
                        return this.handleHttpError(error, {
                            showMessage: true,
                            silentAbort: true
                        });
                    }

                    throw error;
                });
            };
        },

        async handleHttpError(error, options = {}) {
            const { showMessage = true, silentAbort = true } = options;

            if (error?.name === 'AbortError') {
                return Promise.reject({
                    name: 'AbortError',
                    handled: true,
                    silent: silentAbort
                });
            }

            if (error instanceof TypeError) {
                if (showMessage) {
                    this.showToast(
                        'Network error. Please check your internet connection and try again.',
                        'error',
                        7000
                    );
                }

                return Promise.reject({
                    name: 'NetworkError',
                    message: 'Network error',
                    handled: true
                });
            }

            return Promise.reject({
                message: error?.message || 'Unexpected error',
                handled: true
            });
        },

        showToast(message, type = 'error', duration = 7000) {
            if (typeof showToast !== 'undefined') {
                showToast(message, type, duration);
            }
        },

        logError(error) {
            if (!error?.message && !error?.stack) return;

            if (
                window.location.hostname === 'localhost' ||
                window.location.hostname.includes('localhost')
            ) {
                console.error('Global Error:', error);
                return;
            }

            try {
                fetch('/api/log-client-error', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(error)
                }).catch(() => {});
            } catch {}
        },

        checkRateLimit() {
            const now = Date.now();

            if (now - this.lastErrorTime > this.errorWindow) {
                this.errorCount = 0;
                this.lastErrorTime = now;
            }

            if (this.errorCount >= this.maxErrorsPerWindow) return false;

            this.errorCount++;
            this.lastErrorTime = now;
            return true;
        },

        shouldShowErrorToast() {
            return false; // intentionally conservative
        },

        showUserFriendlyError() {
            this.showToast(
                'Something went wrong. Please refresh the page or try again later.',
                'error',
                8000
            );
        }
    };

    if (!window.DISABLE_ERROR_HANDLER) {
        document.readyState === 'loading'
            ? document.addEventListener('DOMContentLoaded', () => ErrorHandler.init())
            : ErrorHandler.init();
    }

    window.ErrorHandler = ErrorHandler;
})(window);
