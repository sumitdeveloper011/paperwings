@extends('layouts.frontend.main')

@section('content')
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('bundles.index') }}">Bundles</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $bundle->name }}</li>
                        </ol>
                    </nav>
                    <h1 class="page-title">{{ $bundle->name }}</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="bundle-detail-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="bundle-detail__image">
                        <img src="{{ $bundle->image ? asset('storage/' . $bundle->image) : asset('assets/images/placeholder.jpg') }}" 
                             alt="{{ $bundle->name }}" 
                             class="bundle-detail__img">
                        @if($bundle->discount_percentage)
                        <span class="bundle-badge-large">{{ $bundle->discount_percentage }}% OFF</span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bundle-detail__info">
                        <h2 class="bundle-detail__title">{{ $bundle->name }}</h2>
                        <div class="bundle-detail__price">
                            <span class="bundle-price-large">${{ number_format($bundle->bundle_price, 2) }}</span>
                            @if($bundle->products->count() > 0)
                            @php
                                $totalValue = $bundle->products->sum(function($product) {
                                    return ($product->discount_price ?? $product->total_price) * ($product->pivot->quantity ?? 1);
                                });
                            @endphp
                            <span class="bundle-savings">Save ${{ number_format($totalValue - $bundle->bundle_price, 2) }}</span>
                            @endif
                        </div>
                        <div class="bundle-detail__description">
                            {!! $bundle->description !!}
                        </div>
                        <button class="btn btn-primary btn-lg bundle-add-to-cart" data-bundle-id="{{ $bundle->id }}">
                            <i class="fas fa-shopping-cart"></i> Add Bundle to Cart
                        </button>
                    </div>
                </div>
            </div>

            @if($bundle->products->count() > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="bundle-products-title">Bundle Includes:</h3>
                    <div class="row">
                        @foreach($bundle->products as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="bundle-product-item">
                                <div class="bundle-product-item__image">
                                    <a href="{{ route('product.detail', $product->slug) }}">
                                        <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="bundle-product-item__img">
                                    </a>
                                </div>
                                <div class="bundle-product-item__body">
                                    <h4 class="bundle-product-item__name">
                                        <a href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a>
                                    </h4>
                                    <div class="bundle-product-item__quantity">
                                        Quantity: {{ $product->pivot->quantity ?? 1 }}
                                    </div>
                                    <div class="bundle-product-item__price">
                                        ${{ number_format(($product->discount_price ?? $product->total_price) * ($product->pivot->quantity ?? 1), 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addToCartBtn = document.querySelector('.bundle-add-to-cart');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                const bundleId = this.dataset.bundleId;
                // Add bundle to cart logic here
                alert('Bundle add to cart functionality will be implemented');
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
.bundle-detail-section {
    padding: 3rem 0;
}

.bundle-detail__image {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.bundle-detail__img {
    width: 100%;
    height: auto;
    display: block;
}

.bundle-badge-large {
    position: absolute;
    top: 20px;
    right: 20px;
    background: var(--coral-red);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 700;
    font-size: 1rem;
}

.bundle-detail__info {
    padding: 2rem;
}

.bundle-detail__title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.bundle-detail__price {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.bundle-price-large {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--coral-red);
}

.bundle-savings {
    background: #28a745;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
}

.bundle-detail__description {
    color: #495057;
    line-height: 1.8;
    margin-bottom: 2rem;
}

.bundle-products-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 2rem;
    color: #2c3e50;
}

.bundle-product-item {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.bundle-product-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.bundle-product-item__image {
    height: 200px;
    overflow: hidden;
}

.bundle-product-item__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.bundle-product-item__body {
    padding: 1rem;
}

.bundle-product-item__name {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.bundle-product-item__name a {
    color: #2c3e50;
    text-decoration: none;
}

.bundle-product-item__name a:hover {
    color: var(--coral-red);
}

.bundle-product-item__quantity {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.bundle-product-item__price {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--coral-red);
}
</style>
@endpush

