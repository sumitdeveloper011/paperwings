@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-plus-circle"></i>
                    Add Shipping Price
                </h1>
                <p class="page-header__subtitle">Create a new shipping price for a region</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.shipping-prices.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Shipping Prices</span>
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.shipping-prices.store') }}" class="modern-form" id="shippingPriceForm">
        @csrf

        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-info-circle"></i>
                            Shipping Price Information
                        </h3>
                    </div>
                    <div class="modern-card__body">

                        <div class="mb-3">
                            <label for="region_id" class="form-label">Region <span class="text-danger">*</span></label>
                            <select class="form-select @error('region_id') is-invalid @enderror"
                                    id="region_id"
                                    name="region_id"
                                    required>
                                <option value="">Select Region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ old('region_id', $selectedRegionId ?? null) == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Select the region for which you want to set shipping prices.
                            </small>
                            @error('region_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="shipping_price" class="form-label">Shipping Price ($) <span class="text-danger">*</span></label>
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   max="999999.99"
                                   class="form-control @error('shipping_price') is-invalid @enderror"
                                   id="shipping_price"
                                   name="shipping_price"
                                   value="{{ old('shipping_price') }}"
                                   placeholder="0.00"
                                   required>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Enter the base shipping cost for orders shipped to this region.
                            </small>
                            @error('shipping_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="free_shipping_minimum" class="form-label">Free Shipping Minimum ($)</label>
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   max="999999.99"
                                   class="form-control @error('free_shipping_minimum') is-invalid @enderror"
                                   id="free_shipping_minimum"
                                   name="free_shipping_minimum"
                                   value="{{ old('free_shipping_minimum') }}"
                                   placeholder="0.00">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Minimum order amount for free shipping. Leave empty if not applicable.
                            </small>
                            @error('free_shipping_minimum')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                @include('admin.shipping-price.partials.tips')

                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-cog"></i>
                            Settings
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="status"
                                       name="status"
                                       value="1"
                                       {{ old('status', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Active
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Enable or disable this shipping price for the selected region.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-save"></i>
                        Create Shipping Price
                    </button>
                    <a href="{{ route('admin.shipping-prices.index') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

