@extends('layouts.frontend.main')

@section('content')
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Product Bundles</li>
                        </ol>
                    </nav>
                    <h1 class="page-title">Product Bundles</h1>
                    <p class="page-subtitle">Save more with our special product bundles</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bundles-section">
        <div class="container">
            @if($bundles && $bundles->count() > 0)
            <div class="products-header">
                <div class="products-header__left">
                    <p class="products-count">Showing {{ $bundles->firstItem() ?? 0 }}-{{ $bundles->lastItem() ?? 0 }} of {{ $bundles->total() }} bundles</p>
                </div>
            </div>

            <div class="products-grid" id="bundlesGrid">
                @foreach($bundles as $bundle)
                <div class="cute-stationery__item">
                    <div class="cute-stationery__image">
                        <a href="{{ route('bundle.show', $bundle->slug) }}" class="cute-stationery__image-link">
                            <img src="{{ $bundle->image ? asset('storage/' . $bundle->image) : asset('assets/images/placeholder.jpg') }}" 
                                 alt="{{ $bundle->name }}" 
                                 class="cute-stationery__img"
                                 loading="lazy">
                        </a>
                        
                        @if($bundle->discount_percentage)
                        <span class="product-badge product-badge--sale">{{ $bundle->discount_percentage }}% OFF</span>
                        @endif
                        
                        <div class="cute-stationery__actions">
                            <a href="{{ route('bundle.show', $bundle->slug) }}" class="cute-stationery__action" title="View Bundle">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    <div class="cute-stationery__info">
                        <h3 class="cute-stationery__name">
                            <a href="{{ route('bundle.show', $bundle->slug) }}" class="cute-stationery__name-link">
                                {{ $bundle->name }}
                            </a>
                        </h3>
                        
                        @if($bundle->products->count() > 0)
                        <div class="product-stock-status">
                            <span class="stock-badge stock-badge--in">{{ $bundle->products->count() }} {{ $bundle->products->count() == 1 ? 'Product' : 'Products' }}</span>
                        </div>
                        @endif
                        
                        <div class="cute-stationery__price">
                            <span class="cute-stationery__price-current">${{ number_format($bundle->bundle_price, 2) }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if($bundles->hasPages())
            <div class="bundles-pagination">
                @include('include.frontend.pagination', ['paginator' => $bundles])
            </div>
            @endif
            @else
            <div class="products-grid__empty">
                <div class="empty-state">
                    <i class="fas fa-box-open empty-state__icon"></i>
                    <p class="empty-state__text">No bundles available at the moment. Check back soon!</p>
                </div>
            </div>
            @endif
        </div>
    </section>
@endsection

@push('styles')
<style>
.bundles-section {
    padding: 3rem 0 4rem;
}

.bundles-pagination {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #e9ecef;
}

.cute-stationery__action {
    text-decoration: none;
}

.cute-stationery__action:hover {
    color: var(--coral-red);
}
</style>
@endpush

