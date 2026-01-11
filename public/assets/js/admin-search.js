/**
 * Admin Search Module
 * Reusable AJAX search functionality for admin pages
 *
 * Usage:
 * Initialize with: AdminSearch.init({
 *   searchInput: '#search-input',
 *   searchForm: '#search-form',
 *   searchButton: '#search-button',
 *   clearButton: '#clear-search',
 *   resultsContainer: '#results-container',
 *   paginationContainer: '#pagination-container',
 *   loadingIndicator: '#search-loading',
 *   searchUrl: '/admin/categories',
 *   debounceDelay: 300
 * });
 */

(function(window) {
    'use strict';

    const AdminSearch = {
        config: {},
        searchTimeout: null,
        isSearching: false,
        currentRequest: null,

        /**
         * Initialize search functionality
         */
        init: function(config) {
            this.config = {
                searchInput: config.searchInput || '#search-input',
                searchForm: config.searchForm || '#search-form',
                searchButton: config.searchButton || '#search-button',
                clearButton: config.clearButton || '#clear-search',
                resultsContainer: config.resultsContainer || null,
                paginationContainer: config.paginationContainer || null,
                loadingIndicator: config.loadingIndicator || '#search-loading',
                searchUrl: config.searchUrl || window.location.href,
                debounceDelay: config.debounceDelay || 300,
                additionalParams: config.additionalParams || {},
                onSuccess: config.onSuccess || null,
                onError: config.onError || null,
            };

            this.bindEvents();
            this.updateClearButton();
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            const searchInput = document.querySelector(this.config.searchInput);
            const searchForm = document.querySelector(this.config.searchForm);
            const searchButton = document.querySelector(this.config.searchButton);
            const clearButton = document.querySelector(this.config.clearButton);

            if (!searchInput) {
                console.warn('AdminSearch: Search input not found');
                return;
            }

            // Live search on input (debounced)
            searchInput.addEventListener('input', () => {
                this.updateClearButton();
                this.debounceSearch();
            });

            // Search button click
            if (searchButton) {
                searchButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.performSearch();
                });
            }

            // Clear button click
            if (clearButton) {
                clearButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.clearSearch();
                });
            }

            // Form submit (Enter key)
            if (searchForm) {
                searchForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.performSearch();
                });
            }

            // Keyboard navigation
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.clearSearch();
                }
            });
        },

        /**
         * Debounce search function
         */
        debounceSearch: function() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch();
            }, this.config.debounceDelay);
        },

        /**
         * Perform search
         */
        performSearch: function() {
            if (this.isSearching) return;

            const searchInput = document.querySelector(this.config.searchInput);
            if (!searchInput) return;

            const searchTerm = searchInput.value.trim();
            this.showLoading();

            // Cancel previous request if exists
            if (this.currentRequest) {
                this.currentRequest.abort();
            }

            // Build URL with search parameters
            const url = new URL(this.config.searchUrl, window.location.origin);
            if (searchTerm) {
                url.searchParams.set('search', searchTerm);
            } else {
                url.searchParams.delete('search');
            }

            // Add additional parameters
            let additionalParams = this.config.additionalParams;
            if (typeof additionalParams === 'function') {
                additionalParams = additionalParams();
            }
            if (additionalParams && typeof additionalParams === 'object') {
                Object.keys(additionalParams).forEach(key => {
                    const value = additionalParams[key];
                    if (value !== null && value !== undefined && value !== '') {
                        url.searchParams.set(key, value);
                    }
                });
            }

            // Add AJAX flag
            url.searchParams.set('ajax', '1');

            // Perform AJAX request
            this.currentRequest = fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                // Check if response is ok
                if (!response.ok) {
                    // Try to get error message from response
                    return response.text().then(text => {
                        let errorMessage = 'Network response was not ok';
                        try {
                            const json = JSON.parse(text);
                            errorMessage = json.message || json.error || errorMessage;
                        } catch (e) {
                            errorMessage = `Error ${response.status}: ${response.statusText}`;
                        }
                        throw new Error(errorMessage);
                    });
                }
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If not JSON, return as text and try to parse
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Invalid JSON response from server');
                        }
                    });
                }
            })
            .then(data => {
                // Check if data has required structure
                if (data && (data.html !== undefined || data.success !== undefined)) {
                    this.handleSuccess(data);
                } else {
                    throw new Error('Invalid response format from server');
                }
            })
            .catch(error => {
                if (error.name !== 'AbortError') {
                    this.handleError(error);
                }
            })
            .finally(() => {
                this.hideLoading();
                this.isSearching = false;
                this.currentRequest = null;
            });

            this.isSearching = true;
        },

        /**
         * Handle successful search response
         */
        handleSuccess: function(data) {
            // Update results container if provided
            if (this.config.resultsContainer && data.html) {
                const container = document.querySelector(this.config.resultsContainer);
                if (container) {
                    // Clear container first
                    container.innerHTML = '';
                    container.innerHTML = data.html;
                }
            }

            // Update pagination container if provided
            if (this.config.paginationContainer) {
                const container = document.querySelector(this.config.paginationContainer);
                if (container) {
                    // Clear pagination if empty or set the pagination HTML
                    if (data.pagination && data.pagination.trim() !== '') {
                        container.innerHTML = data.pagination;
                        // Intercept pagination links immediately after updating HTML
                        // Use setTimeout to ensure DOM is updated
                        setTimeout(() => {
                            this.interceptPaginationLinks();
                        }, 0);
                    } else {
                        // Clear pagination container if no pagination HTML
                        container.innerHTML = '';
                    }
                }
            }

            // Update URL without page reload
            const url = new URL(window.location.href);
            const searchInput = document.querySelector(this.config.searchInput);
            if (searchInput && searchInput.value.trim()) {
                url.searchParams.set('search', searchInput.value.trim());
            } else {
                url.searchParams.delete('search');
            }

            // Preserve category filter in URL
            const categoryFilter = document.getElementById('category-filter');
            if (categoryFilter && categoryFilter.value) {
                url.searchParams.set('category_id', categoryFilter.value);
            } else {
                url.searchParams.delete('category_id');
            }

            window.history.pushState({}, '', url.toString());

            // Call custom success callback
            if (this.config.onSuccess && typeof this.config.onSuccess === 'function') {
                this.config.onSuccess(data);
            }
        },

        /**
         * Handle search error
         */
        handleError: function(error) {
            console.error('AdminSearch error:', error);

            // Call custom error callback
            if (this.config.onError && typeof this.config.onError === 'function') {
                this.config.onError(error);
            } else {
                // Default error handling - use toast if available
                const errorMessage = error.message || 'Search failed. Please try again.';
                if (typeof window.showToast === 'function') {
                    // showToast function signature: showToast(title, message, type, duration)
                    window.showToast('Search Error', errorMessage, 'error', 5000);
                } else if (typeof showToast === 'function') {
                    showToast(errorMessage, 'error');
                } else {
                    alert(errorMessage);
                }
            }
        },

        /**
         * Clear search
         */
        clearSearch: function() {
            const searchInput = document.querySelector(this.config.searchInput);
            if (searchInput) {
                searchInput.value = '';
                this.updateClearButton();
                this.performSearch();
            }
        },

        /**
         * Update clear button visibility
         */
        updateClearButton: function() {
            const searchInput = document.querySelector(this.config.searchInput);
            const clearButton = document.querySelector(this.config.clearButton);

            if (searchInput && clearButton) {
                if (searchInput.value.trim()) {
                    clearButton.style.display = 'block';
                } else {
                    clearButton.style.display = 'none';
                }
            }
        },

        /**
         * Show loading indicator
         */
        showLoading: function() {
            const loadingIndicator = document.querySelector(this.config.loadingIndicator);
            if (loadingIndicator) {
                loadingIndicator.style.display = 'none'; // Hide separate loading indicator
            }

            // Hide clear button during loading
            const clearButton = document.querySelector(this.config.clearButton);
            if (clearButton) {
                clearButton.style.display = 'none';
            }

            const searchButton = document.querySelector(this.config.searchButton);
            if (searchButton) {
                searchButton.disabled = true;
                const originalHTML = searchButton.innerHTML;
                searchButton.setAttribute('data-original-html', originalHTML);
                searchButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
        },

        /**
         * Hide loading indicator
         */
        hideLoading: function() {
            const loadingIndicator = document.querySelector(this.config.loadingIndicator);
            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }

            const searchButton = document.querySelector(this.config.searchButton);
            if (searchButton) {
                searchButton.disabled = false;
                const originalHTML = searchButton.getAttribute('data-original-html');
                if (originalHTML) {
                    searchButton.innerHTML = originalHTML;
                }
            }

            // Show clear button again if there's search text
            this.updateClearButton();
        },

        /**
         * Intercept pagination links to use AJAX
         */
        interceptPaginationLinks: function() {
            const paginationContainer = document.querySelector(this.config.paginationContainer);
            if (!paginationContainer) return;

            // Get all pagination links (including Previous/Next)
            const existingLinks = paginationContainer.querySelectorAll('a.pagination-link');
            existingLinks.forEach(link => {
                // Store original href
                const originalHref = link.getAttribute('href');
                if (!originalHref) return;

                // Remove existing listener by replacing
                const newLink = link.cloneNode(true);
                link.parentNode.replaceChild(newLink, link);

                newLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    // Build URL from current location to preserve all query params
                    const url = new URL(window.location.origin + window.location.pathname);

                    // Get page number from the original href
                    const originalUrl = new URL(originalHref, window.location.origin);
                    const page = originalUrl.searchParams.get('page');
                    if (page) {
                        url.searchParams.set('page', page);
                    }

                    // Always preserve category filter from dropdown (current selection)
                    const categoryFilter = document.getElementById('category-filter');
                    if (categoryFilter && categoryFilter.value) {
                        url.searchParams.set('category_id', categoryFilter.value);
                    } else {
                        // If no selection, remove category_id
                        url.searchParams.delete('category_id');
                    }

                    // Always preserve search from input
                    const searchInput = document.querySelector(this.config.searchInput);
                    if (searchInput && searchInput.value.trim()) {
                        url.searchParams.set('search', searchInput.value.trim());
                    } else {
                        url.searchParams.delete('search');
                    }

                    // Add AJAX flag
                    url.searchParams.set('ajax', '1');

                    // Perform search with the URL
                    this.performSearchWithUrl(url.toString());
                });
            });
        },

        /**
         * Perform search with specific URL
         */
        performSearchWithUrl: function(urlString) {
            if (this.isSearching) return;

            this.showLoading();

            if (this.currentRequest) {
                this.currentRequest.abort();
            }

            this.currentRequest = fetch(urlString, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        let errorMessage = 'Network response was not ok';
                        try {
                            const json = JSON.parse(text);
                            errorMessage = json.message || json.error || errorMessage;
                        } catch (e) {
                            errorMessage = `Error ${response.status}: ${response.statusText}`;
                        }
                        throw new Error(errorMessage);
                    });
                }
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Invalid JSON response from server');
                        }
                    });
                }
            })
            .then(data => {
                if (data && (data.html !== undefined || data.success !== undefined)) {
                    this.handleSuccess(data);
                } else {
                    throw new Error('Invalid response format from server');
                }
            })
            .catch(error => {
                if (error.name !== 'AbortError') {
                    this.handleError(error);
                }
            })
            .finally(() => {
                this.hideLoading();
                this.isSearching = false;
                this.currentRequest = null;
            });

            this.isSearching = true;
        }
    };

    // Export to window
    window.AdminSearch = AdminSearch;
})(window);
