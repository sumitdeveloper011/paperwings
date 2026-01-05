/**
 * FAQ Accordion Module
 * Handles FAQ accordion functionality
 */
(function() {
    'use strict';

    function initFAQ() {
        // Wait for jQuery
        if (typeof jQuery === 'undefined') {
            setTimeout(initFAQ, 100);
            return;
        }

        const $ = jQuery;

        $(document).ready(function() {
            $('.faq-question').on('click', function() {
                const $item = $(this).closest('.faq-item');
                
                // Close other items
                $('.faq-item').not($item).removeClass('active');
                
                // Toggle current item
                $item.toggleClass('active');
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFAQ);
    } else {
        initFAQ();
    }
})();

