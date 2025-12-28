{{-- Critical scripts - Must load synchronously and in order --}}
{{-- jQuery must load first as it's a dependency for everything --}}
<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/frontend/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/frontend/js/bootstrap.min.js') }}"></script>

{{-- Plugin libraries - Must load before application scripts --}}
<script src="{{ asset('assets/frontend/js/slick.min.js') }}"></script>
<script src="{{ asset('assets/frontend/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('assets/frontend/js/select2.min.js') }}"></script>

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
