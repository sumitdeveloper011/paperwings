{{-- Critical scripts - Must load synchronously and in order --}}
{{-- jQuery REMOVED: All dependencies migrated to native JavaScript --}}
{{-- Bootstrap JS - Bootstrap 4 requires jQuery, Bootstrap 5 doesn't --}}
{{-- If Bootstrap JS components break, check Bootstrap version and add jQuery back if needed --}}
<script src="{{ asset('assets/frontend/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/frontend/js/bootstrap.min.js') }}"></script>

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
<script src="{{ asset('assets/frontend/js/modules/utils.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/cookie-consent.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/analytics.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/wishlist.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/cart.js') }}" defer></script>

{{-- Script modules - Load before script.js --}}
<script src="{{ asset('assets/frontend/js/modules/script-utils.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/carousels.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/search.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/register-page.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/form-validation-native.js') }}" defer></script>

{{-- Main application scripts - Load after dependencies --}}
{{-- These use DOMContentLoaded, so they can be deferred for better performance --}}
<script src="{{ asset('assets/frontend/js/script.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/functions.js') }}" defer></script>

{{-- Non-critical animation scripts (load async to not block rendering) --}}
<script src="{{ asset('assets/frontend/js/gsap.min.js') }}" async></script>
<script src="{{ asset('assets/frontend/js/ScrollTrigger.min.js') }}" async></script>
<script src="{{ asset('assets/frontend/js/ScrollSmoother.min.js') }}" async></script>
<script src="{{ asset('assets/frontend/js/ScrollToPlugin.min.js') }}" async></script>

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
