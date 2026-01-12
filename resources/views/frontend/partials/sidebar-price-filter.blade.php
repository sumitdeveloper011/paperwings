{{-- Price Range Filter Partial --}}
{{-- Usage: @include('frontend.partials.sidebar-price-filter', ['priceMin' => $priceMin, 'priceMax' => $priceMax, 'minPrice' => $minPrice, 'maxPrice' => $maxPrice, 'showApplyButton' => true|false]) --}}

@php
    $showApplyButton = $showApplyButton ?? false;
    $currentMaxPrice = $maxPrice ?? $priceMax;
@endphp

<div class="sidebar-widget">
    <div class="sidebar-widget__header">
        <h3 class="sidebar-widget__title">Price Range</h3>
    </div>
    <div class="sidebar-widget__body">
        <div class="price-filter">
            <div class="price-range-display-simple">
                <span class="price-display-text">$<span id="priceMinDisplay">{{ $minPrice ?? $priceMin ?? 0 }}</span> - $<span id="priceMaxDisplay">{{ $currentMaxPrice }}</span></span>
            </div>
            <div class="price-range-slider">
                <input type="range" class="price-range" id="priceRange"
                       min="{{ $priceMin ?? 0 }}"
                       max="{{ $priceMax ?? 100 }}"
                       value="{{ $currentMaxPrice }}"
                       step="1">
            </div>
            <div class="price-filter-actions">
                @if($showApplyButton)
                <button type="button" class="price-filter-btn price-filter-btn--primary" id="applyPriceFilter">
                    Apply Filter
                </button>
                @endif
                @if($minPrice || $maxPrice)
                <a href="{{ request()->url() }}" class="price-filter-clear-link" id="clearPriceFilter">Clear{{ $showApplyButton ? '' : ' Price Filter' }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
