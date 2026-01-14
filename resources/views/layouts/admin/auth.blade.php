<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? ($siteName ?? 'Paper Wings') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ $siteFavicon ?? asset('assets/frontend/images/icon.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ $siteFavicon ?? asset('assets/frontend/images/icon.png') }}">
    @include('common.common-styles')
</head>
<body>
    @yield('content')
    @include('common.common-scripts')
    <x-toast />
</body>
</html>
