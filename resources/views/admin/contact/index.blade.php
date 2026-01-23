@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-envelope"></i>
                    Contact Messages
                </h1>
                <p class="page-header__subtitle">Manage customer inquiries and messages</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Messages
                </h3>
                <p class="modern-card__subtitle">{{ $messages->total() }} total messages</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form" id="search-form">
                    <div class="search-form__wrapper">
                        <div class="search-form__input-wrapper">
                            <select name="status" id="status-filter" class="search-form__input" style="width: 150px; margin-right: 0.5rem;">
                                <option value="">All Status</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="solved" {{ $status == 'solved' ? 'selected' : '' }}>Solved</option>
                                <option value="closed" {{ $status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input"
                                   placeholder="Search messages..."
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
                @include('admin.contact.partials.table', ['messages' => $messages])
            </div>

            <!-- Pagination Container -->
            <div id="pagination-container">
                @if($messages->total() > 0 && $messages->hasPages())
                    <div class="pagination-wrapper">
                        {{ $messages->appends(request()->query())->links('components.pagination') }}
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
            searchUrl: '{{ route('admin.contacts.index') }}',
            debounceDelay: 300,
            additionalParams: function() {
                return {
                    status: document.getElementById('status-filter').value
                };
            }
        });
    }

    // Handle status filter change
    const statusFilter = document.getElementById('status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            AdminSearch.performSearch();
        });
    }

    // Intercept pagination links on initial load
    AdminSearch.interceptPaginationLinks();
});
</script>
@endpush
@endsection

