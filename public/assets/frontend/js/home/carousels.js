/**
 * Home Page Carousels Module
 * Handles all Swiper.js carousel initializations for home page
 */
(function() {
    'use strict';

    function initCarousels() {
        if (typeof Swiper === 'undefined') {
            setTimeout(initCarousels, 100);
            return;
        }

        // Special Offers Banner Carousel
        const bannerCarousel = document.querySelector('.special-offers-banner-carousel');
        if (bannerCarousel && !bannerCarousel.swiper) {
            const bannerItems = bannerCarousel.children.length;
            new Swiper(bannerCarousel, {
                loop: bannerItems > 1,
                spaceBetween: 0,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                autoplay: bannerItems > 1 ? {
                    delay: 5000,
                    pauseOnMouseEnter: true,
                    disableOnInteraction: false
                } : false,
                pagination: {
                    el: bannerCarousel.querySelector('.swiper-pagination') || null,
                    clickable: true
                },
                slidesPerView: 1
            });
        }

        // Testimonials Carousel
        const testimonialsCarousel = document.querySelector('.testimonials-carousel');
        if (testimonialsCarousel && !testimonialsCarousel.swiper) {
            const swiperWrapper = testimonialsCarousel.querySelector('.swiper-wrapper');
            const testimonialsItems = swiperWrapper ? swiperWrapper.children.length : 0;
            
            if (testimonialsItems > 0) {
                const swiperInstance = new Swiper(testimonialsCarousel, {
                    loop: false,
                    spaceBetween: 30,
                    autoplay: testimonialsItems > 3 ? {
                        delay: 5000,
                        pauseOnMouseEnter: true,
                        disableOnInteraction: false
                    } : false,
                    pagination: {
                        el: testimonialsCarousel.querySelector('.swiper-pagination') || null,
                        clickable: true
                    },
                    breakpoints: {
                        0: { 
                            slidesPerView: 1,
                            spaceBetween: 20
                        },
                        768: { 
                            slidesPerView: 2,
                            spaceBetween: 25
                        },
                        992: { 
                            slidesPerView: 3,
                            spaceBetween: 30
                        }
                    },
                    watchOverflow: true,
                    observer: true,
                    observeParents: true,
                    speed: 600
                });
            }
        }

        // You May Also Like Carousel
        const youMayAlsoLikeCarousel = document.querySelector('.you-may-also-like-carousel');
        if (youMayAlsoLikeCarousel && !youMayAlsoLikeCarousel.swiper) {
            const youMayAlsoLikeItems = youMayAlsoLikeCarousel.children.length;
            new Swiper(youMayAlsoLikeCarousel, {
                loop: youMayAlsoLikeItems > 6,
                spaceBetween: 20,
                slidesOffsetBefore: 15,
                slidesOffsetAfter: 15,
                autoplay: youMayAlsoLikeItems > 1 ? {
                    delay: 4000,
                    pauseOnMouseEnter: true,
                    disableOnInteraction: false
                } : false,
                pagination: {
                    el: youMayAlsoLikeCarousel.querySelector('.swiper-pagination') || null,
                    clickable: true
                },
                breakpoints: {
                    0: { slidesPerView: 2 },
                    576: { slidesPerView: 2 },
                    768: { slidesPerView: 3 },
                    992: { slidesPerView: 4 },
                    1200: { slidesPerView: 6 }
                }
            });
        }

        // New Arrivals Carousel
        const newArrivalsCarousel = document.querySelector('.new-arrivals-carousel');
        if (newArrivalsCarousel && !newArrivalsCarousel.swiper) {
            const newArrivalsItems = newArrivalsCarousel.children.length;
            new Swiper(newArrivalsCarousel, {
                loop: newArrivalsItems > 6,
                spaceBetween: 20,
                slidesOffsetBefore: 15,
                slidesOffsetAfter: 15,
                autoplay: newArrivalsItems > 1 ? {
                    delay: 4000,
                    pauseOnMouseEnter: true,
                    disableOnInteraction: false
                } : false,
                pagination: {
                    el: newArrivalsCarousel.querySelector('.swiper-pagination') || null,
                    clickable: true
                },
                breakpoints: {
                    0: { slidesPerView: 2 },
                    576: { slidesPerView: 2 },
                    768: { slidesPerView: 3 },
                    992: { slidesPerView: 4 },
                    1200: { slidesPerView: 6 }
                }
            });
        }

        // Recently Viewed Carousel
        const recentlyViewedCarousel = document.querySelector('.recently-viewed-carousel');
        if (recentlyViewedCarousel && !recentlyViewedCarousel.swiper) {
            const recentlyViewedItems = recentlyViewedCarousel.children.length;
            new Swiper(recentlyViewedCarousel, {
                loop: recentlyViewedItems > 6,
                spaceBetween: 20,
                slidesOffsetBefore: 15,
                slidesOffsetAfter: 15,
                autoplay: recentlyViewedItems > 1 ? {
                    delay: 4000,
                    pauseOnMouseEnter: true,
                    disableOnInteraction: false
                } : false,
                pagination: {
                    el: recentlyViewedCarousel.querySelector('.swiper-pagination') || null,
                    clickable: true
                },
                breakpoints: {
                    0: { slidesPerView: 2 },
                    576: { slidesPerView: 2 },
                    768: { slidesPerView: 3 },
                    992: { slidesPerView: 4 },
                    1200: { slidesPerView: 6 }
                }
            });
        }

        // Bundles Carousel
        const bundlesCarousel = document.querySelector('.bundles-carousel');
        if (bundlesCarousel && !bundlesCarousel.swiper) {
            const swiperWrapper = bundlesCarousel.querySelector('.swiper-wrapper');
            const bundlesItems = swiperWrapper ? swiperWrapper.children.length : 0;
            new Swiper(bundlesCarousel, {
                loop: bundlesItems > 6,
                spaceBetween: 20,
                slidesOffsetBefore: 15,
                slidesOffsetAfter: 15,
                autoplay: bundlesItems > 1 ? {
                    delay: 4000,
                    pauseOnMouseEnter: true,
                    disableOnInteraction: false
                } : false,
                pagination: {
                    el: bundlesCarousel.querySelector('.swiper-pagination') || null,
                    clickable: true
                },
                breakpoints: {
                    0: { slidesPerView: 1 },
                    576: { slidesPerView: 2 },
                    768: { slidesPerView: 3 },
                    992: { slidesPerView: 4 },
                    1200: { slidesPerView: 6 }
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCarousels);
    } else {
        initCarousels();
    }
})();

