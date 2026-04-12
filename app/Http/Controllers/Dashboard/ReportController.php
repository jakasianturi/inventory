<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\ProductBatch;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * 1. Laporan Stok dan Kedaluwarsa
     * Menampilkan semua batch yang masih memiliki stok, diurutkan dari yang paling cepat expired.
     */
    public function stockReport(Request $request)
    {
        // Parameter filter opsional: 'all', 'expiring_soon' (misal < 7 hari), 'expired'
        $filter = $request->query('status', 'all');

        $query = ProductBatch::with('product.category')
            ->where('stock_quantity', '>', 0);

        if ($filter == 'expiring_soon') {
            $query->whereBetween('expiration_date', [Carbon::today(), Carbon::today()->addDays(7)]);
        } elseif ($filter == 'expired') {
            $query->where('expiration_date', '<', Carbon::today());
        }

        // Urutkan dari yang paling mendekati masa kedaluwarsa
        $batches = $query->orderBy('expiration_date', 'asc')->paginate(20);

        return view('reports.stock', compact('batches', 'filter'));
    }

    /**
     * 2. Laporan Barang Masuk
     */
    public function incomingReport(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Mengambil transaksi tipe 'in', load relasi detail, produk, dan user pembuatnya
        $transactions = Transaction::with(['user', 'details.product', 'details.batch'])
            ->where('transaction_type', 'in')
            // Filter rentang tanggal jika diisi
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('transaction_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('transaction_date', '<=', $endDate);
            })
            ->latest('transaction_date')
            ->paginate(20);

        return view('reports.incoming', compact('transactions', 'startDate', 'endDate'));
    }

    /**
     * 3. Laporan Barang Keluar (Penjualan)
     */
    public function outgoingReport(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Mengambil transaksi tipe 'out'
        $transactions = Transaction::with(['user', 'details.product', 'details.batch'])
            ->where('transaction_type', 'out')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('transaction_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('transaction_date', '<=', $endDate);
            })
            ->latest('transaction_date')
            ->paginate(20);

        return view('reports.outgoing', compact('transactions', 'startDate', 'endDate'));
    }
}
