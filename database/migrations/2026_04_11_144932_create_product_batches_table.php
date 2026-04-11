<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductBatchesTable extends Migration
{
    public function up()
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            // Relasi ke produk
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            $table->string('batch_number');
            $table->integer('stock_quantity');
            $table->date('production_date')->nullable();
            $table->date('expiration_date'); // Penting untuk sort FIFO
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_batches');
    }
}