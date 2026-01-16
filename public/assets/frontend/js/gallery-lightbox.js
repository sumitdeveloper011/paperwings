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

        /**
         * Initialize lightbox with gallery items
         */
        init: function(items) {
            this.items = items;
            this.lightbox = document.getElementById('lightbox');
            this.lightboxImage = document.getElementById('lightbox-image');
            this.lightboxCaption = document.getElementById('lightbox-caption');

            if (!this.lightbox || !this.lightboxImage) {
                return;
            }

            this.bindEvents();
        },

        /**
         * Bind keyboard and click events
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
                    if (e.target === this.lightbox || e.target.closest('.lightbox__close')) {
                        this.close();
                    }
                });
            }
        },

        /**
         * Open lightbox at specific index
         */
        open: function(index) {
            if (index < 0 || index >= this.items.length) {
                return;
            }

            this.currentIndex = index;
            const item = this.items[index];

            if (this.lightboxImage) {
                this.lightboxImage.src = item.image;
            }

            if (this.lightboxCaption) {
                this.lightboxCaption.innerHTML = '';
                if (item.title) {
                    this.lightboxCaption.innerHTML += '<h3>' + item.title + '</h3>';
                }
                if (item.description) {
                    this.lightboxCaption.innerHTML += '<p>' + item.description + '</p>';
                }
            }

            if (this.lightbox) {
                this.lightbox.classList.add('active');
                document.body.classList.add('lightbox-open');
            }
        },

        /**
         * Close lightbox
         */
        close: function() {
            if (this.lightbox) {
                this.lightbox.classList.remove('active');
                document.body.classList.remove('lightbox-open');
            }
        },

        /**
         * Change image by direction (-1 for previous, 1 for next)
         */
        changeImage: function(direction) {
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
})(window);
