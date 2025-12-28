@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-shipping-fast"></i>
                    Shipping Price Details
                </h1>
                <p class="page-header__subtitle">View shipping price information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.shipping-prices.edit', $shippingPrice) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit Shipping Price</span>
                </a>
                <a href="{{ route('admin.shipping-prices.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Shipping Prices</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Shipping Price Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-map-marker-alt"></i>
                                Region
                            </div>
                            <div class="info-item__value">
                                <strong>{{ $shippingPrice->region->name }}</strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-dollar-sign"></i>
                                Shipping Price
                            </div>
                            <div class="info-item__value">
                                <strong>${{ number_format($shippingPrice->shipping_price, 2) }}</strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-gift"></i>
                                Free Shipping Minimum
                            </div>
                            <div class="info-item__value">
                                @if($shippingPrice->free_shipping_minimum)
                                    <span class="badge bg-success">${{ number_format($shippingPrice->free_shipping_minimum, 2) }}</span>
                                    <small class="text-muted d-block mt-1">Orders above this amount get free shipping</small>
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-info-circle"></i>
                                Status
                            </div>
                            <div class="info-item__value">
                                @if($shippingPrice->status == 1)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-calendar"></i>
                                Created At
                            </div>
                            <div class="info-item__value">
                                {{ $shippingPrice->created_at->format('F d, Y h:i A') }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-calendar-check"></i>
                                Updated At
                            </div>
                            <div class="info-item__value">
                                {{ $shippingPrice->updated_at->format('F d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-cog"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="action-list">
                        <a href="{{ route('admin.shipping-prices.edit', $shippingPrice) }}" class="action-item">
                            <i class="fas fa-edit"></i>
                            <span>Edit Shipping Price</span>
                        </a>
                        <form method="POST" action="{{ route('admin.shipping-prices.destroy', $shippingPrice) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this shipping price?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-item action-item--danger">
                                <i class="fas fa-trash"></i>
                                <span>Delete Shipping Price</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info"></i>
                        Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-box">
                        <p class="info-box__text">
                            <strong>UUID:</strong> {{ $shippingPrice->uuid }}
                        </p>
                        <p class="info-box__text">
                            <strong>ID:</strong> {{ $shippingPrice->id }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

