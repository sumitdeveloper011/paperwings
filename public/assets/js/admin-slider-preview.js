/**
 * Admin Slider Preview Handler
 * Handles live preview and validation for slider forms
 */
(function() {
    'use strict';

    /**
     * Initialize slider preview functionality
     * @param {Object} options - Configuration options
     */
    function initSliderPreview(options = {}) {
        const {
            formId = 'sliderForm',
            sliderPreviewId = 'sliderPreview',
            previewContentId = 'previewContent',
            originalImageUrl = null,
            onImageLoad = null
        } = options;

        const form = document.getElementById(formId);
        const sliderPreview = document.getElementById(sliderPreviewId);
        const previewContent = document.getElementById(previewContentId);

        if (!form || !sliderPreview) {
            return;
        }

        // Form inputs for live preview
        const headingInput = document.getElementById('heading');
        const subHeadingInput = document.getElementById('sub_heading');
        const button1NameInput = document.getElementById('button_1_name');
        const button1UrlInput = document.getElementById('button_1_url');
        const button2NameInput = document.getElementById('button_2_name');
        const button2UrlInput = document.getElementById('button_2_url');

        if (!headingInput) {
            return;
        }

        // Initialize image preview with custom callback
        if (typeof AdminImagePreview !== 'undefined') {
            AdminImagePreview.init('image', 'imagePreview', 'previewImg', {
                onImageLoad: function(imageSrc) {
                    // Update slider preview background
                    sliderPreview.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url(${imageSrc})`;
                    updatePreview();
                    
                    if (onImageLoad && typeof onImageLoad === 'function') {
                        onImageLoad(imageSrc);
                    }
                },
                resetCallback: function() {
                    // Reset to original image or clear
                    if (originalImageUrl) {
                        sliderPreview.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url(${originalImageUrl})`;
                    } else {
                        sliderPreview.style.backgroundImage = '';
                        if (previewContent) {
                            previewContent.style.display = 'none';
                        }
                    }
                }
            });
        }

        // Live preview updates
        function updatePreview() {
            const heading = headingInput.value.trim();
            const subHeading = subHeadingInput ? subHeadingInput.value.trim() : '';
            const button1Name = button1NameInput ? button1NameInput.value.trim() : '';
            const button1Url = button1UrlInput ? button1UrlInput.value.trim() : '';
            const button2Name = button2NameInput ? button2NameInput.value.trim() : '';
            const button2Url = button2UrlInput ? button2UrlInput.value.trim() : '';

            if (!previewContent) {
                return;
            }

            if (heading || subHeading || (button1Name && button1Url) || (button2Name && button2Url)) {
                previewContent.style.display = 'block';

                // Update heading
                const previewHeading = document.getElementById('previewHeading');
                if (previewHeading) {
                    previewHeading.textContent = heading || 'Your Heading Here';
                    previewHeading.style.display = heading ? 'block' : 'none';
                }

                // Update sub heading
                const previewSubHeading = document.getElementById('previewSubHeading');
                if (previewSubHeading) {
                    if (subHeading) {
                        previewSubHeading.textContent = subHeading;
                        previewSubHeading.style.display = 'block';
                    } else {
                        previewSubHeading.style.display = 'none';
                    }
                }

                // Update buttons
                const previewButtons = document.getElementById('previewButtons');
                if (previewButtons) {
                    previewButtons.innerHTML = '';

                    if (button1Name && button1Url) {
                        const btn1 = document.createElement('a');
                        btn1.href = '#';
                        btn1.className = 'preview-button';
                        btn1.textContent = button1Name;
                        previewButtons.appendChild(btn1);
                    }

                    if (button2Name && button2Url) {
                        const btn2 = document.createElement('a');
                        btn2.href = '#';
                        btn2.className = 'preview-button secondary';
                        btn2.textContent = button2Name;
                        previewButtons.appendChild(btn2);
                    }
                }
            } else {
                previewContent.style.display = 'none';
            }
        }

        // Button validation
        function validateButtons() {
            if (!button1NameInput || !button1UrlInput || !button2NameInput || !button2UrlInput) {
                return;
            }

            const button1Name = button1NameInput.value.trim();
            const button1Url = button1UrlInput.value.trim();
            const button2Name = button2NameInput.value.trim();
            const button2Url = button2UrlInput.value.trim();

            // If button 1 name is filled, URL is required
            if (button1Name && !button1Url) {
                button1UrlInput.setCustomValidity('URL is required when button name is provided');
            } else {
                button1UrlInput.setCustomValidity('');
            }

            // If button 1 URL is filled, name is required
            if (button1Url && !button1Name) {
                button1NameInput.setCustomValidity('Button name is required when URL is provided');
            } else {
                button1NameInput.setCustomValidity('');
            }

            // If button 2 name is filled, URL is required
            if (button2Name && !button2Url) {
                button2UrlInput.setCustomValidity('URL is required when button name is provided');
            } else {
                button2UrlInput.setCustomValidity('');
            }

            // If button 2 URL is filled, name is required
            if (button2Url && !button2Name) {
                button2NameInput.setCustomValidity('Button name is required when URL is provided');
            } else {
                button2NameInput.setCustomValidity('');
            }
        }

        // Add event listeners for live preview
        const inputs = [headingInput];
        if (subHeadingInput) inputs.push(subHeadingInput);
        if (button1NameInput) inputs.push(button1NameInput);
        if (button1UrlInput) inputs.push(button1UrlInput);
        if (button2NameInput) inputs.push(button2NameInput);
        if (button2UrlInput) inputs.push(button2UrlInput);

        inputs.forEach(input => {
            if (input) {
                input.addEventListener('input', updatePreview);
            }
        });

        // Add validation event listeners
        const validationInputs = [button1NameInput, button1UrlInput, button2NameInput, button2UrlInput];
        validationInputs.forEach(input => {
            if (input) {
                input.addEventListener('blur', validateButtons);
            }
        });

        // Form submission validation
        form.addEventListener('submit', function(e) {
            validateButtons();

            // Check if form is valid
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('was-validated');
                return false;
            }

            // Form is valid, allow submission
            this.classList.add('was-validated');
        });

        // Initial preview update
        updatePreview();
    }

    // Auto-initialize on DOM ready
    function autoInit() {
        const sliderForm = document.getElementById('sliderForm');
        if (sliderForm) {
            const sliderPreview = document.getElementById('sliderPreview');
            const originalImageUrl = sliderPreview ? sliderPreview.dataset.originalImage : null;
            
            initSliderPreview({
                originalImageUrl: originalImageUrl
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoInit);
    } else {
        autoInit();
    }

    // Export for manual initialization
    window.AdminSliderPreview = {
        init: initSliderPreview
    };
})();
