@extends('dashboard.layouts.app')
@section('title', empty($product) ? 'Tambah Produk Baru' : 'Perbaharui Data Produk')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ empty($product) ? 'Tambah Produk Baru' : 'Perbaharui Data Produk' }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.products.index') }}">Produk</a></li>
                            <li class="breadcrumb-item active">{{ empty($product) ? 'Tambah' : 'Edit' }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container">
                @if (session('message'))
                    <div class="alert alert-success alert-dismissible fade show mb-4 border-left-success" role="alert">
                        {{ session('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4 border-left-danger" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card card-primary card-outline mb-4">
                    <div class="card-body">
                        <form method="POST" action="{{ empty($product) ? route('dashboard.products.store') : route('dashboard.products.update', $product->id) }}">
                            @csrf
                            
                            @if (!empty($product))
                                @method('put')
                            @endif

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="sku_code">Kode SKU <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control @error('sku_code') is-invalid @enderror"
                                        name="sku_code" id="sku_code" value="{{ old('sku_code') ?? ($product->sku_code ?? '') }}"
                                        placeholder="Contoh: SKU-MILK-001" required>
                                    @error('sku_code')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="name">Nama Produk <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" id="name" value="{{ old('name') ?? ($product->name ?? '') }}"
                                        placeholder="Nama Lengkap Produk" required>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="category_id">Kategori <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ (old('category_id') ?? ($product->category_id ?? '')) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="base_price">Harga Jual (Rp) <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="number" min="0"  class="form-control @error('base_price') is-invalid @enderror"
                                        name="base_price" id="base_price" value="{{ old('base_price') ?? ($product->base_price ?? '') }}"
                                        placeholder="Contoh: 15000" required>
                                    @error('base_price')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="description">Deskripsi Lengkap</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                        name="description" id="description" rows="4" 
                                        placeholder="Informasi detail mengenai produk ini (opsional)...">{{ old('description') ?? ($product->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-9 offset-sm-3">
                                    <a href="{{ route('dashboard.products.index') }}" class="btn btn-secondary mr-2 mt-2">Batal</a>
                                    <button type="submit" class="btn btn-primary mt-2">
                                        {{ empty($product) ? 'Simpan Produk Baru' : 'Perbaharui Produk' }}
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