<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'sku_code',
        'name',
        'description',
        'base_price'
    ];

    // Relasi: Sebuah produk dimiliki oleh satu kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi: Satu produk memiliki banyak Batch (stok masuk)
    public function batches()
    {
        return $this->hasMany(ProductBatch::class);
    }

    // Custom Accessor: Menghitung total stok aktif dari seluruh batch yang belum expired
    public function getTotalStockAttribute()
    {
        return $this->batches()
            ->where('expiration_date', '>', now()) // Abaikan yang sudah expired
            ->sum('stock_quantity');
    }
}
