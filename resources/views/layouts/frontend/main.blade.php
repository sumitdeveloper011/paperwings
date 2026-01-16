<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        try {
            $settings = \App\Helpers\SettingHelper::all();
        } catch (\Exception $e) {
            $settings = [];
        }
        $gaId = $settings['google_analytics_id'] ?? '';
        $gaEnabled = isset($settings['google_analytics_enabled']) && $settings['google_analytics_enabled'] == '1';
        
        // Meta tags from settings - allow override from view
        $metaTitle = $title ?? $metaTitle ?? $settings['meta_title'] ?? config('app.name', 'Paper Wings');
        $appName = config('app.name', 'Paper Wings');
        
        // Format title as "Title | App Name" (only if title is not already the app name)
        if ($metaTitle !== $appName && !empty($metaTitle)) {
            $metaTitle = $metaTitle . ' | ' . $appName;
        }
        
        $metaDescription = $metaDescription ?? $settings['meta_description'] ?? '';
        $metaKeywords = $metaKeywords ?? $settings['meta_keywords'] ?? '';
        $metaAuthor = $metaAuthor ?? $settings['meta_author'] ?? '';
        $siteLogo = \App\Helpers\SettingHelper::logo();
        $siteFavicon = \App\Helpers\SettingHelper::favicon();
    @endphp
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $siteFavicon }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ $siteFavicon }}">
    <link rel="apple-touch-icon" href="{{ $siteFavicon }}">
    
    <title>{{ $metaTitle }}</title>
    
    @if(!empty($metaDescription))
    <meta name="description" content="{{ $metaDescription }}">
    @endif
    
    @if(!empty($metaKeywords))
    <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    
    @if(!empty($metaAuthor))
    <meta name="author" content="{{ $metaAuthor }}">
    @endif
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:site_name" content="{{ config('app.name', 'Paper Wings') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if(!empty($metaTitle))
    <meta property="og:title" content="{{ $ogTitle ?? $metaTitle }}">
    @endif
    @if(!empty($metaDescription))
    <meta property="og:description" content="{{ $ogDescription ?? $metaDescription }}">
    @endif
    <meta property="og:image" content="{{ $ogImage ?? $siteLogo }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    @if(!empty($metaTitle))
    <meta name="twitter:title" content="{{ $twitterTitle ?? $metaTitle }}">
    @endif
    @if(!empty($metaDescription))
    <meta name="twitter:description" content="{{ $twitterDescription ?? $metaDescription }}">
    @endif
    <meta name="twitter:image" content="{{ $twitterImage ?? $siteLogo }}">
    
    <!-- Resource Hints for Performance -->
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    @if(request()->routeIs('home') || request()->routeIs('product.*') || request()->routeIs('category.*') || request()->routeIs('shop.*'))
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js" as="script">
    @endif
    
    @include('common.css-frontend.styles')
    @stack('head')
    
    @if($gaEnabled && !empty($gaId))
    <!-- Google Analytics ID (will be loaded by cookie-consent.js if consent given) -->
    <meta name="ga-id" content="{{ $gaId }}">
    <meta name="ga-enabled" content="1">
    @endif
</head>
<body data-authenticated="{{ auth()->check() ? 'true' : 'false' }}">
    @include('include.frontend.header')
    @include('include.frontend.cart-sidebar')
    @include('include.frontend.wishlist-sidebar')
    <x-frontend-toast />
    <x-cookie-consent-banner />
    <x-cookie-preferences-modal />

    @yield('content')
    @include('include.frontend.footer')
    @include('common.css-frontend.script')
    @stack('scripts')
    
    {{-- Error Handler - Can be disabled by setting window.DISABLE_ERROR_HANDLER = true before this script --}}
    @if(config('app.env') === 'production')
    {{-- In production, error handler is enabled but very conservative (won't show toasts for most errors) --}}
    @else
    {{-- In development, error handler only logs to console, no toasts --}}
    @endif
    <script src="{{ asset('assets/frontend/js/modules/error-handler.js') }}"></script>

    @if($gaEnabled && !empty($gaId) && auth()->check())
    @php
        $user = auth()->user();
        $userData = [
            'id' => $user->id,
            'type' => ($user->hasRole('SuperAdmin') || $user->hasRole('Admin')) ? 'admin' : 'registered',
            'registration_date' => $user->created_at->format('Y-m-d'),
            'total_orders' => $user->orders()->where('payment_status', 'paid')->count(),
            'total_spent' => $user->orders()->where('payment_status', 'paid')->sum('total')
        ];
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Analytics && window.Analytics.isEnabled()) {
                const user = @json($userData);
                
                window.Analytics.setUserProperties(user.id, {
                    user_type: user.type,
                    registration_date: user.registration_date,
                    total_orders: user.total_orders,
                    total_spent: user.total_spent
                });
            }
        });
    </script>
    @endif
</body>
</html>
