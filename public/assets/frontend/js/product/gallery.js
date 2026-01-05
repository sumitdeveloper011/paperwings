/**
 * Product Gallery Module
 * Handles product image thumbnail switching
 */
(function() {
    'use strict';

    function initGallery() {
        const thumbnailItems = document.querySelectorAll('.thumbnail-item');
        const mainImage = document.getElementById('mainImage');

        if (!thumbnailItems.length || !mainImage) {
            return;
        }

        thumbnailItems.forEach(function(thumbnail) {
            thumbnail.addEventListener('click', function() {
                const imageUrl = this.getAttribute('data-image');

                if (imageUrl && mainImage) {
                    // Update main image source with fade effect
                    mainImage.style.opacity = '0';
                    setTimeout(function() {
                        mainImage.src = imageUrl;
                        mainImage.style.opacity = '1';
                    }, 150);

                    // Remove active class from all thumbnails
                    thumbnailItems.forEach(function(item) {
                        item.classList.remove('active');
                    });

                    // Add active class to clicked thumbnail
                    this.classList.add('active');
                }
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGallery);
    } else {
        initGallery();
    }
})();

