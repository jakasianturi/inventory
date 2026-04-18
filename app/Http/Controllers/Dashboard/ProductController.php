<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Ambil data beserta relasinya
            $data = Product::with('category')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                // Menambahkan virtual column untuk total stok (berdasarkan accessor yg kita buat dulu)
                ->addColumn('total_stock', function($row){
                    $stock = $row->total_stock;
                    if($stock < 10) {
                        return '<span class="text-danger font-weight-bold">'.$stock.'</span>';
                    }
                    return '<span class="text-success">'.$stock.'</span>';
                })
                // Merakit tombol aksi dinamis berdasarkan Role
                ->addColumn('action', function($row){
                    if(auth()->user()->role == 'admin'){
                        $editUrl = route('dashboard.products.edit', $row->id);
                        $btn = '<a href="'.$editUrl.'" class="btn btn-warning btn-sm mr-1"><i class="fas fa-edit"></i></a>';
                        // Class "delete" dan id="$row->id" ini yg ditangkap oleh JQuery AJAX di atas
                        $btn .= '<button type="button" class="btn btn-danger btn-sm delete" id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
                        return $btn;
                    }
                    // Jika Kasir, tidak ada tombol edit/hapus
                    return '<span class="badge badge-secondary">Hanya Lihat</span>';
                })
                ->rawColumns(['total_stock', 'action']) // Beritahu DataTables agar tag HTML dirender, bukan dijadikan text
                ->make(true);
        }

        return view('dashboard.products.index');
    }

    /**
     * Menampilkan form untuk membuat produk baru.
     */
    public function create()
    {
        $categories = Category::all();
        // Cukup lemparkan categories, jangan lemparkan $product agar dianggap "Create"
        return view('dashboard.products.form', compact('categories'));
    }

    /**
     * Menyimpan produk baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku_code'    => 'required|string|max:50|unique:products,sku_code',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price'  => 'required|numeric|min:0',
        ], [
            // Kustomisasi pesan error jika perlu
            'sku_code.unique' => 'Kode SKU ini sudah digunakan produk lain.',
        ]);

        Product::create($request->all());

        return redirect()->route('dashboard.products.index')
                         ->with('success', 'Data produk berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail satu produk beserta informasi batch (stok).
     */
    public function show(Product $product)
    {
        // Load relasi category dan batch stok yang masih tersedia
        $product->load(['category', 'batches' => function ($query) {
            $query->where('stock_quantity', '>', 0)
                  ->orderBy('expiration_date', 'asc');
        }]);

        return view('dashboard.products.show', compact('product'));
    }

    /**
     * Menampilkan form untuk mengedit produk.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        // Lemparkan $product agar form mendeteksi ini adalah "Edit" dan mengisi value lama
        return view('dashboard.products.form', compact('product', 'categories'));
    }

    /**
     * Memperbarui data produk di database.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            // Validasi unique dengan pengecualian ID produk ini sendiri agar bisa di-update
            'sku_code'    => 'required|string|max:50|unique:products,sku_code,' . $product->id,
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price'  => 'required|numeric|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('dashboard.products.index')
                         ->with('success', 'Data produk berhasil diperbarui.');
    }

    /**
     * Menghapus produk dari database (Soft Delete).
     */
    public function destroy(Product $product)
    {
        try {
            // Karena kita menggunakan SoftDeletes di Model, data tidak benar-benar hilang dari DB
            $product->delete();
            
            return redirect()->route('dashboard.products.index')
                             ->with('success', 'Data produk berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard.products.index')
                             ->with('error', 'Gagal menghapus produk. Pastikan produk tidak terkait dengan data lain.');
        }
    }
}