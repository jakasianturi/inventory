@extends('admin.layouts.app')
@section('title', 'Edit Profil')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Profil</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Edit Profil</li>
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
            <!-- Item Form -->
            <div class="card card-primary card-outline  mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.profiles.update') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        @php
                            $user = Auth::user();
                        @endphp
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="nama">Nama</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" id="name" value="{{ old('name') ?? ($user->name ?? '') }}"
                                    placeholder="name">
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
                                    name="email" id="email" value="{{ $user->email }}">
                                @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Avatar</label>
                            <div class="col-sm-4">
                                @php
                                $avatarPath = asset('img/img.jpg');

                                if (!empty($user->avatar) && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                                    $avatarPath = asset(\Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar));
                                }
                                @endphp
                                <img id="image-previewAvatar"
                                    src="{{ $avatarPath }}"
                                    class="d-block img-thumbnail rounded p-0 border-0 my-2" width="200">
                                <div class="custom-file position-relative  @error('avatar') is-invalid @enderror">
                                    <input type="file" class="custom-file-input fileAvatar position-absolute"
                                        name="avatar" id="avatar"
                                        value="{{ old('avatar') ?? ($user->avatar ?? '') }}">
                                    <div class="input-group position-absolute" style="z-index: 999;">
                                        <input type="text" class="form-control" disabled placeholder="Unggah File"
                                            id="fileAvatar">
                                        <div class="input-group-append">
                                            <button type="button" class="browseAvatar btn btn-primary">Pilih</button>
                                        </div>
                                    </div>
                                </div>
                                @error('avatar')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <h5 class="text-bold text-dark">Ubah Password?</h5>
                        <div class="form-group row">
                            <div class="col-sm-3">Password</div>
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
                            <div class="col-sm-3">Konfirmasi Password</div>
                            <div class="col-sm-9">
                                <input type="password" id="password-confirm" class="form-control"
                                    name="password_confirmation" placeholder="Ketikkan Ulang Password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-9">
                                <a href="{{ route('admin.profiles.index') }}"
                                    class="btn btn-secondary mr-2 mt-2">Batal</a>
                                <button type="submit" class="btn btn-primary mt-2">Perbaharui</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@section('customStyle')
<style>
    .file {
        visibility: hidden;
        position: absolute;
    }
</style>
<script>
    $(document).ready(function() {
        // Avatar
        document.querySelector(".browseAvatar").addEventListener("click", triggerInput);

        function triggerInput() {
            var file = $(this).parents().find(".fileAvatar");
            file.trigger("click");
        };
        document.getElementById("avatar").addEventListener("change", changeFileNameAvatar);

        function changeFileNameAvatar(e) {
            var fileName = e.target.files[0].name;
            $("#fileAvatar").val(fileName);
        };
        document.getElementById("avatar").addEventListener("change", imagePreviewAvatar);

        function imagePreviewAvatar() {
            let reader = new FileReader();
            reader.onload = function() {
                let output = document.getElementById('image-previewAvatar');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    });
</script>
@endsection