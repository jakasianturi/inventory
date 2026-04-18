@extends('dashboard.layouts.app')
@section('title', 'Laporan Penjualan (Keluar)')
@section('content')
    <div class="content-wrapper">
        <section class="content-header d-print-none">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Laporan Penjualan Barang</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container">
                
                <div class="card card-outline card-info d-print-none mb-4">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-md-8 col-sm-12 mb-2 mb-md-0">
                                <form action="{{ route('dashboard.reports.outgoing') }}" method="GET" class="form-inline">
                                    <label class="mr-2 font-weight-normal"><i class="fas fa-calendar-alt mr-1"></i> Periode:</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm mr-2" value="{{ $startDate }}" required>
                                    <span class="mr-2">s/d</span>
                                    <input type="date" name="end_date" class="form-control form-control-sm mr-3" value="{{ $endDate }}" required>
                                    <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fas fa-search"></i> Tampilkan</button>
                                </form>
                            </div>
                            
                            <div class="col-md-4 col-sm-12 text-md-right text-left">
                                <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm mr-1">
                                    <i class="fas fa-print"></i> Cetak Laporan
                                </button>
                                
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-download"></i> Export Data
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ route('dashboard.reports.exportOutgoing', ['start_date' => $startDate, 'end_date' => $endDate]) }}">
                                            <i class="fas fa-file-excel text-success mr-2"></i> Download Excel
                                        </a>
                                        
                                        <a class="dropdown-item" href="{{ route('dashboard.reports.exportOutgoingPdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}">
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
                            <h4>Laporan Penjualan Barang (Keluar)</h4>
                            <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                            <hr>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-success text-white">
                                    <tr>
                                        <th style="width: 15%">Tanggal</th>
                                        <th style="width: 15%">Kasir</th>
                                        <th style="width: 45%">Barang Terjual</th>
                                        <th style="width: 25%">Catatan Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $trx)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}</td>
                                        <td>{{ $trx->user->name ?? 'Sistem' }}</td>
                                        <td>
                                            <ul class="mb-0 pl-3">
                                                @foreach($trx->details as $detail)
                                                    <li>
                                                        <b>{{ $detail->product->name ?? 'Produk Terhapus' }}</b> : 
                                                        {{ $detail->quantity }} Pcs 
                                                        <small class="text-muted">(Terpotong dr Batch: {{ $detail->batch->batch_number ?? '-' }})</small>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>{{ $trx->notes ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada transaksi penjualan pada periode ini.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection