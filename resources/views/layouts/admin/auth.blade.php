<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Paper Wings' }}</title>
    @php
        $settings = \App\Helpers\SettingHelper::all();
        $siteFavicon = \App\Helpers\SettingHelper::favicon();
    @endphp
    <link rel="icon" type="image/x-icon" href="{{ $siteFavicon }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ $siteFavicon }}">
    @include('common.common-styles')
</head>
<body>
    @yield('content')
    @include('common.common-scripts')
    <x-toast />
</body>
</html>
