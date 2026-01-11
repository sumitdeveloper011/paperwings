<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        try {
            $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        } catch (\Exception $e) {
            $settings = [];
        }
        $gaId = $settings['google_analytics_id'] ?? '';
        $gaEnabled = isset($settings['google_analytics_enabled']) && $settings['google_analytics_enabled'] == '1';
        
        // Meta tags from settings
        $metaTitle = $metaTitle ?? $settings['meta_title'] ?? config('app.name', 'Paper Wings');
        $metaDescription = $metaDescription ?? $settings['meta_description'] ?? '';
        $metaKeywords = $metaKeywords ?? $settings['meta_keywords'] ?? '';
        $metaAuthor = $metaAuthor ?? $settings['meta_author'] ?? '';
        $siteLogo = !empty($settings['logo']) ? asset('storage/' . $settings['logo']) : asset('assets/frontend/images/logo.png');
        $siteFavicon = !empty($settings['icon']) ? asset('storage/' . $settings['icon']) : asset('assets/frontend/images/icon.png');
    @endphp
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $siteFavicon }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ $siteFavicon }}">
    <link rel="apple-touch-icon" href="{{ $siteFavicon }}">
    
    <title>{{ $title ?? $metaTitle }}</title>
    
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
    
    @include('common.css-frontend.styles')
    @stack('head')
    
    @if($gaEnabled && !empty($gaId))
    <!-- Google Analytics (GA4) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $gaId }}', {
            'send_page_view': true
        });
    </script>
    @endif
</head>
<body data-authenticated="{{ auth()->check() ? 'true' : 'false' }}">
    @include('include.frontend.header')
    @include('include.frontend.cart-sidebar')
    @include('include.frontend.wishlist-sidebar')

    @if(session('success'))
        <div class="alert alert-success" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px 20px; background: #10b981; color: white; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);" data-alert="success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px 20px; background: #ef4444; color: white; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);" data-alert="error">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px 20px; background: #3b82f6; color: white; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);" data-alert="info">
            {{ session('info') }}
        </div>
    @endif

    @yield('content')
    @include('include.frontend.footer')
    @include('common.css-frontend.script')
    @stack('scripts')
</body>
</html>
