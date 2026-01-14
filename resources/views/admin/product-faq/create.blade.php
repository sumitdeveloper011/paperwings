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
                    <span>Back to FAQs</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.product-faqs.store') }}" class="modern-form" id="faqForm" enctype="multipart/form-data" novalidate>
                @csrf

                <!-- Basic Information -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-info-circle"></i>
                            Basic Information
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="mb-3">
                            <label for="category_filter" class="form-label">Filter Products by Category (Optional)</label>
                            @include('components.select-category', [
                                'id' => 'category_filter',
                                'name' => 'category_filter',
                                'label' => '',
                                'required' => false,
                                'selected' => old('category_filter'),
                                'categories' => $categories,
                                'useUuid' => false,
                                'placeholder' => 'All Categories',
                                'class' => 'form-control',
                                'useSelect2' => true,
                                'showLabel' => false,
                                'wrapperClass' => '',
                            ])
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Select a category to filter products
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                            <select class="form-control @error('product_id') is-invalid @enderror"
                                    id="product_id"
                                    name="product_id"
                                    required>
                                <option value="">Select Product</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Search and select a product. Type to search products.
                            </small>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- FAQs Section -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <h3 class="modern-card__title mb-0">
                                <i class="fas fa-question-circle"></i>
                                FAQs
                            </h3>
                            <button type="button" class="btn btn-success btn-sm" id="addFaqBtn">
                                <i class="fas fa-plus"></i>
                                Add FAQ
                            </button>
                        </div>
                    </div>
                    <div class="modern-card__body">
                        <div id="faqsContainer">
                            <!-- FAQ items will be added here dynamically -->
                        </div>

                        @if($errors->has('faqs'))
                            <div class="alert alert-danger">{{ $errors->first('faqs') }}</div>
                        @endif
                        @if($errors->has('faqs.*'))
                            <div class="alert alert-danger">{{ $errors->first('faqs.*') }}</div>
                        @endif
                        @if($errors->any())
                            <div id="serverErrors" class="alert alert-danger" style="display: none;">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i>
                        Create FAQs
                    </button>
                    <a href="{{ route('admin.product-faqs.index') }}" class="btn btn-outline-secondary btn-lg" style="background-color: #f8f9fa;">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            @include('admin.product-faq.partials.tips')
        </div>
    </div>
</div>

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<!-- CKEditor 5 - Custom build -->
<script src="{{ asset('assets/js/ckeditor-custom.js') }}"></script>
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
                    if (typeof showToast === 'function') {
                        showToast('Error', 'Failed to load products. Please try again.', 'error', 5000);
                    }
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
        @include('admin.product-faq.partials.repeater-scripts')
        
        window.initFaqRepeater({
            containerId: 'faqsContainer',
            addButtonId: 'addFaqBtn',
            formId: 'faqForm',
            existingFaqs: null,
            uploadUrl: '{{ route('admin.pages.uploadImage') }}'
        });

        // Form validation
        let isSubmitting = false;

        $('#faqForm').on('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }

            const productId = $('#product_id').val();
            if (!productId || productId === '') {
                e.preventDefault();
                if (typeof showToast === 'function') {
                    showToast('Error', 'Please select a product first!', 'error', 5000);
                } else {
                    alert('Please select a product first!');
                }
                $('#product_id').focus();
                return false;
            }

            isSubmitting = true;
            const $submitBtn = $(this).find('button[type="submit"]');
            const originalText = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');

            setTimeout(function() {
                if (isSubmitting) {
                    isSubmitting = false;
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            }, 5000);

            return true;
        });
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

.faq-item .mb-3 {
    margin-bottom: 1rem;
}

.faq-item:last-child {
    margin-bottom: 0 !important;
}
</style>
@endpush
@endsection
