<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransaksiSeeder extends Seeder
{
    /**
     * Data dummy transaksi dibuat manual (tanpa loop), tersebar 2 minggu
     * terakhir, 3-4 nota per minggu. Tiap nota berisi 1-3 baris barang.
     * Asumsi: user_id 1 = admin (dari UserSeeder), barang_id 1-15 sesuai
     * urutan di BarangSeeder.
     */
    public function run(): void
    {
        $admin = User::first();

        if (! $admin) {
            $this->command->warn('Jalankan UserSeeder dulu sebelum TransaksiSeeder.');

            return;
        }

        $userId = $admin->id;

        // ================= Minggu ke-2 (13-14 hari lalu s/d 8 hari lalu) =================

        // Nota 1 - Nasi Goreng + Es Teh Manis
        Transaksi::create(['kode_transaksi' => 'TRX-0001', 'barang_id' => 1, 'user_id' => $userId, 'tanggal' => now()->subDays(13)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 18000, 'subtotal' => 18000 * 3]);
        Transaksi::create(['kode_transaksi' => 'TRX-0001', 'barang_id' => 6, 'user_id' => $userId, 'tanggal' => now()->subDays(13)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 5000, 'subtotal' => 5000 * 3]);

        // Nota 2 - Kopi Hitam saja
        Transaksi::create(['kode_transaksi' => 'TRX-0002', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => now()->subDays(12)->format('Y-m-d'), 'jumlah' => 5, 'harga_satuan' => 8000, 'subtotal' => 8000 * 5]);

        // Nota 3 - Ayam Geprek + Kentang Goreng + Es Jeruk
        Transaksi::create(['kode_transaksi' => 'TRX-0003', 'barang_id' => 3, 'user_id' => $userId, 'tanggal' => now()->subDays(10)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 17000, 'subtotal' => 17000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0003', 'barang_id' => 14, 'user_id' => $userId, 'tanggal' => now()->subDays(10)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 13000, 'subtotal' => 13000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0003', 'barang_id' => 7, 'user_id' => $userId, 'tanggal' => now()->subDays(10)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 7000, 'subtotal' => 7000 * 2]);

        // Nota 4 - Mie Ayam + Pisang Goreng
        Transaksi::create(['kode_transaksi' => 'TRX-0004', 'barang_id' => 2, 'user_id' => $userId, 'tanggal' => now()->subDays(8)->format('Y-m-d'), 'jumlah' => 4, 'harga_satuan' => 15000, 'subtotal' => 15000 * 4]);
        Transaksi::create(['kode_transaksi' => 'TRX-0004', 'barang_id' => 11, 'user_id' => $userId, 'tanggal' => now()->subDays(8)->format('Y-m-d'), 'jumlah' => 4, 'harga_satuan' => 10000, 'subtotal' => 10000 * 4]);

        // ================= Minggu ke-1 (7 hari lalu s/d hari ini) =================

        // Nota 5 - Soto Ayam + Air Mineral
        Transaksi::create(['kode_transaksi' => 'TRX-0005', 'barang_id' => 4, 'user_id' => $userId, 'tanggal' => now()->subDays(6)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 16000, 'subtotal' => 16000 * 3]);
        Transaksi::create(['kode_transaksi' => 'TRX-0005', 'barang_id' => 10, 'user_id' => $userId, 'tanggal' => now()->subDays(6)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 4000, 'subtotal' => 4000 * 3]);

        // Nota 6 - Nasi Uduk + Tahu Isi + Jus Alpukat
        Transaksi::create(['kode_transaksi' => 'TRX-0006', 'barang_id' => 5, 'user_id' => $userId, 'tanggal' => now()->subDays(4)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 15000, 'subtotal' => 15000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0006', 'barang_id' => 12, 'user_id' => $userId, 'tanggal' => now()->subDays(4)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 8000, 'subtotal' => 8000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0006', 'barang_id' => 9, 'user_id' => $userId, 'tanggal' => now()->subDays(4)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 12000, 'subtotal' => 12000 * 2]);

        // Nota 7 - Roti Bakar + Risoles
        Transaksi::create(['kode_transaksi' => 'TRX-0007', 'barang_id' => 15, 'user_id' => $userId, 'tanggal' => now()->subDays(2)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 14000, 'subtotal' => 14000 * 3]);
        Transaksi::create(['kode_transaksi' => 'TRX-0007', 'barang_id' => 13, 'user_id' => $userId, 'tanggal' => now()->subDays(2)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 9000, 'subtotal' => 9000 * 3]);
    }
}
