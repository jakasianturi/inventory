# Dokumentasi Alur Kerja Sistem Persediaan Barang

Dokumen ini menjelaskan alur kerja operasional pada sistem persediaan barang Toko Susu Segar, dibagi berdasarkan hak akses pengguna (*Role*) dan logika sistem yang berjalan di latar belakang.

---

## 1. Alur Kerja Admin (Hak Akses Penuh)

Admin bertugas sebagai pengelola utama sistem, mulai dari mengatur data awal hingga melihat laporan akhir.

### Langkah 1: Persiapan Data (Master Data)
* Admin *login* ke dalam sistem.
* Admin membuat **Kategori Barang** (misal: Minuman, Makanan, dsb).
* Admin mendaftarkan **Produk Baru** lengkap dengan Harga dan Kode SKU.
> **Catatan:** Produk baru yang didaftarkan belum memiliki stok saat pertama kali dibuat.

### Langkah 2: Proses Restock (Transaksi Barang Masuk)
* Saat barang dari *supplier* datang, Admin masuk ke menu **Transaksi Masuk**.
* Admin menginput jumlah barang (*Qty*), **Nomor Batch**, dan **Tanggal Kedaluwarsa** (*Expired Date*).
* Sistem otomatis menyimpan entri ini sebagai *Batch Gudang* baru.

### Langkah 3: Pemeliharaan Stok (Stock Opname / Penyesuaian)
* Jika terdapat barang yang rusak, hilang, atau sudah kedaluwarsa, Admin masuk ke menu **Penyesuaian Stok (Batches)**.
* Admin dapat mengubah jumlah stok pada *batch* tersebut atau memusnahkannya (*dispose* menjadi 0) agar data di sistem sinkron dengan fisik di gudang.

### Langkah 4: Pemantauan Laporan (Reports)
* Admin dapat mengunduh **Laporan Stok**, **Laporan Barang Masuk**, dan **Laporan Barang Keluar** dalam format PDF atau Excel untuk keperluan evaluasi bulanan.

---

## 2. Alur Kerja Kasir (Hak Akses Operasional)

Kasir bertugas murni untuk melayani pelanggan dan menjalankan operasional penjualan sehari-hari.

### Langkah 1: Pengecekan Ketersediaan
* Kasir *login* ke dalam sistem.
* Kasir dapat melihat daftar produk di menu **Laporan Stok** untuk memastikan ketersediaan barang sebelum dijual.
> **Catatan:** Kasir tidak memiliki hak akses untuk menambah, mengedit, atau menghapus data produk.

### Langkah 2: Melayani Pembeli (Transaksi Barang Keluar)
* Saat ada pembeli, Kasir masuk ke menu **Transaksi Keluar**.
* Kasir memilih barang yang dibeli dan menginput jumlahnya (*Qty*).
* Klik **Simpan**. Transaksi selesai dan stok otomatis berkurang.

---

## 3. Logika Sistem di Balik Layar (Penting!)

Agar pencatatan akurat, sistem menjalankan aturan otomatis berikut pada tingkat basis data (*Database Transaction*) tanpa perlu campur tangan manual dari pengguna:

1. **Pencegahan Stok Minus (Stock Validation)**
   Jika Kasir mencoba menjual 10 *pcs* barang, tetapi total stok aktif di gudang hanya tersisa 5 *pcs*, sistem akan membatalkan transaksi secara otomatis (*rollback*) dan menampilkan peringatan: *"Stok Kurang!"*.

2. **Sistem Cerdas FIFO (First-In, First-Out)**
   Saat Kasir memproses penjualan, algoritma sistem akan mencari dan memotong stok dari barang dengan **Tanggal Kedaluwarsa Paling Dekat**.
   * *Contoh:* Terdapat Susu Kotak `Batch A` (Kedaluwarsa besok: 5 pcs) dan `Batch B` (Kedaluwarsa bulan depan: 10 pcs). Jika pembeli membeli 7 pcs, sistem akan menghabiskan stok `Batch A` (5 pcs) terlebih dahulu, lalu mengambil sisa kekurangannya (2 pcs) dari `Batch B`.