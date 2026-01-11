@props([
    'name' => 'accordion_data',
    'existingAccordions' => null,
    'oldAccordionData' => null
])

@php
    // Determine which accordion data to use: old input (validation errors) > existing accordions > empty
    $accordionData = $oldAccordionData ?? ($existingAccordions ? $existingAccordions->map(function($accordion) {
        return [
            'heading' => $accordion->heading,
            'content' => $accordion->content
        ];
    })->toArray() : []);
@endphp

<!-- Accordion Data -->
<div class="modern-card mb-4">
    <div class="modern-card__header">
        <div class="modern-card__header-content">
            <h3 class="modern-card__title">
                <i class="fas fa-list"></i>
                Additional Information (Accordion)
            </h3>
        </div>
        <div class="modern-card__header-actions">
            <button type="button" class="btn btn-sm btn-primary" id="addAccordionItem{{ str_replace(['[', ']', '_'], '', $name) }}">
                <i class="fas fa-plus"></i> Add Section
            </button>
        </div>
    </div>
    <div class="modern-card__body">
        <div id="accordionContainer{{ str_replace(['[', ']', '_'], '', $name) }}">
            @if($accordionData && count($accordionData) > 0)
                @foreach($accordionData as $index => $item)
                    <div class="accordion-item-wrapper mb-3 border rounded p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Section {{ $index + 1 }}</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-accordion-item">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="{{ $name }}[{{ $index }}][heading]"
                                   placeholder="Section heading..." value="{{ $item['heading'] ?? '' }}">
                        </div>
                        <div>
                            <textarea class="form-control accordion-content-editor" id="accordion_content_{{ str_replace(['[', ']', '_'], '', $name) }}_{{ $index }}" name="{{ $name }}[{{ $index }}][content]"
                                      rows="3" placeholder="Section content...">{{ $item['content'] ?? '' }}</textarea>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="text-muted text-center py-3" id="emptyAccordionMessage{{ str_replace(['[', ']', '_'], '', $name) }}" style="{{ $accordionData && count($accordionData) > 0 ? 'display: none;' : '' }}">
            <i class="fas fa-info-circle"></i> No additional sections added yet. Click "Add Section" to create accordion content.
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const componentId = '{{ str_replace(["[", "]", "_"], "", $name) }}';
    const addAccordionBtn = document.getElementById('addAccordionItem' + componentId);
    const accordionContainer = document.getElementById('accordionContainer' + componentId);
    const emptyAccordionMessage = document.getElementById('emptyAccordionMessage' + componentId);

    if (!addAccordionBtn || !accordionContainer) {
        return;
    }

    let accordionCounter = {{ count($accordionData ?? []) }};

    addAccordionBtn.addEventListener('click', function() {
        // Get the highest index from existing items to avoid conflicts
        const existingItems = Array.from(accordionContainer.children);
        let maxIndex = -1;
        existingItems.forEach(item => {
            const inputs = item.querySelectorAll('input[name*="[heading]"]');
            inputs.forEach(input => {
                const match = input.getAttribute('name').match(/\[(\d+)\]/);
                if (match) {
                    const index = parseInt(match[1]);
                    if (index > maxIndex) maxIndex = index;
                }
            });
        });
        const newIndex = maxIndex + 1;

        const accordionItem = createAccordionItem(newIndex);
        // Insert at the top instead of bottom
        if (accordionContainer.firstChild) {
            accordionContainer.insertBefore(accordionItem, accordionContainer.firstChild);
        } else {
            accordionContainer.appendChild(accordionItem);
        }
        accordionCounter = Math.max(accordionCounter, newIndex + 1);
        updateAccordionVisibility();
        reindexAccordionItems();
    });

    function createAccordionItem(index) {
        const div = document.createElement('div');
        div.className = 'accordion-item-wrapper mb-3 border rounded p-3';
        const textareaId = `accordion_content_${componentId}_${index}`;
        div.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Section ${index + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-accordion-item">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-2">
                <input type="text" class="form-control" name="{{ $name }}[${index}][heading]"
                       placeholder="Section heading...">
            </div>
            <div>
                <textarea class="form-control accordion-content-editor" id="${textareaId}" name="{{ $name }}[${index}][content]"
                          rows="3" placeholder="Section content..."></textarea>
            </div>
        `;

        // Add remove functionality
        div.querySelector('.remove-accordion-item').addEventListener('click', function() {
            const textarea = div.querySelector('textarea');
            if (textarea && window[textareaId + 'Editor']) {
                window[textareaId + 'Editor'].destroy()
                    .then(() => {
                        delete window[textareaId + 'Editor'];
                    })
                    .catch(err => console.error('Error destroying editor:', err));
            }
            div.remove();
            updateAccordionVisibility();
            reindexAccordionItems();
        });

        // Initialize CKEditor for the textarea after adding to DOM
        setTimeout(() => {
            initializeAccordionEditor(textareaId);
        }, 100);

        return div;
    }

    function initializeAccordionEditor(textareaId) {
        const textarea = document.getElementById(textareaId);
        if (!textarea || typeof ClassicEditor === 'undefined') {
            return;
        }

        ClassicEditor
            .create(textarea, {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'link', '|',
                        'bulletedList', 'numberedList', '|',
                        'sourceEditing', '|',
                        'undo', 'redo'
                    ]
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
            })
            .then(editor => {
                window[textareaId + 'Editor'] = editor;

                // Ensure old() values are loaded into editor after initialization
                // This handles cases where textarea has content from validation errors
                if (textarea.value && textarea.value.trim()) {
                    editor.setData(textarea.value);
                }

                // Sync editor content to textarea before form submission
                const form = textarea.closest('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        textarea.value = editor.getData();
                    }, { once: false });
                }
            })
            .catch(error => {
                console.error('CKEditor initialization error for #' + textareaId + ':', error);
            });
    }

    function updateAccordionVisibility() {
        const hasItems = accordionContainer.children.length > 0;
        if (emptyAccordionMessage) {
            emptyAccordionMessage.style.display = hasItems ? 'none' : 'block';
        }
    }

    function reindexAccordionItems() {
        Array.from(accordionContainer.children).forEach((item, index) => {
            item.querySelector('h6').textContent = `Section ${index + 1}`;
            const inputs = item.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
                }

                // Reindex textarea IDs and recreate CKEditor if needed
                if (input.tagName === 'TEXTAREA' && input.classList.contains('accordion-content-editor')) {
                    const oldId = input.id;
                    const newId = `accordion_content_${componentId}_${index}`;

                    // Destroy old editor if exists
                    if (oldId && window[oldId + 'Editor']) {
                        window[oldId + 'Editor'].destroy()
                            .then(() => {
                                delete window[oldId + 'Editor'];
                                input.id = newId;
                                // Reinitialize editor with new ID
                                setTimeout(() => {
                                    initializeAccordionEditor(newId);
                                }, 100);
                            })
                            .catch(err => console.error('Error reindexing editor:', err));
                    } else {
                        input.id = newId;
                        // Initialize editor if not already initialized
                        setTimeout(() => {
                            if (!window[newId + 'Editor']) {
                                initializeAccordionEditor(newId);
                            }
                        }, 100);
                    }
                }
            });
        });
        accordionCounter = accordionContainer.children.length;
    }

    // Add remove functionality to existing accordion items
    document.querySelectorAll('#accordionContainer' + componentId + ' .remove-accordion-item').forEach(button => {
        button.addEventListener('click', function() {
            const wrapper = this.closest('.accordion-item-wrapper');
            const textarea = wrapper.querySelector('textarea');
            if (textarea && textarea.id) {
                const editorId = textarea.id + 'Editor';
                if (window[editorId]) {
                    window[editorId].destroy()
                        .then(() => {
                            delete window[editorId];
                        })
                        .catch(err => console.error('Error destroying editor:', err));
                }
            }
            wrapper.remove();
            updateAccordionVisibility();
            reindexAccordionItems();
        });
    });

    // Initialize CKEditor for existing accordion items (from database or old input)
    document.querySelectorAll('#accordionContainer' + componentId + ' .accordion-content-editor').forEach((textarea, index) => {
        if (textarea.id) {
            initializeAccordionEditor(textarea.id);
        }
    });

    // Initial accordion visibility check
    updateAccordionVisibility();
})();
</script>
@endpush
