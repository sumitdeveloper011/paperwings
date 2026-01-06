@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Shipping Price
                </h1>
                <p class="page-header__subtitle">Update shipping price information</p>
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

    <form method="POST" action="{{ route('admin.shipping-prices.update', $shippingPrice) }}" class="modern-form" id="shippingPriceForm">
        @csrf
        @method('PUT')

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
                        
                        <div class="form-group-modern">
                            <label for="region_id" class="form-label-modern">
                                Region <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <select class="form-input-modern @error('region_id') is-invalid @enderror" 
                                        id="region_id" 
                                        name="region_id" 
                                        required>
                                    <option value="">Select Region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ old('region_id', $shippingPrice->region_id) == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('region_id')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="shipping_price" class="form-label-modern">
                                Shipping Price ($) <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-dollar-sign input-icon"></i>
                                <input type="number" 
                                       step="0.01"
                                       min="0"
                                       max="999999.99"
                                       class="form-input-modern @error('shipping_price') is-invalid @enderror" 
                                       id="shipping_price" 
                                       name="shipping_price" 
                                       value="{{ old('shipping_price', $shippingPrice->shipping_price) }}" 
                                       placeholder="0.00"
                                       required>
                            </div>
                            @error('shipping_price')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text">Enter the shipping price for this region</small>
                        </div>

                        <div class="form-group-modern">
                            <label for="free_shipping_minimum" class="form-label-modern">
                                Free Shipping Minimum ($)
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-gift input-icon"></i>
                                <input type="number" 
                                       step="0.01"
                                       min="0"
                                       max="999999.99"
                                       class="form-input-modern @error('free_shipping_minimum') is-invalid @enderror" 
                                       id="free_shipping_minimum" 
                                       name="free_shipping_minimum" 
                                       value="{{ old('free_shipping_minimum', $shippingPrice->free_shipping_minimum) }}" 
                                       placeholder="0.00">
                            </div>
                            @error('free_shipping_minimum')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text">Minimum order amount for free shipping (leave empty if not applicable)</small>
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
                            Settings
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-group-modern">
                            <div class="checkbox-modern">
                                <input type="checkbox" 
                                       id="status" 
                                       name="status" 
                                       value="1"
                                       {{ old('status', $shippingPrice->status) ? 'checked' : '' }}>
                                <label for="status" class="checkbox-modern__label">
                                    <span class="checkbox-modern__check"></span>
                                    <span class="checkbox-modern__text">Active</span>
                                </label>
                            </div>
                            <small class="form-text">Enable or disable this shipping price</small>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-save"></i>
                        Update Shipping Price
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

