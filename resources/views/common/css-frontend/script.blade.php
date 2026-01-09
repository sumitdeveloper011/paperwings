{{-- Critical scripts - Must load synchronously and in order --}}
{{-- jQuery must load first as it's a dependency for everything --}}
<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/frontend/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/frontend/js/bootstrap.min.js') }}"></script>

{{-- Plugin libraries - Load conditionally based on page needs --}}
{{-- Slick Carousel - Only load on pages that use it --}}
@if(request()->routeIs('home') || request()->routeIs('product.*') || request()->routeIs('category.*') || request()->routeIs('shop.*'))
<script src="{{ asset('assets/frontend/js/slick.min.js') }}" defer></script>
@endif

{{-- Owl Carousel - Only load on pages that use it --}}
@if(request()->routeIs('home') || request()->routeIs('product.*'))
<script src="{{ asset('assets/frontend/js/owl.carousel.min.js') }}" defer></script>
@endif

{{-- Select2 - Only load on pages that use it (forms, filters) --}}
@if(request()->routeIs('shop.*') || request()->routeIs('category.*') || request()->routeIs('checkout.*'))
<script src="{{ asset('assets/frontend/js/select2.min.js') }}" defer></script>
@endif

{{-- jQuery Validation Plugin - Load only on checkout page --}}
@if(request()->routeIs('checkout.*'))
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js" defer></script>
<script src="{{ asset('assets/frontend/js/modules/form-validation.js') }}" defer></script>
@endif

{{-- Core modules - Must load in order --}}
<script src="{{ asset('assets/frontend/js/modules/utils.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/wishlist.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/cart.js') }}" defer></script>

{{-- Script modules - Load before script.js --}}
<script src="{{ asset('assets/frontend/js/modules/script-utils.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/carousels.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/modules/search.js') }}" defer></script>

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
