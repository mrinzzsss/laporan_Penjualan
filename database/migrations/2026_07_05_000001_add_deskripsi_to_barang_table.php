<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom deskripsi singkat untuk ditampilkan di card menu
     * pada halaman tambah transaksi.
     */
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->string('deskripsi')->nullable()->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn('deskripsi');
        });
    }
};
