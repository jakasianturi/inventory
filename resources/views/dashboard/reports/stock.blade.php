@extends('dashboard.layouts.app')
@section('title', 'Laporan Ketersediaan Stok')
@section('content')
    <div class="content-wrapper">
        <section class="content-header d-print-none">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Laporan Stok & Kedaluwarsa</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container">
                <div class="card card-outline card-info d-print-none mb-4">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
                                <h6 class="m-0 text-muted"><i class="fas fa-info-circle mr-1"></i> Menampilkan data stok gudang terkini secara <b>Real-Time</b>.</h6>
                            </div>
                            
                            <div class="col-md-6 col-sm-12 text-md-right text-left">
                                <button onclick="window.print()" class="btn btn-secondary btn-sm mr-1">
                                    <i class="fas fa-print"></i> Cetak Laporan
                                </button>
                                
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-download"></i> Export Data
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ route('dashboard.reports.exportStock') }}">
                                            <i class="fas fa-file-excel text-success mr-2"></i> Download Excel
                                        </a>
                                        
                                        <a class="dropdown-item" href="{{ route('dashboard.reports.exportStockPdf') }}">
                                            <i class="fas fa-file-pdf text-danger mr-2"></i> Download PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-none d-print-block text-center mb-4">
                            <h2>TOKO SUSU SEGAR</h2>
                            <h4>Laporan Ketersediaan Stok & Status Kedaluwarsa</h4>
                            <p>Dicetak pada: {{ date('d M Y H:i') }}</p>
                            <hr>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>SKU</th>
                                        <th>Nama Produk</th>
                                        <th>Kategori</th>
                                        <th class="text-center">Total Stok Aktif</th>
                                        <th>Tgl Kedaluwarsa Terdekat (FIFO)</th>
                                        <th>Status Expired</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        @php
                                            // Mengambil batch pertama (karena sudah diurutkan ASC dari Controller)
                                            $nearestBatch = $product->batches->first();
                                            $isWarning = false;
                                            $isDanger = false;
                                            
                                            if($nearestBatch) {
                                                $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($nearestBatch->expiration_date), false);
                                                if($daysLeft < 0) $isDanger = true; // Sudah basi
                                                elseif($daysLeft <= 7) $isWarning = true; // Kurang dari seminggu
                                            }
                                        @endphp
                                    <tr>
                                        <td>{{ $product->sku_code }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name ?? '-' }}</td>
                                        <td class="text-center font-weight-bold {{ $product->total_stock < 10 ? 'text-danger' : 'text-success' }}">
                                            {{ $product->total_stock }}
                                        </td>
                                        <td>
                                            @if($nearestBatch)
                                                {{ \Carbon\Carbon::parse($nearestBatch->expiration_date)->format('d M Y') }}
                                                <small class="text-muted">(Batch: {{ $nearestBatch->batch_number }})</small>
                                            @else
                                                <span class="text-muted">Stok Kosong</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$nearestBatch)
                                                <span class="badge badge-secondary">-</span>
                                            @elseif($isDanger)
                                                <span class="badge badge-danger">SUDAH KEDALUWARSA!</span>
                                            @elseif($isWarning)
                                                <span class="badge badge-warning">Segera Habis (H-{{ intval($daysLeft) }})</span>
                                            @else
                                                <span class="badge badge-success">Aman ({{ intval($daysLeft) }} Hari)</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection