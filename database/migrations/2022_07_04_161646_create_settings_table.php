<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_situs')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('auth_background')->nullable();
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->longText('alamat')->nullable();
            $table->string('footer_teks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}