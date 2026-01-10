@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-users"></i>
                    Users
                </h1>
                <p class="page-header__subtitle">Manage users and assign roles</p>
            </div>
            <div class="page-header__actions">
                @can('users.create')
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add User</span>
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
                    All Users
                </h3>
                <p class="modern-card__subtitle">{{ $users->total() }} total users</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="filter-form" id="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" id="search-input" class="search-form__input"
                               placeholder="Search users..." value="{{ $search }}" autocomplete="off">
                        @if($search)
                            <a href="{{ route('admin.users.index') }}" class="search-form__clear" id="clear-search">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                        <div id="search-loading" class="search-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </div>
                    <select name="role" id="role-filter" class="filter-select">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $roleFilter === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" id="status-filter" class="filter-select">
                        <option value="">All Status</option>
                        <option value="1" {{ $status === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $status === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
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

            <div id="users-table-container">
                @include('admin.user.partials.table')
            </div>

            <div id="users-pagination-container">
                @include('admin.user.partials.pagination')
            </div>
        </div>
    </div>
</div>

<style>
.user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

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
    background-color: white;
    cursor: pointer;
}

.filter-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

.status-form {
    display: inline-block;
}

.status-select {
    padding: 0.375rem 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.875rem;
    cursor: pointer;
    background-color: white;
    min-width: 100px;
    transition: all 0.2s ease;
}

.status-select:hover {
    border-color: #667eea;
}

.status-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
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

.search-loading {
    position: absolute;
    right: 40px;
    top: 50%;
    transform: translateY(-50%);
    color: #667eea;
}

.search-form__wrapper {
    position: relative;
}
</style>

<script>
(function() {
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');
    const statusSelect = document.getElementById('status-filter');
    const tableContainer = document.getElementById('users-table-container');
    const paginationContainer = document.getElementById('users-pagination-container');
    const searchLoading = document.getElementById('search-loading');
    const clearSearch = document.getElementById('clear-search');

    if (!searchInput || !tableContainer) return;

    let searchTimeout;
    let isSearching = false;

    // Debounced search function
    function performSearch() {
        if (isSearching) return;

        const searchTerm = searchInput.value.trim();
        const status = statusSelect ? statusSelect.value : '';
        const role = roleSelect ? roleSelect.value : '';

        // Show loading indicator
        if (searchLoading) searchLoading.style.display = 'block';
        isSearching = true;

        // Build URL with current parameters
        const url = new URL('{{ route("admin.users.index") }}', window.location.origin);
        if (searchTerm) url.searchParams.set('search', searchTerm);
        if (status) url.searchParams.set('status', status);
        if (role) url.searchParams.set('role', role);
        url.searchParams.set('ajax', '1');

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            tableContainer.innerHTML = data.html;
            paginationContainer.innerHTML = data.pagination;
            if (searchLoading) searchLoading.style.display = 'none';
            isSearching = false;

            // Update URL without page reload
            const newUrl = url.toString().replace('&ajax=1', '').replace('?ajax=1', '');
            window.history.pushState({}, '', newUrl);
        })
        .catch(error => {
            console.error('Search error:', error);
            if (searchLoading) searchLoading.style.display = 'none';
            isSearching = false;
        });
    }

    // Debounce search input
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 300); // 300ms debounce
    });

    // Handle status filter change
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            clearTimeout(searchTimeout);
            performSearch();
        });
    }

    // Handle role filter change
    const roleSelect = document.getElementById('role-filter');
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            clearTimeout(searchTimeout);
            performSearch();
        });
    }

    // Clear search
    if (clearSearch) {
        clearSearch.addEventListener('click', function(e) {
        e.preventDefault();
        searchInput.value = '';
        if (statusSelect) statusSelect.value = '';
        if (roleSelect) roleSelect.value = '';
        clearTimeout(searchTimeout);
        performSearch();
    });
    }

    // Prevent form submission on Enter (use AJAX instead)
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearTimeout(searchTimeout);
            performSearch();
        });
    }
})();
</script>
@endsection

