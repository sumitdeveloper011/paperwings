{{-- CKEditor Component - Reusable Rich Text Editor (CKEditor 5 Classic Build)
     Usage: @include('components.ckeditor', [
         'id' => 'description',
         'uploadUrl' => route('admin.pages.uploadImage'),
         'toolbar' => 'full' // Options: 'full', 'basic', 'minimal'
     ])

    Note:
    - CKEditor script should be loaded once in @push('scripts') before including this component
    - Height is controlled via CSS (.ck-editor__editable in admin-style.css)
    - SourceEditing plugin is available in custom build
--}}
@php
    $editorId = $id ?? 'description';
    $uploadUrl = $uploadUrl ?? null;
    $toolbarType = $toolbar ?? 'full';
@endphp

<script>
(function() {
    const editorId = '{{ $editorId }}';
    const uploadUrl = @if($uploadUrl) '{{ $uploadUrl }}' @else null @endif;
    const toolbarType = '{{ $toolbarType }}';

    // Wait for DOM to be ready
    function initializeEditor() {
        // Check if CKEditor is loaded
        if (typeof ClassicEditor === 'undefined') {
            console.error('CKEditor is not loaded. Please include the CKEditor script before using this component.');
            return;
        }

        const textarea = document.querySelector('#' + editorId);
        if (!textarea) {
            console.error('Textarea with id "' + editorId + '" not found.');
            return;
        }

        // Check if editor already initialized
        if (window[editorId + 'Editor']) {
            return;
        }

    // Define toolbar configurations (Custom build with SourceEditing plugin)
    const toolbarConfigs = {
        full: [
            'heading', '|',
            'bold', 'italic', 'link', '|',
            'bulletedList', 'numberedList', '|',
            'outdent', 'indent', '|',
            'imageUpload', 'blockQuote', 'insertTable', '|',
            'sourceEditing', '|',
            'undo', 'redo'
        ],
        basic: [
            'heading', '|',
            'bold', 'italic', 'link', '|',
            'bulletedList', 'numberedList', '|',
            'sourceEditing', '|',
            'undo', 'redo'
        ],
        minimal: [
            'bold', 'italic', '|',
            'bulletedList', 'numberedList', '|',
            'undo', 'redo'
        ]
    };

    const toolbarItems = toolbarConfigs[toolbarType] || toolbarConfigs.full;

    // Editor configuration (according to CKEditor 5 Classic build official documentation)
    const editorConfig = {
        toolbar: {
            items: toolbarItems,
            shouldNotGroupWhenFull: true
        },
        language: 'en',
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
            ]
        },
        image: {
            toolbar: [
                'imageTextAlternative',
                'imageStyle:inline',
                'imageStyle:block',
                'imageStyle:side'
            ]
        },
        // Enable paste from Word, Google Docs, etc. (official config)
        pasteFromOffice: {
            enabled: true,
            forcePlainText: false
        },
        // General HTML Support for better HTML paste handling
        htmlSupport: {
            allow: [
                {
                    name: /.*/,
                    attributes: true,
                    classes: true,
                    styles: true
                }
            ]
        }
    };

    // Add image upload if uploadUrl is provided
    if (uploadUrl && toolbarItems.includes('imageUpload')) {
        editorConfig.simpleUpload = {
            uploadUrl: uploadUrl,
            withCredentials: true,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                                document.querySelector('input[name="_token"]')?.value || ''
            }
        };
    }

        // Preserve textarea content before CKEditor takes over
        const preservedContent = textarea.value || '';

    ClassicEditor
        .create(textarea, editorConfig)
        .then(editor => {
            window[editorId + 'Editor'] = editor;
            const isRequired = textarea.hasAttribute('data-required') || textarea.hasAttribute('required');

            // Always load content from preserved value or textarea (from old() or database)
            // This ensures content persists on page refresh or validation errors
            const contentToLoad = preservedContent || textarea.value || '';
            if (contentToLoad.trim()) {
                // Use setData to load existing content
                editor.setData(contentToLoad);
                // Also update textarea to ensure sync
                textarea.value = contentToLoad;
            }

            // Sync editor content to textarea on every change to keep them in sync
            editor.model.document.on('change:data', () => {
                const currentContent = editor.getData();
                textarea.value = currentContent;
            });

            // Sync editor content to textarea before form submission
            const form = textarea.closest('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Update textarea with editor content
                    textarea.value = editor.getData();

                    // Validate if required
                    if (isRequired) {
                        const content = editor.getData().trim();
                        // Remove HTML tags for validation
                        const textContent = content.replace(/<[^>]*>/g, '').trim();

                        if (!textContent) {
                            e.preventDefault();
                            e.stopPropagation();

                            // Show validation error
                            textarea.classList.add('is-invalid');
                            if (!textarea.nextElementSibling || !textarea.nextElementSibling.classList.contains('invalid-feedback')) {
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                errorDiv.textContent = 'This field is required.';
                                textarea.parentNode.insertBefore(errorDiv, textarea.nextSibling);
                            }

                            // Focus on editor
                            editor.editing.view.focus();
                            return false;
                        } else {
                            textarea.classList.remove('is-invalid');
                            const errorDiv = textarea.nextElementSibling;
                            if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                                errorDiv.remove();
                            }
                        }
                    }
                });
            }
        })
        .catch(error => {
            console.error('CKEditor initialization error for #' + editorId + ':', error);
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeEditor, 100);
        });
    } else {
        setTimeout(initializeEditor, 100);
    }
})();
</script>
