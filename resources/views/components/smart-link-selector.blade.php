@props([
    'name' => 'url',
    'id' => 'url',
    'label' => 'Link URL',
    'value' => '',
    'required' => false,
    'categories' => [],
    'products' => [],
    'bundles' => [],
    'pages' => []
])

@php
    // Detect current link type from value
    $currentType = 'custom';
    $currentValue = '';

    if ($value) {
        // Check if it's a category link
        if (preg_match('#^/categories/(.+)$#', $value, $matches)) {
            $currentType = 'category';
            $currentValue = $matches[1]; // slug
        }
        // Check if it's a product link
        elseif (preg_match('#^/products/(.+)$#', $value, $matches)) {
            $currentType = 'product';
            $currentValue = $matches[1]; // slug
        }
        // Check if it's a bundle link
        elseif (preg_match('#^/bundles/(.+)$#', $value, $matches)) {
            $currentType = 'bundle';
            $currentValue = $matches[1]; // slug
        }
        // Check if it's a page link
        elseif (preg_match('#^/(about-us|privacy-policy|terms-and-conditions|delivery-policy|return-policy|cookie-policy|contact|faq|shop)$#', $value, $matches)) {
            $currentType = 'page';
            $currentValue = $matches[1]; // slug
        }
        else {
            $currentType = 'custom';
            $currentValue = $value;
        }
    }

    $uniqueId = str_replace(['[', ']', '_'], '', $id);
@endphp

<div class="smart-link-selector" data-selector-id="{{ $uniqueId }}">
    <label for="{{ $id }}_type" class="form-label-modern">
        <i class="fas fa-link"></i>
        {{ $label }}
        @if($required)
            <span class="required">*</span>
        @endif
    </label>

    <!-- Link Type Selector -->
    <div class="mb-3">
        <select class="form-input-modern" id="{{ $id }}_type" name="{{ $name }}_type" data-link-type-selector>
            <option value="custom" {{ $currentType === 'custom' ? 'selected' : '' }}>Custom URL</option>
            <option value="category" {{ $currentType === 'category' ? 'selected' : '' }}>Category</option>
            <option value="product" {{ $currentType === 'product' ? 'selected' : '' }}>Product</option>
            <option value="bundle" {{ $currentType === 'bundle' ? 'selected' : '' }}>Bundle</option>
            <option value="page" {{ $currentType === 'page' ? 'selected' : '' }}>Page</option>
        </select>
    </div>

    <!-- Category Selector -->
    <div class="link-type-option" data-link-type="category" style="display: {{ $currentType === 'category' ? 'block' : 'none' }};">
        <select class="form-input-modern" id="{{ $id }}_category" name="{{ $name }}_category" data-link-selector>
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->slug }}"
                        data-url="/categories/{{ $category->slug }}"
                        {{ $currentType === 'category' && $currentValue === $category->slug ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Product Selector -->
    <div class="link-type-option" data-link-type="product" style="display: {{ $currentType === 'product' ? 'block' : 'none' }};">
        <select class="form-input-modern" id="{{ $id }}_product" name="{{ $name }}_product" data-link-selector>
            <option value="">Select Product</option>
            @foreach($products as $product)
                <option value="{{ $product->slug }}"
                        data-url="/products/{{ $product->slug }}"
                        {{ $currentType === 'product' && $currentValue === $product->slug ? 'selected' : '' }}>
                    {{ $product->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Bundle Selector -->
    <div class="link-type-option" data-link-type="bundle" style="display: {{ $currentType === 'bundle' ? 'block' : 'none' }};">
        <select class="form-input-modern" id="{{ $id }}_bundle" name="{{ $name }}_bundle" data-link-selector>
            <option value="">Select Bundle</option>
            @foreach($bundles as $bundle)
                <option value="{{ $bundle->slug }}"
                        data-url="/bundles/{{ $bundle->slug }}"
                        {{ $currentType === 'bundle' && $currentValue === $bundle->slug ? 'selected' : '' }}>
                    {{ $bundle->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Page Selector -->
    <div class="link-type-option" data-link-type="page" style="display: {{ $currentType === 'page' ? 'block' : 'none' }};">
        <select class="form-input-modern" id="{{ $id }}_page" name="{{ $name }}_page" data-link-selector>
            <option value="">Select Page</option>
            <option value="/about-us" data-url="/about-us" {{ $currentType === 'page' && $currentValue === 'about-us' ? 'selected' : '' }}>About Us</option>
            <option value="/privacy-policy" data-url="/privacy-policy" {{ $currentType === 'page' && $currentValue === 'privacy-policy' ? 'selected' : '' }}>Privacy Policy</option>
            <option value="/terms-and-conditions" data-url="/terms-and-conditions" {{ $currentType === 'page' && $currentValue === 'terms-and-conditions' ? 'selected' : '' }}>Terms & Conditions</option>
            <option value="/delivery-policy" data-url="/delivery-policy" {{ $currentType === 'page' && $currentValue === 'delivery-policy' ? 'selected' : '' }}>Delivery Policy</option>
            <option value="/return-policy" data-url="/return-policy" {{ $currentType === 'page' && $currentValue === 'return-policy' ? 'selected' : '' }}>Return Policy</option>
            <option value="/cookie-policy" data-url="/cookie-policy" {{ $currentType === 'page' && $currentValue === 'cookie-policy' ? 'selected' : '' }}>Cookie Policy</option>
            <option value="/contact" data-url="/contact" {{ $currentType === 'page' && $currentValue === 'contact' ? 'selected' : '' }}>Contact</option>
            <option value="/faq" data-url="/faq" {{ $currentType === 'page' && $currentValue === 'faq' ? 'selected' : '' }}>FAQ</option>
            <option value="/shop" data-url="/shop" {{ $currentType === 'page' && $currentValue === 'shop' ? 'selected' : '' }}>Shop</option>
        </select>
    </div>

    <!-- Custom URL Input -->
    <div class="link-type-option" data-link-type="custom" style="display: {{ $currentType === 'custom' ? 'block' : 'none' }};">
        <div class="input-wrapper">
            <i class="fas fa-link input-icon"></i>
            <input type="text"
                   class="form-input-modern"
                   id="{{ $id }}_custom"
                   name="{{ $name }}_custom"
                   value="{{ $currentType === 'custom' ? $currentValue : '' }}"
                   placeholder="/about-us or https://example.com"
                   data-link-selector>
        </div>
        <small class="form-text text-muted">
            <i class="fas fa-info-circle"></i>
            Enter relative URL (e.g., /about-us) or full URL (e.g., https://example.com)
        </small>
    </div>

    <!-- Hidden input for actual URL value -->
    <input type="hidden" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" data-final-url>
</div>

@push('scripts')
<script>
(function() {
    const selectorId = '{{ $uniqueId }}';
    const typeSelector = document.getElementById('{{ $id }}_type');
    const finalUrlInput = document.getElementById('{{ $id }}');

    if (!typeSelector || !finalUrlInput) return;

    // Initialize Select2 for dropdowns
    function initSelect2ForDropdowns() {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            const container = typeSelector.closest('.smart-link-selector');
            const selects = [
                container.querySelector('#{{ $id }}_category'),
                container.querySelector('#{{ $id }}_product'),
                container.querySelector('#{{ $id }}_bundle'),
                container.querySelector('#{{ $id }}_page')
            ];

            selects.forEach(select => {
                if (select && !$(select).data('select2')) {
                    $(select).select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Select...',
                        allowClear: true,
                        width: '100%'
                    });
                }
            });
        } else {
            setTimeout(initSelect2ForDropdowns, 100);
        }
    }

    // Show/hide options based on type selection
    function updateLinkTypeOptions() {
        const selectedType = typeSelector.value;
        const container = typeSelector.closest('.smart-link-selector');

        // Hide all options
        container.querySelectorAll('.link-type-option').forEach(option => {
            option.style.display = 'none';
        });

        // Show selected option
        const selectedOption = container.querySelector(`[data-link-type="${selectedType}"]`);
        if (selectedOption) {
            selectedOption.style.display = 'block';

            // Initialize Select2 for newly shown dropdown
            if (selectedType !== 'custom' && typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                const select = selectedOption.querySelector('select');
                if (select && !$(select).data('select2')) {
                    $(select).select2({
                        theme: 'bootstrap-5',
                        placeholder: 'Select...',
                        allowClear: true,
                        width: '100%'
                    });
                }
            }
        }

        // Update final URL
        updateFinalUrl();
    }

    // Update final URL based on current selection
    function updateFinalUrl() {
        const selectedType = typeSelector.value;
        const container = typeSelector.closest('.smart-link-selector');
        let url = '';

        if (selectedType === 'category') {
            const select = container.querySelector('#{{ $id }}_category');
            if (select && typeof jQuery !== 'undefined' && $(select).data('select2')) {
                const val = $(select).val();
                const selectedOption = select.querySelector(`option[value="${val}"]`);
                url = selectedOption?.getAttribute('data-url') || '';
            } else {
                const selectedOption = select?.options[select.selectedIndex];
                url = selectedOption?.getAttribute('data-url') || '';
            }
        } else if (selectedType === 'product') {
            const select = container.querySelector('#{{ $id }}_product');
            if (select && typeof jQuery !== 'undefined' && $(select).data('select2')) {
                const val = $(select).val();
                const selectedOption = select.querySelector(`option[value="${val}"]`);
                url = selectedOption?.getAttribute('data-url') || '';
            } else {
                const selectedOption = select?.options[select.selectedIndex];
                url = selectedOption?.getAttribute('data-url') || '';
            }
        } else if (selectedType === 'bundle') {
            const select = container.querySelector('#{{ $id }}_bundle');
            if (select && typeof jQuery !== 'undefined' && $(select).data('select2')) {
                const val = $(select).val();
                const selectedOption = select.querySelector(`option[value="${val}"]`);
                url = selectedOption?.getAttribute('data-url') || '';
            } else {
                const selectedOption = select?.options[select.selectedIndex];
                url = selectedOption?.getAttribute('data-url') || '';
            }
        } else if (selectedType === 'page') {
            const select = container.querySelector('#{{ $id }}_page');
            if (select && typeof jQuery !== 'undefined' && $(select).data('select2')) {
                const val = $(select).val();
                const selectedOption = select.querySelector(`option[value="${val}"]`);
                url = selectedOption?.getAttribute('data-url') || '';
            } else {
                const selectedOption = select?.options[select.selectedIndex];
                url = selectedOption?.getAttribute('data-url') || '';
            }
        } else if (selectedType === 'custom') {
            const input = container.querySelector('#{{ $id }}_custom');
            url = input?.value || '';
        }

        finalUrlInput.value = url;

        // Trigger change event for preview updates
        finalUrlInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Event listeners
    typeSelector.addEventListener('change', updateLinkTypeOptions);

    // Listen to changes in all link selectors
    const container = typeSelector.closest('.smart-link-selector');
    container.querySelectorAll('[data-link-selector]').forEach(element => {
        element.addEventListener('change', updateFinalUrl);
        element.addEventListener('input', updateFinalUrl);
    });

    // Initialize Select2 when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initSelect2ForDropdowns();
            updateLinkTypeOptions();
        });
    } else {
        initSelect2ForDropdowns();
        updateLinkTypeOptions();
    }

    // Listen for Select2 changes
    if (typeof jQuery !== 'undefined') {
        $(document).on('change', '#{{ $id }}_category, #{{ $id }}_product, #{{ $id }}_bundle, #{{ $id }}_page', updateFinalUrl);
    }
})();
</script>
@endpush
