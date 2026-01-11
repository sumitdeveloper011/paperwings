@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-chart-line"></i>
                    Analytics Dashboard
                </h1>
                <p class="page-header__subtitle">Track your website performance</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4 g-3">
        <div class="col-lg-3 col-md-6">
            <div class="modern-card modern-card--stat" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                <div class="modern-card__body" style="padding: 1.5rem;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label" style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Product Views</div>
                            <div class="stat-value" style="font-size: 2rem; font-weight: 700; line-height: 1.2;">{{ number_format($totalViews) }}</div>
                        </div>
                        <div class="stat-icon" style="font-size: 3rem; opacity: 0.3;">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.2);">
                        <small style="opacity: 0.9;">
                            <i class="fas fa-info-circle"></i>
                            Total number of product page views
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="modern-card modern-card--stat" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none;">
                <div class="modern-card__body" style="padding: 1.5rem;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label" style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Active Products</div>
                            <div class="stat-value" style="font-size: 2rem; font-weight: 700; line-height: 1.2;">{{ number_format($totalProducts) }}</div>
                        </div>
                        <div class="stat-icon" style="font-size: 3rem; opacity: 0.3;">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.2);">
                        <small style="opacity: 0.9;">
                            <i class="fas fa-info-circle"></i>
                            Currently active products in catalog
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="modern-card modern-card--stat" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border: none;">
                <div class="modern-card__body" style="padding: 1.5rem;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label" style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Orders</div>
                            <div class="stat-value" style="font-size: 2rem; font-weight: 700; line-height: 1.2;">{{ number_format($totalOrders) }}</div>
                        </div>
                        <div class="stat-icon" style="font-size: 3rem; opacity: 0.3;">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.2);">
                        <small style="opacity: 0.9;">
                            <i class="fas fa-info-circle"></i>
                            All orders (excluding cancelled)
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="modern-card modern-card--stat" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; border: none;">
                <div class="modern-card__body" style="padding: 1.5rem;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label" style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Revenue</div>
                            <div class="stat-value" style="font-size: 2rem; font-weight: 700; line-height: 1.2;">${{ number_format($totalRevenue, 2) }}</div>
                        </div>
                        <div class="stat-icon" style="font-size: 3rem; opacity: 0.3;">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.2);">
                        <small style="opacity: 0.9;">
                            <i class="fas fa-info-circle"></i>
                            Revenue from paid orders only
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Most Viewed Products -->
        <div class="col-lg-6">
            <div class="modern-card">
                <div class="modern-card__header">
                    <div class="modern-card__header-content">
                        <h3 class="modern-card__title">
                            <i class="fas fa-fire" style="color: #ff6b6b;"></i>
                            Most Viewed Products
                        </h3>
                        <p class="modern-card__subtitle">Top 10 products by view count</p>
                    </div>
                    <div class="modern-card__header-actions">
                        <a href="{{ route('admin.analytics.productViews') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt"></i>
                            View Full Report
                        </a>
                    </div>
                </div>
                <div class="modern-card__body">
                    @if($mostViewed->count() > 0)
                        <div class="modern-table-wrapper">
                            <table class="modern-table">
                                <thead class="modern-table__head">
                                    <tr>
                                        <th class="modern-table__th" style="width: 50px;">#</th>
                                        <th class="modern-table__th">Product Name</th>
                                        <th class="modern-table__th text-end">Views</th>
                                    </tr>
                                </thead>
                                <tbody class="modern-table__body">
                                    @foreach($mostViewed as $index => $view)
                                        <tr class="modern-table__row">
                                            <td class="modern-table__td">
                                                <span class="badge bg-light text-dark" style="font-weight: 600;">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="modern-table__td">
                                                <strong>{{ $view->product->name ?? 'N/A' }}</strong>
                                            </td>
                                            <td class="modern-table__td text-end">
                                                <span class="badge bg-primary" style="font-size: 0.875rem; padding: 0.5rem 0.75rem;">{{ number_format($view->views) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state__icon">
                                <i class="fas fa-eye-slash"></i>
                            </div>
                            <h3 class="empty-state__title">No Views Recorded</h3>
                            <p class="empty-state__text">Product views will appear here once customers start browsing your products.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Views -->
        <div class="col-lg-6">
            <div class="modern-card">
                <div class="modern-card__header">
                    <div class="modern-card__header-content">
                        <h3 class="modern-card__title">
                            <i class="fas fa-clock" style="color: #4ecdc4;"></i>
                            Recent Views
                        </h3>
                        <p class="modern-card__subtitle">Latest 10 product page views</p>
                    </div>
                </div>
                <div class="modern-card__body">
                    @if($recentViews->count() > 0)
                        <div class="recent-views-list" style="max-height: 500px; overflow-y: auto;">
                            @foreach($recentViews->take(10) as $view)
                                <div class="recent-view-item mb-3 p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #667eea; transition: all 0.2s ease;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div style="flex: 1;">
                                            <strong style="color: #2c3e50; display: block; margin-bottom: 0.25rem;">
                                                <i class="fas fa-box" style="color: #667eea; margin-right: 0.5rem;"></i>
                                                {{ $view->product->name ?? 'N/A' }}
                                            </strong>
                                            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem;">
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i>
                                                    {{ $view->user->name ?? 'Guest' }}
                                                </small>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i>
                                                    {{ $view->viewed_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state__icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3 class="empty-state__title">No Recent Views</h3>
                            <p class="empty-state__text">Recent product views will appear here as customers browse your products.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mt-4 g-3">
        <div class="col-12">
            <div class="modern-card">
                <div class="modern-card__header">
                    <div class="modern-card__header-content">
                        <h3 class="modern-card__title">
                            <i class="fas fa-chart-bar"></i>
                            Analytics Reports
                        </h3>
                        <p class="modern-card__subtitle">Detailed analytics and insights</p>
                    </div>
                </div>
                <div class="modern-card__body">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('admin.analytics.productViews') }}" class="report-card-link" style="display: block; text-decoration: none;">
                                <div class="modern-card modern-card--hover" style="height: 100%; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                    <div class="modern-card__body text-center" style="padding: 2rem;">
                                        <div style="font-size: 3rem; color: #667eea; margin-bottom: 1rem;">
                                            <i class="fas fa-eye"></i>
                                        </div>
                                        <h4 style="color: #2c3e50; margin-bottom: 0.5rem;">Product Views Report</h4>
                                        <p class="text-muted" style="margin-bottom: 1rem;">Detailed view analytics for all products with filtering options</p>
                                        <span class="btn btn-outline-primary">
                                            View Report <i class="fas fa-arrow-right ml-1"></i>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('admin.analytics.conversion') }}" class="report-card-link" style="display: block; text-decoration: none;">
                                <div class="modern-card modern-card--hover" style="height: 100%; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                    <div class="modern-card__body text-center" style="padding: 2rem;">
                                        <div style="font-size: 3rem; color: #f093fb; margin-bottom: 1rem;">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                        <h4 style="color: #2c3e50; margin-bottom: 0.5rem;">Conversion Analytics</h4>
                                        <p class="text-muted" style="margin-bottom: 1rem;">Track product views to cart conversion rates and performance</p>
                                        <span class="btn btn-outline-primary">
                                            View Report <i class="fas fa-arrow-right ml-1"></i>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('admin.analytics.sales') }}" class="report-card-link" style="display: block; text-decoration: none;">
                                <div class="modern-card modern-card--hover" style="height: 100%; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                    <div class="modern-card__body text-center" style="padding: 2rem;">
                                        <div style="font-size: 3rem; color: #4facfe; margin-bottom: 1rem;">
                                            <i class="fas fa-dollar-sign"></i>
                                        </div>
                                        <h4 style="color: #2c3e50; margin-bottom: 0.5rem;">Sales Report</h4>
                                        <p class="text-muted" style="margin-bottom: 1rem;">Comprehensive sales analytics and revenue tracking</p>
                                        <span class="btn btn-outline-primary">
                                            View Report <i class="fas fa-arrow-right ml-1"></i>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<style>
.modern-card--hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-color: #667eea !important;
}

.report-card-link:hover {
    text-decoration: none;
}

.recent-view-item:hover {
    background: #e9ecef !important;
    transform: translateX(5px);
}
</style>
</div>
@endsection

