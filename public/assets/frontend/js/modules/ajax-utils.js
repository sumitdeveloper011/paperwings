/**
 * AJAX Utilities Module
 * Standardized fetch wrapper with error handling, authentication, and response parsing
 * 
 * @module AjaxUtils
 */
(function() {
    'use strict';

    /**
     * AJAX Utilities
     * Provides standardized methods for making AJAX requests with consistent error handling
     */
    const AjaxUtils = {
        /**
         * Get CSRF token from meta tag
         * @returns {string} CSRF token
         */
        getCsrfToken: function() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        /**
         * Get default headers for AJAX requests
         * @param {Object} additionalHeaders - Additional headers to include
         * @returns {Object} Headers object
         */
        getDefaultHeaders: function(additionalHeaders = {}) {
            return {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCsrfToken(),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...additionalHeaders
            };
        },

        /**
         * Handle authentication redirect (401)
         * @param {string} currentUrl - Current page URL
         */
        handleAuthentication: function(currentUrl = null) {
            const url = currentUrl || window.location.href;
            if (window.AppUtils && typeof window.AppUtils.redirectToLogin === 'function') {
                window.AppUtils.redirectToLogin();
            } else {
                window.location.href = '/login?intended=' + encodeURIComponent(url);
            }
        },

        /**
         * Parse JSON response with error handling
         * @param {Response} response - Fetch response object
         * @returns {Promise<Object>} Parsed JSON data
         */
        parseJsonResponse: async function(response) {
            const contentType = response.headers.get('content-type');
            
            // Handle 401 Unauthorized
            if (response.status === 401) {
                this.handleAuthentication();
                return Promise.reject({ 
                    status: 401, 
                    message: 'Not authenticated',
                    isAuthError: true,
                    handled: true
                });
            }

            // Handle 403 Forbidden
            if (response.status === 403) {
                return Promise.reject({
                    status: 403,
                    message: 'You do not have permission to perform this action.',
                    isAuthError: true,
                    isForbidden: true,
                    handled: true
                });
            }

            // Check if response is JSON
            if (contentType && contentType.includes('application/json')) {
                try {
                    const data = await response.json();
                    
                    // Handle validation errors (400, 422)
                    if (response.status === 400 || response.status === 422) {
                        return Promise.reject({
                            status: response.status,
                            message: data.message || 'Invalid request. Please check your input.',
                            errors: data.errors || {},
                            isValidationError: true,
                            data: data,
                            handled: true
                        });
                    }

                    // Handle 404 Not Found
                    if (response.status === 404) {
                        return Promise.reject({
                            status: 404,
                            message: data.message || 'The requested resource was not found.',
                            isApiError: true,
                            isNotFound: true,
                            handled: true
                        });
                    }

                    // Handle 429 Too Many Requests (Rate Limiting)
                    if (response.status === 429) {
                        return Promise.reject({
                            status: 429,
                            message: data.message || 'Too many requests. Please wait a moment and try again.',
                            isApiError: true,
                            isRateLimit: true,
                            retryAfter: data.retry_after || null,
                            handled: true
                        });
                    }

                    // Handle 5xx Server Errors
                    if (response.status >= 500 && response.status < 600) {
                        let message = 'Server error. Please try again later.';
                        if (response.status === 500) {
                            message = data.message || 'Internal server error. Please try again later.';
                        } else if (response.status === 502) {
                            message = data.message || 'Bad gateway. The server is temporarily unavailable.';
                        } else if (response.status === 503) {
                            message = data.message || 'Service unavailable. Please try again later.';
                        } else if (response.status === 504) {
                            message = data.message || 'Gateway timeout. Please try again.';
                        }

                        return Promise.reject({
                            status: response.status,
                            message: message,
                            data: data,
                            isApiError: true,
                            isServerError: true,
                            handled: true
                        });
                    }

                    // Handle other error statuses
                    if (!response.ok) {
                        return Promise.reject({
                            status: response.status,
                            message: data.message || 'An error occurred. Please try again.',
                            data: data,
                            isApiError: true,
                            handled: true
                        });
                    }

                    return data;
                } catch (e) {
                    return Promise.reject({
                        status: response.status,
                        message: 'Failed to parse response.',
                        isParseError: true,
                        originalError: e,
                        handled: true
                    });
                }
            }

            // Non-JSON response - likely HTML error page
            if (!response.ok) {
                let message = 'An error occurred. Please try again.';
                
                if (response.status === 403) {
                    message = 'You do not have permission to perform this action.';
                } else if (response.status === 404) {
                    message = 'The requested resource was not found.';
                } else if (response.status === 429) {
                    message = 'Too many requests. Please wait a moment and try again.';
                } else if (response.status >= 500) {
                    message = 'Server error. Please try again later.';
                }

                return Promise.reject({
                    status: response.status,
                    message: message,
                    isApiError: true,
                    handled: true
                });
            }

            // Return text if not JSON
            return response.text();
        },

        /**
         * Show error message using available notification system
         * @param {string} message - Error message
         * @param {string} type - Message type (error, success, warning)
         */
        showMessage: function(message, type = 'error') {
            if (typeof showToast !== 'undefined') {
                showToast(message, type);
            } else if (window.AppUtils && typeof window.AppUtils.showNotification === 'function') {
                window.AppUtils.showNotification(message, type);
            } else {
                // Fallback to alert (should rarely happen)
                alert(message);
            }
        },

        /**
         * Handle error and show appropriate message
         * @param {Error|Object} error - Error object
         * @param {Object} options - Options for error handling
         * @param {boolean} options.showMessage - Whether to show error message (default: true)
         * @param {boolean} options.silentAuth - Whether to silently handle auth errors (default: false)
         * @returns {Promise<never>} Rejected promise
         */
        handleError: function(error, options = {}) {
            const {
                showMessage = true,
                silentAuth = false
            } = options;

            // Mark as handled to prevent console errors
            if (error && typeof error === 'object') {
                error.handled = true;
            }

            // Don't handle authentication errors if silent
            if (error && (error.isAuthError || error.message === 'Not authenticated')) {
                return Promise.reject(error);
            }

            // Handle cancelled/aborted requests silently
            if (error && error.name === 'AbortError') {
                return Promise.reject(error);
            }

            // Handle CORS errors
            if (error && error.message && (
                error.message.includes('CORS') || 
                error.message.includes('cross-origin') ||
                error.message.includes('Access-Control')
            )) {
                if (showMessage) {
                    this.showMessage('Cross-origin request blocked. Please contact support if this persists.', 'error');
                }
                return Promise.reject(error);
            }

            // Handle timeout errors
            if (error && (error.name === 'TimeoutError' || error.message.includes('timeout'))) {
                if (showMessage) {
                    this.showMessage('Request timed out. Please try again.', 'error');
                }
                return Promise.reject(error);
            }

            // Extract error message based on error type
            let errorMessage = 'An error occurred. Please try again.';
            
            if (error && typeof error === 'object') {
                // Handle rate limiting
                if (error.isRateLimit) {
                    errorMessage = error.message || 'Too many requests. Please wait a moment and try again.';
                    if (error.retryAfter) {
                        errorMessage += ` Please try again in ${error.retryAfter} seconds.`;
                    }
                }
                // Handle server errors
                else if (error.isServerError) {
                    errorMessage = error.message || 'Server error. Please try again later.';
                }
                // Handle not found
                else if (error.isNotFound) {
                    errorMessage = error.message || 'The requested resource was not found.';
                }
                // Handle forbidden
                else if (error.isForbidden) {
                    errorMessage = error.message || 'You do not have permission to perform this action.';
                }
                // Handle validation errors
                else if (error.isValidationError) {
                    errorMessage = error.message || 'Invalid request. Please check your input.';
                }
                // Handle parse errors
                else if (error.isParseError) {
                    errorMessage = 'Invalid response from server. Please try again.';
                }
                // Handle network errors
                else if (error.name === 'TypeError' && error.message && error.message.includes('fetch')) {
                    errorMessage = 'Network error. Please check your connection and try again.';
                }
                // Generic API error
                else if (error.message) {
                    errorMessage = error.message;
                } else if (error.data && error.data.message) {
                    errorMessage = error.data.message;
                }
            } else if (typeof error === 'string') {
                errorMessage = error;
            }

            // Show message if enabled
            if (showMessage && !silentAuth) {
                this.showMessage(errorMessage, 'error');
            }

            // Don't log to console - errors are handled and shown to user
            // Only actual unhandled errors should appear in console

            return Promise.reject(error);
        },

        /**
         * Make a GET request
         * @param {string} url - Request URL
         * @param {Object} options - Request options
         * @returns {Promise<Object>} Response data
         */
        get: async function(url, options = {}) {
            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: this.getDefaultHeaders(options.headers),
                    ...options
                });

                return await this.parseJsonResponse(response);
            } catch (error) {
                return this.handleError(error, options);
            }
        },

        /**
         * Make a POST request
         * @param {string} url - Request URL
         * @param {Object} data - Request body data
         * @param {Object} options - Request options
         * @returns {Promise<Object>} Response data
         */
        post: async function(url, data = {}, options = {}) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: this.getDefaultHeaders(options.headers),
                    body: JSON.stringify(data),
                    ...options
                });

                return await this.parseJsonResponse(response);
            } catch (error) {
                return this.handleError(error, options);
            }
        },

        /**
         * Make a PUT request
         * @param {string} url - Request URL
         * @param {Object} data - Request body data
         * @param {Object} options - Request options
         * @returns {Promise<Object>} Response data
         */
        put: async function(url, data = {}, options = {}) {
            try {
                const response = await fetch(url, {
                    method: 'PUT',
                    headers: this.getDefaultHeaders(options.headers),
                    body: JSON.stringify(data),
                    ...options
                });

                return await this.parseJsonResponse(response);
            } catch (error) {
                return this.handleError(error, options);
            }
        },

        /**
         * Make a DELETE request
         * @param {string} url - Request URL
         * @param {Object} options - Request options
         * @returns {Promise<Object>} Response data
         */
        delete: async function(url, options = {}) {
            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: this.getDefaultHeaders(options.headers),
                    ...options
                });

                return await this.parseJsonResponse(response);
            } catch (error) {
                return this.handleError(error, options);
            }
        },

        /**
         * Make a custom fetch request with standardized error handling
         * @param {string} url - Request URL
         * @param {Object} fetchOptions - Fetch API options
         * @param {Object} errorOptions - Error handling options
         * @returns {Promise<Object>} Response data
         */
        request: async function(url, fetchOptions = {}, errorOptions = {}) {
            try {
                // Merge headers
                const headers = this.getDefaultHeaders(fetchOptions.headers);
                
                const response = await fetch(url, {
                    ...fetchOptions,
                    headers: {
                        ...headers,
                        ...(fetchOptions.headers || {})
                    }
                });

                return await this.parseJsonResponse(response);
            } catch (error) {
                return this.handleError(error, errorOptions);
            }
        }
    };

    // Export to global scope
    window.AjaxUtils = AjaxUtils;
})();
