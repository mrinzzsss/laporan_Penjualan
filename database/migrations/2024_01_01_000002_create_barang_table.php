<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel barang (dulu "products"). Setiap barang boleh punya kategori
     * lewat relasi ke tabel kategori (bukan lagi kolom string bebas).
     */
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->nullOnDelete();
            $table->string('nama');
            $table->string('deskripsi')->nullable();
            $table->unsignedBigInteger('harga'); // harga jual per unit (dalam rupiah)
            $table->string('gambar')->nullable(); // path gambar di storage
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
