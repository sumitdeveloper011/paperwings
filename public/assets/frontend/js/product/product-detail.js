/**
 * Product Detail Page Module
 * Handles product-specific functionality: copy link, form URL setup
 */
(function() {
    'use strict';

    const ProductDetail = {
        /**
         * Initialize copy link functionality
         */
        initCopyLink: function() {
            const copyBtn = document.querySelector('.share-btn--copy');
            if (!copyBtn) {
                return;
            }

            copyBtn.addEventListener('click', function() {
                const url = this.getAttribute('data-copy-url');
                if (!url) {
                    return;
                }

                const fullUrl = window.location.origin + url;

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(fullUrl).then(() => {
                        ProductDetail.showCopySuccess(copyBtn);
                    }).catch(() => {
                        ProductDetail.fallbackCopy(fullUrl, copyBtn);
                    });
                } else {
                    ProductDetail.fallbackCopy(fullUrl, copyBtn);
                }
            });
        },

        /**
         * Show copy success feedback
         */
        showCopySuccess: function(button) {
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.style.background = '#28a745';
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.style.background = '';
            }, 2000);
        },

        /**
         * Fallback copy method for older browsers
         */
        fallbackCopy: function(text, button) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.opacity = '0';
            document.body.appendChild(textArea);
            textArea.select();

            try {
                document.execCommand('copy');
                ProductDetail.showCopySuccess(button);
            } catch (err) {
                if (window.customAlert) {
                    window.customAlert('Failed to copy link', 'Error', 'error');
                } else {
                    alert('Failed to copy link');
                }
            }

            document.body.removeChild(textArea);
        },

        /**
         * Setup form route URLs from data attributes
         */
        initFormUrls: function() {
            const reviewForm = document.getElementById('reviewForm');
            const questionForm = document.getElementById('questionForm');

            if (reviewForm && reviewForm.dataset.reviewUrl) {
                reviewForm.setAttribute('data-review-url', reviewForm.dataset.reviewUrl);
            }

            if (questionForm && questionForm.dataset.questionUrl) {
                questionForm.setAttribute('data-question-url', questionForm.dataset.questionUrl);
            }
        },

        /**
         * Initialize tab scrolling functionality for mobile
         */
        initTabScrolling: function() {
            const productTabs = document.querySelector('.product-tabs');
            const navTabs = document.querySelector('.product-tabs .nav-tabs');
            
            if (!productTabs || !navTabs) {
                return;
            }

            // Check if tabs are scrollable
            const checkScrollable = () => {
                const isScrollable = navTabs.scrollWidth > navTabs.clientWidth;
                if (isScrollable) {
                    productTabs.classList.add('has-scroll');
                    this.updateScrollIndicators(navTabs, productTabs);
                } else {
                    productTabs.classList.remove('has-scroll');
                }
            };

            // Update scroll indicators based on scroll position
            this.updateScrollIndicators = (tabsContainer, tabsWrapper) => {
                const scrollLeft = tabsContainer.scrollLeft;
                const scrollWidth = tabsContainer.scrollWidth;
                const clientWidth = tabsContainer.clientWidth;
                const maxScroll = scrollWidth - clientWidth;

                // Remove all indicator classes
                tabsWrapper.classList.remove('scrollable-left', 'scrollable-right');

                // Add indicators based on scroll position
                if (scrollLeft > 10) {
                    tabsWrapper.classList.add('scrollable-left');
                }
                if (scrollLeft < maxScroll - 10) {
                    tabsWrapper.classList.add('scrollable-right');
                }
            };

            // Listen for scroll events
            navTabs.addEventListener('scroll', () => {
                this.updateScrollIndicators(navTabs, productTabs);
            });

            // Check on resize
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    checkScrollable();
                    this.updateScrollIndicators(navTabs, productTabs);
                }, 250);
            });

            // Auto-scroll active tab into view on mobile
            const scrollActiveTabIntoView = () => {
                const activeTab = navTabs.querySelector('.nav-link.active');
                if (!activeTab) return;

                const isMobile = window.innerWidth <= 768;
                if (!isMobile) return;

                const tabRect = activeTab.getBoundingClientRect();
                const containerRect = navTabs.getBoundingClientRect();
                const scrollLeft = navTabs.scrollLeft;

                // Calculate if tab is out of view
                const tabLeft = activeTab.offsetLeft;
                const tabWidth = activeTab.offsetWidth;
                const containerWidth = navTabs.clientWidth;

                // Scroll to center the active tab
                const targetScroll = tabLeft - (containerWidth / 2) + (tabWidth / 2);
                
                navTabs.scrollTo({
                    left: targetScroll,
                    behavior: 'smooth'
                });
            };

            // Listen for tab changes (Bootstrap tab events)
            const tabButtons = navTabs.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener('shown.bs.tab', () => {
                    setTimeout(scrollActiveTabIntoView, 100);
                });
            });

            // Initial check
            checkScrollable();
            
            // Check after a short delay to ensure layout is complete
            setTimeout(() => {
                checkScrollable();
                scrollActiveTabIntoView();
            }, 300);
        },

        /**
         * Initialize all product detail functionality
         */
        init: function() {
            this.initCopyLink();
            this.initFormUrls();
            this.initTabScrolling();
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => ProductDetail.init());
    } else {
        ProductDetail.init();
    }

    // Export for manual initialization if needed
    window.ProductDetail = ProductDetail;
})();
