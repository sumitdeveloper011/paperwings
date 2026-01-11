{{-- Brand Select Component (Select2) - Reusable
     Usage: @include('components.select-brand', [
         'id' => 'brand_id',
         'name' => 'brand_id',
         'label' => 'Brand',
         'required' => false,
         'selected' => old('brand_id'),
         'brands' => $brands,
         'placeholder' => 'Select Brand',
         'class' => 'form-select',
         'useSelect2' => true
     ])
--}}
@php
    $selectId = $id ?? 'brand_id';
    $selectName = $name ?? 'brand_id';
    $labelText = $label ?? 'Brand';
    $isRequired = $required ?? false;
    $selectedValue = $selected ?? old($selectName);
    $brandsList = $brands ?? [];
    $placeholder = $placeholder ?? 'Select Brand';
    $selectClass = $class ?? 'form-select';
    $useSelect2 = $useSelect2 ?? true;
    $hidden = $hidden ?? false; // Hide for future use
@endphp

<div class="mb-3" @if($hidden) style="display: none;" @endif>
    <label for="{{ $selectId }}" class="form-label">
        {{ $labelText }}
        @if($isRequired)
            <span class="text-danger">*</span>
        @endif
    </label>
    <select class="{{ $selectClass }} @error($selectName) is-invalid @enderror @if($useSelect2) select2-brand @endif"
            id="{{ $selectId }}"
            name="{{ $selectName }}"
            @if($isRequired) required @endif>
        <option value="">{{ $placeholder }}</option>
        @foreach($brandsList->where('status', 1) as $brand)
            <option value="{{ $brand->id }}" {{ $selectedValue == $brand->id ? 'selected' : '' }}>
                {{ $brand->name }}
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
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        $('#{{ $selectId }}').select2({
            theme: 'bootstrap-5',
            placeholder: '{{ $placeholder }}',
            allowClear: true,
            width: '100%'
        });
    } else {
        setTimeout(function() {
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                $('#{{ $selectId }}').select2({
                    theme: 'bootstrap-5',
                    placeholder: '{{ $placeholder }}',
                    allowClear: true,
                    width: '100%'
                });
            }
        }, 500);
    }
});
</script>
@endpush
@endif
