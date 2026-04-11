@extends('layouts.auth')

@section('content')
    <div class="register-box">
        <div class="register-logo">
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
        <div class="card">
            <div class="card-body">
                <h3 class="text-center mb-4">Registrasi</h3>
                <p class="login-box-msg">Registrasi akun baru.</p>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="input-group mb-3">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                            placeholder="Nama">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
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
                            name="password" required autocomplete="new-password" placeholder="Kata sandi">
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
                    <div class="input-group mb-3">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                            required autocomplete="new-password" placeholder="Ketik ulang kata sandi">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Registrasi</button>
                </form>
                {{-- <p class="text-center my-3">
                    Sudah punya akun? Silahkan masuk ke halaman <a href="{{ route('login') }}" class="text-center">Login</a>
                </p> --}}
                <p class="text-center mt-3">Kembali ke<a class="text-decoration-none" href="{{ url('/') }}">
                        Home
                    </a>
                </p>
            </div>

        </div>
    </div>
@endsection
