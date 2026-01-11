@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-boxes"></i>
                    Product Bundles
                </h1>
                <p class="page-header__subtitle">Manage product bundles</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.bundles.trash') }}" class="btn btn-warning btn-icon" style="background-color: #ffc107; color: #000; border-color: #ffc107;">
                    <i class="fas fa-trash-restore"></i>
                    <span>Trash</span>
                </a>
                <a href="{{ route('admin.bundles.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Bundle</span>
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">All Bundles</h3>
                <p class="modern-card__subtitle">{{ $bundles->total() }} total bundles</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form" id="search-form">
                    <div class="search-form__wrapper">
                        <div class="search-form__input-wrapper">
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input"
                                   placeholder="Search bundles..."
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
            <div id="bundles-results-container">
                @include('admin.bundle.partials.table', ['bundles' => $bundles])
            </div>
            <div id="bundles-pagination-container">
                @if($bundles->hasPages())
                    <div class="pagination-wrapper">
                        {{ $bundles->links('components.pagination') }}
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
    // Initialize Admin Search
    AdminSearch.init({
        searchInput: '#search-input',
        searchForm: '#search-form',
        searchButton: '#search-button',
        clearButton: '#clear-search',
        resultsContainer: '#bundles-results-container',
        paginationContainer: '#bundles-pagination-container',
        loadingIndicator: '#search-loading',
        searchUrl: '{{ route('admin.bundles.index') }}',
        debounceDelay: 300
    });

    // Intercept pagination links on initial load
    AdminSearch.interceptPaginationLinks();

    // Handle status change with AJAX (prevent page freeze)
    // Use event delegation to handle dynamically added elements
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('status-select')) {
            e.preventDefault();
            e.stopPropagation();

            const select = e.target;
            const form = select.closest('.status-form');
            if (!form) return;

            const bundleId = select.getAttribute('data-bundle-id');
            const newStatus = select.value;
            const originalValue = select.value === '1' ? '0' : '1';

            // Disable select during request
            select.disabled = true;
            const originalText = select.options[select.selectedIndex].textContent;
            select.options[select.selectedIndex].textContent = 'Updating...';

            // Get CSRF token
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const formAction = form.getAttribute('action');

            // Send AJAX request
            fetch(formAction, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: new URLSearchParams({
                    '_token': csrfToken,
                    '_method': 'PATCH',
                    'status': newStatus
                })
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
                select.options[select.selectedIndex].textContent = originalText;

                // Show success message if available
                if (data && data.message) {
                    if (typeof showToast === 'function') {
                        showToast('Success', data.message, 'success', 3000);
                    }
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                // Revert to original value on error
                select.value = originalValue;
                select.disabled = false;
                select.options[select.selectedIndex].textContent = originalText;
                if (typeof showToast === 'function') {
                    showToast('Error', 'Failed to update bundle status', 'error', 5000);
                } else {
                    alert('Error updating status. Please try again.');
                }
            });
        }
    });
});
</script>
@endpush
@endsection

