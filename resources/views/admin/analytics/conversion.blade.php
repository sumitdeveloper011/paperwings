@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-chart-bar"></i>
                    Conversion Analytics
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
            <h3 class="modern-card__title">Product Conversion Rates</h3>
        </div>
        <div class="modern-card__body">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Views</th>
                                <th>Cart Adds</th>
                                <th>Conversion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                @php
                                    $conversionRate = $product->views_count > 0 
                                        ? round(($product->cart_items_count / $product->views_count) * 100, 2) 
                                        : 0;
                                @endphp
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td><span class="badge bg-info">{{ $product->views_count }}</span></td>
                                    <td><span class="badge bg-warning">{{ $product->cart_items_count }}</span></td>
                                    <td>
                                        <span class="badge {{ $conversionRate > 5 ? 'bg-success' : ($conversionRate > 2 ? 'bg-warning' : 'bg-secondary') }}">
                                            {{ $conversionRate }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No conversion data available.</p>
            @endif
        </div>
    </div>
</div>
@endsection

