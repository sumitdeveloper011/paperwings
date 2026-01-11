{{-- Product Select Component (Select2 with AJAX) - Reusable
     Usage: @include('components.select-product', [
         'id' => 'product_id',
         'name' => 'product_id',
         'label' => 'Product',
         'required' => false,
         'selected' => old('product_id'),
         'categoryFilterId' => 'category_id', // ID of category filter select
         'searchUrl' => route('admin.analytics.searchProducts'), // AJAX search URL
         'placeholder' => 'Select Product',
         'class' => 'form-select',
         'showLabel' => true,
         'wrapperClass' => 'mb-3',
         'useSelect2' => true,
         'selectedProduct' => $product // Optional: Product model instance for initial value
     ])
--}}
@php
    $selectId = $id ?? 'product_id';
    $selectName = $name ?? 'product_id';
    $labelText = $label ?? 'Product';
    $isRequired = $required ?? false;
    $selectedValue = $selected ?? old($selectName);
    $categoryFilterId = $categoryFilterId ?? 'category_id';
    $searchUrl = $searchUrl ?? '';
    $placeholder = $placeholder ?? 'Select Product';
    $selectClass = $class ?? 'form-select';
    $useSelect2 = $useSelect2 ?? true;
    $showLabel = $showLabel ?? true;
    // Handle wrapperClass: null or empty string means no wrapper, otherwise use default 'mb-3'
    if (!isset($wrapperClass)) {
        $wrapperClass = 'mb-3'; // Default if not passed
    } elseif ($wrapperClass === null || $wrapperClass === '') {
        $wrapperClass = ''; // No wrapper if explicitly null or empty
    }
    $selectedProduct = $selectedProduct ?? null;
@endphp

@if($wrapperClass && $wrapperClass !== '')
<div class="{{ $wrapperClass }}">
@endif
    @if($showLabel && $labelText)
    <label for="{{ $selectId }}" class="form-label">
        {{ $labelText }}
        @if($isRequired)
            <span class="text-danger">*</span>
        @endif
    </label>
    @endif
    <select class="{{ $selectClass }} @error($selectName) is-invalid @enderror @if($useSelect2) select2-product @endif"
            id="{{ $selectId }}"
            name="{{ $selectName }}"
            data-category-filter="{{ $categoryFilterId }}"
            data-search-url="{{ $searchUrl }}"
            @if($isRequired) required @endif>
        <option value="">{{ $placeholder }}</option>
        @if($selectedProduct)
            <option value="{{ $selectedProduct->id }}" selected>
                {{ $selectedProduct->name }}@if($selectedProduct->total_price) - ${{ number_format($selectedProduct->total_price, 2) }}@endif
            </option>
        @elseif($selectedValue)
            <option value="{{ $selectedValue }}" selected>
                {{-- Product name will be loaded via AJAX if needed --}}
            </option>
        @endif
    </select>
    @error($selectName)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
@if($wrapperClass && $wrapperClass !== '')
</div>
@endif

@if($useSelect2 && $searchUrl)
@push('scripts')
<script>
(function() {
    function initProductSelect2() {
        // Wait for jQuery and Select2 to be loaded
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') {
            setTimeout(initProductSelect2, 100);
            return;
        }

        const $select = jQuery('#{{ $selectId }}');
        if ($select.length === 0) {
            setTimeout(initProductSelect2, 100);
            return;
        }

        // Destroy existing Select2 instance if any
        if ($select.data('select2')) {
            $select.select2('destroy');
        }

        const categoryFilterId = $select.data('category-filter') || '{{ $categoryFilterId }}';
        const searchUrl = $select.data('search-url') || '{{ $searchUrl }}';

        // Initialize Select2 with AJAX
        $select.select2({
            theme: 'bootstrap-5',
            placeholder: '{{ $placeholder }}',
            allowClear: true,
            width: '100%',
            ajax: {
                url: searchUrl,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term || '',
                        category_id: jQuery('#' + categoryFilterId).val() || '',
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results || [],
                        pagination: {
                            more: (data.pagination && data.pagination.more) || false
                        }
                    };
                },
                cache: true,
                error: function(xhr, status, error) {
                    console.error('Select2 AJAX error:', error);
                }
            },
            minimumInputLength: 0,
            escapeMarkup: function(markup) {
                return markup;
            }
        });

        // When category filter changes, clear and reload products
        jQuery('#' + categoryFilterId).on('change', function() {
            $select.val(null).trigger('change');
        });

        // Trigger custom event after Select2 initialization
        $select.trigger('select2:initialized');
    }

    // Wait for DOM and scripts to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            // Wait a bit more for Select2 library to load
            setTimeout(initProductSelect2, 200);
        });
    } else {
        // DOM already loaded, wait for Select2
        setTimeout(initProductSelect2, 200);
    }
})();
</script>
@endpush
@endif
