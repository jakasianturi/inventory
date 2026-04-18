<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ !empty($setting->site_name) ? $setting->site_name : 'My Website' }}</title>

    <!-- Favicon -->
    @php
    $faviconPath = asset('img/img.jpg');
    if (!empty($setting->favicon) && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->favicon)) {
        $faviconPath = asset(\Illuminate\Support\Facades\Storage::disk('public')->url($setting->favicon));
    }
    @endphp
    <link rel="icon" href="{{ $faviconPath }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Icons -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
@php
$authBackgroundPath = asset('img/background-auth.jpg');

if (!empty($setting->auth_background) && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->auth_background)) {
    $authBackgroundPath = asset(\Illuminate\Support\Facades\Storage::disk('public')->url($setting->auth_background));
}
@endphp
<style>
    body {
        background-image: url('{{ $authBackgroundPath }}');
        background-size: cover;
    }
</style>

<body class="hold-transition login-page">
    @yield('content')

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>

    @yield('customStyle')
</body>

</html>
