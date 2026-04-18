@extends('dashboard.layouts.app')
@section('title', 'Restock Barang Masuk')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Restock Barang Masuk</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Barang Masuk</li>
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
                        <form method="POST" action="{{ route('dashboard.transaction.storeIn') }}">
                            @csrf
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="transaction_date">Tanggal Transaksi <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="notes">Catatan / Supplier</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="notes" id="notes" value="{{ old('notes') }}" placeholder="Contoh: Pembelian dari PT. Susu Segar">
                                </div>
                            </div>

                            <hr class="mt-4 mb-4">
                            <h5 class="mb-3">Detail Item Barang Masuk</h5>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="items-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Produk <span class="text-danger">*</span></th>
                                            <th style="width: 15%">Qty (Pcs) <span class="text-danger">*</span></th>
                                            <th style="width: 20%">No. Batch <span class="text-danger">*</span></th>
                                            <th style="width: 20%">Tgl Kedaluwarsa <span class="text-danger">*</span></th>
                                            <th style="width: 5%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-body">
                                        
                                        @if(old('items'))
                                            @foreach(old('items') as $index => $item)
                                                @php
                                                    // Deteksi error di setiap field pada baris ini
                                                    $hasError = $errors->has("items.$index.product_id") || 
                                                                $errors->has("items.$index.quantity") || 
                                                                $errors->has("items.$index.batch_number") || 
                                                                $errors->has("items.$index.expiration_date");
                                                @endphp
                                                <tr class="{{ $hasError ? 'table-danger' : '' }}">
                                                    <td>
                                                        <select name="items[{{ $index }}][product_id]" class="form-control select2 @error("items.$index.product_id") is-invalid @enderror" required>
                                                            <option value="">-- Pilih Produk --</option>
                                                            @foreach($products as $product)
                                                                <option value="{{ $product->id }}" {{ (isset($item['product_id']) && $item['product_id'] == $product->id) ? 'selected' : '' }}>
                                                                    {{ $product->sku_code }} - {{ $product->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("items.$index.product_id") <div class="invalid-feedback d-block font-weight-bold">{{ $message }}</div> @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control @error("items.$index.quantity") is-invalid @enderror" min="1" value="{{ $item['quantity'] ?? '' }}" required>
                                                        @error("items.$index.quantity") <div class="invalid-feedback d-block font-weight-bold">{{ $message }}</div> @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[{{ $index }}][batch_number]" class="form-control @error("items.$index.batch_number") is-invalid @enderror" value="{{ $item['batch_number'] ?? '' }}" required>
                                                        @error("items.$index.batch_number") <div class="invalid-feedback d-block font-weight-bold">{{ $message }}</div> @enderror
                                                    </td>
                                                    <td>
                                                        <input type="date" name="items[{{ $index }}][expiration_date]" class="form-control @error("items.$index.expiration_date") is-invalid @enderror" value="{{ $item['expiration_date'] ?? '' }}" required>
                                                        @error("items.$index.expiration_date") <div class="invalid-feedback d-block font-weight-bold">{{ $message }}</div> @enderror
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
                                                        <option value="">-- Pilih Produk --</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}">{{ $product->sku_code }} - {{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" name="items[0][quantity]" class="form-control" min="1" placeholder="0" required></td>
                                                <td><input type="text" name="items[0][batch_number]" class="form-control" placeholder="B-001" required></td>
                                                <td><input type="date" name="items[0][expiration_date]" class="form-control" required></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove-row" disabled><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                            
                            <button type="button" class="btn btn-success btn-sm mb-4" id="add-item">
                                <i class="fas fa-plus mr-1"></i> Tambah Item Baru
                            </button>
                            
                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <a href="{{ route('dashboard.transaction.createIn') }}" class="btn btn-secondary mr-2">Reset Form</a>
                                    <button type="submit" class="btn btn-primary">Simpan Transaksi Masuk</button>
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
            // Melanjutkan nomor index jika ada old data
            let itemIndex = {{ old('items') ? count(old('items')) : 1 }};

            if ($.fn.select2) {
                $('.select2').select2({ theme: 'bootstrap4' });
            }

            $('#add-item').click(function() {
                let newRow = `
                    <tr>
                        <td>
                            <select name="items[${itemIndex}][product_id]" class="form-control select2" required>
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->sku_code }} - {{ $product->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="1" placeholder="0" required></td>
                        <td><input type="text" name="items[${itemIndex}][batch_number]" class="form-control" placeholder="B-001" required></td>
                        <td><input type="date" name="items[${itemIndex}][expiration_date]" class="form-control" required></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                $('#items-body').append(newRow);
                
                if ($.fn.select2) {
                    $('#items-body').find('.select2').last().select2({ theme: 'bootstrap4' });
                }
                
                // Aktifkan kembali tombol hapus jika baris > 1
                if ($('#items-body tr').length > 1) {
                    $('.remove-row').removeAttr('disabled');
                }
                
                itemIndex++;
            });

            $('#items-table').on('click', '.remove-row', function() {
                if ($('#items-body tr').length > 1) {
                    $(this).closest('tr').remove();
                }
                if ($('#items-body tr').length === 1) {
                    $('.remove-row').attr('disabled', 'disabled');
                }
            });
        });
    </script>
@endsection