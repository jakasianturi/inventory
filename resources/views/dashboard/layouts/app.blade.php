<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ !empty($setting->nama_situs) ? $setting->nama_situs : 'Sistem Inventori' }}</title>
    <meta name='robots' content='index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' />
    <link rel="canonical" href="{{ url('/') }}" />

    @php
    $faviconPath = asset('img/img.jpg');
    if (!empty($setting->favicon) && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->favicon)) {
        $faviconPath = asset(\Illuminate\Support\Facades\Storage::disk('public')->url($setting->favicon));
    }
    @endphp
    <link rel="icon" href="{{ $faviconPath }}">

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/datatables-responsive/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
</head>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white shadow-sm">
            <div class="container">
                <a href="{{ route('home') }}" class="navbar-brand brand-link border-0">
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
                
                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.index') ? 'active font-weight-bold' : '' }}">
                                <i class="fas fa-home mr-1"></i> Dashboard
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a id="navMasterData" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" 
                               class="nav-link dropdown-toggle {{ request()->routeIs('dashboard.products.*') || request()->routeIs('dashboard.categories.*') ? 'active font-weight-bold' : '' }}">
                                <i class="fas fa-boxes mr-1"></i> Master Data
                            </a>
                            <ul aria-labelledby="navMasterData" class="dropdown-menu border-0 shadow">
                                <li>
                                    <a href="{{ route('dashboard.products.index') }}" class="dropdown-item {{ request()->routeIs('dashboard.products.*') ? 'active' : '' }}">
                                        Katalog Produk
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard.categories.index') }}" class="dropdown-item {{ request()->routeIs('dashboard.categories.*') ? 'active' : '' }}">
                                        Kategori Produk
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a id="navTransaksi" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" 
                               class="nav-link dropdown-toggle {{ request()->routeIs('dashboard.transaction.*') ? 'active font-weight-bold' : '' }}">
                                <i class="fas fa-exchange-alt mr-1"></i> Transaksi
                            </a>
                            <ul aria-labelledby="navTransaksi" class="dropdown-menu border-0 shadow">
                                <li>
                                    <a href="{{ route('dashboard.transaction.createOut') }}" class="dropdown-item {{ request()->routeIs('dashboard.transaction.createOut') ? 'active' : '' }}">
                                        <i class="fas fa-cash-register text-success mr-2"></i> Kasir (POS)
                                    </a>
                                </li>
                                @if(auth()->user()->role == 'admin')
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a href="{{ route('dashboard.transaction.createIn') }}" class="dropdown-item {{ request()->routeIs('dashboard.transaction.createIn') ? 'active' : '' }}">
                                        <i class="fas fa-truck-loading text-primary mr-2"></i> Restock Barang Masuk
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>

                        <li class="nav-item dropdown">
                            <a id="navLaporan" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" 
                               class="nav-link dropdown-toggle {{ request()->routeIs('dashboard.reports.*') ? 'active font-weight-bold' : '' }}">
                                <i class="fas fa-chart-line mr-1"></i> Laporan
                            </a>
                            <ul aria-labelledby="navLaporan" class="dropdown-menu border-0 shadow">
                                <li>
                                    <a href="{{ route('dashboard.reports.stock') }}" class="dropdown-item {{ request()->routeIs('dashboard.reports.stock') ? 'active' : '' }}">
                                        Stok & Kedaluwarsa
                                    </a>
                                </li>
                                @if(auth()->user()->role == 'admin')
                                <li>
                                    <a href="{{ route('dashboard.reports.incoming') }}" class="dropdown-item {{ request()->routeIs('dashboard.reports.incoming') ? 'active' : '' }}">
                                        Pembelian (Masuk)
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard.reports.outgoing') }}" class="dropdown-item {{ request()->routeIs('dashboard.reports.outgoing') ? 'active' : '' }}">
                                        Penjualan (Keluar)
                                    </a>
                                </li>
                                @endif
                            </ul>
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
                            <img class="img-profile rounded-circle elevation-1 mr-2"
                                src="{{ $avatarPath }}"
                                alt="{{ Auth::user()->name }}"
                                style="width: 30px; height: 30px; object-fit: cover;"> 
                            {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right border-0 shadow" aria-labelledby="navbarDropdown">

                            @if(auth()->user()->role == 'admin')
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-users-cog mr-1"></i> Manajemen Sistem
                            </a>
                            @endif
                            <a class="dropdown-item" href="{{ route('dashboard.profiles.index') }}">
                                <i class="fas fa-user text-muted mr-2"></i> Profil Saya
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="#!" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        
        @yield('content')

        <footer class="main-footer">
            <div class="text-center">
                <span class="text-dark">{!! !empty($setting->footer_teks) ? $setting->footer_teks : 'Toko Susu Segar &copy; ' . date('Y') !!}</span>
            </div>
        </footer>
    </div>

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Keluar</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary px-4" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-danger px-4" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Ya, Keluar
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>

    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('vendor/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('vendor/summernote/lang/summernote-id-ID.min.js') }}"></script>
    <script src="{{ asset('vendor/iziToast/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>

    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('vendor/adminlte-3.2.0/dist/js/adminlte.min.js') }}"></script>
    @yield('customScript')
    @yield('customStyle')
</body>
</html>