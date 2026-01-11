@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Product FAQ
                </h1>
                <p class="page-header__subtitle">Update FAQ information</p>
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
            <form method="POST" action="{{ route('admin.product-faqs.update', $productFaq) }}" class="modern-form" id="faqForm" novalidate>
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-info-circle"></i>
                            Basic Information
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        @include('components.select-category', [
                            'id' => 'category_id',
                            'name' => 'category_id',
                            'label' => 'Category',
                            'required' => false,
                            'selected' => old('category_id', $productFaq->category_id ?? $productFaq->product->category_id ?? ''),
                            'categories' => $categories,
                            'useUuid' => false,
                            'placeholder' => 'Select Category (Optional)',
                            'class' => 'form-control',
                            'useSelect2' => true,
                            'showLabel' => true,
                            'wrapperClass' => 'mb-3',
                        ])

                        <div class="mb-3">
                            <label for="category_filter" class="form-label">Filter Products by Category (Optional)</label>
                            @include('components.select-category', [
                                'id' => 'category_filter',
                                'name' => 'category_filter',
                                'label' => '',
                                'required' => false,
                                'selected' => old('category_filter', $productFaq->product->category_id ?? ''),
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
                                @if($productFaq->product)
                                    <option value="{{ $productFaq->product->id }}" selected data-price="{{ $productFaq->product->total_price }}" data-name="{{ $productFaq->product->name }}">
                                        {{ $productFaq->product->name }} - ${{ number_format($productFaq->product->total_price, 2) }}
                                    </option>
                                @endif
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
                            <!-- FAQ items will be populated here -->
                        </div>

                        @error('faqs')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        @error('faqs.*')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i>
                        Update FAQs
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
        if ($('#product_id').length === 0) {
            console.error('Product select element not found');
            return;
        }

        if ($('#product_id').data('select2')) {
            $('#product_id').select2('destroy');
        }

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

        $('#category_filter').on('change', function() {
            const currentProductId = $('#product_id').val();
            $('#product_id').val(null).trigger('change');
            if (currentProductId) {
                setTimeout(function() {
                    $('#product_id').val(currentProductId).trigger('change');
                }, 100);
            }
        });

        initFaqRepeater();
    }

    function initFaqRepeater() {
        let faqIndex = 0;

        function getFaqRowTemplate(index, faqData = null) {
            const question = faqData ? faqData.question : '';
            const answer = faqData ? faqData.answer : '';
            const sortOrder = faqData ? faqData.sort_order : index;
            const status = faqData ? (faqData.status ? '1' : '0') : '1';

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

                    <div class="mb-3">
                        <label class="form-label">Question <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               name="faqs[${index}][question]"
                               value="${question.replace(/"/g, '&quot;')}"
                               placeholder="Enter question"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Answer <span class="text-danger">*</span></label>
                        <textarea class="form-control"
                                  name="faqs[${index}][answer]"
                                  rows="4"
                                  placeholder="Enter answer" required>${answer.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</textarea>
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

        function loadExistingFaqs() {
            const existingFaqs = @json($productFaq->faqs ?? []);

            if (existingFaqs && existingFaqs.length > 0) {
                // Reverse the array to show latest first, then prepend each
                const reversedFaqs = existingFaqs.slice().reverse();
                reversedFaqs.forEach(function(faq) {
                    $('#faqsContainer').prepend(getFaqRowTemplate(faqIndex, faq));
                    faqIndex++;
                });
            } else {
                $('#faqsContainer').prepend(getFaqRowTemplate(faqIndex));
                faqIndex++;
            }
            // After loading, update numbers to reflect correct order
            updateFaqNumbers();
            updateRemoveButtons();
        }

        // Add FAQ button click - prepend instead of append to show latest first
        $('#addFaqBtn').on('click', function() {
            const currentCount = $('#faqsContainer .faq-item').length;
            const newFaqHtml = getFaqRowTemplate(currentCount);
            // Prepend to show latest first
            $('#faqsContainer').prepend(newFaqHtml);
            faqIndex = currentCount + 1;
            updateRemoveButtons();
            updateFaqNumbers();
        });

        $(document).on('click', '.remove-faq-btn', function() {
            $(this).closest('.faq-item').remove();
            updateFaqNumbers();
            updateRemoveButtons();
        });

        function updateFaqNumbers() {
            $('#faqsContainer .faq-item').each(function(index) {
                $(this).find('.faq-number').text(index + 1);
                $(this).attr('data-index', index);
                $(this).find('input, textarea, select').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/faqs\[\d+\]/, `faqs[${index}]`);
                        $(this).attr('name', newName);
                    }
                });
            });
        }

        function updateRemoveButtons() {
            const faqCount = $('#faqsContainer .faq-item').length;
            if (faqCount <= 1) {
                $('.remove-faq-btn').hide();
            } else {
                $('.remove-faq-btn').show();
            }
        }

        $('#faqForm').on('submit', function(e) {
            const faqCount = $('#faqsContainer .faq-item').length;
            if (faqCount === 0) {
                e.preventDefault();
                if (typeof showToast === 'function') {
                    showToast('Error', 'Please add at least one FAQ.', 'error', 5000);
                } else {
                    alert('Please add at least one FAQ.');
                }
                return false;
            }
        });

        loadExistingFaqs();
    }

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
