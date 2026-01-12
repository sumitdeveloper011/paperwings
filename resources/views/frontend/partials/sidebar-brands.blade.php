{{-- Brands Widget Partial --}}
{{-- Usage: @include('frontend.partials.sidebar-brands', ['brands' => $brands, 'selectedBrands' => []]) --}}

@if(isset($brands) && $brands->count() > 0)
<div class="sidebar-widget">
    <div class="sidebar-widget__header">
        <h3 class="sidebar-widget__title">Brands</h3>
    </div>
    <div class="sidebar-widget__body">
        <ul class="sidebar-categories">
            @foreach($brands as $brand)
            <li class="sidebar-category">
                <label class="sidebar-category__link sidebar-category__link--checkbox">
                    <input class="brand-filter" type="checkbox" value="{{ $brand->id }}" id="brand{{ $brand->id }}"
                        {{ in_array($brand->id, $selectedBrands ?? []) ? 'checked' : '' }}>
                    <span class="sidebar-category__name">{{ $brand->name }} <span class="category-count">({{ $brand->active_products_count ?? 0 }})</span></span>
                </label>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
