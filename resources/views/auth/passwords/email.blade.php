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
        <div class="card">

            <div class="card-body">
                <h3 class="text-center mb-4">{{ __('Reset Password') }}</h3>
                <p class="login-box-msg text-sm">
                    {{ __('Just enter your email address below and we will send you a link to reset your password!') }}
                </p>
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}" required>
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
                    <button type="submit" class="btn btn-primary btn-block">{{ __('Reset Password') }}</button>
                </form>
                <div class="text-center mt-2">
                    <p>{{ __('Go back to') }}<a class="text-decoration-none" href="{{ route('home') }}">
                            {{ __('Home') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
