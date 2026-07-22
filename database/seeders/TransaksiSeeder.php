<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransaksiSeeder extends Seeder
{
    /**
     * Data transaksi manual (statis tanpa loop/randomize).
     * Tersebar 3 bulan (12 minggu), 3 transaksi per minggu (TRX-0001 s/d TRX-0036).
     */
    public function run(): void
    {
        $user = User::where('role', 'kasir')->first() ?? User::first();

        if (! $user) {
            $this->command->warn('Jalankan UserSeeder dulu sebelum TransaksiSeeder.');
            return;
        }

        $userId = $user->id;

        // Soft cleanup transaksi lama jika seeder dijalankan ulang
        Transaksi::truncate();

        // ==================== MINGGU 12 (3 Bulan Lalu) ====================
        // TRX-0001: Nasi Goreng (15.000) + Es Teh Manis (5.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0001', 'barang_id' => 1, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(84)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 15000, 'subtotal' => 15000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0001', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(84)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 5000, 'subtotal' => 5000 * 2]);

        // TRX-0002: Mie Goreng (13.000) + Air Mineral (4.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0002', 'barang_id' => 2, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(82)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 13000, 'subtotal' => 13000 * 1]);
        Transaksi::create(['kode_transaksi' => 'TRX-0002', 'barang_id' => 6, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(82)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 4000, 'subtotal' => 4000 * 1]);

        // TRX-0003: Ayam Geprek (16.000) + Es Teh Manis (5.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0003', 'barang_id' => 5, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(80)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 16000, 'subtotal' => 16000 * 3]);
        Transaksi::create(['kode_transaksi' => 'TRX-0003', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(80)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 5000, 'subtotal' => 5000 * 3]);

        // ==================== MINGGU 11 ====================
        // TRX-0004: Kentang Goreng (12.000) + Kopi Hitam (7.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0004', 'barang_id' => 3, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(77)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 12000, 'subtotal' => 12000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0004', 'barang_id' => 7, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(77)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 7000, 'subtotal' => 7000 * 2]);

        // TRX-0005: Pisang Goreng (10.000) + Kopi Hitam (7.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0005', 'barang_id' => 4, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(75)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 10000, 'subtotal' => 10000 * 3]);
        Transaksi::create(['kode_transaksi' => 'TRX-0005', 'barang_id' => 7, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(75)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 7000, 'subtotal' => 7000 * 1]);

        // TRX-0006: Nasi Goreng (15.000) + Air Mineral (4.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0006', 'barang_id' => 1, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(73)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 15000, 'subtotal' => 15000 * 1]);
        Transaksi::create(['kode_transaksi' => 'TRX-0006', 'barang_id' => 6, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(73)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 4000, 'subtotal' => 4000 * 1]);

        // ==================== MINGGU 10 ====================
        // TRX-0007: Mie Goreng (13.000) + Es Teh Manis (5.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0007', 'barang_id' => 2, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(70)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 13000, 'subtotal' => 13000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0007', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(70)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 5000, 'subtotal' => 5000 * 2]);

        // TRX-0008: Ayam Geprek (16.000) + Kopi Hitam (7.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0008', 'barang_id' => 5, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(68)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 16000, 'subtotal' => 16000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0008', 'barang_id' => 7, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(68)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 7000, 'subtotal' => 7000 * 2]);

        // TRX-0009: Kentang Goreng (12.000) + Air Mineral (4.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0009', 'barang_id' => 3, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(66)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 12000, 'subtotal' => 12000 * 1]);
        Transaksi::create(['kode_transaksi' => 'TRX-0009', 'barang_id' => 6, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(66)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 4000, 'subtotal' => 4000 * 1]);

        // ==================== MINGGU 9 ====================
        // TRX-0010: Nasi Goreng (15.000) + Es Teh Manis (5.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0010', 'barang_id' => 1, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(63)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 15000, 'subtotal' => 15000 * 3]);
        Transaksi::create(['kode_transaksi' => 'TRX-0010', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(63)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 5000, 'subtotal' => 5000 * 3]);

        // TRX-0011: Pisang Goreng (10.000) + Kopi Hitam (7.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0011', 'barang_id' => 4, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(61)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 10000, 'subtotal' => 10000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0011', 'barang_id' => 7, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(61)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 7000, 'subtotal' => 7000 * 2]);

        // TRX-0012: Mie Goreng (13.000) + Air Mineral (4.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0012', 'barang_id' => 2, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(59)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 13000, 'subtotal' => 13000 * 1]);
        Transaksi::create(['kode_transaksi' => 'TRX-0012', 'barang_id' => 6, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(59)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 4000, 'subtotal' => 4000 * 1]);

        // ==================== MINGGU 8 (2 Bulan Lalu) ====================
        // TRX-0013: Ayam Geprek (16.000) + Es Teh Manis (5.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0013', 'barang_id' => 5, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(56)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 16000, 'subtotal' => 16000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0013', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(56)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 5000, 'subtotal' => 5000 * 2]);

        // TRX-0014: Kentang Goreng (12.000) + Kopi Hitam (7.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0014', 'barang_id' => 3, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(54)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 12000, 'subtotal' => 12000 * 3]);
        Transaksi::create(['kode_transaksi' => 'TRX-0014', 'barang_id' => 7, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(54)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 7000, 'subtotal' => 7000 * 1]);

        // TRX-0015: Nasi Goreng (15.000) + Air Mineral (4.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0015', 'barang_id' => 1, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(52)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 15000, 'subtotal' => 15000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0015', 'barang_id' => 6, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(52)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 4000, 'subtotal' => 4000 * 2]);

        // ==================== MINGGU 7 ====================
        // TRX-0016: Mie Goreng (13.000) + Es Teh Manis (5.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0016', 'barang_id' => 2, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(49)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 13000, 'subtotal' => 13000 * 3]);
        Transaksi::create(['kode_transaksi' => 'TRX-0016', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(49)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 5000, 'subtotal' => 5000 * 3]);

        // TRX-0017: Pisang Goreng (10.000) + Air Mineral (4.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0017', 'barang_id' => 4, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(47)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 10000, 'subtotal' => 10000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0017', 'barang_id' => 6, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(47)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 4000, 'subtotal' => 4000 * 2]);

        // TRX-0018: Ayam Geprek (16.000) + Kopi Hitam (7.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0018', 'barang_id' => 5, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(45)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 16000, 'subtotal' => 16000 * 1]);
        Transaksi::create(['kode_transaksi' => 'TRX-0018', 'barang_id' => 7, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(45)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 7000, 'subtotal' => 7000 * 1]);

        // ==================== MINGGU 6 ====================
        // TRX-0019: Nasi Goreng (15.000) + Es Teh Manis (5.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0019', 'barang_id' => 1, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(42)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 15000, 'subtotal' => 15000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0019', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(42)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 5000, 'subtotal' => 5000 * 2]);

        // TRX-0020: Kentang Goreng (12.000) + Air Mineral (4.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0020', 'barang_id' => 3, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(40)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 12000, 'subtotal' => 12000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0020', 'barang_id' => 6, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(40)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 4000, 'subtotal' => 4000 * 2]);

        // TRX-0021: Mie Goreng (13.000) + Kopi Hitam (7.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0021', 'barang_id' => 2, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(38)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 13000, 'subtotal' => 13000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0021', 'barang_id' => 7, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(38)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 7000, 'subtotal' => 7000 * 2]);

        // ==================== MINGGU 5 ====================
        // TRX-0022: Ayam Geprek (16.000) + Es Teh Manis (5.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0022', 'barang_id' => 5, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(35)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 16000, 'subtotal' => 16000 * 3]);
        Transaksi::create(['kode_transaksi' => 'TRX-0022', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(35)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 5000, 'subtotal' => 5000 * 3]);

        // TRX-0023: Pisang Goreng (10.000) + Kopi Hitam (7.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0023', 'barang_id' => 4, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(33)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 10000, 'subtotal' => 10000 * 3]);
        Transaksi::create(['kode_transaksi' => 'TRX-0023', 'barang_id' => 7, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(33)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 7000, 'subtotal' => 7000 * 2]);

        // TRX-0024: Nasi Goreng (15.000) + Air Mineral (4.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0024', 'barang_id' => 1, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(31)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 15000, 'subtotal' => 15000 * 1]);
        Transaksi::create(['kode_transaksi' => 'TRX-0024', 'barang_id' => 6, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(31)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 4000, 'subtotal' => 4000 * 1]);

        // ==================== MINGGU 4 (1 Bulan Lalu) ====================
        // TRX-0025: Mie Goreng (13.000) + Es Teh Manis (5.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0025', 'barang_id' => 2, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(28)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 13000, 'subtotal' => 13000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0025', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(28)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 5000, 'subtotal' => 5000 * 2]);

        // TRX-0026: Kentang Goreng (12.000) + Air Mineral (4.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0026', 'barang_id' => 3, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(26)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 12000, 'subtotal' => 12000 * 1]);
        Transaksi::create(['kode_transaksi' => 'TRX-0026', 'barang_id' => 6, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(26)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 4000, 'subtotal' => 4000 * 1]);

        // TRX-0027: Ayam Geprek (16.000) + Kopi Hitam (7.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0027', 'barang_id' => 5, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(24)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 16000, 'subtotal' => 16000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0027', 'barang_id' => 7, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(24)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 7000, 'subtotal' => 7000 * 2]);

        // ==================== MINGGU 3 ====================
        // TRX-0028: Nasi Goreng (15.000) + Es Teh Manis (5.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0028', 'barang_id' => 1, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(21)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 15000, 'subtotal' => 15000 * 3]);
        Transaksi::create(['kode_transaksi' => 'TRX-0028', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(21)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 5000, 'subtotal' => 5000 * 3]);

        // TRX-0029: Pisang Goreng (10.000) + Air Mineral (4.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0029', 'barang_id' => 4, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(19)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 10000, 'subtotal' => 10000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0029', 'barang_id' => 6, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(19)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 4000, 'subtotal' => 4000 * 2]);

        // TRX-0030: Mie Goreng (13.000) + Kopi Hitam (7.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0030', 'barang_id' => 2, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(17)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 13000, 'subtotal' => 13000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0030', 'barang_id' => 7, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(17)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 7000, 'subtotal' => 7000 * 1]);

        // ==================== MINGGU 2 ====================
        // TRX-0031: Ayam Geprek (16.000) + Es Teh Manis (5.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0031', 'barang_id' => 5, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(14)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 16000, 'subtotal' => 16000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0031', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(14)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 5000, 'subtotal' => 5000 * 2]);

        // TRX-0032: Kentang Goreng (12.000) + Kopi Hitam (7.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0032', 'barang_id' => 3, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(12)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 12000, 'subtotal' => 12000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0032', 'barang_id' => 7, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(12)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 7000, 'subtotal' => 7000 * 2]);

        // TRX-0033: Nasi Goreng (15.000) + Air Mineral (4.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0033', 'barang_id' => 1, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(10)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 15000, 'subtotal' => 15000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0033', 'barang_id' => 6, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(10)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 4000, 'subtotal' => 4000 * 2]);

        // ==================== MINGGU 1 (Minggu Ini) ====================
        // TRX-0034: Mie Goreng (13.000) + Es Teh Manis (5.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0034', 'barang_id' => 2, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(6)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 13000, 'subtotal' => 13000 * 3]);
        Transaksi::create(['kode_transaksi' => 'TRX-0034', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(6)->format('Y-m-d'), 'jumlah' => 3, 'harga_satuan' => 5000, 'subtotal' => 5000 * 3]);

        // TRX-0035: Pisang Goreng (10.000) + Kopi Hitam (7.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0035', 'barang_id' => 4, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(3)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 10000, 'subtotal' => 10000 * 2]);
        Transaksi::create(['kode_transaksi' => 'TRX-0035', 'barang_id' => 7, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(3)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 7000, 'subtotal' => 7000 * 2]);

        // TRX-0036: Ayam Geprek (16.000) + Air Mineral (4.000)
        Transaksi::create(['kode_transaksi' => 'TRX-0036', 'barang_id' => 5, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(1)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 16000, 'subtotal' => 16000 * 1]);
        Transaksi::create(['kode_transaksi' => 'TRX-0036', 'barang_id' => 6, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(1)->format('Y-m-d'), 'jumlah' => 1, 'harga_satuan' => 4000, 'subtotal' => 4000 * 1]);
    }
}
