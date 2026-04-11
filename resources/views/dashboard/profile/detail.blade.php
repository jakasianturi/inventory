@extends('dashboard.layouts.app')
@section('title', 'Profil')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ __('Profil') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                    href="{{ route('dashboard.index') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Profil') }}</li>
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
                <!-- Criteria List -->
                <div class="card card-primary card-outline  mb-4">
                    <div class="card-body">
                        <div class="row gutters-sm">
                            <div class="col-lg-4">
                                <div class="card card-primary card-outline  mb-4">
                                    <div class="d-flex justify-content-center align-items-center mt-4">
                                        <form method="post">
                                            <figure
                                                class="image_area position-relative rounded-circle font-weight-bold overflow-hidden mx-2"
                                                style="height: 150px; width: 150px;" data-initial="User">
                                                <img src="{{ !empty($user->avatar) ? asset('storage/users/' . $user->avatar) : asset('img/undraw_male_avatar_323b.svg') }}"
                                                    id="avatar" class="d-block w-100 h-100" />
                                                <div class="overlay d-flex justify-content-center align-items-center">
                                                    <span id="uploadimageModalOpen" class="text-center text-dark m-0 p-2"
                                                        style="font-size: 0.875rem;" data-toggle="modal"
                                                        data-target="#uploadimageModal">{{ __('Klik untuk mengubah foto profil') }}</span>
                                                </div>
                                            </figure>
                                        </form>
                                        <!-- Modal -->
                                        <div class="modal fade" id="uploadimageModal" tabindex="-1" role="dialog"
                                            aria-labelledby="uploadimageModalTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                                            {{ __('Ubah Foto Profil') }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-lg-8">
                                                                <div id="image_demo"></div>
                                                            </div>
                                                            <div class="col-lg-4 text-center">
                                                                <button class="btn btn-primary position-relative">
                                                                    <span>{{ __('Pilih Gambar') }}</span>
                                                                    <input type="file" name="upload_image"
                                                                        id="upload_image" accept="image/*" />
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">{{ __('Batal') }}</button>
                                                        <button id="crop" type="button"
                                                            class="btn btn-primary crop_image">{{ __('Potong dan Simpan') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="text-center">
                                                    <h5 class="font-weight-bold">{{ $user->name ?? '' }}</h5>
                                                    <p>{{ ucfirst(trans($user->user_role)) ?? '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="card card-primary card-outline   mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">{{ __('Nama Lengkap') }}</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                {{ $user->name ?? '' }}
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">{{ __('Email') }}</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                {{ $user->email ?? '' }}
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">{{ __('Jenis Kelamin') }}</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                @!empty($user->gender)
                                                    {{ ucfirst(trans($user->gender)) ?? '' }}
                                                @endisset
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">{{ __('No. Telepon') }}</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                {{ $user->phone ?? '' }}
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">{{ __('Alamat') }}</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                {{ $user->address ?? '' }}
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <a class="btn btn-primary"
                                                    href="{{ route('dashboard.profiles.edit', $user->id) }}">{{ $button }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
