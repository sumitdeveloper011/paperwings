/**
 * Home Page Carousels Module
 * Handles all Owl Carousel initializations for home page
 */
(function() {
    'use strict';

    function initCarousels() {
        // Wait for jQuery and Owl Carousel
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.owlCarousel === 'undefined') {
            setTimeout(initCarousels, 100);
            return;
        }

        const $ = jQuery;

        // Special Offers Banner Carousel
        if ($('.special-offers-banner-carousel').length) {
            const bannerCarousel = $('.special-offers-banner-carousel');
            const bannerItems = bannerCarousel.children().length;
            $('.special-offers-banner-carousel').owlCarousel({
                loop: bannerItems > 1,
                margin: 0,
                nav: false,
                dots: true,
                autoplay: bannerItems > 1,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                items: 1,
                animateOut: 'fadeOut',
                animateIn: 'fadeIn'
            });
        }

        // Testimonials Carousel
        if ($('.testimonials-carousel').length) {
            const testimonialsCarousel = $('.testimonials-carousel');
            const testimonialsItems = testimonialsCarousel.children().length;
            $('.testimonials-carousel').owlCarousel({
                loop: testimonialsItems > 3,
                margin: 30,
                nav: false,
                dots: true,
                autoplay: testimonialsItems > 1,
                autoplayTimeout: 5000,
                responsive: {
                    0: { items: 1 },
                    768: { items: 2 },
                    992: { items: 3 }
                }
            });
        }

        // You May Also Like Carousel
        if ($('.you-may-also-like-carousel').length) {
            const youMayAlsoLikeCarousel = $('.you-may-also-like-carousel');
            const youMayAlsoLikeItems = youMayAlsoLikeCarousel.children().length;
            $('.you-may-also-like-carousel').owlCarousel({
                loop: youMayAlsoLikeItems > 5,
                margin: 20,
                nav: false,
                dots: true,
                autoplay: youMayAlsoLikeItems > 1,
                autoplayTimeout: 4000,
                autoplayHoverPause: true,
                responsive: {
                    0: { items: 1 },
                    576: { items: 2 },
                    768: { items: 3 },
                    992: { items: 4 },
                    1200: { items: 5 }
                }
            });
        }

        // New Arrivals Carousel
        if ($('.new-arrivals-carousel').length) {
            const newArrivalsCarousel = $('.new-arrivals-carousel');
            const newArrivalsItems = newArrivalsCarousel.children().length;
            $('.new-arrivals-carousel').owlCarousel({
                loop: newArrivalsItems > 5,
                margin: 20,
                nav: false,
                dots: true,
                autoplay: newArrivalsItems > 1,
                autoplayTimeout: 4000,
                autoplayHoverPause: true,
                responsive: {
                    0: { items: 1 },
                    576: { items: 2 },
                    768: { items: 3 },
                    992: { items: 4 },
                    1200: { items: 5 }
                }
            });
        }

        // Recently Viewed Carousel
        if ($('.recently-viewed-carousel').length) {
            const recentlyViewedCarousel = $('.recently-viewed-carousel');
            const recentlyViewedItems = recentlyViewedCarousel.children().length;
            $('.recently-viewed-carousel').owlCarousel({
                loop: recentlyViewedItems > 5,
                margin: 20,
                nav: false,
                dots: true,
                autoplay: recentlyViewedItems > 1,
                autoplayTimeout: 4000,
                autoplayHoverPause: true,
                responsive: {
                    0: { items: 1 },
                    576: { items: 2 },
                    768: { items: 3 },
                    992: { items: 4 },
                    1200: { items: 5 }
                }
            });
        }

        // Cute Stationery Carousel
        $('.cute-stationery-carousel').each(function() {
            const $carousel = $(this);
            const items = $carousel.children().length;
            $carousel.owlCarousel({
                loop: items > 5,
                margin: 20,
                nav: false,
                dots: true,
                autoplay: items > 1,
                autoplayTimeout: 4000,
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

        // Bundles Carousel
        if ($('.bundles-carousel').length) {
            const bundlesCarousel = $('.bundles-carousel');
            const bundlesItems = bundlesCarousel.children().length;
            $('.bundles-carousel').owlCarousel({
                loop: bundlesItems > 4,
                margin: 20,
                nav: false,
                dots: true,
                autoplay: bundlesItems > 1,
                autoplayTimeout: 4000,
                autoplayHoverPause: true,
                responsive: {
                    0: { items: 1 },
                    576: { items: 2 },
                    768: { items: 3 },
                    992: { items: 4 }
                }
            });
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCarousels);
    } else {
        initCarousels();
    }
})();

