<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan semua seeder secara berurutan.
     * Urutan penting: User & Kategori dulu, baru Barang (butuh kategori),
     * baru Transaksi (butuh User & Barang).
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KategoriSeeder::class,
            BarangSeeder::class,
            TransaksiSeeder::class,
        ]);
    }
}
