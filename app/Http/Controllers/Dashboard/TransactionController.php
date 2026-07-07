<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\ProductBatch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Menampilkan form barang masuk
     */
    public function createIn()
    {
        $products = Product::all();
        return view('dashboard.transactions.in', compact('products'));
    }

    /**
     * Menyimpan Transaksi Barang Masuk (Restock)
     */
    public function storeIn(Request $request)
    {
        // Validasi bawaan Laravel. Jika gagal, akan otomatis redirect back + membawa old() dan $errors
        $request->validate([
            'transaction_date'        => 'required|date',
            'notes'                   => 'nullable|string',
            'items'                   => 'required|array',
            'items.*.product_id'      => 'required|exists:products,id',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.batch_number'    => 'required|string',
            // Memastikan expired date harus di masa depan (tidak boleh barang masuk sudah basi)
            'items.*.expiration_date' => 'required|date|after:today', 
        ], [
            // Kustomisasi pesan error
            'items.*.expiration_date.after' => 'Tanggal kedaluwarsa harus di masa depan!',
            'items.*.quantity.min'          => 'Jumlah minimal 1 Pcs!'
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Buat Header Transaksi
                $transaction = Transaction::create([
                    'user_id'          => Auth::id() ?? 1,
                    'transaction_type' => 'in',
                    'transaction_date' => $request->transaction_date,
                    'notes'            => $request->notes,
                ]);

                // 2. Looping Item yang masuk
                foreach ($request->items as $item) {
                    $batch = ProductBatch::create([
                        'product_id'      => $item['product_id'],
                        'batch_number'    => $item['batch_number'],
                        'stock_quantity'  => $item['quantity'],
                        'expiration_date' => $item['expiration_date'],
                    ]);

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $item['product_id'],
                        'batch_id'       => $batch->id,
                        'quantity'       => $item['quantity'],
                    ]);
                }
            });

            return redirect()->back()->with('message', 'Transaksi Barang Masuk (Restock) berhasil disimpan!');
            
        } catch (\Exception $e) {
            // Tangkap error sistem / database
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form barang keluar (kasir)
     */
    public function createOut()
    {
        // Hanya ambil produk yang stok aktifnya lebih dari 0 untuk ditampilkan di kasir
        $products = Product::get()->filter(function($product) {
            return $product->total_stock > 0;
        });
        
        return view('dashboard.transactions.out', compact('products'));
    }

    /**
     * Menyimpan Transaksi Barang Keluar (Penjualan) - LOGIKA FIFO
     */
    public function storeOut(Request $request)
    {
        $request->validate([
            'transaction_date'   => 'required|date',
            'notes'              => 'nullable|string',
            'items'              => 'required|array',
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
                // Array untuk mengumpulkan jumlah yang diminta per produk jika 
                // ada kasir yang menginput produk yang sama di 2 baris berbeda
                $requestedQuantities = [];
                foreach ($request->items as $item) {
                    $pid = $item['product_id'];
                    if (!isset($requestedQuantities[$pid])) {
                        $requestedQuantities[$pid] = 0;
                    }
                    $requestedQuantities[$pid] += $item['quantity'];
                }

                // Validasi Total Stok terlebih dahulu
                foreach ($requestedQuantities as $productId => $totalQtyNeeded) {
                    $product = Product::findOrFail($productId);
                    if ($product->total_stock < $totalQtyNeeded) {
                        
                        // Cari baris (index) mana yang menyebabkan error ini
                        $errorIndex = 0;
                        foreach ($request->items as $idx => $reqItem) {
                            if ($reqItem['product_id'] == $productId) {
                                $errorIndex = $idx;
                                break;
                            }
                        }

                        // Lempar error spesifik ke input qty di baris tersebut
                        throw ValidationException::withMessages([
                            "items.{$errorIndex}.quantity" => "Stok '{$product->name}' kurang! Sisa stok aktif: {$product->total_stock} Pcs."
                        ]);
                    }
                }

                // ... (Lanjutan logika FIFO) ...
                foreach ($request->items as $item) {
                    $qtyNeeded = $item['quantity'];
                    $productId = $item['product_id'];

                    $batches = ProductBatch::where('product_id', $productId)
                        ->availableFifo()
                        ->lockForUpdate()
                        ->get();

                    foreach ($batches as $batch) {
                        if ($qtyNeeded <= 0) break; 

                        if ($batch->stock_quantity >= $qtyNeeded) {
                            TransactionDetail::create([
                                'transaction_id' => $transaction->id,
                                'product_id'     => $productId,
                                'batch_id'       => $batch->id,
                                'quantity'       => $qtyNeeded,
                            ]);

                            $batch->stock_quantity -= $qtyNeeded;
                            $batch->save();
                            $qtyNeeded = 0; 
                        } else {
                            $qtyAvailableInBatch = $batch->stock_quantity;

                            TransactionDetail::create([
                                'transaction_id' => $transaction->id,
                                'product_id'     => $productId,
                                'batch_id'       => $batch->id,
                                'quantity'       => $qtyAvailableInBatch,
                            ]);

                            $batch->stock_quantity = 0;
                            $batch->save();
                            $qtyNeeded -= $qtyAvailableInBatch;
                        }
                    }
                }
            });

            return redirect()->back()->with('message', 'Transaksi berhasil diproses sesuai FIFO!');
            
        } catch (ValidationException $e) {
            // 1. TANGKAP ERROR VALIDASI STOK
            // Lempar kembali error ini agar Laravel otomatis melakukan redirect()->back(), 
            // menyimpan withInput(), dan mempopulasi variabel $errors untuk Blade
            throw $e;
            
        } catch (\Exception $e) {
            // 2. TANGKAP ERROR UMUM LAINNYA 
            // (Misal: database mati, atau error sistem lainnya)
            return redirect()->back()->withInput()->with('error', 'Terjadi Kesalahan Sistem: ' . $e->getMessage());
        }
    }
}