/**
 * Gallery Lightbox Module
 * Handles image lightbox functionality for gallery components
 */

(function(window) {
    'use strict';

    const GalleryLightbox = {
        items: [],
        currentIndex: 0,
        lightbox: null,
        lightboxImage: null,
        lightboxCaption: null,
        closeButton: null,
        prevButton: null,
        nextButton: null,
        isAnimating: false,
        touchStartX: 0,
        touchEndX: 0,

        /**
         * Initialize lightbox with gallery items
         */
        init: function(items) {
            if (!items || items.length === 0) {
                console.warn('No gallery items provided to lightbox');
                return;
            }

            this.items = items;
            this.lightbox = document.getElementById('lightbox');
            this.lightboxImage = document.getElementById('lightbox-image');
            this.lightboxCaption = document.getElementById('lightbox-caption');
            this.closeButton = this.lightbox?.querySelector('.lightbox__close');
            this.prevButton = this.lightbox?.querySelector('.lightbox__prev');
            this.nextButton = this.lightbox?.querySelector('.lightbox__next');

            if (!this.lightbox || !this.lightboxImage) {
                console.error('Lightbox elements not found');
                return;
            }

            this.bindEvents();
            this.preloadImages();
        },

        /**
         * Preload first few images for better performance
         */
        preloadImages: function() {
            const preloadCount = Math.min(3, this.items.length);
            for (let i = 0; i < preloadCount; i++) {
                const img = new Image();
                img.src = this.items[i].image;
            }
        },

        /**
         * Bind keyboard, click, and touch events
         */
        bindEvents: function() {
            document.addEventListener('keydown', (e) => {
                if (this.lightbox && this.lightbox.classList.contains('active')) {
                    if (e.key === 'Escape') {
                        this.close();
                    } else if (e.key === 'ArrowLeft') {
                        this.changeImage(-1);
                    } else if (e.key === 'ArrowRight') {
                        this.changeImage(1);
                    }
                }
            });

            if (this.lightbox) {
                this.lightbox.addEventListener('click', (e) => {
                    if (e.target === this.lightbox) {
                        this.close();
                    }
                });
            }

            if (this.closeButton) {
                this.closeButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.close();
                });
            }

            if (this.prevButton) {
                this.prevButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.changeImage(-1);
                });
            }

            if (this.nextButton) {
                this.nextButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.changeImage(1);
                });
            }

            if (this.lightboxImage) {
                this.lightboxImage.addEventListener('touchstart', (e) => {
                    this.touchStartX = e.changedTouches[0].screenX;
                }, false);

                this.lightboxImage.addEventListener('touchend', (e) => {
                    this.touchEndX = e.changedTouches[0].screenX;
                    this.handleSwipe();
                }, false);
            }
        },

        /**
         * Handle swipe gestures
         */
        handleSwipe: function() {
            const swipeThreshold = 50;
            const diff = this.touchStartX - this.touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    this.changeImage(1);
                } else {
                    this.changeImage(-1);
                }
            }
        },

        /**
         * Open lightbox at specific index
         */
        open: function(index) {
            if (index < 0 || index >= this.items.length || this.isAnimating) {
                return;
            }

            this.isAnimating = true;
            this.currentIndex = index;
            const item = this.items[index];

            if (this.lightboxImage) {
                this.lightboxImage.style.opacity = '0';
                this.lightboxImage.src = item.image;
                this.lightboxImage.alt = item.title || 'Gallery image';

                this.lightboxImage.onload = () => {
                    setTimeout(() => {
                        this.lightboxImage.style.opacity = '1';
                        this.isAnimating = false;
                    }, 50);
                };

                this.lightboxImage.onerror = () => {
                    console.error('Failed to load image:', item.image);
                    this.lightboxImage.src = '/assets/images/placeholder.jpg';
                    this.isAnimating = false;
                };
            }

            if (this.lightboxCaption) {
                this.lightboxCaption.innerHTML = '';
                if (item.title) {
                    const title = document.createElement('h3');
                    title.textContent = item.title;
                    this.lightboxCaption.appendChild(title);
                }
                if (item.description) {
                    const desc = document.createElement('p');
                    desc.textContent = item.description;
                    this.lightboxCaption.appendChild(desc);
                }
            }

            if (this.lightbox && !this.lightbox.classList.contains('active')) {
                this.lightbox.classList.add('active');
                document.body.classList.add('lightbox-open');
            }

            if (this.prevButton) {
                this.prevButton.style.display = this.items.length > 1 ? 'flex' : 'none';
            }
            if (this.nextButton) {
                this.nextButton.style.display = this.items.length > 1 ? 'flex' : 'none';
            }

            this.preloadAdjacentImages(index);
        },

        /**
         * Preload adjacent images for smooth navigation
         */
        preloadAdjacentImages: function(currentIndex) {
            const prevIndex = currentIndex > 0 ? currentIndex - 1 : this.items.length - 1;
            const nextIndex = currentIndex < this.items.length - 1 ? currentIndex + 1 : 0;

            [prevIndex, nextIndex].forEach(index => {
                if (this.items[index]) {
                    const img = new Image();
                    img.src = this.items[index].image;
                }
            });
        },

        /**
         * Close lightbox
         */
        close: function() {
            if (this.lightbox) {
                this.lightbox.classList.remove('active');
                document.body.classList.remove('lightbox-open');
                
                setTimeout(() => {
                    if (this.lightboxImage) {
                        this.lightboxImage.src = '';
                    }
                    if (this.lightboxCaption) {
                        this.lightboxCaption.innerHTML = '';
                    }
                }, 300);
            }
        },

        /**
         * Change image by direction (-1 for previous, 1 for next)
         */
        changeImage: function(direction) {
            if (this.isAnimating || this.items.length <= 1) {
                return;
            }

            this.currentIndex += direction;
            
            if (this.currentIndex < 0) {
                this.currentIndex = this.items.length - 1;
            } else if (this.currentIndex >= this.items.length) {
                this.currentIndex = 0;
            }
            
            this.open(this.currentIndex);
        }
    };

    window.GalleryLightbox = GalleryLightbox;

    window.closeLightbox = function(event) {
        if (event) {
            event.stopPropagation();
        }
        GalleryLightbox.close();
    };

    window.changeImage = function(direction) {
        GalleryLightbox.changeImage(direction);
    };
})(window);
