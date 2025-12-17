@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-shopping-cart"></i>
                    Orders
                </h1>
                <p class="page-header__subtitle">Manage e-commerce orders and transactions</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card__icon stat-card__icon--primary">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-card__content">
                    <h3 class="stat-card__value">{{ $stats['total'] }}</h3>
                    <p class="stat-card__label">Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card__icon stat-card__icon--warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-card__content">
                    <h3 class="stat-card__value">{{ $stats['pending'] }}</h3>
                    <p class="stat-card__label">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card__icon stat-card__icon--info">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="stat-card__content">
                    <h3 class="stat-card__value">{{ $stats['shipped'] }}</h3>
                    <p class="stat-card__label">Shipped</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card__icon stat-card__icon--success">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-card__content">
                    <h3 class="stat-card__value">${{ number_format($stats['total_revenue'], 2) }}</h3>
                    <p class="stat-card__label">Total Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Orders
                </h3>
                <p class="modern-card__subtitle">{{ $orders->total() }} total orders</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="filter-form" id="search-form">
                    <div class="search-form__wrapper" style="position: relative;">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" id="search-input" class="search-form__input"
                               placeholder="Search orders..." value="{{ $search }}" autocomplete="off">
                        @if($search)
                            <a href="{{ route('admin.orders.index') }}" class="search-form__clear" id="clear-search">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                        <div id="search-loading" class="search-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </div>
                    <select name="status" id="status-filter" class="filter-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <select name="payment_status" id="payment-status-filter" class="filter-select">
                        <option value="">All Payment Status</option>
                        <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ $paymentStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ $paymentStatus === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ $paymentStatus === 'refunded' ? 'selected' : '' }}>Refunded</option>
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

            <div id="orders-table-container">
                @include('admin.order.partials.table')
            </div>

            <div id="orders-pagination-container">
                @include('admin.order.partials.pagination')
            </div>
        </div>
    </div>
</div>

<style>
.stat-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card__icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-card__icon--primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-card__icon--warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-card__icon--info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-card__icon--success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.stat-card__value {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
    color: #2c3e50;
}

.stat-card__label {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0;
}

.filter-form {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.filter-select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.875rem;
}

.search-loading {
    position: absolute;
    right: 40px;
    top: 50%;
    transform: translateY(-50%);
    color: #667eea;
}
</style>

<script>
(function() {
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');
    const statusFilter = document.getElementById('status-filter');
    const paymentStatusFilter = document.getElementById('payment-status-filter');
    const tableContainer = document.getElementById('orders-table-container');
    const paginationContainer = document.getElementById('orders-pagination-container');
    const searchLoading = document.getElementById('search-loading');
    const clearSearch = document.getElementById('clear-search');
    
    let searchTimeout;
    let isSearching = false;

    // Debounced search function
    function performSearch() {
        if (isSearching) return;
        
        const searchTerm = searchInput.value.trim();
        const status = statusFilter ? statusFilter.value : '';
        const paymentStatus = paymentStatusFilter ? paymentStatusFilter.value : '';
        
        // Show loading indicator
        searchLoading.style.display = 'block';
        isSearching = true;

        // Build URL with current parameters
        const url = new URL('{{ route("admin.orders.index") }}', window.location.origin);
        if (searchTerm) url.searchParams.set('search', searchTerm);
        if (status) url.searchParams.set('status', status);
        if (paymentStatus) url.searchParams.set('payment_status', paymentStatus);
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
            searchLoading.style.display = 'none';
            isSearching = false;
            
            // Update URL without page reload
            const newUrl = url.toString().replace('&ajax=1', '').replace('?ajax=1', '');
            window.history.pushState({}, '', newUrl);
        })
        .catch(error => {
            console.error('Search error:', error);
            searchLoading.style.display = 'none';
            isSearching = false;
        });
    }

    // Debounce search input
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 300); // 300ms debounce
    });

    // Handle filter changes
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            clearTimeout(searchTimeout);
            performSearch();
        });
    }

    if (paymentStatusFilter) {
        paymentStatusFilter.addEventListener('change', function() {
            clearTimeout(searchTimeout);
            performSearch();
        });
    }

    // Clear search
    if (clearSearch) {
        clearSearch.addEventListener('click', function(e) {
            e.preventDefault();
            searchInput.value = '';
            if (statusFilter) statusFilter.value = '';
            if (paymentStatusFilter) paymentStatusFilter.value = '';
            clearTimeout(searchTimeout);
            performSearch();
        });
    }

    // Prevent form submission on Enter (use AJAX instead)
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        clearTimeout(searchTimeout);
        performSearch();
    });
})();
</script>
@endsection

