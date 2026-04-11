<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_batches';

    protected $fillable = [
        'product_id',
        'batch_number',
        'stock_quantity',
        'production_date',
        'expiration_date'
    ];

    // Relasi: Batch ini milik produk apa
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi: Batch ini pernah muncul di detail transaksi mana saja
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'batch_id');
    }

    // Scope untuk mempermudah query FIFO (Mengambil batch dengan stok > 0, urut expired terdekat)
    public function scopeAvailableFifo($query)
    {
        return $query->where('stock_quantity', '>', 0)
            ->orderBy('expiration_date', 'asc');
    }
}
