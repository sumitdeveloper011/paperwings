{{-- Subcategory Select Component - Reusable (for future use)
     Usage: @include('components.select-subcategory', [
         'id' => 'subcategory_id',
         'name' => 'subcategory_id',
         'label' => 'Sub Category',
         'required' => false,
         'selected' => old('subcategory_id'),
         'subcategories' => $subcategories,
         'categoryId' => 'category_id', // Parent category select ID
         'loadUrl' => route('admin.products.getSubCategories'),
         'placeholder' => 'Select Sub Category',
         'class' => 'form-select'
     ])
--}}
@php
    $selectId = $id ?? 'subcategory_id';
    $selectName = $name ?? 'subcategory_id';
    $labelText = $label ?? 'Sub Category';
    $isRequired = $required ?? false;
    $selectedValue = $selected ?? old($selectName);
    $subcategoriesList = $subcategories ?? [];
    $categoryId = $categoryId ?? 'category_id';
    $loadUrl = $loadUrl ?? null;
    $placeholder = $placeholder ?? 'Select Sub Category';
    $selectClass = $class ?? 'form-select';
    $useSelect2 = $useSelect2 ?? true; // Enable Select2 for future use
    $hidden = $hidden ?? false; // Hide for future use
@endphp

<div class="mb-3" @if($hidden) style="display: none;" @endif>
    <label for="{{ $selectId }}" class="form-label">
        {{ $labelText }}
        @if($isRequired)
            <span class="text-danger">*</span>
        @endif
    </label>
    <select class="{{ $selectClass }} @error($selectName) is-invalid @enderror @if($useSelect2) select2-subcategory @endif"
            id="{{ $selectId }}"
            name="{{ $selectName }}"
            @if($isRequired) required @endif>
        <option value="">{{ $placeholder }}</option>
        @if($subcategoriesList && count($subcategoriesList) > 0)
            @foreach($subcategoriesList as $subcategory)
                <option value="{{ $subcategory->id }}" {{ $selectedValue == $subcategory->id ? 'selected' : '' }}>
                    {{ $subcategory->name }}
                </option>
            @endforeach
        @endif
    </select>
    @error($selectName)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('{{ $categoryId }}');
    const subcategorySelect = document.getElementById('{{ $selectId }}');
    
    // Initialize Select2 if enabled
    @if($useSelect2)
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
    @endif
    
    // Dynamic loading from category
    @if($loadUrl)
    if (categorySelect && subcategorySelect) {
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            
            // Clear subcategory options
            subcategorySelect.innerHTML = '<option value="">{{ $placeholder }}</option>';
            
            @if($useSelect2)
            // Trigger Select2 update
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                $(subcategorySelect).trigger('change');
            }
            @endif
            
            if (categoryId) {
                fetch('{{ $loadUrl }}?category_id=' + categoryId)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(subCategory => {
                            const option = document.createElement('option');
                            option.value = subCategory.id;
                            option.textContent = subCategory.name;
                            subcategorySelect.appendChild(option);
                        });
                        
                        @if($useSelect2)
                        // Trigger Select2 update after options are added
                        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                            $(subcategorySelect).trigger('change');
                        }
                        @endif
                    })
                    .catch(error => {
                        console.error('Error loading subcategories:', error);
                    });
            }
        });
    }
    @endif
});
</script>
@endpush
