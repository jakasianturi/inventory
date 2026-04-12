<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar semua kategori.
     */
    public function index()
    {
        // Mengambil data kategori, diurutkan dari yang terbaru, dengan pagination
        $categories = Category::latest()->paginate(10);

        return view('categories.index', compact('categories'));
    }

    /**
     * Menampilkan form untuk membuat kategori baru.
     */
    public function create()
    {
        return view('categories.create');
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

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail satu kategori beserta daftar produk di dalamnya (opsional).
     */
    public function show(Category $category)
    {
        // Mengambil kategori beserta produk-produk yang ada di dalamnya
        $category->load('products');

        return view('categories.show', compact('category'));
    }

    /**
     * Menampilkan form untuk mengedit kategori.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
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

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Menghapus kategori dari database (Soft Delete).
     */
    public function destroy(Category $category)
    {
        try {
            // Mencoba menghapus kategori
            $category->delete();

            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (QueryException $e) {
            // Menangkap error dari database (misal: kode 23000 adalah error integritas data / foreign key)
            if ($e->getCode() == '23000') {
                return redirect()->route('categories.index')
                    ->with('error', 'Gagal: Kategori ini tidak dapat dihapus karena masih digunakan oleh satu atau beberapa produk.');
            }

            // Error database lainnya
            return redirect()->route('categories.index')
                ->with('error', 'Terjadi kesalahan pada database saat menghapus kategori.');
        } catch (\Exception $e) {
            // Error umum lainnya
            return redirect()->route('categories.index')
                ->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}