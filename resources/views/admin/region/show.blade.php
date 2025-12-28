@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-map-marker-alt"></i>
                    Region Details
                </h1>
                <p class="page-header__subtitle">View region information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.regions.edit', $region) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit Region</span>
                </a>
                <a href="{{ route('admin.regions.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Regions</span>
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
                        Region Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-map-marker-alt"></i>
                                Region Name
                            </div>
                            <div class="info-item__value">
                                <strong>{{ $region->name }}</strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-link"></i>
                                Slug
                            </div>
                            <div class="info-item__value">
                                <code class="code-badge code-badge--large">{{ $region->slug }}</code>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-info-circle"></i>
                                Status
                            </div>
                            <div class="info-item__value">
                                @if($region->status == 1)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                        </div>

                        @if($region->shippingPrice)
                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-shipping-fast"></i>
                                Shipping Price
                            </div>
                            <div class="info-item__value">
                                <strong>${{ number_format($region->shippingPrice->shipping_price, 2) }}</strong>
                                @if($region->shippingPrice->free_shipping_minimum)
                                    <br>
                                    <small class="text-muted">Free shipping above ${{ number_format($region->shippingPrice->free_shipping_minimum, 2) }}</small>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-shipping-fast"></i>
                                Shipping Price
                            </div>
                            <div class="info-item__value">
                                <span class="text-muted">Not configured</span>
                                <br>
                                <a href="{{ route('admin.shipping-prices.create', ['region_id' => $region->id]) }}" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Add Shipping Price
                                </a>
                            </div>
                        </div>
                        @endif

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-calendar"></i>
                                Created At
                            </div>
                            <div class="info-item__value">
                                {{ $region->created_at->format('F d, Y h:i A') }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-calendar-check"></i>
                                Updated At
                            </div>
                            <div class="info-item__value">
                                {{ $region->updated_at->format('F d, Y h:i A') }}
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
                        <a href="{{ route('admin.regions.edit', $region) }}" class="action-item">
                            <i class="fas fa-edit"></i>
                            <span>Edit Region</span>
                        </a>
                        @if($region->shippingPrice)
                            <a href="{{ route('admin.shipping-prices.edit', $region->shippingPrice) }}" class="action-item">
                                <i class="fas fa-shipping-fast"></i>
                                <span>Edit Shipping Price</span>
                            </a>
                        @else
                            <a href="{{ route('admin.shipping-prices.create', ['region_id' => $region->id]) }}" class="action-item">
                                <i class="fas fa-plus"></i>
                                <span>Add Shipping Price</span>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('admin.regions.destroy', $region) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this region?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-item action-item--danger">
                                <i class="fas fa-trash"></i>
                                <span>Delete Region</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

