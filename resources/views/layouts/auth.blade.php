<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ !empty($setting->site_name) ? $setting->site_name : 'My Website' }}</title>

    <!-- Favicon -->
    @if (!empty($pengaturan->favicon) && file_exists(public_path('storage/uploads/' . $pengaturan->favicon)))
        <link rel="icon" href="{{ asset('storage/uploads/' . $pengaturan->favicon) }}">
    @else
        <link rel="icon" href="{{ asset('img/logo.png') }}">
    @endif

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Icons -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

@if (!empty($pengaturan->auth_background) && file_exists(public_path('storage/uploads/' . $pengaturan->auth_background)))
    <style>
        body {
            background-image: url('{{ asset('storage/uploads/' . $pengaturan->auth_background) }}');
            background-size: cover;
        }
    </style>
@else
    <style>
        body {
            background-image: url('{{ asset('img/background-auth.jpeg') }}');
            background-size: cover;
        }
    </style>
@endif

<body class="hold-transition login-page">
    @yield('content')

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>

    @yield('customStyle')
</body>

</html>
