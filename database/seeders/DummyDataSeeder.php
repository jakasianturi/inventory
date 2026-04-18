<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // Pastikan ada User Admin & Kasir
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin Inventory', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@gmail.com'],
            ['name' => 'Kasir Inventory', 'password' => bcrypt('password'), 'role' => 'user']
        );

        // Buat Kategori
        $catSusuUht = Category::create(['name' => 'Susu UHT', 'description' => 'Susu olahan suhu tinggi']);
        $catSusuBubuk = Category::create(['name' => 'Susu Bubuk', 'description' => 'Susu dalam bentuk bubuk kering']);

        // Buat Produk
        $p1 = Product::create([
            'category_id' => $catSusuUht->id,
            'sku_code' => 'SKU-UHT-001',
            'name' => 'Susu UHT Cokelat 1L',
            'base_price' => 18000
        ]);

        $p2 = Product::create([
            'category_id' => $catSusuUht->id,
            'sku_code' => 'SKU-UHT-002',
            'name' => 'Susu UHT Full Cream 1L',
            'base_price' => 17500
        ]);

        // TRANSAKSI MASUK (IN) - Restock Barang
        $trxIn = Transaction::create([
            'user_id' => $admin->id,
            'transaction_type' => 'in',
            'transaction_date' => Carbon::now()->subDays(10),
            'notes' => 'Restock Awal dari Supplier PT. Susu Nusantara'
        ]);

        // Buat Batch untuk Produk 1
        $batch1 = ProductBatch::create([
            'product_id' => $p1->id,
            'batch_number' => 'BATCH-A1',
            'stock_quantity' => 50,
            'expiration_date' => Carbon::now()->addMonths(6),
        ]);

        TransactionDetail::create([
            'transaction_id' => $trxIn->id,
            'product_id' => $p1->id,
            'batch_id' => $batch1->id,
            'quantity' => 50
        ]);

        // TRANSAKSI KELUAR (OUT) - Penjualan Kasir
        $trxOut = Transaction::create([
            'user_id' => $kasir->id,
            'transaction_type' => 'out',
            'transaction_date' => Carbon::now()->subDays(2),
            'notes' => 'Penjualan Retail Kasir'
        ]);

        // Mengurangi stok Batch 1 (Simulasi FIFO)
        $qtyJual = 5;
        TransactionDetail::create([
            'transaction_id' => $trxOut->id,
            'product_id' => $p1->id,
            'batch_id' => $batch1->id,
            'quantity' => $qtyJual
        ]);

        $batch1->decrement('stock_quantity', $qtyJual);

        $this->command->info('Dummy data inventori susu berhasil dibuat!');
    }
}