{{-- Critical scripts - Must load synchronously and in order --}}
{{-- jQuery REMOVED: All dependencies migrated to native JavaScript --}}
{{-- Bootstrap JS - Bootstrap 4 requires jQuery, Bootstrap 5 doesn't --}}
{{-- If Bootstrap JS components break, check Bootstrap version and add jQuery back if needed --}}
<script src="{{ asset('assets/frontend/js/popper.min.js') }}?v={{ config('app.asset_version', '1.0.0') }}"></script>
<script src="{{ asset('assets/frontend/js/bootstrap.min.js') }}?v={{ config('app.asset_version', '1.0.0') }}"></script>

{{-- Plugin libraries - Load conditionally based on page needs --}}
{{-- Slick Carousel - REMOVED: Migrated to Swiper.js --}}

{{-- Swiper.js - Modern carousel library (no jQuery needed) --}}
@if(request()->routeIs('home') || request()->routeIs('product.*') || request()->routeIs('category.*') || request()->routeIs('shop.*'))
<script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js" defer></script>
@endif

{{-- Select2 - REMOVED: Replaced with native HTML selects (no jQuery dependency) --}}

{{-- jQuery Validation Plugin - REMOVED: All forms now use native validation --}}
{{-- Native validation module is loaded globally above --}}

{{-- Core modules - Must load in order --}}
<script src="{{ asset('assets/frontend/js/modules/utils.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/ajax-utils.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/skeleton-loader.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/form-submission-handler.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/cookie-consent.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/analytics.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/wishlist.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/cart.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>

{{-- Script modules - Load before script.js --}}
<script src="{{ asset('assets/frontend/js/modules/script-utils.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/carousels.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/search.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/register-page.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/form-validation-native.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>

{{-- Main application scripts - Load after dependencies --}}
{{-- These use DOMContentLoaded, so they can be deferred for better performance --}}
<script src="{{ asset('assets/frontend/js/script.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/functions.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>

{{-- Non-critical animation scripts (load async to not block rendering) --}}
<script src="{{ asset('assets/frontend/js/gsap.min.js') }}?v={{ config('app.asset_version', '1.0.0') }}" async></script>
<script src="{{ asset('assets/frontend/js/ScrollTrigger.min.js') }}?v={{ config('app.asset_version', '1.0.0') }}" async></script>
<script src="{{ asset('assets/frontend/js/ScrollSmoother.min.js') }}?v={{ config('app.asset_version', '1.0.0') }}" async></script>
<script src="{{ asset('assets/frontend/js/ScrollToPlugin.min.js') }}?v={{ config('app.asset_version', '1.0.0') }}" async></script>

{{-- Tax configuration for JavaScript --}}
<script>
    window.GST_MULTIPLIER = {{ config('tax.gst_multiplier', 1.15) }};
    window.GST_RATE = {{ config('tax.gst_rate', 15) }};
</script>

{{-- Alert auto-hide script (inline, optimized) --}}
<script>
(function() {
    'use strict';
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAlerts);
    } else {
        initAlerts();
    }

    function initAlerts() {
        const alerts = document.querySelectorAll('[data-alert]');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                if (alert && alert.parentNode) {
                    alert.style.display = 'none';
                }
            }, 5000);
        });
    }
})();
</script>
