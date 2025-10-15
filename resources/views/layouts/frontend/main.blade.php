<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Paper Wings' }}</title>
    @include('common.common-styles')
</head>
<body>
    <x-loader />
    @yield('content')
    @include('common.common-scripts')
</body>
</html>