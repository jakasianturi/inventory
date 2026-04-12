<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk.
     */
    public function index()
    {
        // Menggunakan eager loading ('category') untuk mencegah N+1 Query Problem.
        // total_stock otomatis bisa diakses berkat Accessor di Model.
        $products = Product::with('category')->latest()->paginate(10);
        
        return view('products.index', compact('products'));
    }

    /**
     * Menampilkan form untuk membuat produk baru.
     */
    public function create()
    {
        // Ambil semua kategori untuk diisi ke elemen <select> di Blade
        $categories = Category::all();
        
        return view('products.create', compact('categories'));
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

        return redirect()->route('products.index')
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

        return view('products.show', compact('product'));
    }

    /**
     * Menampilkan form untuk mengedit produk.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        
        return view('products.edit', compact('product', 'categories'));
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

        return redirect()->route('products.index')
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
            
            return redirect()->route('products.index')
                             ->with('success', 'Data produk berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('products.index')
                             ->with('error', 'Gagal menghapus produk. Pastikan produk tidak terkait dengan data lain.');
        }
    }
}