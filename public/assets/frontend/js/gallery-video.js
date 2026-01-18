/**
 * Gallery Video Module
 * Handles video playback in gallery components
 */

(function(window) {
    'use strict';

    const GalleryVideo = {
        /**
         * Initialize video players
         */
        init: function() {
            const videoThumbnails = document.querySelectorAll('.gallery-item__video-thumbnail');
            
            videoThumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    GalleryVideo.play(this);
                });
            });
        },

        /**
         * Play video by hiding thumbnail and showing video player
         */
        play: function(element) {
            const thumbnail = element.closest('.gallery-item__video-thumbnail');
            if (!thumbnail) {
                console.error('Video thumbnail not found');
                return;
            }

            const videoId = thumbnail.getAttribute('data-video-wrapper');
            const embed = thumbnail.querySelector(`.gallery-item__embed[data-video-id="${videoId}"]`);
            const videoContainer = thumbnail.querySelector(`.gallery-item__video[data-video-id="${videoId}"]`);

            thumbnail.style.pointerEvents = 'none';

            if (embed) {
                embed.classList.add('gallery-video-active');
                
                const iframe = embed.querySelector('iframe');
                if (iframe) {
                    if (!iframe.src) {
                        const dataSrc = iframe.getAttribute('data-src');
                        if (dataSrc) {
                            iframe.src = dataSrc;
                        }
                    }
                    
                    let src = iframe.src;
                    if (src) {
                        if (src.includes('youtube.com') || src.includes('youtu.be')) {
                            if (!src.includes('autoplay=1')) {
                                src = src + (src.includes('?') ? '&' : '?') + 'autoplay=1&mute=0';
                                iframe.src = src;
                            }
                        } else if (src.includes('vimeo.com')) {
                            if (!src.includes('autoplay=1')) {
                                src = src + (src.includes('?') ? '&' : '?') + 'autoplay=1&muted=0';
                                iframe.src = src;
                            }
                        }
                    }
                }
            } else if (videoContainer) {
                videoContainer.classList.add('gallery-video-active');
                
                const videoPlayer = videoContainer.querySelector('.gallery-item__video-player');
                if (videoPlayer) {
                    videoPlayer.play().catch(error => {
                        console.error('Video playback failed:', error);
                        alert('Unable to play video. Please check your browser settings.');
                    });
                }
            } else {
                console.error('No video element found for ID:', videoId);
            }
        },

        /**
         * Pause all videos
         */
        pauseAll: function() {
            const videoPlayers = document.querySelectorAll('.gallery-item__video-player');
            videoPlayers.forEach(player => {
                if (!player.paused) {
                    player.pause();
                }
            });

            const iframes = document.querySelectorAll('.gallery-item__embed iframe');
            iframes.forEach(iframe => {
                const src = iframe.src;
                if (src) {
                    iframe.src = src.replace('autoplay=1', 'autoplay=0');
                }
            });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            GalleryVideo.init();
        });
    } else {
        GalleryVideo.init();
    }

    window.GalleryVideo = GalleryVideo;
    
    window.playVideo = function(element) {
        GalleryVideo.play(element);
    };
})(window);
