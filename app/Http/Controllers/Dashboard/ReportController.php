<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Exports\StockExport;
use App\Exports\IncomingExport;
use App\Exports\OutgoingExport;
use App\Models\Product;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Laporan 1: Pantau Stok & Tanggal Kedaluwarsa Terdekat
     */
    public function stockReport()
    {
        // Ambil produk beserta relasi batch yang stoknya masih ada
        // Urutkan batch berdasarkan tanggal expired paling dekat
        $products = Product::with(['category', 'batches' => function($query) {
            $query->where('stock_quantity', '>', 0)->orderBy('expiration_date', 'asc');
        }])->get();

        return view('dashboard.reports.stock', compact('products'));
    }

    /**
     * Laporan 2: Riwayat Barang Masuk (Restock)
     */
    public function incomingReport(Request $request)
    {
        // Set default filter tanggal: Awal bulan sampai Akhir bulan ini
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        $transactions = Transaction::with(['user', 'details.product', 'details.batch'])
            ->where('transaction_type', 'in')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('dashboard.reports.incoming', compact('transactions', 'startDate', 'endDate'));
    }

    /**
     * Laporan 3: Riwayat Penjualan / Barang Keluar
     */
    public function outgoingReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        $transactions = Transaction::with(['user', 'details.product', 'details.batch'])
            ->where('transaction_type', 'out')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('dashboard.reports.outgoing', compact('transactions', 'startDate', 'endDate'));
    }

    // --- FUNGSI EXPORT EXCEL ---

    public function exportStock()
    {
        $fileName = 'Laporan_Stok_Susu_' . date('Y-m-d') . '.xlsx';
        return Excel::download(new StockExport, $fileName);
    }

    public function exportIncoming(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        
        $fileName = 'Laporan_Barang_Masuk_' . $startDate . '_sd_' . $endDate . '.xlsx';
        return Excel::download(new IncomingExport($startDate, $endDate), $fileName);
    }

    public function exportOutgoing(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate   = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        
        $fileName = 'Laporan_Penjualan_' . $startDate . '_sd_' . $endDate . '.xlsx';
        return Excel::download(new OutgoingExport($startDate, $endDate), $fileName);
    }

    public function exportStockPdf()
    {
        $products = Product::with(['category', 'batches' => function($query) {
            $query->where('stock_quantity', '>', 0)->orderBy('expiration_date', 'asc');
        }])->get();

        $pdf = Pdf::loadView('dashboard.reports.pdf_stock', compact('products'))
                ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Stok_' . date('Y-m-d') . '.pdf');
    }

    public function exportIncomingPdf(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $transactions = Transaction::with(['user', 'details.product', 'details.batch'])
            ->where('transaction_type', 'in')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        $pdf = Pdf::loadView('dashboard.reports.pdf_incoming', compact('transactions', 'startDate', 'endDate'))
                ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Barang_Masuk_' . $startDate . '.pdf');
    }

    public function exportOutgoingPdf(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $transactions = Transaction::with(['user', 'details.product', 'details.batch'])
            ->where('transaction_type', 'out')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        $pdf = Pdf::loadView('dashboard.reports.pdf_outgoing', compact('transactions', 'startDate', 'endDate'))
                ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Penjualan_' . $startDate . '.pdf');
    }
}