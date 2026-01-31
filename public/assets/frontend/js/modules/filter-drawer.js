/**
 * Filter Drawer Module
 * Handles mobile filter drawer toggle functionality
 */
(function() {
    'use strict';

    const FilterDrawer = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            const filterToggleBtn = document.getElementById('filterToggleBtn');
            const categoryDrawer = document.getElementById('categoryFilterDrawer');
            const shopDrawer = document.getElementById('shopFilterDrawer');
            
            const categoryCloseBtn = document.getElementById('categoryFilterDrawerClose');
            const categoryOverlay = document.getElementById('categoryFilterDrawerOverlay');
            
            const shopCloseBtn = document.getElementById('shopFilterDrawerClose');
            const shopOverlay = document.getElementById('shopFilterDrawerOverlay');

            if (filterToggleBtn) {
                filterToggleBtn.addEventListener('click', () => {
                    const drawer = categoryDrawer || shopDrawer;
                    if (drawer) {
                        this.openDrawer(drawer);
                    }
                });
            }

            if (categoryCloseBtn) {
                categoryCloseBtn.addEventListener('click', () => {
                    this.closeDrawer(categoryDrawer);
                });
            }

            if (categoryOverlay) {
                categoryOverlay.addEventListener('click', () => {
                    this.closeDrawer(categoryDrawer);
                });
            }

            if (shopCloseBtn) {
                shopCloseBtn.addEventListener('click', () => {
                    this.closeDrawer(shopDrawer);
                });
            }

            if (shopOverlay) {
                shopOverlay.addEventListener('click', () => {
                    this.closeDrawer(shopDrawer);
                });
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (categoryDrawer && categoryDrawer.classList.contains('active')) {
                        this.closeDrawer(categoryDrawer);
                    }
                    if (shopDrawer && shopDrawer.classList.contains('active')) {
                        this.closeDrawer(shopDrawer);
                    }
                }
            });

            const applyFiltersMobile = document.getElementById('applyFiltersMobile');
            if (applyFiltersMobile) {
                applyFiltersMobile.addEventListener('click', () => {
                    const applyFilters = document.getElementById('applyFilters');
                    if (applyFilters) {
                        applyFilters.click();
                    }
                    if (shopDrawer) {
                        this.closeDrawer(shopDrawer);
                    }
                });
            }

            const clearFiltersMobile = document.getElementById('clearFiltersMobile');
            if (clearFiltersMobile) {
                clearFiltersMobile.addEventListener('click', () => {
                    const clearFilters = document.getElementById('clearFilters');
                    if (clearFilters) {
                        clearFilters.click();
                    }
                    if (shopDrawer) {
                        this.closeDrawer(shopDrawer);
                    }
                });
            }
        },

        openDrawer: function(drawer) {
            if (!drawer) return;
            
            drawer.classList.add('active');
            document.body.style.overflow = 'hidden';
        },

        closeDrawer: function(drawer) {
            if (!drawer) return;
            
            drawer.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => FilterDrawer.init());
    } else {
        FilterDrawer.init();
    }

    window.FilterDrawer = FilterDrawer;
})();
