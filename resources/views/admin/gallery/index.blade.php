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
                            <select name="category" id="category-filter" class="search-form__input gallery-search-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ $category === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <select name="status" id="status-filter" class="search-form__input gallery-status-select">
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

<style>
.status-select {
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
    font-size: 0.875rem;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 120px;
}

.status-select:hover {
    border-color: var(--primary-color);
}

.status-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(62, 47, 47, 0.1);
}

.status-select:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.modern-table__th:nth-child(2),
.modern-table__td:nth-child(2) {
    min-width: 120px;
    white-space: normal;
}

.modern-table__th:nth-child(3),
.modern-table__td:nth-child(3) {
    min-width: 100px;
    white-space: normal;
}

.modern-table__th:nth-child(4),
.modern-table__td:nth-child(4) {
    min-width: 140px;
    white-space: normal;
}

.badge {
    display: inline-flex !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.gallery-search-select,
.gallery-status-select {
    min-width: 150px;
    margin-right: 0.5rem;
}
</style>

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
        loadingIndicator: '#search-loading',
        searchUrl: '{{ route('admin.galleries.index') }}',
        debounceDelay: 300
    });

    AdminSearch.interceptPaginationLinks();

    const categoryFilter = document.querySelector('.gallery-search-select');
    const statusFilter = document.querySelector('.gallery-status-select');

    function performFilterSearch() {
        if (typeof AdminSearch !== 'undefined' && AdminSearch.currentRequest) {
            AdminSearch.currentRequest.abort();
        }

        const searchInput = document.getElementById('search-input');
        const searchValue = searchInput ? searchInput.value : '';
        const categoryValue = categoryFilter ? categoryFilter.value : '';
        const statusValue = statusFilter ? statusFilter.value : '';

        const url = new URL('{{ route('admin.galleries.index') }}');
        if (searchValue) url.searchParams.append('search', searchValue);
        if (categoryValue) url.searchParams.append('category', categoryValue);
        if (statusValue) url.searchParams.append('status', statusValue);
        url.searchParams.append('ajax', '1');

        const searchLoading = document.getElementById('search-loading');
        if (searchLoading) {
            searchLoading.style.display = 'flex';
        }

        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const resultsContainer = document.getElementById('galleries-results-container');
                const paginationContainer = document.getElementById('galleries-pagination-container');
                
                if (resultsContainer) {
                    resultsContainer.innerHTML = data.html;
                }
                
                if (paginationContainer) {
                    paginationContainer.innerHTML = data.pagination || '';
                }

                AdminSearch.interceptPaginationLinks();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            const searchLoading = document.getElementById('search-loading');
            if (searchLoading) {
                searchLoading.style.display = 'none';
            }
        });
    }

    if (categoryFilter) {
        categoryFilter.addEventListener('change', performFilterSearch);
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', performFilterSearch);
    }

    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('status-select')) {
            e.preventDefault();
            e.stopPropagation();

            const select = e.target;
            const form = select.closest('.status-form');
            if (!form) return;

            const galleryId = select.getAttribute('data-gallery-id');
            const newStatus = select.value;
            const originalValue = newStatus === 'active' ? 'inactive' : 'active';

            select.disabled = true;
            const originalText = select.options[select.selectedIndex].textContent;
            select.options[select.selectedIndex].textContent = 'Updating...';

            const csrfToken = form.querySelector('input[name="_token"]').value;
            const formAction = form.getAttribute('action');

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
                select.disabled = false;
                select.options[select.selectedIndex].textContent = originalText;

                if (data && data.message) {
                    if (typeof showToast === 'function') {
                        showToast('Success', data.message, 'success', 3000);
                    }
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                select.value = originalValue;
                select.disabled = false;
                select.options[select.selectedIndex].textContent = originalText;
                if (typeof showToast === 'function') {
                    showToast('Error', 'Failed to update gallery status', 'error', 5000);
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
