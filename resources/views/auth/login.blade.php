@extends('layouts.auth')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('home') }}" class="brand-wrap">
                @php
                $logoPath = asset('img/img.jpg');

                if (!empty($setting->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->logo)) {
                    $logoPath = asset(\Illuminate\Support\Facades\Storage::disk('public')->url($setting->logo));
                }
                @endphp
                <img class="d-flex mx-auto mw-100 object-fit-contain"
                    src="{{ $logoPath }}"
                    alt="{{ !empty($setting->nama_situs) ? $setting->nama_situs : '' }}"
                    title="{{ !empty($setting->nama_situs) ? $setting->nama_situs : '' }}"
                    style="width: 200px; height: 60px;object-fit:contain;">
            </a>
        </div>
        @if (session('register_success'))
            <div class="alert alert-success">
                {{ session('register_success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h3 class="text-center mb-4">Login</h3>
                <p class="login-box-msg">Silahkan login terlebih dahulu.</p>
                <form method="POST" action="{{ route('login') }}" class="mb-3">
                    @csrf
                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="email"
                            autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password" placeholder="Kata Sandi">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
                <p class="mb-3 text-center">
                    @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            Lupa kata sandi?
                        </a>
                    @endif
                </p>
            </div>

        </div>
    </div>
@endsection
