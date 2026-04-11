<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaction_details';

    protected $fillable = [
        'transaction_id',
        'product_id',
        'batch_id',
        'quantity'
    ];

    // Relasi ke Transaksi Header
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi ke Produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke Batch Spesifik (Penting untuk pelacakan barang mana yang keluar)
    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }
}
