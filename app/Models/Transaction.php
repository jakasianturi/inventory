<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'transaction_type',
        'transaction_date',
        'notes'
    ];

    // Karena format datetime, pastikan dicast
    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    // Relasi: Transaksi ini dilakukan oleh User siapa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Satu transaksi memiliki banyak detail item
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
