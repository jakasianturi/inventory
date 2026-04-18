<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .badge { padding: 3px 6px; border-radius: 4px; color: white; }
        .bg-danger { background-color: #dc3545; }
        .bg-success { background-color: #28a745; }
    </style>
</head>
<body>
    <div class="header">
        <h2>TOKO SUSU SEGAR</h2>
        <h3>Laporan Ketersediaan Stok</h3>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Tgl Kedaluwarsa Terdekat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                @php $nearest = $product->batches->first(); @endphp
                <tr>
                    <td>{{ $product->sku_code }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>{{ $product->total_stock }}</td>
                    <td>{{ $nearest ? $nearest->expiration_date : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>