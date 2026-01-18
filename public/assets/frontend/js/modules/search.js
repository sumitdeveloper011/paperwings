/**
 * Header Search Module
 * Handles search functionality with autocomplete dropdown
 * 
 * @module Search
 */
(function() {
    'use strict';

    /**
     * Initialize search functionality for desktop and mobile
     */
    function initSearch() {
        // Desktop search elements
        const searchInput = document.getElementById('header-search-input');
        const searchBtn = document.getElementById('header-search-btn');
        const searchDropdown = document.getElementById('search-results-dropdown');
        const searchResultsList = document.getElementById('search-results-list');
        const searchLoading = document.getElementById('search-loading');
        const searchFooter = document.getElementById('search-results-footer');
        const viewAllResults = document.getElementById('view-all-results');

        // Mobile search elements
        const mobileSearchInput = document.getElementById('header-search-input-mobile');
        const mobileSearchBtn = document.getElementById('header-search-btn-mobile');
        const mobileSearchDropdown = document.getElementById('search-results-dropdown-mobile');
        const mobileSearchResultsList = document.getElementById('search-results-list-mobile');
        const mobileSearchLoading = document.getElementById('search-loading-mobile');
        const mobileSearchFooter = document.getElementById('search-results-footer-mobile');
        const mobileViewAllResults = document.getElementById('view-all-results-mobile');

        // Initialize desktop search if elements exist
        if (searchInput && searchDropdown) {
            initSearchForInput(searchInput, searchBtn, searchDropdown, searchResultsList, searchLoading, searchFooter, viewAllResults, 'header-search');
        }

        // Initialize mobile search if elements exist
        if (mobileSearchInput && mobileSearchDropdown) {
            initSearchForInput(mobileSearchInput, mobileSearchBtn, mobileSearchDropdown, mobileSearchResultsList, mobileSearchLoading, mobileSearchFooter, mobileViewAllResults, 'header-search-mobile');
        }
    }

    /**
     * Initialize search for a specific input element
     * @param {HTMLElement} searchInput - Search input element
     * @param {HTMLElement} searchBtn - Search button element
     * @param {HTMLElement} searchDropdown - Dropdown container element
     * @param {HTMLElement} searchResultsList - Results list container
     * @param {HTMLElement} searchLoading - Loading indicator element
     * @param {HTMLElement} searchFooter - Footer element
     * @param {HTMLElement} viewAllResults - View all results link
     * @param {string} containerId - Container ID for click outside detection
     */
    function initSearchForInput(searchInput, searchBtn, searchDropdown, searchResultsList, searchLoading, searchFooter, viewAllResults, containerId) {
        if (!searchInput || !searchDropdown) return;

        const Utils = window.ScriptUtils || { debounce: (fn, delay) => fn, error: () => {} };
        const CONFIG = Utils.getConfig ? Utils.getConfig() : { debounceDelay: 300 };

        let searchTimeout;
        let isSearching = false;

        const performSearch = Utils.debounce((query) => {
            if (isSearching || query.length < 2) {
                if (query.length < 2) {
                    searchDropdown.style.display = 'none';
                }
                return;
            }

            searchLoading.style.display = 'block';
            searchResultsList.innerHTML = '';
            searchFooter.style.display = 'none';
            searchDropdown.style.display = 'block';
            isSearching = true;

            const url = new URL('/search/results/render', window.location.origin);
            url.searchParams.set('q', query);

            // Use AjaxUtils if available, fallback to direct fetch
            const fetchPromise = window.AjaxUtils
                ? window.AjaxUtils.get(url.toString(), { showMessage: false, silentAuth: true })
                : fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                }).then(response => response.json());

            fetchPromise
                .then(data => {
                    searchLoading.style.display = 'none';
                    isSearching = false;

                    const html = data.data?.html ?? data.html ?? '';
                    if (data.success && html && html.trim() !== '') {
                        searchResultsList.innerHTML = html;
                        searchFooter.style.display = 'block';
                        if (viewAllResults) {
                            viewAllResults.href = `/search?q=${encodeURIComponent(query)}`;
                        }
                    } else {
                        searchResultsList.innerHTML = '<div class="search-result-item" style="text-align: center; color: #6c757d;">No products found</div>';
                        searchFooter.style.display = 'none';
                    }
                })
                .catch(error => {
                    Utils.error('Search error:', error);
                    searchLoading.style.display = 'none';
                    isSearching = false;
                    searchResultsList.innerHTML = '<div class="search-result-item" style="text-align: center; color: #dc3545;">Error loading results</div>';
                });
        }, CONFIG.debounceDelay);

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(searchTimeout);
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => performSearch(query), CONFIG.debounceDelay);
            } else {
                searchDropdown.style.display = 'none';
            }
        });

        if (searchBtn) {
            searchBtn.addEventListener('click', function() {
                const query = searchInput.value.trim();
                if (query) {
                    if (window.Analytics) {
                        window.Analytics.trackSearch(query);
                    }
                    window.location.href = `/search?q=${encodeURIComponent(query)}`;
                }
            });
        }

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.value.trim();
                if (query) {
                    if (window.Analytics) {
                        window.Analytics.trackSearch(query);
                    }
                    window.location.href = `/search?q=${encodeURIComponent(query)}`;
                }
            }
        });

        document.addEventListener('click', function(e) {
            const searchContainer = document.getElementById(containerId);
            if (searchContainer && !searchContainer.contains(e.target)) {
                searchDropdown.style.display = 'none';
            }
        });

        searchDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSearch);
    } else {
        initSearch();
    }
})();

