/**
 * Product Gallery Module
 * Handles product image thumbnail switching and updates GalleryLightbox index
 */
(function() {
    'use strict';

    function initGallery() {
        const thumbnailItems = document.querySelectorAll('.thumbnail-item');
        const mainImage = document.getElementById('mainImage');
        const mainImageWrapper = document.querySelector('.product-image-clickable');

        if (!thumbnailItems.length || !mainImage) {
            return;
        }

        thumbnailItems.forEach(function(thumbnail, index) {
            thumbnail.addEventListener('click', function(e) {
                e.preventDefault();
                const originalImage = this.getAttribute('data-image');
                const mediumImage = this.getAttribute('data-medium-image') || originalImage;

                if (originalImage && mediumImage && mainImage) {
                    // Show skeleton while loading new image
                    const imageWrapper = mainImage.closest('.skeleton-image-wrapper');
                    const skeleton = imageWrapper ? imageWrapper.querySelector('.skeleton-main-image') : null;
                    
                    if (skeleton) {
                        skeleton.style.display = 'block';
                        skeleton.style.opacity = '1';
                        skeleton.classList.remove('hidden');
                    }
                    
                    // Update main image source with fade effect
                    mainImage.style.opacity = '0.5';
                    mainImage.classList.remove('loaded');
                    
                    const newImg = new Image();
                    newImg.onload = function() {
                        mainImage.src = mediumImage;
                        mainImage.setAttribute('data-full-image', originalImage);
                        mainImage.setAttribute('data-medium-image', mediumImage);
                        mainImage.style.opacity = '1';
                        mainImage.classList.add('loaded');
                        
                        // Update GalleryLightbox index for main image wrapper
                        if (mainImageWrapper) {
                            mainImageWrapper.setAttribute('data-image-index', index);
                        }
                        
                        // Hide skeleton after image loads
                        if (skeleton && window.skeletonLoader) {
                            window.skeletonLoader.hideSkeleton(skeleton, mainImage);
                        }
                    };
                    newImg.onerror = function() {
                        mainImage.style.opacity = '1';
                        if (skeleton && window.skeletonLoader) {
                            window.skeletonLoader.hideSkeleton(skeleton, mainImage);
                        }
                    };
                    newImg.src = mediumImage;

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

