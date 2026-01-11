@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-user-shield"></i>
                    Admin Users
                </h1>
                <p class="page-header__subtitle">Manage admin users (SuperAdmin, Admin, Manager, Editor)</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.admin-users.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Admin User</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Admin Users
                </h3>
                <p class="modern-card__subtitle">{{ $users->total() }} total admin users</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="filter-form" id="search-form">
                    <div class="search-form__wrapper">
                        <div class="search-form__input-wrapper">
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input"
                                   placeholder="Search admin users..."
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
                @include('admin.admin-user.partials.table')
            </div>

            <div id="users-pagination-container">
                @include('admin.admin-user.partials.pagination')
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
            searchUrl: '{{ route('admin.admin-users.index') }}',
            debounceDelay: 300,
            additionalParams: function() {
                return {
                    role: document.getElementById('role-filter').value,
                    status: document.getElementById('status-filter').value
                };
            }
        });
    }

    // AJAX role filter
    const roleFilter = document.getElementById('role-filter');
    if (roleFilter) {
        roleFilter.addEventListener('change', function() {
            if (typeof AdminSearch !== 'undefined' && AdminSearch.currentRequest) {
                AdminSearch.currentRequest.abort();
            }
            const searchValue = document.getElementById('search-input').value;
            const roleValue = this.value;
            const statusValue = document.getElementById('status-filter').value;

            const url = new URL('{{ route('admin.admin-users.index') }}');
            if (searchValue) url.searchParams.append('search', searchValue);
            if (roleValue) url.searchParams.append('role', roleValue);
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

    // AJAX status filter
    const statusFilter = document.getElementById('status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            if (typeof AdminSearch !== 'undefined' && AdminSearch.currentRequest) {
                AdminSearch.currentRequest.abort();
            }
            const searchValue = document.getElementById('search-input').value;
            const statusValue = this.value;
            const roleValue = document.getElementById('role-filter').value;

            const url = new URL('{{ route('admin.admin-users.index') }}');
            if (searchValue) url.searchParams.append('search', searchValue);
            if (roleValue) url.searchParams.append('role', roleValue);
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
        const uuid = form.data('user-uuid');

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
            } else if (data.message) {
                if (typeof window.showToast === 'function') {
                    window.showToast('error', data.message);
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
@endsection
