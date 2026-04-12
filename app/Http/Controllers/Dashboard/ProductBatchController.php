<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\ProductBatch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBatchController extends Controller
{
    /**
     * Menampilkan daftar semua Batch/Gudang per Produk
     * Biasanya diakses dari halaman Detail Produk (Product Show)
     */
    public function index(Request $request)
    {
        // Opsional: Filter berdasarkan produk tertentu
        $productId = $request->query('product_id');

        $batches = ProductBatch::with('product')
            ->when($productId, function ($query) use ($productId) {
                return $query->where('product_id', $productId);
            })
            // Tampilkan yang stoknya masih ada atau baru saja habis
            ->orderBy('expiration_date', 'asc')
            ->paginate(15);

        return view('batches.index', compact('batches'));
    }

    /**
     * Menampilkan form untuk melakukan Stock Opname / Penyesuaian / Pemusnahan
     */
    public function editAdjustment(ProductBatch $batch)
    {
        $batch->load('product');
        return view('batches.adjustment', compact('batch'));
    }

    /**
     * Menyimpan perubahan Stock Opname atau Pemusnahan Barang Rusak/Expired
     */
    public function storeAdjustment(Request $request, ProductBatch $batch)
    {
        $request->validate([
            'adjustment_type' => 'required|in:opname,dispose',
            'adjusted_qty'    => 'required|integer|min:0',
            'reason'          => 'required|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request, $batch) {
                $oldQty = $batch->stock_quantity;
                $newQty = $request->adjusted_qty;

                // Logika: Jika ini pemusnahan (dispose), quantity pasti menjadi 0
                if ($request->adjustment_type === 'dispose') {
                    $newQty = 0;
                }

                // Opsional tingkat lanjut: 
                // Di sistem ERP sungguhan, perubahan ini harus dicatat ke tabel "stock_adjustments" 
                // atau "transactions" dengan tipe "adjustment" agar ada riwayatnya.
                // Untuk contoh ini, kita langsung update batch-nya.

                $batch->update([
                    'stock_quantity' => $newQty
                ]);
            });

            return redirect()->route('products.show', $batch->product_id)
                ->with('success', 'Penyesuaian stok batch berhasil dilakukan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal melakukan penyesuaian: ' . $e->getMessage());
        }
    }
}
