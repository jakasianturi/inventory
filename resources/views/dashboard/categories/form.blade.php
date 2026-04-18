@extends('dashboard.layouts.app')
@section('title', empty($category) ? 'Tambah Kategori Baru' : 'Perbaharui Data Kategori')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ empty($category) ? 'Tambah Kategori Baru' : 'Perbaharui Data Kategori' }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.categories.index') }}">Kategori</a></li>
                            <li class="breadcrumb-item active">{{ empty($category) ? 'Tambah' : 'Edit' }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container">
                <div class="card card-primary card-outline mb-4">
                    <div class="card-body">
                        <form method="POST" action="{{ empty($category) ? route('dashboard.categories.store') : route('dashboard.categories.update', $category->id) }}">
                            @csrf
                            
                            @if (!empty($category))
                                @method('put')
                            @endif

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="name">Nama Kategori <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" id="name" value="{{ old('name') ?? ($category->name ?? '') }}"
                                        placeholder="Contoh: Susu Cair, Susu Bubuk, Yogurt" required>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="description">Deskripsi (Opsional)</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                        name="description" id="description" rows="4" 
                                        placeholder="Keterangan mengenai kategori ini...">{{ old('description') ?? ($category->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-9 offset-sm-3">
                                    <a href="{{ route('dashboard.categories.index') }}" class="btn btn-secondary mr-2 mt-2">Batal</a>
                                    <button type="submit" class="btn btn-primary mt-2">
                                        {{ empty($category) ? 'Simpan Kategori Baru' : 'Perbaharui Kategori' }}
                                    </button>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection