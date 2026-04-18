<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #28a745; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 18px; color: #28a745; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #666; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        
        table.main-table { width: 100%; border-collapse: collapse; }
        table.main-table th { background-color: #e9f7ef; border: 1px solid #c3e6cb; padding: 8px; text-align: left; }
        table.main-table td { border: 1px solid #c3e6cb; padding: 8px; vertical-align: top; }
        
        .total-row { background-color: #f8f9fa; font-weight: bold; }
        .badge-out { padding: 2px 5px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 3px; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Toko Susu Segar Jaka</h2>
        <p>Laporan Riwayat Penjualan Barang (Keluar - FIFO)</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Periode Penjualan</td>
            <td width="2%">:</td>
            <td>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</td>
        </tr>
        <tr>
            <td>Total Transaksi</td>
            <td>:</td>
            <td>{{ $transactions->count() }} Transaksi</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="12%">Tanggal</th>
                <th width="15%">Kasir</th>
                <th>Produk Terjual</th>
                <th width="20%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trx)
            <tr>
                <td>{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y') }}</td>
                <td>{{ $trx->user->name ?? 'Sistem' }}</td>
                <td>
                    @foreach($trx->details as $detail)
                        <div style="margin-bottom: 4px;">
                            {{ $detail->product->name }} 
                            <strong>(x{{ $detail->quantity }})</strong><br>
                            <small class="text-muted">Diambil dari Batch: {{ $detail->batch->batch_number }}</small>
                        </div>
                    @endforeach
                </td>
                <td>{{ $trx->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px;">
        <table width="100%">
            <tr>
                <td width="70%"></td>
                <td align="center">
                    Tangerang, {{ date('d M Y') }}<br>
                    Manajer Operasional<br><br><br><br>
                    (................................)
                </td>
            </tr>
        </table>
    </div>
</body>
</html>