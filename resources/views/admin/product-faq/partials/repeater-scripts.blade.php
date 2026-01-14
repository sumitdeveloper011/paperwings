(function() {
    function initFaqRepeater(options = {}) {
        const {
            containerId = 'faqsContainer',
            addButtonId = 'addFaqBtn',
            formId = 'faqForm',
            existingFaqs = null
        } = options;

        let faqIndex = 0;

        function getFaqRowTemplate(index, faqData = null) {
            const question = faqData ? faqData.question.replace(/"/g, '&quot;') : '';
            const answer = faqData ? faqData.answer.replace(/</g, '&lt;').replace(/>/g, '&gt;') : '';
            const sortOrder = faqData ? faqData.sort_order : index;
            const status = faqData ? (faqData.status ? '1' : '0') : '1';

            return `
                <div class="faq-item mb-4 p-3 border rounded" data-index="${index}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-question"></i>
                            FAQ #<span class="faq-number">${index + 1}</span>
                        </h5>
                        <button type="button" class="btn btn-danger btn-sm remove-faq-btn">
                            <i class="fas fa-trash"></i>
                            Remove
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Question <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               name="faqs[${index}][question]"
                               value="${question}"
                               placeholder="Enter question">
                        <div class="invalid-feedback" style="display: none;"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Answer <span class="text-danger">*</span></label>
                        <textarea class="form-control ckeditor-textarea"
                                  id="faq_answer_${index}"
                                  name="faqs[${index}][answer]"
                                  rows="4"
                                  placeholder="Enter answer">${answer}</textarea>
                        <div class="invalid-feedback" style="display: none;"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number"
                                       class="form-control"
                                       name="faqs[${index}][sort_order]"
                                       value="${sortOrder}"
                                       min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="faqs[${index}][status]">
                                    <option value="1" ${status === '1' ? 'selected' : ''}>Active</option>
                                    <option value="0" ${status === '0' ? 'selected' : ''}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function updateFaqNumbers() {
            $(`#${containerId} .faq-item`).each(function(index) {
                $(this).find('.faq-number').text(index + 1);
                $(this).attr('data-index', index);
                
                $(this).find('input, textarea, select').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/faqs\[\d+\]/, `faqs[${index}]`);
                        $(this).attr('name', newName);
                    }
                });
                
                // Update textarea ID and sync CKEditor if exists
                const textarea = $(this).find('.ckeditor-textarea');
                if (textarea.length) {
                    const oldId = textarea.attr('id');
                    const newId = `faq_answer_${index}`;
                    
                    if (oldId !== newId) {
                        // Get content from old editor if exists
                        let content = '';
                        if (window[`${oldId}_editor`]) {
                            content = window[`${oldId}_editor`].getData();
                            window[`${oldId}_editor`].destroy()
                                .then(() => {
                                    delete window[`${oldId}_editor`];
                                });
                        } else {
                            content = textarea.val();
                        }
                        
                        textarea.attr('id', newId).val(content);
                        
                        // Reinitialize with new ID
                        setTimeout(() => {
                            initCKEditorForTextarea(newId);
                        }, 100);
                    }
                }
            });
        }

        function updateRemoveButtons() {
            const faqCount = $(`#${containerId} .faq-item`).length;
            if (faqCount <= 1) {
                $(`.remove-faq-btn`).hide();
            } else {
                $(`.remove-faq-btn`).show();
            }
        }

        function displayServerErrors(errors) {
            $(`#${containerId} .faq-item`).each(function() {
                $(this).find('.form-control').removeClass('is-invalid');
                $(this).find('.invalid-feedback').hide().text('');
            });

            if (errors && typeof errors === 'object') {
                Object.keys(errors).forEach(function(key) {
                    const match = key.match(/faqs\.(\d+)\.(question|answer)/);
                    if (match) {
                        const faqIndex = match[1];
                        const field = match[2];
                        const faqItem = $(`#${containerId} .faq-item[data-index="${faqIndex}"]`);
                        const input = faqItem.find(`[name="faqs[${faqIndex}][${field}]"]`);
                        const errorDiv = input.siblings('.invalid-feedback');
                        input.addClass('is-invalid');
                        errorDiv.text(errors[key][0]).show();
                    }
                });
            }
        }

        function initCKEditorForTextarea(textareaId) {
            if (typeof ClassicEditor === 'undefined') {
                console.warn('CKEditor not loaded');
                return null;
            }

            const textarea = document.getElementById(textareaId);
            if (!textarea) {
                return null;
            }

            // Check if already initialized
            if (window[`${textareaId}_editor`]) {
                return window[`${textareaId}_editor`];
            }

            const editorPromise = ClassicEditor.create(textarea, {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'link', '|',
                        'bulletedList', 'numberedList', '|',
                        'outdent', 'indent', '|',
                        'imageUpload', 'blockQuote', 'insertTable', '|',
                        'sourceEditing', '|',
                        'undo', 'redo'
                    ],
                    shouldNotGroupWhenFull: true
                },
                language: 'en',
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                },
                simpleUpload: {
                    uploadUrl: options.uploadUrl || null,
                    withCredentials: true,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                }
            }).then(editor => {
                window[`${textareaId}_editor`] = editor;
                return editor;
            }).catch(error => {
                console.error('CKEditor initialization error:', error);
                return null;
            });

            return editorPromise;
        }

        function loadExistingFaqs() {
            if (existingFaqs && existingFaqs.length > 0) {
                const reversedFaqs = existingFaqs.slice().reverse();
                reversedFaqs.forEach(function(faq) {
                    $(`#${containerId}`).prepend(getFaqRowTemplate(faqIndex, faq));
                    faqIndex++;
                });
            } else {
                $(`#${containerId}`).prepend(getFaqRowTemplate(faqIndex));
                faqIndex++;
            }
            updateFaqNumbers();
            updateRemoveButtons();
            
            // Initialize CKEditor for all loaded FAQs
            setTimeout(() => {
                $(`#${containerId} .faq-item`).each(function() {
                    const textarea = $(this).find('.ckeditor-textarea');
                    if (textarea.length) {
                        const textareaId = textarea.attr('id');
                        initCKEditorForTextarea(textareaId);
                    }
                });
            }, 200);
        }

        $(`#${formId}`).on('submit', function(e) {
            const faqCount = $(`#${containerId} .faq-item`).length;
            if (faqCount === 0) {
                e.preventDefault();
                if (typeof showToast === 'function') {
                    showToast('Error', 'Please add at least one FAQ.', 'error', 5000);
                } else {
                    alert('Please add at least one FAQ.');
                }
                return false;
            }
            
            // Sync all CKEditor instances to textareas before submission
            $(`#${containerId} .faq-item`).each(function() {
                const textarea = $(this).find('.ckeditor-textarea');
                if (textarea.length) {
                    const textareaId = textarea.attr('id');
                    const editor = window[`${textareaId}_editor`];
                    if (editor) {
                        const content = editor.getData();
                        textarea.val(content);
                    }
                }
            });
        });

        $(`#${addButtonId}`).on('click', function() {
            const currentCount = $(`#${containerId} .faq-item`).length;
            $(`#${containerId}`).prepend(getFaqRowTemplate(currentCount));
            faqIndex = currentCount + 1;
            updateRemoveButtons();
            updateFaqNumbers();
        });

        $(document).on('click', '.remove-faq-btn', function() {
            const faqItem = $(this).closest('.faq-item');
            const textarea = faqItem.find('.ckeditor-textarea');
            
            // Destroy CKEditor instance before removing
            if (textarea.length) {
                const textareaId = textarea.attr('id');
                if (window[`${textareaId}_editor`]) {
                    window[`${textareaId}_editor`].destroy()
                        .then(() => {
                            delete window[`${textareaId}_editor`];
                        });
                }
            }
            
            faqItem.remove();
            updateFaqNumbers();
            updateRemoveButtons();
        });

        const form = document.getElementById(formId);
        if (form) {
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('invalid', function(ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                }, true);
            });
        }

        @if(isset($errors) && $errors->any())
            $(document).ready(function() {
                const serverErrors = @json($errors->getMessages());
                displayServerErrors(serverErrors);
            });
        @endif

        loadExistingFaqs();
    }

    window.initFaqRepeater = initFaqRepeater;
})();
