<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Barang Masuk</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 18px; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #666; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 2px 0; }
        
        table.main-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.main-table th { background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: left; }
        table.main-table td { border: 1px solid #ccc; padding: 8px; vertical-align: top; word-wrap: break-word; }
        
        .footer { margin-top: 30px; text-align: right; font-style: italic; font-size: 10px; }
        .badge { padding: 2px 5px; background: #eee; border: 1px solid #ddd; border-radius: 3px; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Toko Susu Segar Jaka</h2>
        <p>Laporan Riwayat Barang Masuk (Restock)</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Periode Laporan</td>
            <td width="2%">:</td>
            <td>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</td>
        </tr>
        <tr>
            <td>Tanggal Cetak</td>
            <td>:</td>
            <td>{{ date('d F Y H:i') }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="12%">Tanggal</th>
                <th width="15%">Admin</th>
                <th>Rincian Produk & Batch</th>
                <th width="20%">Catatan/Supplier</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trx)
            <tr>
                <td>{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y') }}</td>
                <td>{{ $trx->user->name ?? 'Admin' }}</td>
                <td>
                    @foreach($trx->details as $detail)
                        <div style="margin-bottom: 5px;">
                            <strong>{{ $detail->product->name }}</strong><br>
                            <span class="badge">Qty: +{{ $detail->quantity }} Pcs</span> 
                            <span class="badge">Batch: {{ $detail->batch->batch_number }}</span><br>
                            <small>Exp: {{ \Carbon\Carbon::parse($detail->batch->expiration_date)->format('d/m/y') }}</small>
                        </div>
                    @endforeach
                </td>
                <td>{{ $trx->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Inventori Toko Susu
    </div>
</body>
</html>