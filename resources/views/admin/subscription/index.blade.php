@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-envelope"></i>
                    Subscriptions
                </h1>
                <p class="page-header__subtitle">Manage newsletter subscriptions</p>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Subscriptions
                </h3>
                <p class="modern-card__subtitle">{{ $subscriptions->total() }} total subscriptions</p>
            </div>
            <div class="modern-card__header-actions">
                <a href="{{ route('admin.subscriptions.create-newsletter') }}" class="btn btn-primary btn-icon" style="margin-right: 1rem;">
                    <i class="fas fa-paper-plane"></i>
                    <span>Send Newsletter</span>
                </a>
                <form method="GET" class="search-form" id="search-form">
                    <div class="search-form__wrapper">
                        <div class="search-form__input-wrapper">
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input"
                                   placeholder="Search by email..."
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
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div id="subscriptions-table-container">
                @include('admin.subscription.partials.table')
            </div>

            <div id="subscriptions-pagination-container">
                @include('admin.subscription.partials.pagination')
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
        resultsContainer: '#subscriptions-table-container',
        paginationContainer: '#subscriptions-pagination-container',
        loadingIndicator: '#search-loading',
        searchUrl: '{{ route('admin.subscriptions.index') }}',
        debounceDelay: 300
    });

    // Intercept pagination links on initial load
    AdminSearch.interceptPaginationLinks();
});
</script>
@endpush
@endsection

