@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-shipping-fast"></i>
                    Shipping Prices
                </h1>
                <p class="page-header__subtitle">Manage shipping prices by region</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.shipping-prices.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Shipping Price</span>
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
                    All Shipping Prices
                </h3>
                <p class="modern-card__subtitle">{{ $shippingPrices->total() }} total shipping prices</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form" id="search-form">
                    <div class="search-form__wrapper">
                        <div class="search-form__input-wrapper">
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input"
                                   placeholder="Search by region..."
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
                @include('admin.shipping-price.partials.table', ['shippingPrices' => $shippingPrices])
            </div>

            <!-- Pagination Container -->
            <div id="pagination-container">
                @if($shippingPrices->total() > 0 && $shippingPrices->hasPages())
                    <div class="pagination-wrapper">
                        {{ $shippingPrices->links('components.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/admin-search.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
            searchUrl: '{{ route('admin.shipping-prices.index') }}',
            debounceDelay: 300
        });
    }

    // AJAX status update
    $(document).on('change', '.ajax-status-form .status-select', function() {
        const form = $(this).closest('form');
        const formData = new FormData(form[0]);
        const uuid = form.data('shipping-price-uuid');

        fetch(form.attr('action'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof window.showToast === 'function') {
                    window.showToast('success', data.message);
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof window.showToast === 'function') {
                window.showToast('error', 'Failed to update status');
            } else {
                alert('Failed to update status');
            }
        });
    });
});
</script>
@endpush
@endsection

