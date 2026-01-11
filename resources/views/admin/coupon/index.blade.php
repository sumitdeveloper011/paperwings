@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-ticket-alt"></i>
                    Coupons
                </h1>
                <p class="page-header__subtitle">Manage discount coupons and promotional codes</p>
            </div>
            <div class="page-header__actions">
                @can('coupons.create')
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Coupon</span>
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Coupons
                </h3>
                <p class="modern-card__subtitle">{{ $coupons->total() }} total coupons</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form" id="search-form">
                    <div class="search-form__wrapper">
                        <div class="search-form__input-wrapper">
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input"
                                   placeholder="Search coupons..."
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
                @include('admin.coupon.partials.table', ['coupons' => $coupons])
            </div>

            <!-- Pagination Container -->
            <div id="pagination-container">
                @if($coupons->total() > 0 && $coupons->hasPages())
                    <div class="pagination-wrapper">
                        {{ $coupons->links('components.pagination') }}
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
            searchUrl: '{{ route('admin.coupons.index') }}',
            debounceDelay: 300
        });
    }

    // AJAX status update
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('status-select') && e.target.hasAttribute('data-coupon-id')) {
            e.preventDefault();
            const select = e.target;
            const form = select.closest('form');
            const couponId = select.getAttribute('data-coupon-id');
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
                    showToast('Success', data.message || 'Coupon status updated successfully', 'success', 3000);
                }
            })
            .catch(error => {
                // Revert select value on error
                select.value = originalValue;
                select.disabled = false;

                console.error('Error updating status:', error);
                if (typeof showToast === 'function') {
                    showToast('Error', 'Failed to update coupon status', 'error', 5000);
                } else {
                    alert('Failed to update coupon status. Please try again.');
                }
            });
        }
    });
});
</script>
@endpush
@endsection

