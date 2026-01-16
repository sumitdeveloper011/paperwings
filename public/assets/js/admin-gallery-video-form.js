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
                // Only validate, don't prevent default unless validation fails
                const activeTab = document.querySelector('#videoTab button.active');
                
                if (activeTab) {
                    if (activeTab.id === 'embed-tab' && !embedCode.value.trim()) {
                        e.preventDefault();
                        e.stopPropagation();
                        alert('Please provide embed code or switch to URL tab');
                        return false;
                    }
                    if (activeTab.id === 'url-tab' && !videoUrl.value.trim()) {
                        e.preventDefault();
                        e.stopPropagation();
                        alert('Please provide video URL or switch to Embed Code tab');
                        return false;
                    }
                }
                
                // Allow form to submit normally - don't convert to AJAX
                // Remove any event listeners that might be trying to intercept
                return true;
            });
        }
    };

    window.AdminGalleryVideoForm = AdminGalleryVideoForm;
})(window);
