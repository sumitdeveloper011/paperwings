/**
 * Tab Navigation Module
 * Handles tab navigation for Cute Stationery section
 */
(function() {
    'use strict';

    function initTabs() {
        // Wait for jQuery
        if (typeof jQuery === 'undefined') {
            setTimeout(initTabs, 100);
            return;
        }

        const $ = jQuery;

        $(document).ready(function() {
            $('.cute-stationery__nav-item').on('click', function() {
                const categorySlug = $(this).data('category');
                $('.cute-stationery__nav-item').removeClass('active');
                $(this).addClass('active');
                $('.cute-stationery__tab-content').removeClass('active');
                $('#' + categorySlug + '-content').addClass('active');
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTabs);
    } else {
        initTabs();
    }
})();

