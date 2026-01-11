@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-trash-restore"></i>
                    Trash
                </h1>
                <p class="page-header__subtitle">Deleted bundles</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.bundles.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Bundles</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-trash"></i>
                    Deleted Bundles
                </h3>
                <p class="modern-card__subtitle">{{ $bundles->total() }} deleted bundles</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form" id="search-form">
                    <div class="search-form__wrapper">
                        <div class="search-form__input-wrapper">
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input"
                                   placeholder="Search deleted bundles..."
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
                @include('admin.bundle.partials.trash-table', ['bundles' => $bundles])
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
        searchUrl: '{{ route('admin.bundles.trash') }}',
        debounceDelay: 300
    });

    // Intercept pagination links
    AdminSearch.interceptPaginationLinks();
});
</script>
@endpush
@endsection
