@extends('admin.layouts.app')
@section('title', 'Pengaturan Website')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pengaturan Website</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pengaturan Website</li>
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
            <!-- User List -->
            <div class="card card-primary card-outline  mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.update', $setting->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <h4 class="mb-4">Pengaturan Umum</h4>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="nama_situs">Nama Website</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('nama_situs') is-invalid @enderror"
                                    name="nama_situs" id="nama_situs"
                                    value="{{ old('nama_situs') ?? ($setting->nama_situs ?? '') }}"
                                    placeholder="nama_situs">
                                @error('nama_situs')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Logo</label>
                            <div class="col-sm-4">
                                @php
                                $logoPath = asset('img/img.jpg');

                                if (!empty($setting->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->logo)) {
                                    $logoPath = asset(\Illuminate\Support\Facades\Storage::disk('public')->url($setting->logo));
                                }
                                @endphp
                                <img id="image-previewLogo"
                                    src="{{ $logoPath }}"
                                    class="d-block img-thumbnail rounded p-0 border-0 my-2" width="200">
                                <div class="custom-file position-relative  @error('logo') is-invalid @enderror">
                                    <input type="file" class="custom-file-input fileLogo position-absolute"
                                        name="logo" id="logo"
                                        value="{{ old('logo') ?? ($setting->logo ?? '') }}">
                                    <div class="input-group position-absolute" style="z-index: 999;">
                                        <input type="text" class="form-control" disabled placeholder="Unggah File"
                                            id="fileLogo">
                                        <div class="input-group-append">
                                            <button type="button" class="browseLogo btn btn-primary">Pilih</button>
                                        </div>
                                    </div>
                                </div>
                                @error('logo')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Favicon</label>
                            <div class="col-sm-4">
                                @php
                                $faviconPath = asset('img/img.jpg');

                                if (!empty($setting->favicon) && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->favicon)) {
                                    $faviconPath = asset(\Illuminate\Support\Facades\Storage::disk('public')->url($setting->favicon));
                                }
                                @endphp
                                <img id="image-previewFavicon"
                                    src="{{ $faviconPath }}"
                                    class="d-block img-thumbnail rounded p-0 border-0 my-2" width="200">
                                <div class="custom-file position-relative  @error('favicon') is-invalid @enderror">
                                    <input type="file" class="custom-file-input fileFavicon position-absolute"
                                        name="favicon" id="favicon"
                                        value="{{ old('favicon') ?? ($setting->favicon ?? '') }}">
                                    <div class="input-group position-absolute" style="z-index: 999;">
                                        <input type="text" class="form-control" disabled placeholder="Unggah File"
                                            id="fileFavicon">
                                        <div class="input-group-append">
                                            <button type="button" class="browseFavicon btn btn-primary">Pilih</button>
                                        </div>
                                    </div>
                                </div>
                                @error('favicon')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Backgound Auth</label>
                            <div class="col-sm-4">
                                @php
                                $authBackgroundPath = asset('img/background-auth.jpeg');

                                if (!empty($setting->auth_background) && \Illuminate\Support\Facades\Storage::disk('public')->exists($setting->auth_background)) {
                                    $authBackgroundPath = asset(\Illuminate\Support\Facades\Storage::disk('public')->url($setting->auth_background));
                                }
                                @endphp
                                <img id="image-previewAuthBackground"
                                    src="{{ $authBackgroundPath }}"
                                    class="d-block img-thumbnail rounded p-0 border-0 my-2" width="200">
                                <div class="custom-file position-relative  @error('auth_background') is-invalid @enderror">
                                    <input type="file" class="custom-file-input fileAuthBackground position-absolute"
                                        name="auth_background" id="auth_background"
                                        value="{{ old('auth_background') ?? ($setting->auth_background ?? '') }}">
                                    <div class="input-group position-absolute" style="z-index: 999;">
                                        <input type="text" class="form-control" disabled placeholder="Unggah File"
                                            id="fileAuthBackground">
                                        <div class="input-group-append">
                                            <button type="button" class="browseAuthBackground btn btn-primary">Pilih</button>
                                        </div>
                                    </div>
                                </div>
                                @error('auth_background')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="email">Email</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('email') is-invalid @enderror"
                                    name="email" id="email"
                                    value="{{ old('email') ?? ($setting->email ?? '') }}" placeholder="Email">
                                @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="telepon">Telepon</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('telepon') is-invalid @enderror"
                                    name="telepon" id="telepon"
                                    value="{{ old('telepon') ?? ($setting->telepon ?? '') }}"
                                    placeholder="Telepon">
                                @error('telepon')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="alamat">Alamat</label>
                            <div class="col-sm-9">
                                <textarea id="alamat" class="form-control @error('alamat') is-invalid @enderror" name="alamat"
                                    placeholder="Alamat" rows="4">{{ old('alamat') ?? ($setting->alamat ?? '') }}</textarea>
                                @error('alamat')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="footer_teks">Footer Teks</label>
                            <div class="col-sm-9">
                                <textarea id="footer_teks" class="form-control @error('footer_teks') is-invalid @enderror" name="footer_teks"
                                    placeholder="Footer Teks">{{ old('footer_teks') ?? ($setting->footer_teks ?? '') }}</textarea>
                                @error('footer_teks')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-9">
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
        // Logo
        document.querySelector(".browseLogo").addEventListener("click", triggerInput);

        function triggerInput() {
            var file = $(this).parents().find(".fileLogo");
            file.trigger("click");
        };
        document.getElementById("logo").addEventListener("change", changeFileNameLogo);

        function changeFileNameLogo(e) {
            var fileName = e.target.files[0].name;
            $("#fileLogo").val(fileName);
        };
        document.getElementById("logo").addEventListener("change", imagePreviewLogo);

        function imagePreviewLogo() {
            let reader = new FileReader();
            reader.onload = function() {
                let output = document.getElementById('image-previewLogo');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }


    });
    $(document).ready(function() {
        // Favicon
        document.querySelector(".browseFavicon").addEventListener("click", triggerInput);

        function triggerInput() {
            var file = $(this).parents().find(".fileFavicon");
            file.trigger("click");
        };
        document.getElementById("favicon").addEventListener("change", changeFileNameFavicon);

        function changeFileNameFavicon(e) {
            var fileName = e.target.files[0].name;
            $("#fileFavicon").val(fileName);
        };
        document.getElementById("favicon").addEventListener("change", imagePreviewFavicon);

        function imagePreviewFavicon() {
            let reader = new FileReader();
            reader.onload = function() {
                let output = document.getElementById('image-previewFavicon');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    });
    $(document).ready(function() {
        // AuthBackground
        document.querySelector(".browseAuthBackground").addEventListener("click", triggerInput);

        function triggerInput() {
            var file = $(this).parents().find(".fileAuthBackground");
            file.trigger("click");
        };
        document.getElementById("auth_background").addEventListener("change", changeFileNameAuthBackground);

        function changeFileNameAuthBackground(e) {
            var fileName = e.target.files[0].name;
            $("#fileAuthBackground").val(fileName);
        };
        document.getElementById("auth_background").addEventListener("change", imagePreviewAuthBackground);

        function imagePreviewAuthBackground() {
            let reader = new FileReader();
            reader.onload = function() {
                let output = document.getElementById('image-previewAuthBackground');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    });
</script>
@endsection