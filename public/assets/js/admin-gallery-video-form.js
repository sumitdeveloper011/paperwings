/**
 * Admin Gallery Video Form Module
 * Handles video form tab switching and validation
 */

(function(window) {
    'use strict';

    const AdminGalleryVideoForm = {
        /**
         * Initialize video form with tab switching
         */
        init: function(formId) {
            const form = document.getElementById(formId);
            if (!form) return;

            const embedTab = document.getElementById('embed-tab');
            const urlTab = document.getElementById('url-tab');
            const embedCode = document.getElementById('video_embed_code');
            const videoUrl = document.getElementById('video_url');

            if (!embedTab || !urlTab || !embedCode || !videoUrl) return;

            embedTab.addEventListener('shown.bs.tab', function() {
                videoUrl.removeAttribute('required');
                embedCode.setAttribute('required', 'required');
            });

            urlTab.addEventListener('shown.bs.tab', function() {
                embedCode.removeAttribute('required');
                videoUrl.setAttribute('required', 'required');
            });

            form.addEventListener('submit', function(e) {
                const activeTab = document.querySelector('#videoTab button.active');
                if (activeTab.id === 'embed-tab' && !embedCode.value.trim()) {
                    e.preventDefault();
                    alert('Please provide embed code or switch to URL tab');
                    return false;
                }
                if (activeTab.id === 'url-tab' && !videoUrl.value.trim()) {
                    e.preventDefault();
                    alert('Please provide video URL or switch to Embed Code tab');
                    return false;
                }
            });
        }
    };

    window.AdminGalleryVideoForm = AdminGalleryVideoForm;
})(window);
