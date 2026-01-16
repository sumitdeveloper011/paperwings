@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-envelope"></i>
                    Email Templates
                </h1>
                <p class="page-header__subtitle">Manage email templates</p>
            </div>
            <div class="page-header__actions">
                @can('email-templates.create')
                <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Template</span>
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
                    All Templates
                </h3>
                <p class="modern-card__subtitle">{{ $templates->total() }} total templates</p>
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
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input gallery-search-input"
                                   placeholder="Search templates..."
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
            <div id="templates-results-container">
                @include('admin.email-template.partials.table', ['templates' => $templates])
            </div>
            <div id="templates-pagination-container">
                @if($templates->hasPages())
                    <div class="pagination-wrapper">
                        {{ $templates->links('components.pagination') }}
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
        resultsContainer: '#templates-results-container',
        paginationContainer: '#templates-pagination-container',
        searchUrl: '{{ route('admin.email-templates.index') }}',
        debounceDelay: 300
    });
});
</script>
@endpush
@endsection
