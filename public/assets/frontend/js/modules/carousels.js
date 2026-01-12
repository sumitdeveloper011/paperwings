/**
 * Carousels Module
 * Handles all carousel initializations
 */
(function() {
    'use strict';

    function initCarousels() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initCarousels, 100);
            return;
        }

        const $ = jQuery;
        const Utils = window.ScriptUtils || { log: () => {} };

        // Slick Slider
        if (typeof $.fn.slick !== 'undefined') {
            $('.slider').slick({
                dots: true,
                infinite: true,
                speed: 500,
                fade: true,
                cssEase: 'linear',
                autoplay: true,
                autoplaySpeed: 5000,
                arrows: false,
                responsive: [{
                    breakpoint: 768,
                    settings: {
                        arrows: false,
                        dots: true
                    }
                }]
            });
        }

        // Products Carousel
        if (typeof $.fn.owlCarousel !== 'undefined') {
            $('.products-carousel').each(function() {
                const $carousel = $(this);
                const items = $carousel.children().length;
                $carousel.owlCarousel({
                    loop: items > 5,
                    margin: 20,
                    nav: false,
                    dots: true,
                    autoplay: items > 1,
                    autoplayTimeout: 5000,
                    autoplayHoverPause: true,
                    responsive: {
                        0: { items: 1 },
                        576: { items: 2 },
                        768: { items: 3 },
                        992: { items: 4 },
                        1200: { items: 5 }
                    }
                });
            });

            // Cute Stationery Carousels
            $('.cute-stationery-carousel').each(function(index) {
                try {
                    const $carousel = $(this);
                    const items = $carousel.children().length;
                    $carousel.owlCarousel({
                        loop: items > 5,
                        margin: 20,
                        nav: false,
                        dots: true,
                        autoplay: items > 1,
                        autoplayTimeout: 5000,
                        autoplayHoverPause: true,
                        responsive: {
                            0: { items: 1 },
                            576: { items: 2 },
                            768: { items: 3 },
                            992: { items: 4 },
                            1200: { items: 5 }
                        }
                    });
                    Utils.log('Carousel', index + 1, 'initialized successfully');
                } catch (error) {
                    Utils.error('Error initializing carousel', index + 1, ':', error);
                }
            });
        }

        // Product Tabs
        $(document).on('click', '.products__tab', function() {
            const targetTab = $(this).data('tab');
            $('.products__tab').removeClass('products__tab--active');
            $('.products__content').removeClass('products__content--active');
            $(this).addClass('products__tab--active');
            $('#' + targetTab).addClass('products__content--active');
            $('#' + targetTab + ' .products-carousel').trigger('refresh.owl.carousel');
        });

        // Cute Stationery Tabs
        $(document).on('click', '.cute-stationery__nav-item', function(e) {
            e.preventDefault();
            const category = $(this).data('category');
            Utils.log('Tab clicked:', category);

            $('.cute-stationery__nav-item').removeClass('active');
            $(this).addClass('active');
            $('.cute-stationery__tab-content').removeClass('active');
            $('#' + category + '-content').addClass('active');

            setTimeout(function() {
                $('#' + category + '-content .cute-stationery-carousel').trigger('refresh.owl.carousel');
            }, 100);
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCarousels);
    } else {
        initCarousels();
    }
})();

