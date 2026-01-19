/**
 * Skeleton Loader Module
 * Handles skeleton loading states for images
 */

(function() {
    'use strict';
    
    // Prevent redeclaration
    if (window.SkeletonLoaderClass) {
        return;
    }

    class SkeletonLoader {
        constructor() {
            this.init();
        }

        init() {
            this.setupImageLoaders();
        }

        /**
         * Setup skeleton loaders for all images
         */
        setupImageLoaders() {
            // Find all images with skeleton containers
            const imageWrappers = document.querySelectorAll('.skeleton-image-wrapper, .image-wrapper');
            
            imageWrappers.forEach(wrapper => {
                const img = wrapper.querySelector('img');
                const skeleton = wrapper.querySelector('.skeleton-image, .skeleton-main-image, .skeleton-thumbnail, .skeleton-small-image, .skeleton-container');
                
                if (img && skeleton) {
                    this.setupImageLoader(img, skeleton, wrapper);
                }
            });
        }

        /**
         * Setup loader for individual image
         */
        setupImageLoader(img, skeleton, wrapper) {
            // If image is already loaded (cached)
            if (img.complete && img.naturalHeight !== 0) {
                this.hideSkeleton(skeleton, img);
                return;
            }

            // Mark as processed to avoid duplicate handlers
            if (img.dataset.skeletonProcessed) {
                return;
            }
            img.dataset.skeletonProcessed = 'true';

            // Hide skeleton when image loads
            img.addEventListener('load', () => {
                this.hideSkeleton(skeleton, img);
            }, { once: true });

            // Handle image errors
            img.addEventListener('error', () => {
                this.hideSkeleton(skeleton, img);
            }, { once: true });

            // Fallback: Hide skeleton after timeout (in case image never loads)
            setTimeout(() => {
                if (skeleton && !skeleton.classList.contains('hidden') && skeleton.style.display !== 'none') {
                    this.hideSkeleton(skeleton, img);
                }
            }, 5000);
        }

        /**
         * Hide skeleton and show image
         */
        hideSkeleton(skeleton, img) {
            if (!skeleton) return;

            // Add fade out animation
            skeleton.style.transition = 'opacity 0.3s ease-out';
            skeleton.style.opacity = '0';

            // Hide skeleton after animation
            setTimeout(() => {
                if (skeleton) {
                    skeleton.classList.add('hidden');
                    skeleton.style.display = 'none';
                }
            }, 300);

            // Show image with fade in
            if (img) {
                img.classList.add('loaded');
                img.style.opacity = '1';
            }
        }

        /**
         * Manually trigger skeleton hide (for programmatic image changes)
         */
        hideSkeletonForImage(imageElement) {
            const wrapper = imageElement.closest('.image-wrapper, .skeleton-image-wrapper, .cute-stationery__image, .product-main-image');
            if (wrapper) {
                const skeleton = wrapper.querySelector('.skeleton-container, .skeleton-image, .skeleton-main-image, .skeleton-thumbnail');
                if (skeleton) {
                    this.hideSkeleton(skeleton, imageElement);
                }
            }
        }
    }

    // Store class reference to prevent redeclaration
    window.SkeletonLoaderClass = SkeletonLoader;

    // Initialize on DOM ready (only once)
    if (!window.skeletonLoaderInitialized) {
        window.skeletonLoaderInitialized = true;
        
        function initSkeletonLoader() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    if (!window.skeletonLoader) {
                        window.skeletonLoader = new SkeletonLoader();
                    }
                });
            } else {
                if (!window.skeletonLoader) {
                    window.skeletonLoader = new SkeletonLoader();
                }
            }
        }
        
        initSkeletonLoader();
    }

    // Re-initialize for dynamically loaded content
    if (typeof window !== 'undefined') {
        window.initSkeletonLoaders = function() {
            if (window.skeletonLoader) {
                window.skeletonLoader.setupImageLoaders();
            } else if (window.SkeletonLoaderClass) {
                window.skeletonLoader = new window.SkeletonLoaderClass();
            }
        };
    }
})();
