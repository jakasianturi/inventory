<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar semua kategori.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Tambahkan withCount('products') untuk menghitung jumlah produk secara otomatis
            $data = Category::withCount('products')->latest();
            
            return DataTables::of($data)
                ->addIndexColumn() // Untuk nomor urut otomatis
                
                // Mengubah tampilan angka menjadi badge agar lebih menarik (Opsional)
                ->addColumn('total_products', function($row) {
                    return '<span class="badge badge-info">' . $row->products_count . ' Produk</span>';
                })
                
                ->addColumn('action', function($row){
                    // Tombol aksi hanya untuk admin
                    // Catatan: Pastikan menggunakan field role yang sesuai (role atau user_role)
                    if(auth()->user()->role == 'admin'){ 
                        $editUrl = route('dashboard.categories.edit', $row->id);
                        $btn = '<a href="'.$editUrl.'" class="btn btn-warning btn-sm mr-1"><i class="fas fa-edit"></i></a>';
                        $btn .= '<button type="button" class="btn btn-danger btn-sm delete" id="'.$row->id.'"><i class="fas fa-trash"></i></button>';
                        return $btn;
                    }
                    return '<span class="badge badge-secondary">Hanya Lihat</span>';
                })
                // Daftarkan kolom baru di rawColumns agar tag HTML (badge) di-render oleh browser
                ->rawColumns(['total_products', 'action']) 
                ->make(true);
        }

        return view('dashboard.categories.index');
    }

    /**
     * Menampilkan form untuk membuat kategori baru.
     */
    public function create()
    {
        return view('dashboard.categories.form');
    }

    /**
     * Menyimpan kategori baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori ini sudah ada, silakan gunakan nama lain.',
        ]);

        // Simpan data
        Category::create($request->all());

        return redirect()->route('dashboard.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit kategori.
     */
    public function edit(Category $category)
    {
        return view('dashboard.categories.form', compact('category'));
    }

    /**
     * Memperbarui data kategori di database.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            // Validasi unique dengan mengecualikan ID kategori ini sendiri
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->route('dashboard.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Menghapus kategori dari database (Soft Delete).
     */
    public function destroy(Category $category)
    {
        try {
            // 1. Cek di tingkat aplikasi apakah ada produk yang terkait
            if ($category->products()->count() > 0) {
                return response()->json([
                    'message' => 'Gagal menghapus! Kategori ini masih digunakan oleh ' . $category->products()->count() . ' produk.'
                ], 400); // 400 Bad Request
            }

            // 2. Jika aman, lakukan soft delete
            $category->delete();

            return redirect()->route('dashboard.categories.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (QueryException $e) {
            // Menangkap error dari database (misal: kode 23000 adalah error integritas data / foreign key)
            if ($e->getCode() == '23000') {
                return redirect()->route('dashboard.categories.index')
                    ->with('error', 'Gagal: Kategori ini tidak dapat dihapus karena masih digunakan oleh satu atau beberapa produk.');
            }

            // Error database lainnya
            return redirect()->route('dashboard.categories.index')
                ->with('error', 'Terjadi kesalahan pada database saat menghapus kategori.');
        } catch (\Exception $e) {
            // Error umum lainnya
            return redirect()->route('dashboard.categories.index')
                ->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}