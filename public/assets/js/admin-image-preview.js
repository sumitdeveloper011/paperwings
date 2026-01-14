/**
 * Admin Image Preview Handler
 * Reusable image preview functionality for admin forms
 */
(function() {
    'use strict';

    /**
     * Initialize image preview for a given input
     * @param {string} inputId - ID of the file input element
     * @param {string} previewId - ID of the preview container
     * @param {string} previewImgId - ID of the preview image element
     * @param {Object} options - Additional options
     */
    function initImagePreview(inputId, previewId, previewImgId, options = {}) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const previewImg = document.getElementById(previewImgId);

        if (!input || !preview || !previewImg) {
            return;
        }

        const {
            onImageLoad = null,
            onImageRemove = null,
            resetCallback = null
        } = options;

        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                    
                    if (onImageLoad && typeof onImageLoad === 'function') {
                        onImageLoad(e.target.result, file);
                    }
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        window.removeImagePreview = function() {
            input.value = '';
            preview.style.display = 'none';
            
            if (resetCallback && typeof resetCallback === 'function') {
                resetCallback();
            }
            
            if (onImageRemove && typeof onImageRemove === 'function') {
                onImageRemove();
            }
        };
    }

    /**
     * Auto-initialize image previews on page load
     * Looks for standard image preview structure
     */
    function autoInitImagePreviews() {
        const imageInputs = document.querySelectorAll('input[type="file"][id="image"]');
        
        imageInputs.forEach(function(input) {
            const previewId = 'imagePreview';
            const previewImgId = 'previewImg';
            const preview = document.getElementById(previewId);
            const previewImg = document.getElementById(previewImgId);

            if (preview && previewImg) {
                initImagePreview(input.id, previewId, previewImgId);
            }
        });
    }

    // Auto-initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoInitImagePreviews);
    } else {
        autoInitImagePreviews();
    }

    // Export for manual initialization
    window.AdminImagePreview = {
        init: initImagePreview,
        autoInit: autoInitImagePreviews
    };
})();
