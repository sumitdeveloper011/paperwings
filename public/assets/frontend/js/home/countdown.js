/**
 * Countdown Timer Module
 * Handles countdown timer functionality
 */
(function() {
    'use strict';

    function initCountdown() {
        // Wait for jQuery
        if (typeof jQuery === 'undefined') {
            setTimeout(initCountdown, 100);
            return;
        }

        const $ = jQuery;

        $(document).ready(function() {
            $('.countdown-timer').each(function() {
                const $timer = $(this);
                const endDate = new Date($timer.data('end-date')).getTime();

                function updateCountdown() {
                    const now = new Date().getTime();
                    const distance = endDate - now;

                    if (distance < 0) {
                        $timer.html('<div class="countdown-expired">Offer Expired</div>');
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    $timer.find('[data-days]').text(String(days).padStart(2, '0'));
                    $timer.find('[data-hours]').text(String(hours).padStart(2, '0'));
                    $timer.find('[data-minutes]').text(String(minutes).padStart(2, '0'));
                    $timer.find('[data-seconds]').text(String(seconds).padStart(2, '0'));
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCountdown);
    } else {
        initCountdown();
    }
})();

