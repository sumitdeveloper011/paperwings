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
                <form method="GET" class="search-form" id="search-form">
                    <div class="search-form__wrapper">
                        <div class="search-form__input-wrapper">
                            <select name="status" id="status-filter" class="search-form__input" style="width: 150px; margin-right: 0.5rem;">
                                <option value="">All Status</option>
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <select name="payment_status" id="payment-status-filter" class="search-form__input" style="width: 160px; margin-right: 0.5rem;">
                                <option value="">All Payment Status</option>
                                <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="pending" {{ $paymentStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ $paymentStatus === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $paymentStatus === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   class="search-form__input"
                                   placeholder="Search by order #, name, email..."
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

            <div id="orders-table-container">
                @include('admin.order.partials.table')
            </div>

            <div id="orders-pagination-container">
                @include('admin.order.partials.pagination')
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
        resultsContainer: '#orders-table-container',
        paginationContainer: '#orders-pagination-container',
        loadingIndicator: '#search-loading',
        searchUrl: '{{ route('admin.orders.index') }}',
        debounceDelay: 300,
        additionalParams: function() {
            const status = document.getElementById('status-filter')?.value || '';
            const paymentStatus = document.getElementById('payment-status-filter')?.value || '';
            const params = {};
            if (status) params.status = status;
            if (paymentStatus) params.payment_status = paymentStatus;
            return params;
        }
    });

    // Handle filter changes
    const statusFilter = document.getElementById('status-filter');
    const paymentStatusFilter = document.getElementById('payment-status-filter');

    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            AdminSearch.performSearch();
        });
    }

    if (paymentStatusFilter) {
        paymentStatusFilter.addEventListener('change', function() {
            AdminSearch.performSearch();
        });
    }

    // Intercept pagination links on initial load
    AdminSearch.interceptPaginationLinks();
});
</script>
@endpush
@endsection

