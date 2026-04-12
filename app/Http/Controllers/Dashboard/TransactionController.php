<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\ProductBatch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Menyimpan Transaksi Barang Masuk (Restock)
     */
    public function storeIn(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'notes'            => 'nullable|string',
            'items'            => 'required|array',
            'items.*.product_id'      => 'required|exists:products,id',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.batch_number'    => 'required|string',
            'items.*.production_date' => 'nullable|date',
            'items.*.expiration_date' => 'required|date|after:today',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Buat Header Transaksi
                $transaction = Transaction::create([
                    'user_id'          => Auth::id() ?? 1, // Ganti dengan Auth::id() jika auth sudah aktif
                    'transaction_type' => 'in',
                    'transaction_date' => $request->transaction_date,
                    'notes'            => $request->notes,
                ]);

                // 2. Looping Item yang masuk
                foreach ($request->items as $item) {
                    // Buat Batch Baru karena ini barang masuk
                    $batch = ProductBatch::create([
                        'product_id'      => $item['product_id'],
                        'batch_number'    => $item['batch_number'],
                        'stock_quantity'  => $item['quantity'],
                        'production_date' => $item['production_date'] ?? null,
                        'expiration_date' => $item['expiration_date'],
                    ]);

                    // Catat ke Detail Transaksi
                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $item['product_id'],
                        'batch_id'       => $batch->id,
                        'quantity'       => $item['quantity'],
                    ]);
                }
            });

            return response()->json(['message' => 'Transaksi Barang Masuk berhasil disimpan!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menyimpan Transaksi Barang Keluar (Penjualan) - LOGIKA FIFO
     */
    public function storeOut(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'notes'            => 'nullable|string',
            'items'            => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Buat Header Transaksi
                $transaction = Transaction::create([
                    'user_id'          => Auth::id() ?? 1,
                    'transaction_type' => 'out',
                    'transaction_date' => $request->transaction_date,
                    'notes'            => $request->notes,
                ]);

                // 2. Looping Item yang dijual/keluar
                foreach ($request->items as $item) {
                    $qtyNeeded = $item['quantity'];
                    $productId = $item['product_id'];

                    // Validasi Total Stok Keseluruhan sebelum memproses FIFO
                    $product = Product::findOrFail($productId);
                    if ($product->total_stock < $qtyNeeded) {
                        throw new \Exception("Stok untuk produk {$product->name} tidak mencukupi. Stok tersedia: {$product->total_stock}");
                    }

                    // Ambil batch yang stoknya > 0, urutkan dari tanggal expired terdekat (FIFO)
                    // (Memanfaatkan scopeAvailableFifo yang kita buat di Model ProductBatch)
                    $batches = ProductBatch::where('product_id', $productId)
                        ->availableFifo()
                        ->lockForUpdate() // Mencegah race condition saat diakses bersamaan
                        ->get();

                    foreach ($batches as $batch) {
                        if ($qtyNeeded <= 0) break; // Jika kebutuhan sudah terpenuhi, hentikan looping batch

                        // Jika stok di batch ini cukup untuk memenuhi semua kebutuhan
                        if ($batch->stock_quantity >= $qtyNeeded) {

                            // Catat ke Detail Transaksi (barang keluar dari batch ini)
                            TransactionDetail::create([
                                'transaction_id' => $transaction->id,
                                'product_id'     => $productId,
                                'batch_id'       => $batch->id,
                                'quantity'       => $qtyNeeded,
                            ]);

                            // Kurangi stok di batch dan simpan
                            $batch->stock_quantity -= $qtyNeeded;
                            $batch->save();

                            $qtyNeeded = 0; // Kebutuhan terpenuhi

                        } else {
                            // Jika stok di batch ini TIDAK cukup (misal: butuh 10, tapi batch ini sisa 4)
                            $qtyAvailableInBatch = $batch->stock_quantity;

                            // Catat ke Detail Transaksi sebanyak sisa stok di batch ini (yaitu 4)
                            TransactionDetail::create([
                                'transaction_id' => $transaction->id,
                                'product_id'     => $productId,
                                'batch_id'       => $batch->id,
                                'quantity'       => $qtyAvailableInBatch,
                            ]);

                            // Kosongkan stok di batch ini
                            $batch->stock_quantity = 0;
                            $batch->save();

                            // Kurangi total kebutuhan (10 - 4 = sisa 6 yang harus diambil dari batch berikutnya)
                            $qtyNeeded -= $qtyAvailableInBatch;
                        }
                    }

                    // Pencegahan ganda: Jika setelah looping semua batch ternyata stok masih kurang
                    if ($qtyNeeded > 0) {
                        throw new \Exception("Terjadi kesalahan perhitungan stok FIFO pada produk {$product->name}.");
                    }
                }
            });

            return response()->json(['message' => 'Transaksi Barang Keluar berhasil disimpan dengan metode FIFO!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}