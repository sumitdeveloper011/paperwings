@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-star"></i>
                    Product Reviews
                </h1>
                <p class="page-header__subtitle">Manage customer product reviews</p>
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
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Reviews
                </h3>
                <p class="modern-card__subtitle">{{ $reviews->total() }} total reviews</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="filter-form" id="search-form">
                    <div class="search-form__wrapper">
                        <div class="search-form__input-wrapper">
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input"
                                   placeholder="Search reviews..."
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
                    <select name="status" id="status-filter" class="filter-select">
                        <option value="">All Status</option>
                        <option value="0" {{ $status === '0' ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ $status === '1' ? 'selected' : '' }}>Approved</option>
                        <option value="2" {{ $status === '2' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            <div id="results-container">
                @include('admin.review.partials.table', ['reviews' => $reviews])
            </div>

            <!-- Pagination Container -->
            <div id="pagination-container">
                @if($reviews->total() > 0 && $reviews->hasPages())
                    <div class="pagination-wrapper">
                        {{ $reviews->links('components.pagination') }}
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
            searchUrl: '{{ route('admin.reviews.index') }}',
            debounceDelay: 300,
            additionalParams: function() {
                return {
                    status: document.getElementById('status-filter').value
                };
            }
        });
    }

    // AJAX status filter
    const statusFilter = document.getElementById('status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            if (typeof AdminSearch !== 'undefined' && AdminSearch.currentRequest) {
                AdminSearch.currentRequest.abort();
            }
            
            const searchValue = document.getElementById('search-input').value;
            const statusValue = this.value;
            
            const url = new URL('{{ route('admin.reviews.index') }}');
            if (searchValue) url.searchParams.append('search', searchValue);
            if (statusValue) url.searchParams.append('status', statusValue);
            url.searchParams.append('ajax', '1');
            
            document.getElementById('search-loading').style.display = 'flex';
            
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
                    document.getElementById('results-container').innerHTML = data.html;
                    document.getElementById('pagination-container').innerHTML = data.pagination || '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                document.getElementById('search-loading').style.display = 'none';
            });
        });
    }

    // AJAX status update
    $(document).on('change', '.ajax-status-form .status-select', function() {
        const form = $(this).closest('form');
        const formData = new FormData(form[0]);
        const uuid = form.data('review-uuid');
        
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

<style>
.filter-form {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.filter-select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.875rem;
    min-width: 150px;
    height: 38px;
}

@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
        width: 100%;
    }
    
    .filter-form .search-form__wrapper,
    .filter-form .filter-select {
        width: 100%;
    }
}
</style>
@endsection
