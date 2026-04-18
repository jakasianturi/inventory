<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class IncomingExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $startDate, $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Transaction::with(['user', 'details.product', 'details.batch'])
            ->where('transaction_type', 'in')
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Admin', 'Catatan/Supplier', 'Rincian Produk yang Masuk'];
    }

    public function map($trx): array
    {
        $details = [];
        foreach($trx->details as $detail) {
            $productName = $detail->product->name ?? 'Terhapus';
            $batchNum = $detail->batch->batch_number ?? '-';
            $details[] = "{$productName} (+{$detail->quantity} Pcs, Batch: {$batchNum})";
        }

        return [
            Carbon::parse($trx->transaction_date)->format('d M Y'),
            $trx->user->name ?? 'Admin',
            $trx->notes ?? '-',
            implode(" | ", $details) // Digabung agar rapi di 1 cell Excel
        ];
    }
}