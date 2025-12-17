<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Paper Wings' }}</title>
    @include('common.css-frontend.styles')
    @stack('head')
</head>
<body>
    @include('include.frontend.header')
    @include('include.frontend.cart-sidebar')
    @include('include.frontend.wishlist-sidebar')

    @if(session('success'))
        <div class="alert alert-success" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px 20px; background: #10b981; color: white; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(function() {
                document.querySelector('.alert-success').style.display = 'none';
            }, 5000);
        </script>
    @endif

    @if(session('error'))
        <div class="alert alert-error" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px 20px; background: #ef4444; color: white; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            {{ session('error') }}
        </div>
        <script>
            setTimeout(function() {
                document.querySelector('.alert-error').style.display = 'none';
            }, 5000);
        </script>
    @endif

    @if(session('info'))
        <div class="alert alert-info" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px 20px; background: #3b82f6; color: white; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            {{ session('info') }}
        </div>
        <script>
            setTimeout(function() {
                document.querySelector('.alert-info').style.display = 'none';
            }, 5000);
        </script>
    @endif

    @yield('content')
    @include('include.frontend.footer')
    @include('common.css-frontend.script')
</body>
</html>
