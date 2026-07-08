<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel transaksi (dulu terpisah jadi "sales" + "sale_items").
     * Disatukan jadi satu tabel flat: 1 baris = 1 barang yang terjual.
     * Transaksi dengan banyak barang tetap bisa dicatat dalam satu kali input
     * dengan menyamakan "kode_transaksi" di beberapa baris (dikelompokkan di UI/laporan).
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi'); // penanda satu nota, bisa dipakai lebih dari 1 baris
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // kasir/admin yang input
            $table->date('tanggal'); // tanggal transaksi, dipakai untuk filter tren per bulan
            $table->unsignedInteger('jumlah'); // qty barang terjual
            $table->unsignedBigInteger('harga_satuan'); // harga barang saat transaksi (snapshot)
            $table->unsignedBigInteger('subtotal'); // jumlah * harga_satuan
            $table->timestamps();

            $table->index('tanggal');
            $table->index('kode_transaksi');
            $table->index(['barang_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
