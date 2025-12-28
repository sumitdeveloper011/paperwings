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
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="modern-card">
                <div class="modern-card__body text-center">
                    <div class="stat-icon mb-2" style="font-size: 2rem; color: #007bff;">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-value" style="font-size: 2rem; font-weight: bold;">{{ number_format($totalViews) }}</div>
                    <div class="stat-label text-muted">Total Product Views</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="modern-card">
                <div class="modern-card__body text-center">
                    <div class="stat-icon mb-2" style="font-size: 2rem; color: #28a745;">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-value" style="font-size: 2rem; font-weight: bold;">{{ number_format($totalProducts) }}</div>
                    <div class="stat-label text-muted">Active Products</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="modern-card">
                <div class="modern-card__body text-center">
                    <div class="stat-icon mb-2" style="font-size: 2rem; color: #ffc107;">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-value" style="font-size: 2rem; font-weight: bold;">{{ number_format($totalOrders) }}</div>
                    <div class="stat-label text-muted">Total Orders</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="modern-card">
                <div class="modern-card__body text-center">
                    <div class="stat-icon mb-2" style="font-size: 2rem; color: #dc3545;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-value" style="font-size: 2rem; font-weight: bold;">${{ number_format($totalRevenue, 2) }}</div>
                    <div class="stat-label text-muted">Total Revenue</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Most Viewed Products -->
        <div class="col-lg-6">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-fire"></i>
                        Most Viewed Products
                    </h3>
                </div>
                <div class="modern-card__body">
                    @if($mostViewed->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Views</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mostViewed as $view)
                                        <tr>
                                            <td>{{ $view->product->name ?? 'N/A' }}</td>
                                            <td><span class="badge bg-primary">{{ $view->views }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No views recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Views -->
        <div class="col-lg-6">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-clock"></i>
                        Recent Views
                    </h3>
                </div>
                <div class="modern-card__body">
                    @if($recentViews->count() > 0)
                        <div class="recent-views-list">
                            @foreach($recentViews->take(10) as $view)
                                <div class="recent-view-item mb-2 p-2" style="background: #f8f9fa; border-radius: 5px;">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $view->product->name ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $view->user->name ?? 'Guest' }} - {{ $view->viewed_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No recent views.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Analytics Reports</h3>
                </div>
                <div class="modern-card__body">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('admin.analytics.productViews') }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-eye"></i> Product Views Report
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.analytics.conversion') }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-chart-bar"></i> Conversion Analytics
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.analytics.sales') }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-dollar-sign"></i> Sales Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

