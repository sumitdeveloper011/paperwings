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
                <form method="GET" class="filter-form" id="search-form">
                    <div class="search-form__wrapper" style="position: relative;">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" id="search-input" class="search-form__input"
                               placeholder="Search by email..." value="{{ $search }}" autocomplete="off">
                        @if($search)
                            <a href="{{ route('admin.subscriptions.index') }}" class="search-form__clear" id="clear-search">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                        <div id="search-loading" class="search-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');
    const searchLoading = document.getElementById('search-loading');

    function performSearch() {
        searchLoading.style.display = 'block';
        
        const formData = new FormData(searchForm);
        const params = new URLSearchParams(formData);
        
        fetch(`{{ route('admin.subscriptions.index') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('subscriptions-table-container').innerHTML = data.table;
            document.getElementById('subscriptions-pagination-container').innerHTML = data.pagination;
            searchLoading.style.display = 'none';
        })
        .catch(error => {
            console.error('Error:', error);
            searchLoading.style.display = 'none';
        });
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    const debouncedSearch = debounce(performSearch, 500);

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            debouncedSearch();
        });
    }

    // Handle pagination clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('.pagination a').href;
            const urlObj = new URL(url);
            const params = urlObj.searchParams;
            
            // Add current search to pagination
            if (searchInput && searchInput.value) {
                params.set('search', searchInput.value);
            }
            
            fetch(`${urlObj.pathname}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('subscriptions-table-container').innerHTML = data.table;
                document.getElementById('subscriptions-pagination-container').innerHTML = data.pagination;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
});
</script>
@endsection

