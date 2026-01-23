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
         * Initialize all product detail functionality
         */
        init: function() {
            this.initCopyLink();
            this.initFormUrls();
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
