<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ !empty($setting->nama_situs) ? $setting->nama_situs : '' }}</title>
    <meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' />
    <link rel="canonical" href="{{ url('/') }}" />

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

    <!-- Datatables -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/datatables-responsive/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
</head>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
            <div class="container">
                <a href="{{ route('home') }}" class="navbar-brand brand-link">
                    @php
                    $logoPath = asset('img/img.jpg');

                    if (!empty($setting->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->logo)) {
                        $logoPath = asset(\Illuminate\Support\Facades\Storage::disk('public')->url($setting->logo));
                    }
                    @endphp
                    <img class="brand-img d-flex mx-auto object-fit-contain"
                        src="{{ $logoPath }}"
                        alt="{{ !empty($setting->nama_situs) ? $setting->nama_situs : '' }}"
                        title="{{ !empty($setting->nama_situs) ? $setting->nama_situs : '' }}">
                </a>
                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">Dashboard</a>
                        </li>
                    </ul>
                </div>
                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            @php
                            $avatarPath = asset('img/img.jpg');

                            if (!empty(Auth::user()->avatar) && \Illuminate\Support\Facades\Storage::disk('public')->exists(Auth::user()->avatar)) {
                                $avatarPath = asset(\Illuminate\Support\Facades\Storage::disk('public')->url(Auth::user()->avatar));
                            }
                            @endphp

                            <img class="img-profile rounded-circle elevation-2 mr-3"
                                src="{{ $avatarPath }}"
                                alt="{{ Auth::user()->name }}"> {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('dashboard.profiles.index') }}">Profil</a>
                            <a class="dropdown-item" href="#!" data-toggle="modal" data-target="#logoutModal">
                                Keluar
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        @yield('content')

        <footer class="main-footer">
            <div class="text-center">
                <span class="text-dark">{!! !empty($setting->footer_teks) ? $setting->footer_teks : 'My Website' !!}</span>
            </div>
        </footer>
    </div>
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Apakah Anda yakin ingin Keluar?
                    </h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Silahkan pilih "Keluar" jika Anda yakin ingin keluar.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-hovered font-weight-bold px-4" type="button"
                        data-dismiss="modal">Batal</button>
                    <a class="btn btn-danger btn-hovered font-weight-bold px-4" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        Keluar
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Custom Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>

    <!-- All Script from Vendor -->
    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('vendor/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('vendor/summernote/lang/summernote-id-ID.min.js') }}"></script>
    <script src="{{ asset('vendor/iziToast/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    {{-- AdminLTE --}}
    <script src="{{ asset('vendor/adminlte-3.2.0/dist/js/adminlte.min.js') }}"></script>
    @yield('customScript')
    @yield('customStyle')
</body>

</html>
