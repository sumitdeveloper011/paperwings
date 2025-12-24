@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-chart-line"></i>
                    Dashboard
                </h1>
                <p class="page-header__subtitle">Overview of your e-commerce platform</p>
            </div>
        </div>
    </div>

    @php
        $gaSettings = \App\Models\Setting::whereIn('key', ['google_analytics_id', 'google_analytics_enabled', 'google_analytics_ecommerce'])
            ->pluck('value', 'key')
            ->toArray();
        $gaId = $gaSettings['google_analytics_id'] ?? '';
        $gaEnabled = isset($gaSettings['google_analytics_enabled']) && $gaSettings['google_analytics_enabled'] == '1';
    @endphp

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-card__icon stat-card__icon--primary">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-card__content">
                    <h3 class="stat-card__value">{{ number_format($stats['total_products']) }}</h3>
                    <p class="stat-card__label">Total Products</p>
                    <small class="stat-card__subtext">{{ $stats['active_products'] }} Active</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-card__icon stat-card__icon--info">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-card__content">
                    <h3 class="stat-card__value">{{ number_format($stats['total_orders']) }}</h3>
                    <p class="stat-card__label">Total Orders</p>
                    <small class="stat-card__subtext">{{ $stats['pending_orders'] }} Pending</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-card__icon stat-card__icon--success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-card__content">
                    <h3 class="stat-card__value">{{ number_format($stats['total_users']) }}</h3>
                    <p class="stat-card__label">Total Users</p>
                    <small class="stat-card__subtext">{{ $stats['active_users'] }} Active</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-card__icon stat-card__icon--warning">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-card__content">
                    <h3 class="stat-card__value">${{ number_format($stats['total_revenue'], 2) }}</h3>
                    <p class="stat-card__label">Total Revenue</p>
                    <small class="stat-card__subtext">{{ $stats['total_subscriptions'] }} Subscribers</small>
                </div>
            </div>
        </div>
    </div>

    @php
        $gaSettings = \App\Models\Setting::whereIn('key', ['google_analytics_id', 'google_analytics_enabled', 'google_analytics_ecommerce'])
            ->pluck('value', 'key')
            ->toArray();
        $gaId = $gaSettings['google_analytics_id'] ?? '';
        $gaEnabled = isset($gaSettings['google_analytics_enabled']) && $gaSettings['google_analytics_enabled'] == '1';
    @endphp

    @if($gaEnabled && !empty($gaId))
    <!-- Google Analytics Quick Access -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <div class="modern-card__header">
                    <div class="modern-card__header-content">
                        <h3 class="modern-card__title">
                            <i class="fab fa-google"></i>
                            Google Analytics
                        </h3>
                        <p class="modern-card__subtitle">Quick access to your analytics dashboard</p>
                    </div>
                    <div class="modern-card__header-actions">
                        <a href="https://analytics.google.com" target="_blank" class="btn btn-primary">
                            <i class="fas fa-external-link-alt"></i>
                            Open Analytics Dashboard
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-cog"></i>
                            Settings
                        </a>
                    </div>
                </div>
                <div class="modern-card__body">
                    <div class="ga-quick-info">
                        <div class="ga-quick-info__item">
                            <i class="fas fa-fingerprint"></i>
                            <div>
                                <strong>Measurement ID:</strong>
                                <code>{{ $gaId }}</code>
                            </div>
                        </div>
                        <div class="ga-quick-info__item">
                            <i class="fas fa-chart-line"></i>
                            <div>
                                <strong>Status:</strong>
                                <span class="status-badge status-badge--success">Active</span>
                            </div>
                        </div>
                        @if(isset($gaSettings['google_analytics_ecommerce']) && $gaSettings['google_analytics_ecommerce'] == '1')
                        <div class="ga-quick-info__item">
                            <i class="fas fa-shopping-cart"></i>
                            <div>
                                <strong>E-commerce:</strong>
                                <span class="status-badge status-badge--success">Enabled</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Growth Chart Section -->
    <div class="modern-card mb-4">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-chart-area"></i>
                    Monthly Growth Analysis
                </h3>
                <p class="modern-card__subtitle">Track your business growth over time</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="dashboard-filter-form" id="date-filter-form">
                    <div class="date-filter-group">
                        <label for="start_date" class="date-filter-label">From:</label>
                        <input type="date" name="start_date" id="start_date" class="date-filter-input"
                               value="{{ $startDate }}" required>
                    </div>
                    <div class="date-filter-group">
                        <label for="end_date" class="date-filter-label">To:</label>
                        <input type="date" name="end_date" id="end_date" class="date-filter-input"
                               value="{{ $endDate }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filter
                    </button>
                    <button type="button" class="btn btn-secondary" id="reset-date-filter">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </form>
            </div>
        </div>
        <div class="modern-card__body">
            <div class="chart-container">
                <canvas id="growthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Revenue Chart Section -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="modern-card">
                <div class="modern-card__header">
                    <div class="modern-card__header-content">
                        <h3 class="modern-card__title">
                            <i class="fas fa-chart-bar"></i>
                            Revenue Overview
                        </h3>
                        <p class="modern-card__subtitle">Monthly revenue breakdown</p>
                    </div>
                </div>
                <div class="modern-card__body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="modern-card">
                <div class="modern-card__header">
                    <div class="modern-card__header-content">
                        <h3 class="modern-card__title">
                            <i class="fas fa-tasks"></i>
                            Order Status
                        </h3>
                        <p class="modern-card__subtitle">Current order distribution</p>
                    </div>
                </div>
                <div class="modern-card__body">
                    <div class="order-status-list">
                        @php
                            $statusLabels = [
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled'
                            ];
                            $statusColors = [
                                'pending' => 'warning',
                                'processing' => 'info',
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger'
                            ];
                        @endphp
                        @foreach($orderStatusBreakdown as $status => $count)
                            <div class="order-status-item">
                                <div class="order-status-item__label">
                                    <span class="status-badge status-badge--{{ $statusColors[$status] ?? 'secondary' }}">
                                        {{ $statusLabels[$status] ?? ucfirst($status) }}
                                    </span>
                                </div>
                                <div class="order-status-item__value">
                                    {{ number_format($count) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Top Products -->
    <div class="row">
        <div class="col-lg-7 mb-3">
            <div class="modern-card">
                <div class="modern-card__header">
                    <div class="modern-card__header-content">
                        <h3 class="modern-card__title">
                            <i class="fas fa-clock"></i>
                            Recent Orders
                        </h3>
                        <p class="modern-card__subtitle">Latest 5 orders</p>
                    </div>
                    <div class="modern-card__header-actions">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="modern-card__body">
                    @if($recentOrders->count() > 0)
                        <div class="recent-orders-list">
                            @foreach($recentOrders as $order)
                                <div class="recent-order-item">
                                    <div class="recent-order-item__info">
                                        <div class="recent-order-item__header">
                                            <strong>#{{ $order->order_number }}</strong>
                                            <span class="status-badge status-badge--{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                        <div class="recent-order-item__details">
                                            <span><i class="fas fa-user"></i> {{ $order->user->name ?? 'Guest' }}</span>
                                            <span><i class="fas fa-dollar-sign"></i> ${{ number_format($order->total, 2) }}</span>
                                            <span><i class="fas fa-calendar"></i> {{ $order->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="recent-order-item__action">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart fa-3x"></i>
                            <h3>No orders yet</h3>
                            <p>Orders will appear here once customers start placing orders.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-3">
            <div class="modern-card">
                <div class="modern-card__header">
                    <div class="modern-card__header-content">
                        <h3 class="modern-card__title">
                            <i class="fas fa-star"></i>
                            Top Selling Products
                        </h3>
                        <p class="modern-card__subtitle">Best performing products</p>
                    </div>
                </div>
                <div class="modern-card__body">
                    @if($topProducts->count() > 0)
                        <div class="top-products-list">
                            @foreach($topProducts as $index => $product)
                                <div class="top-product-item">
                                    <div class="top-product-item__rank">
                                        <span class="rank-badge rank-badge--{{ $index < 3 ? 'top' : 'normal' }}">
                                            #{{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div class="top-product-item__info">
                                        <div class="top-product-item__name">{{ $product->name }}</div>
                                        <div class="top-product-item__stats">
                                            <span><i class="fas fa-shopping-bag"></i> {{ $product->total_sold }} sold</span>
                                            <span><i class="fas fa-dollar-sign"></i> ${{ number_format($product->total_revenue, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-box fa-3x"></i>
                            <h3>No sales data</h3>
                            <p>Product sales data will appear here once orders are placed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Growth Chart Data
    const growthData = @json($growthData);
    const revenueByMonth = @json($revenueByMonth);

    // Prepare chart data
    const months = growthData.map(item => item.month);
    const ordersData = growthData.map(item => item.orders);
    const usersData = growthData.map(item => item.users);

    // Growth Chart
    const growthCtx = document.getElementById('growthChart');
    if (growthCtx) {
        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Orders',
                        data: ordersData,
                        borderColor: 'rgb(158, 189, 213)',
                        backgroundColor: 'rgba(158, 189, 213, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Users',
                        data: usersData,
                        borderColor: 'rgb(40, 167, 69)',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueMonths = Object.keys(revenueByMonth).sort();
        const revenueValues = revenueMonths.map(month => revenueByMonth[month] || 0);
        const formattedMonths = revenueMonths.map(month => {
            const date = new Date(month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        });

        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: formattedMonths,
                datasets: [{
                    label: 'Revenue ($)',
                    data: revenueValues,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgb(255, 99, 132)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Reset date filter
    document.getElementById('reset-date-filter')?.addEventListener('click', function() {
        const today = new Date();
        const sixMonthsAgo = new Date();
        sixMonthsAgo.setMonth(today.getMonth() - 6);

        document.getElementById('start_date').value = sixMonthsAgo.toISOString().split('T')[0];
        document.getElementById('end_date').value = today.toISOString().split('T')[0];
        document.getElementById('date-filter-form').submit();
    });
});
</script>
@endsection
