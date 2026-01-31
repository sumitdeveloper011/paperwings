/**
 * Cookie Consent Module
 * Handles cookie consent banner and preferences
 * 
 * @module CookieConsent
 */
(function() {
    'use strict';

    const CookieConsent = {
        cookieName: 'cookie_consent_preferences',
        cookieExpiry: 365,
        csrfToken: null,
        preferences: null,

        /**
         * Initialize cookie consent module
         */
        init: function() {
            this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            this.preferences = this.getPreferences();
            this.attachEventListeners();
            this.loadScriptsBasedOnConsent();
        },

        getPreferences: function() {
            const cookie = this.getCookie(this.cookieName);
            
            if (!cookie) {
                return {
                    essential_cookies: true,
                    analytics_cookies: false,
                    marketing_cookies: false,
                    functionality_cookies: false,
                };
            }

            try {
                return JSON.parse(cookie);
            } catch (e) {
                return {
                    essential_cookies: true,
                    analytics_cookies: false,
                    marketing_cookies: false,
                    functionality_cookies: false,
                };
            }
        },

        /**
         * Check if user has consented to a specific cookie category
         * @param {string} category - Cookie category ('essential', 'analytics', 'marketing', 'functionality')
         * @returns {boolean} Whether user has consented
         */
        hasConsent: function(category) {
            switch(category) {
                case 'essential':
                    return true;
                case 'analytics':
                    return this.preferences.analytics_cookies === true;
                case 'marketing':
                    return this.preferences.marketing_cookies === true;
                case 'functionality':
                    return this.preferences.functionality_cookies === true;
                default:
                    return false;
            }
        },

        /**
         * Save cookie preferences to cookie and server
         * @param {Object} preferences - Preferences object
         * @param {Function} callback - Optional callback function
         */
        savePreferences: function(preferences, callback) {
            preferences.essential_cookies = true;
            preferences.preferences_saved_at = new Date().toISOString();

            this.setCookie(this.cookieName, JSON.stringify(preferences), this.cookieExpiry);
            this.preferences = preferences;

            // Use AjaxUtils if available, fallback to direct fetch
            const savePromise = window.AjaxUtils
                ? window.AjaxUtils.post('/cookie-preferences/update', preferences, { 
                    showMessage: false, 
                    silentAuth: true 
                })
                : fetch('/cookie-preferences/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(preferences)
                }).then(response => response.json());

            savePromise
                .then(data => {
                    if (data.success) {
                        this.hideBanner();
                        this.hideModal();
                        this.loadScriptsBasedOnConsent();
                        if (callback) callback();
                    }
                })
                .catch(error => {
                    // Failed to save preferences to server (silent fail)
                    // Still update local cookie and UI
                    this.hideBanner();
                    this.hideModal();
                    this.loadScriptsBasedOnConsent();
                    if (callback) callback();
                });
        },

        acceptAll: function() {
            this.savePreferences({
                essential_cookies: true,
                analytics_cookies: true,
                marketing_cookies: true,
                functionality_cookies: true,
            });
        },

        rejectAll: function() {
            this.savePreferences({
                essential_cookies: true,
                analytics_cookies: false,
                marketing_cookies: false,
                functionality_cookies: false,
            });
        },

        attachEventListeners: function() {
            const banner = document.getElementById('cookieConsentBanner');
            const modal = document.getElementById('cookiePreferencesModal');
            
            if (!banner && !modal) return;

            if (banner) {
                const acceptAllBtn = document.getElementById('cookieAcceptAll');
                const rejectAllBtn = document.getElementById('cookieRejectAll');
                const customizeBtn = document.getElementById('cookieCustomize');

                if (acceptAllBtn) {
                    acceptAllBtn.addEventListener('click', () => this.acceptAll());
                }

                if (rejectAllBtn) {
                    rejectAllBtn.addEventListener('click', () => this.rejectAll());
                }

                if (customizeBtn) {
                    customizeBtn.addEventListener('click', () => this.showModal());
                }
            }

            if (modal) {
                const closeBtn = document.getElementById('cookieModalClose');
                const overlay = modal.querySelector('.cookie-preferences-modal__overlay');
                const saveBtn = document.getElementById('cookieModalSave');
                const acceptAllBtn = document.getElementById('cookieModalAcceptAll');
                const rejectAllBtn = document.getElementById('cookieModalRejectAll');

                if (closeBtn) {
                    closeBtn.addEventListener('click', () => this.hideModal());
                }

                if (overlay) {
                    overlay.addEventListener('click', () => this.hideModal());
                }

                if (saveBtn) {
                    saveBtn.addEventListener('click', () => this.saveFromModal());
                }

                if (acceptAllBtn) {
                    acceptAllBtn.addEventListener('click', () => {
                        this.acceptAll();
                        this.hideModal();
                    });
                }

                if (rejectAllBtn) {
                    rejectAllBtn.addEventListener('click', () => {
                        this.rejectAll();
                        this.hideModal();
                    });
                }

                const checkboxes = modal.querySelectorAll('.cookie-preference-checkbox');
                checkboxes.forEach(checkbox => {
                    const category = checkbox.getAttribute('data-category');
                    checkbox.checked = this.hasConsent(category);
                    checkbox.addEventListener('change', () => {
                        this.updateModalPreferences();
                    });
                });
            }
        },

        showModal: function() {
            const modal = document.getElementById('cookiePreferencesModal');
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    modal.classList.add('active');
                }, 10);
                this.updateModalPreferences();
            }
        },

        hideModal: function() {
            const modal = document.getElementById('cookiePreferencesModal');
            if (modal) {
                modal.classList.remove('active');
                setTimeout(() => {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }, 300);
            }
        },

        updateModalPreferences: function() {
            const checkboxes = document.querySelectorAll('.cookie-preference-checkbox');
            checkboxes.forEach(checkbox => {
                const category = checkbox.getAttribute('data-category');
                checkbox.checked = this.hasConsent(category);
            });
        },

        saveFromModal: function() {
            const preferences = {
                essential_cookies: true,
                analytics_cookies: document.getElementById('analyticsCookies')?.checked || false,
                marketing_cookies: document.getElementById('marketingCookies')?.checked || false,
                functionality_cookies: document.getElementById('functionalityCookies')?.checked || false,
            };

            this.savePreferences(preferences);
        },

        hideBanner: function() {
            const banner = document.getElementById('cookieConsentBanner');
            if (banner) {
                banner.style.display = 'none';
            }
        },

        loadScriptsBasedOnConsent: function() {
            if (this.hasConsent('analytics')) {
                this.loadGoogleAnalytics();
            }
        },

        loadGoogleAnalytics: function() {
            if (window.Analytics && typeof window.Analytics.loadGoogleAnalytics === 'function') {
                window.Analytics.loadGoogleAnalytics();
            }
        },

        getCookie: function(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        },

        setCookie: function(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = `expires=${date.toUTCString()}`;
            document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax;Secure`;
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => CookieConsent.init());
    } else {
        CookieConsent.init();
    }

    window.CookieConsent = CookieConsent;
})();
