{{-- Preload critical fonts --}}
<link rel="preload" href="{{ asset('assets/frontend/fonts/Oswald-Regular.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="{{ asset('assets/frontend/fonts/Oswald-Bold.woff2') }}" as="font" type="font/woff2" crossorigin>

{{-- Preload critical CSS files for faster loading --}}
<link rel="preload" href="{{ asset('assets/frontend/css/bootstrap.min.css') }}" as="style">
<link rel="preload" href="{{ asset('assets/frontend/css/all.min.css') }}" as="style">
<link rel="preload" href="{{ asset('assets/frontend/css/style.css') }}" as="style">

{{-- Critical CSS (inline for above-the-fold content) --}}
@if(\App\Helpers\CriticalCssHelper::exists())
<style>{!! \App\Helpers\CriticalCssHelper::get() !!}</style>
@endif


{{-- Bootstrap CSS (synchronous - required for grid layout) --}}
<link rel="stylesheet" href="{{ asset('assets/frontend/css/bootstrap.min.css') }}">

{{-- Font Awesome CSS (synchronous - required for icons) --}}
<link rel="stylesheet" href="{{ asset('assets/frontend/css/all.min.css') }}">

{{-- Main style.css (synchronous - required for proper styling, prevents FOUC) --}}
<link rel="stylesheet" href="{{ asset('assets/frontend/css/style.css') }}">

{{-- Swiper CSS (conditional - async load, only when needed) --}}
@if(request()->routeIs('home') || request()->routeIs('product.*') || request()->routeIs('category.*') || request()->routeIs('shop.*'))
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"></noscript>
@endif

{{-- Base CSS (common styles - async load) --}}
@if(file_exists(public_path('assets/frontend/css/base.css')))
<link rel="stylesheet" href="{{ asset('assets/frontend/css/base.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/frontend/css/base.css') }}"></noscript>
@endif

{{-- Components CSS (reusable components - async load) --}}
@if(file_exists(public_path('assets/frontend/css/components.css')))
<link rel="stylesheet" href="{{ asset('assets/frontend/css/components.css') }}" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="{{ asset('assets/frontend/css/components.css') }}"></noscript>
@endif

{{-- Page-specific CSS (conditional loading) --}}
@if(request()->routeIs('home'))
    @if(file_exists(public_path('assets/frontend/css/pages/home.css')))
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/pages/home.css') }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ asset('assets/frontend/css/pages/home.css') }}"></noscript>
    @endif
@elseif(request()->routeIs('product.*'))
    @if(file_exists(public_path('assets/frontend/css/pages/product.css')))
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/pages/product.css') }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ asset('assets/frontend/css/pages/product.css') }}"></noscript>
    @endif
@elseif(request()->routeIs('shop.*') || request()->routeIs('category.*'))
    @if(file_exists(public_path('assets/frontend/css/pages/shop.css')))
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/pages/shop.css') }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ asset('assets/frontend/css/pages/shop.css') }}"></noscript>
    @endif
@elseif(request()->routeIs('checkout.*'))
    @if(file_exists(public_path('assets/frontend/css/pages/checkout.css')))
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/pages/checkout.css') }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ asset('assets/frontend/css/pages/checkout.css') }}"></noscript>
    @endif
@elseif(request()->routeIs('account.*'))
    @if(file_exists(public_path('assets/frontend/css/pages/account.css')))
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/pages/account.css') }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ asset('assets/frontend/css/pages/account.css') }}"></noscript>
    @endif
@endif
