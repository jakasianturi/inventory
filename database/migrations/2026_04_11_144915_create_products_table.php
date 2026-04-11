<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel categories
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');

            $table->string('sku_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('base_price', 12, 2); // 12 digit total, 2 di belakang koma
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}