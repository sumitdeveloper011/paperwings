{{-- Filter Drawer for Mobile --}}
{{-- Usage: @include('frontend.partials.filter-drawer', ['drawerId' => 'categoryFilters'|'shopFilters']) --}}

@php
    $drawerId = $drawerId ?? 'filterDrawer';
@endphp

<div class="filter-drawer" id="{{ $drawerId }}">
    <div class="filter-drawer__overlay" id="filterDrawerOverlay"></div>
    <div class="filter-drawer__content">
        <div class="filter-drawer__header">
            <h2 class="filter-drawer__title">Filters</h2>
            <button class="filter-drawer__close" id="filterDrawerClose" aria-label="Close Filters">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="filter-drawer__body">
            @yield('filter-drawer-content')
        </div>
    </div>
</div>
