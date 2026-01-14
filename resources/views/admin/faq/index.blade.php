@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-question-circle"></i>
                    FAQs
                </h1>
                <p class="page-header__subtitle">Manage frequently asked questions</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add FAQ</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All FAQs
                </h3>
                <p class="modern-card__subtitle">{{ $faqs->total() }} total FAQs</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form" id="search-form">
                    <div class="search-form__wrapper">
                        @if(count($categories) > 0)
                        <div style="margin-right: 10px;">
                            <select name="category" id="category-filter" class="form-control form-control-sm select2-category" style="width: 150px;">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $cat)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="search-form__input-wrapper">
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input"
                                   placeholder="Search FAQs..."
                                   value="{{ $search }}"
                                   autocomplete="off">
                            <button type="button" id="search-button" class="search-form__button">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="#" id="clear-search" class="search-form__clear" style="display: {{ $search ? 'flex' : 'none' }};">
                                <i class="fas fa-times"></i>
                            </a>
                            <div id="search-loading" class="search-form__loading" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            <div id="results-container">
                @include('admin.faq.partials.table', ['faqs' => $faqs])
            </div>

            <!-- Pagination Container -->
            <div id="pagination-container">
                @if($faqs->total() > 0 && $faqs->hasPages())
                    <div class="pagination-wrapper">
                        {{ $faqs->links('components.pagination') }}
                    </div>
                @endif
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
<script src="{{ asset('assets/js/admin-search.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for category dropdown
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        $('#category-filter').select2({
            theme: 'bootstrap-5',
            placeholder: 'All Categories',
            allowClear: true,
            width: '150px'
        });
    } else {
        setTimeout(function() {
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                $('#category-filter').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'All Categories',
                    allowClear: true,
                    width: '150px'
                });
            }
        }, 500);
    }

    // Initialize AJAX search
    if (typeof AdminSearch !== 'undefined') {
        AdminSearch.init({
            searchInput: '#search-input',
            searchForm: '#search-form',
            searchButton: '#search-button',
            clearButton: '#clear-search',
            resultsContainer: '#results-container',
            paginationContainer: '#pagination-container',
            loadingIndicator: '#search-loading',
            searchUrl: '{{ route('admin.faqs.index') }}',
            debounceDelay: 300
        });
    }

    // Handle category filter change with AJAX (using jQuery for Select2)
    function performFilter() {
        const form = document.getElementById('search-form');
        if (!form) return;

        const formData = new FormData(form);
        formData.append('ajax', '1');

        const searchLoading = document.getElementById('search-loading');
        const resultsContainer = document.getElementById('results-container');
        const paginationContainer = document.getElementById('pagination-container');

        if (searchLoading) searchLoading.style.display = 'flex';

        fetch('{{ route('admin.faqs.index') }}?' + new URLSearchParams(formData), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (searchLoading) searchLoading.style.display = 'none';
            if (data.success && data.html) {
                resultsContainer.innerHTML = data.html;
                paginationContainer.innerHTML = data.pagination || '';
            }
        })
        .catch(error => {
            if (searchLoading) searchLoading.style.display = 'none';
            console.error('Error:', error);
        });
    }

    // Setup category filter change handler
    if (typeof jQuery !== 'undefined') {
        const $categorySelect = $('#category-filter');
        
        // Wait for Select2 to be initialized
        if ($categorySelect.data('select2')) {
            $categorySelect.on('change', function() {
                performFilter();
            });
        } else {
            // Wait for Select2 initialization
            $categorySelect.on('select2:initialized', function() {
                $categorySelect.on('change', function() {
                    performFilter();
                });
            });
            // Fallback: try after a delay
            setTimeout(function() {
                if ($categorySelect.data('select2')) {
                    $categorySelect.on('change', function() {
                        performFilter();
                    });
                }
            }, 1000);
        }
    }

    // AJAX status update
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('status-select') && e.target.hasAttribute('data-faq-id')) {
            e.preventDefault();
            const select = e.target;
            const form = select.closest('form');
            const faqId = select.getAttribute('data-faq-id');
            const status = select.value;
            const originalValue = select.getAttribute('data-original-value') || (select.querySelector('option[selected]')?.value || select.value);

            // Store original value
            if (!select.hasAttribute('data-original-value')) {
                select.setAttribute('data-original-value', originalValue);
            }

            // Disable select during request
            select.disabled = true;

            // Create form data
            const formData = new FormData();
            formData.append('status', status);
            formData.append('_token', form.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
            formData.append('_method', 'PATCH');

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Re-enable select
                select.disabled = false;
                select.setAttribute('data-original-value', status);

                // Show success message if available
                if (typeof showToast === 'function') {
                    showToast('Success', data.message || 'FAQ status updated successfully', 'success', 3000);
                }
            })
            .catch(error => {
                // Revert select value on error
                select.value = originalValue;
                select.disabled = false;

                console.error('Error updating status:', error);
                if (typeof showToast === 'function') {
                    showToast('Error', 'Failed to update FAQ status', 'error', 5000);
                } else {
                    alert('Failed to update FAQ status. Please try again.');
                }
            });
        }
    });
});
</script>
@endpush
@endsection
