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

    <div class="modern-card">
        <div class="modern-card__header">
            <h3 class="modern-card__title">Product Views</h3>
        </div>
        <div class="modern-card__body">
            <form method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <select name="product_id" class="form-control">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>

            @if($views->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>User</th>
                                <th>IP Address</th>
                                <th>Viewed At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($views as $view)
                                <tr>
                                    <td>{{ $view->product->name ?? 'N/A' }}</td>
                                    <td>{{ $view->user->name ?? 'Guest' }}</td>
                                    <td>{{ $view->ip_address }}</td>
                                    <td>{{ $view->viewed_at->format('M d, Y h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $views->links('components.pagination') }}
            @else
                <p class="text-muted">No views found.</p>
            @endif
        </div>
    </div>

    @if($mostViewed->count() > 0)
    <div class="modern-card mt-4">
        <div class="modern-card__header">
            <h3 class="modern-card__title">Most Viewed Products</h3>
        </div>
        <div class="modern-card__body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Total Views</th>
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
        </div>
    </div>
    @endif
</div>
@endsection

