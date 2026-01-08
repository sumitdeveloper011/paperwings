@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-plus-circle"></i>
                    Add Product FAQ
                </h1>
                <p class="page-header__subtitle">Create a new product FAQ</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.product-faqs.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">FAQ Information</h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.product-faqs.store') }}" class="modern-form" id="faqForm">
                        @csrf
                        
                        <div class="form-group-modern">
                            <label for="category_id" class="form-label-modern">Category</label>
                            <div class="input-wrapper">
                                <i class="fas fa-filter input-icon"></i>
                                <select class="form-input-modern @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id">
                                    <option value="">Select Category (Optional)</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="form-text text-muted">Select category for this FAQ entry</small>
                            @error('category_id')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="category_filter" class="form-label-modern">Filter Products by Category (Optional)</label>
                            <div class="input-wrapper">
                                <i class="fas fa-filter input-icon"></i>
                                <select class="form-input-modern" id="category_filter">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="form-text text-muted">Select a category to filter products</small>
                        </div>

                        <div class="form-group-modern">
                            <label for="product_id" class="form-label-modern">Product <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-box input-icon"></i>
                                <select class="form-input-modern @error('product_id') is-invalid @enderror" 
                                        id="product_id" name="product_id" required>
                                    <option value="">Select Product</option>
                                </select>
                            </div>
                            <small class="form-text text-muted">Search and select a product. Type to search products.</small>
                            @error('product_id')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="faqs-repeater-wrapper">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">
                                    <i class="fas fa-question-circle"></i>
                                    FAQs
                                </h4>
                                <button type="button" class="btn btn-success btn-sm" id="addFaqBtn">
                                    <i class="fas fa-plus"></i>
                                    Add FAQ
                                </button>
                            </div>

                            <div id="faqsContainer">
                                <!-- FAQ items will be added here dynamically -->
                            </div>

                            @error('faqs')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                            @error('faqs.*')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i>
                                Create FAQs
                            </button>
                            <a href="{{ route('admin.product-faqs.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Wait for jQuery and Select2 to be fully loaded
(function() {
    function initSelect2() {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            initializeProductFaqForm();
        } else {
            setTimeout(initSelect2, 100);
        }
    }

    function initializeProductFaqForm() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initForm();
            });
        } else {
            initForm();
        }
    }

    function initForm() {
        // Check if element exists
        if ($('#product_id').length === 0) {
            console.error('Product select element not found');
            return;
        }

        // Destroy existing Select2 instance if any
        if ($('#product_id').data('select2')) {
            $('#product_id').select2('destroy');
        }

        // Initialize Select2 for product
        $('#product_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Click to search and select a product...',
            allowClear: true,
            ajax: {
                url: '{{ route("admin.product-faqs.searchProducts") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term || '',
                        category_id: $('#category_filter').val() || '',
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results || [],
                        pagination: {
                            more: (data.pagination && data.pagination.more) || false
                        }
                    };
                },
                cache: true,
                error: function(xhr, status, error) {
                    console.error('Select2 AJAX error:', error);
                }
            },
            minimumInputLength: 0,
            escapeMarkup: function(markup) {
                return markup;
            }
        });

        // Category filter change - reload products
        $('#category_filter').on('change', function() {
            $('#product_id').val(null).trigger('change');
        });

        // Initialize FAQ repeater
        initFaqRepeater();
    }

    function initFaqRepeater() {
        let faqIndex = 0;

        // Template for FAQ row
        function getFaqRowTemplate(index) {
            return `
                <div class="faq-item mb-4 p-3 border rounded" data-index="${index}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-question"></i>
                            FAQ #<span class="faq-number">${index + 1}</span>
                        </h5>
                        <button type="button" class="btn btn-danger btn-sm remove-faq-btn" ${faqIndex === 0 ? 'style="display:none;"' : ''}>
                            <i class="fas fa-trash"></i>
                            Remove
                        </button>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">Question <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <i class="fas fa-question input-icon"></i>
                            <input type="text" 
                                   class="form-input-modern" 
                                   name="faqs[${index}][question]" 
                                   placeholder="Enter question"
                                   required>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">Answer <span class="required">*</span></label>
                        <textarea class="form-input-modern" 
                                  name="faqs[${index}][answer]" 
                                  rows="4"
                                  placeholder="Enter answer" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern">Sort Order</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-sort input-icon"></i>
                                    <input type="number" 
                                           class="form-input-modern" 
                                           name="faqs[${index}][sort_order]" 
                                           value="${index}"
                                           min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="form-label-modern">Status</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-toggle-on input-icon"></i>
                                    <select class="form-input-modern" name="faqs[${index}][status]">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Add first FAQ row
        function addFirstFaq() {
            if ($('#faqsContainer').children().length === 0) {
                $('#faqsContainer').append(getFaqRowTemplate(0));
                faqIndex = 1;
                updateRemoveButtons();
            }
        }

        // Add FAQ button click
        $('#addFaqBtn').on('click', function() {
            // Get current count to ensure sequential indexing
            const currentCount = $('#faqsContainer .faq-item').length;
            $('#faqsContainer').append(getFaqRowTemplate(currentCount));
            faqIndex = currentCount + 1;
            updateRemoveButtons();
            updateFaqNumbers();
        });

        // Remove FAQ button click (delegated)
        $(document).on('click', '.remove-faq-btn', function() {
            $(this).closest('.faq-item').remove();
            updateFaqNumbers();
            updateRemoveButtons();
        });

        // Update FAQ numbers
        function updateFaqNumbers() {
            $('#faqsContainer .faq-item').each(function(index) {
                $(this).find('.faq-number').text(index + 1);
                $(this).attr('data-index', index);
                // Update input names
                $(this).find('input, textarea, select').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/faqs\[\d+\]/, `faqs[${index}]`);
                        $(this).attr('name', newName);
                    }
                });
            });
        }

        // Update remove buttons visibility
        function updateRemoveButtons() {
            const faqCount = $('#faqsContainer .faq-item').length;
            if (faqCount <= 1) {
                $('.remove-faq-btn').hide();
            } else {
                $('.remove-faq-btn').show();
            }
        }

        // Form validation - prevent double submission
        let isSubmitting = false;
        
        $('#faqForm').on('submit', function(e) {
            // Prevent double submission
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            
            // Check product selection
            const productId = $('#product_id').val();
            if (!productId || productId === '') {
                e.preventDefault();
                alert('Please select a product first!');
                $('#product_id').focus();
                return false;
            }
            
            // Check FAQs
            const faqCount = $('#faqsContainer .faq-item').length;
            if (faqCount === 0) {
                e.preventDefault();
                alert('Please add at least one FAQ.');
                return false;
            }
            
            // Mark as submitting and disable button
            isSubmitting = true;
            const $submitBtn = $(this).find('button[type="submit"]');
            const originalText = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');
            
            // Reset after 5 seconds in case of error
            setTimeout(function() {
                if (isSubmitting) {
                    isSubmitting = false;
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            }, 5000);
            
            return true;
        });

        // Initialize with first FAQ
        addFirstFaq();
    }

    // Start initialization
    initSelect2();
})();
</script>

<style>
.faq-item {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.faq-item:hover {
    background-color: #e9ecef;
}

.faq-item .form-group-modern {
    margin-bottom: 1rem;
}

.faq-item:last-child {
    margin-bottom: 0 !important;
}
</style>
@endpush
@endsection

