@props([
    'name' => 'url',
    'id' => 'url',
    'label' => 'Link URL',
    'value' => '',
    'required' => false,
    'categories' => [],
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
        // Check if it's a bundle link
        elseif (preg_match('#^/bundles/(.+)$#', $value, $matches)) {
            $currentType = 'bundle';
            $currentValue = $matches[1]; // slug
        }
        // Check if it's a product link (treat as custom since product option is removed)
        elseif (preg_match('#^/products/(.+)$#', $value, $matches)) {
            $currentType = 'custom';
            $currentValue = $value; // Keep full URL as custom
        }
        // Check if it's a page link (single segment path like /about-us)
        elseif (preg_match('#^/([^/]+)$#', $value, $matches)) {
            $pageSlug = $matches[1];
            $isPage = false;

            // Check if it matches any page slug from database
            // Handle both arrays and collections
            if (isset($pages) && !empty($pages)) {
                $pagesCollection = is_array($pages) ? collect($pages) : $pages;
                if ($pagesCollection->isNotEmpty()) {
                    $isPage = $pagesCollection->contains(function($page) use ($pageSlug) {
                        return (is_object($page) ? $page->slug : $page['slug']) === $pageSlug;
                    });
                }
            }

            // Fallback to common page slugs if not found in database
            if (!$isPage) {
                $commonPages = ['about-us', 'privacy-policy', 'terms-and-conditions', 'delivery-policy', 'return-policy', 'cookie-policy', 'contact', 'faq', 'shop'];
                $isPage = in_array($pageSlug, $commonPages);
            }

            if ($isPage) {
                $currentType = 'page';
                $currentValue = $pageSlug;
            } else {
                $currentType = 'custom';
                $currentValue = $value;
            }
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
            <option value="bundle" {{ $currentType === 'bundle' ? 'selected' : '' }}>Bundle</option>
            <option value="page" {{ $currentType === 'page' ? 'selected' : '' }}>Page</option>
        </select>
    </div>

    <!-- Category Selector -->
    <div class="link-type-option" data-link-type="category" style="display: {{ $currentType === 'category' ? 'block' : 'none' }};">
        <select class="form-input-modern" id="{{ $id }}_category" name="{{ $name }}_category" data-link-selector>
            <option value="">Select Category</option>
            @php
                $categoriesCollection = is_array($categories) ? collect($categories) : $categories;
            @endphp
            @if(isset($categories) && !empty($categories) && $categoriesCollection->isNotEmpty())
                @foreach($categoriesCollection as $category)
                    @php
                        $categorySlug = is_object($category) ? $category->slug : ($category['slug'] ?? '');
                        $categoryName = is_object($category) ? $category->name : ($category['name'] ?? '');
                    @endphp
                    <option value="{{ $categorySlug }}"
                            data-url="/categories/{{ $categorySlug }}"
                            {{ $currentType === 'category' && $currentValue === $categorySlug ? 'selected' : '' }}>
                        {{ $categoryName }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>

    <!-- Bundle Selector -->
    <div class="link-type-option" data-link-type="bundle" style="display: {{ $currentType === 'bundle' ? 'block' : 'none' }};">
        <select class="form-input-modern" id="{{ $id }}_bundle" name="{{ $name }}_bundle" data-link-selector>
            <option value="">Select Bundle</option>
            @php
                $bundlesCollection = is_array($bundles) ? collect($bundles) : $bundles;
            @endphp
            @if(isset($bundles) && !empty($bundles) && $bundlesCollection->isNotEmpty())
                @foreach($bundlesCollection as $bundle)
                    @php
                        $bundleSlug = is_object($bundle) ? $bundle->slug : ($bundle['slug'] ?? '');
                        $bundleName = is_object($bundle) ? $bundle->name : ($bundle['name'] ?? '');
                    @endphp
                    <option value="{{ $bundleSlug }}"
                            data-url="/bundles/{{ $bundleSlug }}"
                            {{ $currentType === 'bundle' && $currentValue === $bundleSlug ? 'selected' : '' }}>
                        {{ $bundleName }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>

    <!-- Page Selector -->
    <div class="link-type-option" data-link-type="page" style="display: {{ $currentType === 'page' ? 'block' : 'none' }};">
        <select class="form-input-modern" id="{{ $id }}_page" name="{{ $name }}_page" data-link-selector>
            <option value="">Select Page</option>
            @php
                // Convert to collection if array
                $pagesCollection = is_array($pages) ? collect($pages) : $pages;

                // Common pages that should always be available (not stored in database)
                $commonPages = [
                    ['slug' => 'contact', 'title' => 'Contact'],
                    ['slug' => 'faq', 'title' => 'FAQ'],
                    ['slug' => 'shop', 'title' => 'Shop'],
                ];

                // Get existing slugs from database pages
                $existingSlugs = [];
                if (isset($pages) && !empty($pages) && $pagesCollection->isNotEmpty()) {
                    $existingSlugs = $pagesCollection->map(function($page) {
                        return is_object($page) ? $page->slug : ($page['slug'] ?? '');
                    })->toArray();
                }
            @endphp

            {{-- Show database pages first --}}
            @if(isset($pages) && !empty($pages) && $pagesCollection->isNotEmpty())
                @foreach($pagesCollection as $page)
                    @php
                        $pageSlug = is_object($page) ? $page->slug : ($page['slug'] ?? '');
                        $pageTitle = is_object($page) ? $page->title : ($page['title'] ?? '');
                    @endphp
                    <option value="/{{ $pageSlug }}"
                            data-url="/{{ $pageSlug }}"
                            {{ $currentType === 'page' && $currentValue === $pageSlug ? 'selected' : '' }}>
                        {{ $pageTitle }}
                    </option>
                @endforeach
            @endif

            {{-- Always show common pages (Contact, FAQ, Shop) if they're not in database --}}
            @foreach($commonPages as $commonPage)
                @if(!in_array($commonPage['slug'], $existingSlugs))
                    <option value="/{{ $commonPage['slug'] }}"
                            data-url="/{{ $commonPage['slug'] }}"
                            {{ $currentType === 'page' && $currentValue === $commonPage['slug'] ? 'selected' : '' }}>
                        {{ $commonPage['title'] }}
                    </option>
                @endif
            @endforeach

            {{-- Fallback: Show all common pages if no database pages provided --}}
            @if(!isset($pages) || empty($pages) || !$pagesCollection->isNotEmpty())
                <option value="/about-us" data-url="/about-us" {{ $currentType === 'page' && $currentValue === 'about-us' ? 'selected' : '' }}>About Us</option>
                <option value="/privacy-policy" data-url="/privacy-policy" {{ $currentType === 'page' && $currentValue === 'privacy-policy' ? 'selected' : '' }}>Privacy Policy</option>
                <option value="/terms-and-conditions" data-url="/terms-and-conditions" {{ $currentType === 'page' && $currentValue === 'terms-and-conditions' ? 'selected' : '' }}>Terms & Conditions</option>
                <option value="/delivery-policy" data-url="/delivery-policy" {{ $currentType === 'page' && $currentValue === 'delivery-policy' ? 'selected' : '' }}>Delivery Policy</option>
                <option value="/return-policy" data-url="/return-policy" {{ $currentType === 'page' && $currentValue === 'return-policy' ? 'selected' : '' }}>Return Policy</option>
                <option value="/cookie-policy" data-url="/cookie-policy" {{ $currentType === 'page' && $currentValue === 'cookie-policy' ? 'selected' : '' }}>Cookie Policy</option>
            @endif
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
        if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.select2 !== 'undefined') {
            const $ = window.jQuery;
            const container = typeSelector.closest('.smart-link-selector');
            const selects = [
                container.querySelector('#{{ $id }}_category'),
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
            if (selectedType !== 'custom' && typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.select2 !== 'undefined') {
                const $ = window.jQuery;
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
            if (select && typeof window.jQuery !== 'undefined') {
                const $ = window.jQuery;
                if ($(select).data('select2')) {
                    const val = $(select).val();
                    const selectedOption = select.querySelector(`option[value="${val}"]`);
                    url = selectedOption?.getAttribute('data-url') || '';
                } else {
                    const selectedOption = select?.options[select.selectedIndex];
                    url = selectedOption?.getAttribute('data-url') || '';
                }
            } else {
                const selectedOption = select?.options[select.selectedIndex];
                url = selectedOption?.getAttribute('data-url') || '';
            }
        } else if (selectedType === 'bundle') {
            const select = container.querySelector('#{{ $id }}_bundle');
            if (select && typeof window.jQuery !== 'undefined') {
                const $ = window.jQuery;
                if ($(select).data('select2')) {
                    const val = $(select).val();
                    const selectedOption = select.querySelector(`option[value="${val}"]`);
                    url = selectedOption?.getAttribute('data-url') || '';
                } else {
                    const selectedOption = select?.options[select.selectedIndex];
                    url = selectedOption?.getAttribute('data-url') || '';
                }
            } else {
                const selectedOption = select?.options[select.selectedIndex];
                url = selectedOption?.getAttribute('data-url') || '';
            }
        } else if (selectedType === 'page') {
            const select = container.querySelector('#{{ $id }}_page');
            if (select && typeof window.jQuery !== 'undefined') {
                const $ = window.jQuery;
                if ($(select).data('select2')) {
                    const val = $(select).val();
                    const selectedOption = select.querySelector(`option[value="${val}"]`);
                    url = selectedOption?.getAttribute('data-url') || '';
                } else {
                    const selectedOption = select?.options[select.selectedIndex];
                    url = selectedOption?.getAttribute('data-url') || '';
                }
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
    if (typeof window.jQuery !== 'undefined') {
        window.jQuery(document).on('change', '#{{ $id }}_category, #{{ $id }}_bundle, #{{ $id }}_page', updateFinalUrl);
    }
})();
</script>
@endpush
