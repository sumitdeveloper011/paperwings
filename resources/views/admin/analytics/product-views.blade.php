@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-eye"></i>
                    Product Views Report
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="modern-card mb-4">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-filter"></i>
                    Filter Options
                </h3>
                <p class="modern-card__subtitle">Filter product views by product, date range, or both</p>
            </div>
        </div>
        <div class="modern-card__body">
            <form method="GET" class="filter-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="product_id" class="form-label">
                            <i class="fas fa-box"></i>
                            Product
                        </label>
                        <select name="product_id" id="product_id" class="form-select">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">
                            <i class="fas fa-calendar-alt"></i>
                            From Date
                        </label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">
                            <i class="fas fa-calendar-alt"></i>
                            To Date
                        </label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Views Table -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    Product Views
                </h3>
                <p class="modern-card__subtitle">{{ $views->total() }} total views found</p>
            </div>
        </div>
        <div class="modern-card__body">
            @if($views->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Product Name</th>
                                <th class="modern-table__th">User</th>
                                <th class="modern-table__th">IP Address</th>
                                <th class="modern-table__th">Viewed At</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($views as $view)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <strong>{{ $view->product->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td class="modern-table__td">
                                        @if($view->user)
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <i class="fas fa-user-circle" style="color: #667eea;"></i>
                                                <span>{{ $view->user->name }}</span>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Guest</span>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">
                                        <code style="background: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ $view->ip_address }}</code>
                                    </td>
                                    <td class="modern-table__td">
                                        <div>
                                            <i class="fas fa-clock" style="color: #6c757d; margin-right: 0.5rem;"></i>
                                            {{ $view->viewed_at->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">{{ $view->viewed_at->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($views->hasPages())
                    <div class="pagination-wrapper mt-4">
                        {{ $views->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-eye-slash"></i>
                    </div>
                    <h3 class="empty-state__title">No Views Found</h3>
                    <p class="empty-state__text">No product views match your filter criteria. Try adjusting your filters.</p>
                </div>
            @endif
        </div>
    </div>

    @if($mostViewed->count() > 0)
    <div class="modern-card mt-4">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-fire" style="color: #ff6b6b;"></i>
                    Most Viewed Products
                </h3>
                <p class="modern-card__subtitle">Top products by total view count</p>
            </div>
        </div>
        <div class="modern-card__body">
            <div class="modern-table-wrapper">
                <table class="modern-table">
                    <thead class="modern-table__head">
                        <tr>
                            <th class="modern-table__th" style="width: 50px;">#</th>
                            <th class="modern-table__th">Product Name</th>
                            <th class="modern-table__th text-end">Total Views</th>
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
        </div>
    </div>
    @endif
</div>
@endsection

