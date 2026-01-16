@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-images"></i>
                    Galleries
                </h1>
                <p class="page-header__subtitle">Manage image and video galleries</p>
            </div>
            <div class="page-header__actions">
                @can('galleries.create')
                <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Gallery</span>
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Galleries
                </h3>
                <p class="modern-card__subtitle">{{ $galleries->total() }} total galleries</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form" id="search-form">
                    <div class="search-form__wrapper">
                        <div class="search-form__input-wrapper">
                            <select name="category" class="search-form__input gallery-search-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ $category === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <select name="status" class="search-form__input gallery-status-select">
                                <option value="">All Status</option>
                                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input gallery-search-input"
                                   placeholder="Search galleries..."
                                   value="{{ $search }}"
                                   autocomplete="off">
                            <button type="button" id="search-button" class="search-form__button">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="#" id="clear-search" class="search-form__clear @if(!$search) d-none @endif">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            <div id="galleries-results-container">
                @include('admin.gallery.partials.table', ['galleries' => $galleries])
            </div>
            <div id="galleries-pagination-container">
                @if($galleries->hasPages())
                    <div class="pagination-wrapper">
                        {{ $galleries->links('components.pagination') }}
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
    AdminSearch.init({
        searchInput: '#search-input',
        searchForm: '#search-form',
        searchButton: '#search-button',
        clearButton: '#clear-search',
        resultsContainer: '#galleries-results-container',
        paginationContainer: '#galleries-pagination-container',
        searchUrl: '{{ route('admin.galleries.index') }}',
        debounceDelay: 300
    });
});
</script>
@endpush
@endsection
