{{-- Category Select Component (Select2) - Reusable
     Usage: @include('components.select-category', [
         'id' => 'category_id',
         'name' => 'category_id',
         'label' => 'Category',
         'required' => true,
         'selected' => old('category_id'),
         'categories' => $categories,
         'useUuid' => false, // Use UUID for value instead of ID
         'placeholder' => 'Select Category',
         'class' => 'form-select',
         'showLabel' => true, // Show/hide label
         'wrapperClass' => 'mb-3', // Wrapper div class
         'select2Width' => '100%' // Select2 width (can be '100%', 'resolve', or specific width)
     ])
--}}
@php
    $selectId = $id ?? 'category_id';
    $selectName = $name ?? 'category_id';
    $labelText = $label ?? 'Category';
    $isRequired = $required ?? false;
    $selectedValue = $selected ?? old($selectName);
    $categoriesList = $categories ?? [];
    $useUuid = $useUuid ?? false;
    $placeholder = $placeholder ?? 'Select Category';
    $selectClass = $class ?? 'form-select';
    $useSelect2 = $useSelect2 ?? true; // Default to Select2 enabled
    $showLabel = $showLabel ?? true; // Default to showing label
    $wrapperClass = $wrapperClass ?? 'mb-3'; // Default wrapper class
    $selectStyle = $style ?? ''; // Inline styles for select element
    $select2Width = $select2Width ?? '100%'; // Select2 width
@endphp

<div class="{{ $wrapperClass }}">
    @if($showLabel && $labelText)
    <label for="{{ $selectId }}" class="form-label">
        {{ $labelText }}
        @if($isRequired)
            <span class="text-danger">*</span>
        @endif
    </label>
    @endif
    <select class="{{ $selectClass }} @error($selectName) is-invalid @enderror @if($useSelect2) select2-category @endif"
            id="{{ $selectId }}"
            name="{{ $selectName }}"
            @if($isRequired) required @endif
            @if($selectStyle) style="{{ $selectStyle }}" @endif>
        <option value="">{{ $placeholder }}</option>
        @foreach($categoriesList->where('status', 1) as $category)
            @php
                $value = $useUuid ? $category->uuid : $category->id;
                $isSelected = $selectedValue == $value;
            @endphp
            <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error($selectName)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if($useSelect2)
@push('scripts')
<script>
(function() {
    function initSelect2() {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            const $select = $('#{{ $selectId }}');
            if ($select.length > 0) {
                // Destroy existing Select2 instance if any
                if ($select.data('select2')) {
                    $select.select2('destroy');
                }
                // Initialize Select2
                $select.select2({
                    theme: 'bootstrap-5',
                    placeholder: '{{ $placeholder }}',
                    allowClear: true,
                    width: '{{ $select2Width }}'
                });

                // Trigger custom event after Select2 initialization
                $select.trigger('select2:initialized');
            }
        } else {
            // Retry if jQuery or Select2 not loaded yet
            setTimeout(initSelect2, 100);
        }
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSelect2);
    } else {
        // DOM already loaded, try immediately
        initSelect2();
    }
})();
</script>
@endpush
@endif
