/**
 * Tab Navigation Module
 * Handles tab navigation for Cute Stationery section
 */
(function() {
    'use strict';

    function initTabs() {
        const navItems = document.querySelectorAll('.cute-stationery__nav-item');
        
        if (navItems.length === 0) {
            return;
        }

        navItems.forEach(navItem => {
            navItem.addEventListener('click', function() {
                const categorySlug = this.getAttribute('data-category');
                if (!categorySlug) return;

                const allNavItems = document.querySelectorAll('.cute-stationery__nav-item');
                const allTabContents = document.querySelectorAll('.cute-stationery__tab-content');
                const targetContent = document.getElementById(categorySlug + '-content');

                allNavItems.forEach(item => {
                    item.classList.remove('active');
                });

                allTabContents.forEach(content => {
                    content.classList.remove('active');
                });

                this.classList.add('active');
                
                if (targetContent) {
                    targetContent.classList.add('active');
                    
                    setTimeout(() => {
                        const carousel = targetContent.querySelector('.cute-stationery-carousel');
                        if (carousel && carousel.swiper) {
                            carousel.swiper.update();
                            carousel.swiper.updateSlides();
                            carousel.swiper.updateSlidesClasses();
                        }
                    }, 50);
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTabs);
    } else {
        initTabs();
    }
})();

