/**
 * Script Utilities Module
 * Utility functions for script.js modules (debounce, throttle, logging)
 */
(function() {
    'use strict';

    const CONFIG = {
        debounceDelay: 300,
        throttleDelay: 100,
        isDevelopment: false
    };

    window.ScriptUtils = {
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        throttle: function(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        log: function(...args) {
            if (CONFIG.isDevelopment) {
                console.log(...args);
            }
        },

        error: function(...args) {
            console.error(...args);
        },

        getConfig: function() {
            return CONFIG;
        }
    };
})();

