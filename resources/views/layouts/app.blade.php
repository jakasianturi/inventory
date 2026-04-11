<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ !empty($pengaturan->nama_situs) ? $pengaturan->nama_situs : '' }}</title>

    <!-- Favicon -->
    @if (!empty($pengaturan->favicon) && file_exists(public_path('storage/uploads/' . $pengaturan->favicon)))
        <link rel="icon" href="{{ asset('storage/uploads/' . $pengaturan->favicon) }}">
    @else
        <link rel="icon" href="{{ asset('img/logo.png') }}">
    @endif

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    @if (!empty($pengaturan->logo) && file_exists(public_path('storage/uploads/' . $pengaturan->logo)))
                        <img class="brand-img d-flex mx-auto object-fit-contain"
                            src="{{ asset('storage/uploads/' . $pengaturan->logo) }}"
                            alt="{{ !empty($pengaturan->nama_situs) ? $pengaturan->nama_situs : '' }}"
                            title="{{ !empty($pengaturan->nama_situs) ? $pengaturan->nama_situs : '' }}">
                    @else
                        <img class="brand-img d-flex mx-auto object-fit-contain"
                            src="{{ asset('img/logo.png') }}"
                            alt="{{ !empty($pengaturan->nama_situs) ? $pengaturan->nama_situs : '' }}"
                            title="{{ !empty($pengaturan->nama_situs) ? $pengaturan->nama_situs : '' }}">
                    @endif
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMenu"
                    aria-controls="navbarMenu" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarMenu">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Home</a>
                        </li>
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Registerasi</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="@if (Auth::user()->user_role == 'admin') {{ route('admin.dashboard') }} @elseif(Auth::user()->user_role == 'user') {{ route('dashboard.index') }} @else {{ url('/') }} @endif">Dashboard</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Keluar
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="p-0">
            @yield('content')
        </main>
        <!-- Footer -->
        <footer id="footer" class="border-top">
            <div class="bg-white py-5">
                <div class="container">
                    <a href="{{ url('/') }}" class="nav-link d-inline-block p-0 mb-3">
                        @if (!empty($pengaturan->logo) && file_exists(public_path('storage/uploads/' . $pengaturan->logo)))
                            <img class="object-fit-contain"
                                src="{{ asset('storage/uploads/' . $pengaturan->logo) }}"
                                alt="{{ !empty($pengaturan->nama_situs) ? $pengaturan->nama_situs : '' }}"
                                title="{{ !empty($pengaturan->nama_situs) ? $pengaturan->nama_situs : '' }}"
                                style="max-width: 300px;height: 100px;">
                        @else
                            <img class="object-fit-contain"
                                src="{{ asset('img/logo.png') }}"
                                alt="{{ !empty($pengaturan->nama_situs) ? $pengaturan->nama_situs : '' }}"
                                title="{{ !empty($pengaturan->nama_situs) ? $pengaturan->nama_situs : '' }}"
                                style="max-width: 300px;height: 100px;">
                        @endif
                    </a>
                    <div class="row justify-content-between">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-1 d-flex">
                                    <a class="text-dark" href="{{ url('/') }}">
                                        <h3>{{ !empty($pengaturan->nama_situs) ? $pengaturan->nama_situs : '' }}</h3>
                                    </a>
                                </li>
                                <li class="mb-1 d-flex">
                                    <address class="text-dark text-decoration-none mb-0">
                                        {{ !empty($pengaturan->alamat) ? $pengaturan->alamat : '' }}</address>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-dark">Kontak</h5>
                            <ul class="list-unstyled">
                                <li class="mb-1 d-flex"><a
                                        href="tel:{{ !empty($pengaturan->telepon) ? $pengaturan->telepon : '' }}"
                                        class="text-dark text-decoration-none">{{ !empty($pengaturan->telepon) ? $pengaturan->telepon : '' }}</a>
                                </li>
                                <li class="mb-1 d-flex"><a
                                        href="mailto:{{ !empty($pengaturan->email) ? $pengaturan->email : '' }}"
                                        class="text-dark text-decoration-none">{{ !empty($pengaturan->email) ? $pengaturan->email : '' }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-dark">
                <div class="container py-2">
                    <p class="text-white text-center m-0">
                        {{ !empty($pengaturan->footer_teks) ? $pengaturan->footer_teks : 'My Website' }}</p>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
