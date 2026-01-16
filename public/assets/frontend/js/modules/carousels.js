/**
 * Carousels Module
 * Handles all carousel initializations using Swiper.js
 */
(function() {
    'use strict';

    function initCarousels() {
        if (typeof Swiper === 'undefined') {
            setTimeout(initCarousels, 100);
            return;
        }

        const Utils = window.ScriptUtils || { log: () => {} };

        // Main Banner Slider (Swiper.js - migrated from Slick Slider)
        const mainBannerSlider = document.querySelector('.main-banner-slider');
        if (mainBannerSlider) {
            const swiperWrapper = mainBannerSlider.querySelector('.swiper-wrapper');
            const slidesCount = swiperWrapper ? swiperWrapper.children.length : 0;
            
            if (slidesCount > 0) {
                new Swiper(mainBannerSlider, {
                    loop: slidesCount > 1,
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                    autoplay: slidesCount > 1 ? {
                        delay: 5000,
                        disableOnInteraction: false
                    } : false,
                    pagination: {
                        el: mainBannerSlider.querySelector('.swiper-pagination') || null,
                        clickable: true
                    },
                    speed: 500,
                    allowTouchMove: true
                });
            }
        }

        // Products Carousel (Swiper)
        document.querySelectorAll('.products-carousel').forEach(carousel => {
            const items = carousel.children.length;
            new Swiper(carousel, {
                loop: items > 5,
                spaceBetween: 20,
                autoplay: items > 1 ? {
                    delay: 5000,
                    pauseOnMouseEnter: true,
                    disableOnInteraction: false
                } : false,
                pagination: {
                    el: carousel.querySelector('.swiper-pagination') || null,
                    clickable: true
                },
                breakpoints: {
                    0: { slidesPerView: 1 },
                    576: { slidesPerView: 2 },
                    768: { slidesPerView: 3 },
                    992: { slidesPerView: 4 },
                    1200: { slidesPerView: 5 }
                }
            });
        });

        // Cute Stationery Carousels (Swiper)
        document.querySelectorAll('.cute-stationery-carousel').forEach((carousel, index) => {
            try {
                const items = carousel.children.length;
                new Swiper(carousel, {
                    loop: items > 5,
                    spaceBetween: 20,
                    autoplay: items > 1 ? {
                        delay: 5000,
                        pauseOnMouseEnter: true,
                        disableOnInteraction: false
                    } : false,
                    pagination: {
                        el: carousel.querySelector('.swiper-pagination') || null,
                        clickable: true
                    },
                    breakpoints: {
                        0: { slidesPerView: 1 },
                        576: { slidesPerView: 2 },
                        768: { slidesPerView: 3 },
                        992: { slidesPerView: 4 },
                        1200: { slidesPerView: 5 }
                    }
                });
                Utils.log('Carousel', index + 1, 'initialized successfully');
            } catch (error) {
                Utils.error('Error initializing carousel', index + 1, ':', error);
            }
        });

        // Product Tabs (Native JS)
        document.querySelectorAll('.products__tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                if (!targetTab) return;

                document.querySelectorAll('.products__tab').forEach(t => {
                    t.classList.remove('products__tab--active');
                });
                document.querySelectorAll('.products__content').forEach(c => {
                    c.classList.remove('products__content--active');
                });

                this.classList.add('products__tab--active');
                const targetContent = document.getElementById(targetTab);
                if (targetContent) {
                    targetContent.classList.add('products__content--active');
                    const carousel = targetContent.querySelector('.products-carousel');
                    if (carousel && carousel.swiper) {
                        carousel.swiper.update();
                    }
                }
            });
        });

        // Cute Stationery Tabs (Native JS - already handled in tabs.js, but refresh carousel)
        document.querySelectorAll('.cute-stationery__nav-item').forEach(navItem => {
            navItem.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                if (!category) return;

                setTimeout(() => {
                    const tabContent = document.getElementById(category + '-content');
                    if (tabContent) {
                        const carousel = tabContent.querySelector('.cute-stationery-carousel');
                        if (carousel && carousel.swiper) {
                            carousel.swiper.update();
                        }
                    }
                }, 100);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCarousels);
    } else {
        initCarousels();
    }
})();

