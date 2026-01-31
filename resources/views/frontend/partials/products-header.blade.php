{{-- Products Header Partial (Sort & Count) --}}
{{-- Usage: @include('frontend.partials.products-header', ['products' => $products, 'sort' => $sort, 'sortOptions' => [], 'showFilters' => true]) --}}

@php
    $sortOptions = $sortOptions ?? [
        'featured' => 'Sort by: Featured',
        'price_low_high' => 'Price: Low to High',
        'price_high_low' => 'Price: High to Low',
        'name_asc' => 'Name: A to Z',
        'name_desc' => 'Name: Z to A',
        'newest' => 'Newest First',
    ];
    $showFilters = $showFilters ?? true;
@endphp

<div class="products-header">
    <div class="products-header__left">
        <p class="products-count">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</p>
    </div>
    <div class="products-header__right">
        @if($showFilters)
        <button class="filter-toggle-btn" id="filterToggleBtn" aria-label="Toggle Filters">
            <i class="fas fa-filter"></i>
            <span>Filters</span>
        </button>
        @endif
        <div class="sort-dropdown">
            <select class="sort-select" id="sortSelect">
                @foreach($sortOptions as $value => $label)
                <option value="{{ $value }}" {{ (isset($sort) && $sort == $value) ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
