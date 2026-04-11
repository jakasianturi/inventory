@extends('admin.layouts.app')
@section('title', 'Perbaharui Data Akun')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Perbaharui Data Akun</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Perbaharui Data Akun</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @if (session('message'))
                    <div class="alert alert-success alert-dismissible fade show mb-4 border-left-success mb-4"
                        role="alert">
                        {{ session('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4 border-left-danger mb-4"
                        role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <!-- siswa Form -->
                <div class="card card-primary card-outline  mb-4">
                    <div class="card-body">
                        <form method="POST" action="{{ route($url, $user->id ?? '') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @if (!empty($user))
                                @method('put')
                            @endif
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="name">Nama Lengkap</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" id="name" value="{{ old('name') ?? ($user->name ?? '') }}"
                                        placeholder="Nama Lengkap">
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="email">Alamat Email</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                        name="email" id="email"
                                        value="{{ old('email') ?? ($user->email ?? '') }}" placeholder="Email">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <fieldset class="form-group">
                                <div class="row">
                                    <label class="col-form-label col-sm-3 pt-0">Jenis Kelamin</label>
                                    <div class="col-sm-9">
                                        <div class="form-check form-check-inline">
                                            <input name="gender" class="form-check-input" type="radio" id="l"
                                                value="L" @if ((old('gender') ?? ($user->gender ?? '')) == 'L') checked @endif>
                                            <label class="form-check-label" for="l">Laki-laki</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input name="gender" class="form-check-input" type="radio" id="p"
                                                value="P" @if ((old('gender') ?? ($user->gender ?? '')) == 'P') checked @endif>
                                            <label class="form-check-label" for="p">Perempuan</label>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group">
                                <div class="row">
                                    <label class="col-form-label col-sm-3 pt-0">Peran</label>
                                    <div class="col-sm-9">
                                        <div class="form-check form-check-inline">
                                            <input name="user_role" class="form-check-input" type="radio" id="admin"
                                                value="admin" @if ((old('user_role') ?? ($user->user_role ?? '')) == 'admin') checked @endif>
                                            <label class="form-check-label" for="admin">Admin</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input name="user_role" class="form-check-input" type="radio" id="user"
                                                value="user" @if ((old('user_role') ?? ($user->user_role ?? '')) == 'user') checked @endif>
                                            <label class="form-check-label" for="user">User</label>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Password</label>
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
                                <label class="col-sm-3 col-form-label">Konfirmasi Password</label>
                                <div class="col-sm-9">
                                    <input type="password" id="password-confirm" class="form-control"
                                        name="password_confirmation" placeholder="Ketikkan Ulang Password">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-9">
                                    @if (!empty($user))
                                        <a href="{{ route('admin.users.index') }}"
                                            class="btn btn-secondary mr-2 mt-2">Batal</a>
                                    @endif
                                    <button type="submit" class="btn btn-primary mt-2">{{ $button }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
