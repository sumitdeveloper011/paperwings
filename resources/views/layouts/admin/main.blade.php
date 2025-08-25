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
    @include('include.admin.topbar')
    @include('include.admin.sidebar')
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="admin-main">
        @yield('content')
    </div>
    @include('include.admin.footer')
    @include('common.common-scripts')
</body>
</html>