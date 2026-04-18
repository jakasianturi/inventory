@extends('dashboard.layouts.app')
@section('title', 'Kasir / Barang Keluar')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Sistem Kasir (POS)</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Kasir</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container">
                
                <div class="alert alert-warning alert-dismissible fade show border-left-warning mb-4" role="alert">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> SOP Pengambilan Barang</h5>
                    Sistem akan memotong stok secara <b>FIFO</b> (First-In First-Out). Pastikan Anda memberikan produk dengan <strong>tanggal kedaluwarsa paling dekat (rak paling depan)</strong> kepada pelanggan.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

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
                        <form method="POST" action="{{ route('dashboard.transaction.storeOut') }}">
                            @csrf
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="transaction_date">Tanggal Transaksi</label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control bg-light" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" readonly>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="notes">Catatan Transaksi</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="notes" id="notes" value="{{ old('notes') }}" placeholder="Contoh: Pembeli minta dibungkus plastik">
                                </div>
                            </div>

                            <hr class="mt-4 mb-4">
                            <h5 class="mb-3">Keranjang Belanja</h5>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="out-items-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Pilih Produk <span class="text-danger">*</span></th>
                                            <th style="width: 20%">Jumlah (Pcs) <span class="text-danger">*</span></th>
                                            <th style="width: 5%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="out-items-body">
                                        @if(old('items'))
                                            @foreach(old('items') as $index => $item)
                                                @php
                                                    // Cek apakah ada error di product_id atau quantity pada baris ini
                                                    $hasError = $errors->has("items.$index.product_id") || $errors->has("items.$index.quantity");
                                                @endphp
                                                
                                                <tr class="{{ $hasError ? 'table-danger' : '' }}">
                                                    <td>
                                                        <select name="items[{{ $index }}][product_id]" class="form-control select2 @error("items.$index.product_id") is-invalid @enderror" required>
                                                            <option value="">-- Ketik Nama/SKU Produk --</option>
                                                            @foreach($products as $product)
                                                                <option value="{{ $product->id }}" {{ $item['product_id'] == $product->id ? 'selected' : '' }}>
                                                                    {{ $product->sku_code }} - {{ $product->name }} (Sisa: {{ $product->total_stock }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("items.$index.product_id")
                                                            <div class="invalid-feedback d-block font-weight-bold">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control @error("items.$index.quantity") is-invalid @enderror" min="1" value="{{ $item['quantity'] }}" required>
                                                        @error("items.$index.quantity")
                                                            <div class="invalid-feedback d-block font-weight-bold">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-sm remove-row" {{ count(old('items')) <= 1 ? 'disabled' : '' }}><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td>
                                                    <select name="items[0][product_id]" class="form-control select2" required>
                                                        <option value="">-- Ketik Nama/SKU Produk --</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}">
                                                                {{ $product->sku_code }} - {{ $product->name }} (Sisa: {{ $product->total_stock }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" name="items[0][quantity]" class="form-control" min="1" placeholder="0" required></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            
                            <button type="button" class="btn btn-info btn-sm mb-4" id="add-out-item">
                                <i class="fas fa-cart-plus mr-1"></i> Tambah Belanjaan
                            </button>
                            
                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <a href="{{ route('dashboard.transaction.createOut') }}" class="btn btn-secondary mr-2">Reset Kasir</a>
                                    <button type="submit" class="btn btn-primary px-4">Selesaikan Transaksi <i class="fas fa-arrow-right ml-1"></i></button>
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
    <script>
        $(document).ready(function() {
            // Membaca jumlah baris saat ini untuk indeks berikutnya agar tidak menimpa jika ada old input
            let outIndex = {{ old('items') ? count(old('items')) : 1 }};

            if ($.fn.select2) {
                $('.select2').select2({ theme: 'bootstrap4' });
            }

            // Fitur Tambah Baris Keranjang
            $('#add-out-item').click(function() {
                let newRow = `
                    <tr>
                        <td>
                            <select name="items[${outIndex}][product_id]" class="form-control select2" required>
                                <option value="">-- Ketik Nama/SKU Produk --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->sku_code }} - {{ $product->name }} (Sisa: {{ $product->total_stock }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[${outIndex}][quantity]" class="form-control" min="1" placeholder="0" required></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                $('#out-items-body').append(newRow);
                
                if ($.fn.select2) {
                    $('#out-items-body').find('.select2').last().select2({ theme: 'bootstrap4' });
                }
                
                // Aktifkan kembali tombol hapus jika baris > 1
                if ($('#out-items-body tr').length > 1) {
                    $('.remove-row').removeAttr('disabled');
                }
                
                outIndex++;
            });

            // Fitur Hapus Baris Keranjang
            $('#out-items-table').on('click', '.remove-row', function() {
                if ($('#out-items-body tr').length > 1) {
                    $(this).closest('tr').remove();
                }
                // Nonaktifkan tombol hapus jika sisa 1 baris
                if ($('#out-items-body tr').length === 1) {
                    $('.remove-row').attr('disabled', 'disabled');
                }
            });
        });
    </script>
@endsection