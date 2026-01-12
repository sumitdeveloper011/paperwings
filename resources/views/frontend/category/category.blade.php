@extends('layouts.frontend.main')
@section('content')
    @include('frontend.partials.page-header', [
        'title' => $category->name ?? 'Category',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => $category->name ?? 'Category', 'url' => null]
        ]
    ])
    <section class="category-products-section">
        <div class="container">
            <div class="row">
                <!-- Sidebar Filters -->
                <div class="col-lg-3 col-md-4">
                    <div class="category-sidebar">
                        @include('frontend.partials.sidebar-categories', [
                            'categories' => $categories,
                            'type' => 'link',
                            'currentCategory' => $category ?? null
                        ])

                        @include('frontend.partials.sidebar-price-filter', [
                            'priceMin' => $priceMin ?? 0,
                            'priceMax' => $priceMax ?? 100,
                            'minPrice' => $minPrice ?? null,
                            'maxPrice' => $maxPrice ?? null,
                            'showApplyButton' => true
                        ])
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="col-lg-9 col-md-8">
                    @include('frontend.partials.products-header', [
                        'products' => $products,
                        'sort' => $sort ?? null,
                        'sortOptions' => [
                            'featured' => 'Sort by: Featured',
                            'price_low_high' => 'Price: Low to High',
                            'price_high_low' => 'Price: High to Low',
                            'newest' => 'Newest First',
                        ]
                    ])

                    @include('frontend.partials.products-grid', [
                        'products' => $products,
                        'emptyMessage' => 'No products found in this category.'
                    ])
                </div>
            </div>
        </div>
    </section>


@push('scripts')
    @include('frontend.partials.filter-scripts', [
        'type' => 'category',
        'priceMax' => $priceMax ?? 100
    ])
@endpush
@endsection
