/**
 * FAQ Accordion Module
 * Handles FAQ accordion functionality
 */
(function() {
    'use strict';

    function initFAQ() {
        const faqQuestions = document.querySelectorAll('.faq-question');
        
        if (faqQuestions.length === 0) {
            return;
        }

        faqQuestions.forEach(question => {
            question.addEventListener('click', function() {
                const item = this.closest('.faq-item');
                if (!item) return;
                
                const allItems = document.querySelectorAll('.faq-item');
                
                allItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                    }
                });
                
                item.classList.toggle('active');
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFAQ);
    } else {
        initFAQ();
    }
})();

