<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class StockExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Product::with(['category', 'batches' => function($query) {
            $query->where('stock_quantity', '>', 0)->orderBy('expiration_date', 'asc');
        }])->get();
    }

    public function headings(): array
    {
        return ['SKU', 'Nama Produk', 'Kategori', 'Total Stok Aktif', 'Tgl Kedaluwarsa Terdekat', 'Status'];
    }

    public function map($product): array
    {
        $nearestBatch = $product->batches->first();
        $expDate = 'Stok Kosong';
        $status = '-';

        if ($nearestBatch) {
            $expDate = Carbon::parse($nearestBatch->expiration_date)->format('d M Y') . ' (Batch: ' . $nearestBatch->batch_number . ')';
            $daysLeft = Carbon::now()->diffInDays(Carbon::parse($nearestBatch->expiration_date), false);
            
            if ($daysLeft < 0) $status = 'SUDAH KEDALUWARSA';
            elseif ($daysLeft <= 7) $status = 'Segera Habis (H-' . intval($daysLeft) . ')';
            else $status = 'Aman (' . intval($daysLeft) . ' Hari)';
        }

        return [
            $product->sku_code,
            $product->name,
            $product->category->name ?? '-',
            $product->total_stock,
            $expDate,
            $status
        ];
    }
}