@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-question"></i>
                    Product FAQs
                </h1>
                <p class="page-header__subtitle">Manage frequently asked questions for products</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.product-faqs.create') }}" class="btn btn-primary btn-icon">
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
                    All Product FAQs
                </h3>
                <p class="modern-card__subtitle">{{ $faqs->total() }} total FAQs</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form" id="search-form">
                    <div class="search-form__wrapper">
                        @if(count($categories) > 0)
                        <div style="margin-right: 10px;">
                            @include('components.select-category', [
                                'id' => 'category_id',
                                'name' => 'category_id',
                                'label' => '',
                                'required' => false,
                                'selected' => $categoryId,
                                'categories' => $categories,
                                'useUuid' => false,
                                'placeholder' => 'All Categories',
                                'class' => 'form-control form-control-sm',
                                'useSelect2' => true,
                                'showLabel' => false,
                                'wrapperClass' => '',
                                'select2Width' => '150px',
                            ])
                        </div>
                        @endif
                        @if(count($products) > 0)
                        <div style="margin-right: 10px;">
                            <select name="product_id" id="product_id" class="form-control form-control-sm select2-product" style="width: 200px;">
                                <option value="">All Products</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ isset($productId) && $productId == $product->id ? 'selected' : '' }}>
                                        {{ \Illuminate\Support\Str::limit($product->name, 40) }}
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
                @include('admin.product-faq.partials.table', ['faqs' => $faqs])
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
    // Initialize Select2 for product dropdown
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        $('#product_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'All Products',
            allowClear: true,
            width: '200px'
        });
    } else {
        setTimeout(function() {
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                $('#product_id').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'All Products',
                    allowClear: true,
                    width: '200px'
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
            searchUrl: '{{ route('admin.product-faqs.index') }}',
            debounceDelay: 300
        });
    }

    // Handle category filter change with AJAX
    const categoryFilter = document.getElementById('category_id');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            const form = document.getElementById('search-form');
            const formData = new FormData(form);
            formData.append('ajax', '1');

            const searchLoading = document.getElementById('search-loading');
            const resultsContainer = document.getElementById('results-container');
            const paginationContainer = document.getElementById('pagination-container');

            if (searchLoading) searchLoading.style.display = 'flex';

            fetch('{{ route('admin.product-faqs.index') }}?' + new URLSearchParams(formData), {
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
        });
    }

    // Handle product filter change with AJAX (using jQuery for Select2)
    if (typeof jQuery !== 'undefined') {
        $(document).on('change', '#product_id', function() {
            const form = document.getElementById('search-form');
            const formData = new FormData(form);
            formData.append('ajax', '1');

            const searchLoading = document.getElementById('search-loading');
            const resultsContainer = document.getElementById('results-container');
            const paginationContainer = document.getElementById('pagination-container');

            if (searchLoading) searchLoading.style.display = 'flex';

            fetch('{{ route('admin.product-faqs.index') }}?' + new URLSearchParams(formData), {
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
        });
    }
});
</script>
@endpush
@endsection
