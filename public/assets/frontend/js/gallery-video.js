/**
 * Gallery Video Module
 * Handles video playback in gallery components
 */

(function(window) {
    'use strict';

    const GalleryVideo = {
        /**
         * Play video by hiding thumbnail and showing video player
         */
        play: function(element) {
            const videoWrapper = element.closest('.gallery-item__video-wrapper');
            if (!videoWrapper) return;

            const embed = videoWrapper.querySelector('.gallery-item__embed');
            const video = videoWrapper.querySelector('.gallery-item__video');
            const thumbnail = videoWrapper.querySelector('.gallery-item__video-thumbnail');

            if (thumbnail) {
                thumbnail.classList.add('gallery-video-hidden');
            }

            if (embed) {
                embed.classList.add('gallery-video-active');
            } else if (video) {
                video.classList.add('gallery-video-active');
                const videoPlayer = video.querySelector('.gallery-item__video-player');
                if (videoPlayer) {
                    videoPlayer.play();
                }
            }
        }
    };

    window.GalleryVideo = GalleryVideo;
    window.playVideo = function(element) {
        GalleryVideo.play(element);
    };
})(window);
