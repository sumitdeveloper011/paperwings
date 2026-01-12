@extends('layouts.frontend.main')
@section('content')
    @include('frontend.partials.page-header', [
        'title' => 'Shop',
        'subtitle' => 'Discover our amazing collection of products',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Shop', 'url' => null]
        ]
    ])

    <section class="category-products-section">
        <div class="container">
            <div class="row">
                <!-- Filters Sidebar -->
                <div class="col-lg-3 col-md-4">
                    <div class="category-sidebar">
                        @include('frontend.partials.sidebar-categories', [
                            'categories' => $categories,
                            'type' => 'checkbox',
                            'selectedCategories' => $categoriesFilter ?? []
                        ])

                        @include('frontend.partials.sidebar-price-filter', [
                            'priceMin' => $priceMin,
                            'priceMax' => $priceMax,
                            'minPrice' => $minPrice ?? null,
                            'maxPrice' => $maxPrice ?? null,
                            'showApplyButton' => false
                        ])

                        @include('frontend.partials.sidebar-brands', [
                            'brands' => $brands ?? collect(),
                            'selectedBrands' => $brandsFilter ?? []
                        ])

                        @include('frontend.partials.sidebar-tags', [
                            'tags' => $tags ?? collect(),
                            'selectedTags' => $tagsFilter ?? []
                        ])

                        <!-- Clear Filters Button -->
                        <div class="sidebar-widget">
                            <div class="sidebar-widget__body">
                                <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
                                <button class="btn btn-link w-100 mt-2" id="clearFilters" style="text-decoration: none;">Clear All Filters</button>
                            </div>
                        </div>
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
                            'name_asc' => 'Name: A to Z',
                            'name_desc' => 'Name: Z to A',
                            'newest' => 'Newest First',
                        ]
                    ])

                    @include('frontend.partials.products-grid', [
                        'products' => $products,
                        'emptyMessage' => 'No products found. Try adjusting your filters.'
                    ])
                </div>
            </div>
        </div>
    </section>


@push('scripts')
    @include('frontend.partials.filter-scripts', [
        'type' => 'shop',
        'priceMax' => $priceMax ?? 100,
        'routeName' => 'shop'
    ])
@endpush
@endsection

