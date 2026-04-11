@extends('dashboard.layouts.app')
@section('title', 'Edit Profil')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ __('Edit Profil') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                    href="{{ route('dashboard.index') }}">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Edit Profil') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container">
                @if (session('message'))
                    <div class="alert alert-success alert-dismissible fade show mb-4 border-left-success mb-4"
                        role="alert">
                        {{ session('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <!-- Profile Form -->
                <div class="card card-primary card-outline  mb-4">
                    <div class="card-body">
                        <form method="POST" action="{{ route('dashboard.profiles.update', $user->id) }}">
                            @csrf
                            @method('put')
                            @php
                                $user = Auth::user();
                            @endphp
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-normal" for="name">{{ __('Nama Lengkap') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" id="name" value="{{ old('name') ?? ($user->name ?? '') }}"
                                        placeholder="Nama lengkap">
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-normal" for="email">{{ __('Alamat Email') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                        name="email" id="email" value="{{ $user->email }}">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-normal" for="address">{{ __('Alamat') }}</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control @error('address') is-invalid @enderror" name="address" id="address" rows="3">{{ old('address') ?? ($user->address ?? '') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label font-weight-normal" for="phone">{{ __('No. Telepon') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" id="phone" value="{{ old('phone') ?? ($user->phone ?? '') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <fieldset class="form-group">
                                <div class="row">
                                    <legend class="col-form-label col-sm-3 pt-0 font-weight-normal">{{ __('Jenis Kelamin') }}</legend>
                                    <div class="col-sm-9">
                                        <div class="form-check form-check-inline">
                                            <input name="gender" class="form-check-input" type="radio" id="l"
                                                value="L" @if ((old('gender') ?? ($user->gender ?? '')) == 'L') checked @endif>
                                            <label class="form-check-label" for="L">{{ __('Laki-Laki') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input name="gender" class="form-check-input" type="radio" id="p"
                                                value="P" @if ((old('gender') ?? ($user->gender ?? '')) == 'P') checked @endif>
                                            <label class="form-check-label" for="P">{{ __('Perempuan') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <h5 class="font-weight-normal text-dark">{{ __('Ubah Password') }}?</h5>
                            <div class="form-group row">
                                <div class="col-sm-3 font-weight-normal">{{ __('Password') }}</div>
                                <div class="col-sm-9">
                                    <input type="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        placeholder="Password">
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-3 text-normal">{{ __('Konfirmasi Password') }}</div>
                                <div class="col-sm-9">
                                    <input type="password" id="password-confirm" class="form-control"
                                        name="password_confirmation" placeholder="Ketikkan Ulang Password">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-9">
                                    <a href="{{ route('dashboard.profiles.index') }}"
                                        class="btn btn-secondary mr-2 mt-2">{{ __('Batal') }}</a>
                                    <button type="submit" class="btn btn-primary mt-2">{{ __('Perbaharui') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
