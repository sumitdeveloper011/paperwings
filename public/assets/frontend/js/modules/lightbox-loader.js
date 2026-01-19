/**
 * Lightbox2 Configuration Module
 * Configures Lightbox2 options after library is loaded
 * Note: Lightbox2 script is loaded directly in HTML with defer attribute
 */
(function() {
    'use strict';

    function configureLightbox() {
        if (typeof lightbox !== 'undefined' && lightbox && typeof lightbox.option === 'function') {
            try {
                lightbox.option({
                    'resizeDuration': 200,
                    'wrapAround': true,
                    'fadeDuration': 300,
                    'imageFadeDuration': 300,
                    'showImageNumberLabel': true,
                    'alwaysShowNavOnTouchDevices': true,
                    'disableScrolling': true
                });
            } catch (e) {
                console.warn('Lightbox2 configuration error:', e.message);
            }
        }
    }

    function init() {
        // Check if Lightbox2 is already loaded
        if (typeof lightbox !== 'undefined') {
            configureLightbox();
            return;
        }

        // Wait for Lightbox2 to load (it's loaded with defer, so it should be ready after DOMContentLoaded)
        const checkInterval = setInterval(function() {
            if (typeof lightbox !== 'undefined') {
                clearInterval(checkInterval);
                configureLightbox();
            }
        }, 50);

        // Stop checking after 3 seconds
        setTimeout(function() {
            clearInterval(checkInterval);
        }, 3000);
    }

    // Initialize after DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        // DOM already ready
        init();
    }
})();
