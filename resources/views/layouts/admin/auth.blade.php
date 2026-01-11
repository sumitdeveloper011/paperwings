<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Paper Wings' }}</title>
    @php
        $settings = \Illuminate\Support\Facades\Cache::remember('admin_settings', 3600, function() {
            try {
                return \App\Models\Setting::pluck('value', 'key')->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });
        $siteFavicon = !empty($settings['icon']) ? asset('storage/' . $settings['icon']) : asset('assets/frontend/images/icon.png');
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