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

    <!-- Summernote -->
    <link href="{{ asset('vendor/summernote/dist/summernote-bs4.min.css') }}" rel="stylesheet" />

    <!-- Datatables -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/datatables-responsive/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
</head>

<body class="sidebar-mini layout-navbar-fixed layout-fixed">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('home') }}" class="nav-link">Inventory</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto align-items-center">
                <li class="nav-item">
                    <a class="btn btn-danger btn-hovered font-weight-bold px-4" href="#" data-toggle="modal"
                        data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Keluar
                    </a>
                </li>
            </ul>
        </nav>
        <aside class="main-sidebar sidebar-dark-primary bg-gradient-primary elevation-4">
            <a href="{{ route('home') }}" class="brand-link bg-transparent border-bottom">
                @php
                $logoPath = asset('img/img.jpg');

                if (!empty($setting->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->logo)) {
                    $logoPath = asset(\Illuminate\Support\Facades\Storage::disk('public')->url($setting->logo));
                }
                @endphp
                <img class="brand-img d-flex mx-auto object-fit-contain"
                    src="{{ $logoPath }}"
                    alt="{{ !empty($setting->nama_situs) ? $setting->nama_situs : '' }}"
                    title="{{ !empty($setting->nama_situs) ? $setting->nama_situs : '' }}"
                    style="max-height: 40px;">
            </a>
            <div class="sidebar">
                <div class="user-panel border-bottom mt-3 pb-3 mb-3 d-flex align-items-center">
                    <div class="image">
                        @php
                        $avatarPath = asset('img/img.jpg');

                        if (!empty(Auth::user()->avatar) && \Illuminate\Support\Facades\Storage::disk('public')->exists(Auth::user()->avatar)) {
                            $avatarPath = asset(\Illuminate\Support\Facades\Storage::disk('public')->url(Auth::user()->avatar));
                        }
                        @endphp

                        <img class="img-profile rounded-circle elevation-2"
                            src="{{ $avatarPath }}"
                            alt="{{ Auth::user()->name }}">
                    </div>
                    <div class="info">
                        <span class="d-block text-white">{{ Auth::user()->name }}</span>
                        <a href="{{ route('admin.profiles.index') }}" class="badge right align-top" title="Edit"><i
                                class="fas fa-edit text-success mr-2"></i>Edit Profil</a>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        {{-- Dashboard --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}"
                                class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-th"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        {{-- Master Data --}}
                        <li class="nav-header text-uppercase">Master Data</li>
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}"
                                class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Data Akun
                                </p>
                            </a>
                        </li>
                        {{-- Pengaturan --}}
                        <li class="nav-header text-uppercase">Pengaturan</li>
                        <li class="nav-item">
                            <a href="{{ route('admin.settings.index') }}"
                                class="nav-link {{ request()->is('admin/settings') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    Pengaturan Website
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>

            </div>
        </aside>

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
    <script src="{{ asset('vendor/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('vendor/summernote/dist/lang/summernote-id-ID.min.js') }}"></script>
    <script src="{{ asset('vendor/iziToast/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    {{-- AdminLTE --}}
    <script src="{{ asset('vendor/adminlte-3.2.0/dist/js/adminlte.min.js') }}"></script>
    @yield('customStyle')
</body>

</html>