<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Seed barang dummy secara manual (bukan factory), supaya tiap barang
     * punya deskripsi jelas untuk ditampilkan di card menu.
     * Butuh KategoriSeeder sudah jalan duluan.
     */
    public function run(): void
    {
        $makanan = Kategori::where('nama', 'Makanan')->first();
        $minuman = Kategori::where('nama', 'Minuman')->first();
        $snack = Kategori::where('nama', 'Snack')->first();

        // Makanan
        Barang::create(['kategori_id' => $makanan->id, 'nama' => 'Nasi Goreng', 'deskripsi' => 'Nasi goreng telur ayam suwir', 'kode' => 'MKN-001', 'harga' => 18000]);
        Barang::create(['kategori_id' => $makanan->id, 'nama' => 'Mie Ayam', 'deskripsi' => 'Mie ayam pangsit rebus', 'kode' => 'MKN-002', 'harga' => 15000]);
        Barang::create(['kategori_id' => $makanan->id, 'nama' => 'Ayam Geprek', 'deskripsi' => 'Ayam geprek sambal bawang', 'kode' => 'MKN-003', 'harga' => 17000]);
        Barang::create(['kategori_id' => $makanan->id, 'nama' => 'Soto Ayam', 'deskripsi' => 'Soto ayam kuah bening', 'kode' => 'MKN-004', 'harga' => 16000]);
        Barang::create(['kategori_id' => $makanan->id, 'nama' => 'Nasi Uduk', 'deskripsi' => 'Nasi uduk komplit lauk', 'kode' => 'MKN-005', 'harga' => 15000]);

        // Minuman
        Barang::create(['kategori_id' => $minuman->id, 'nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin segar', 'kode' => 'MNM-001', 'harga' => 5000]);
        Barang::create(['kategori_id' => $minuman->id, 'nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras dingin', 'kode' => 'MNM-002', 'harga' => 7000]);
        Barang::create(['kategori_id' => $minuman->id, 'nama' => 'Kopi Hitam', 'deskripsi' => 'Kopi tubruk khas', 'kode' => 'MNM-003', 'harga' => 8000]);
        Barang::create(['kategori_id' => $minuman->id, 'nama' => 'Jus Alpukat', 'deskripsi' => 'Jus alpukat creamy coklat', 'kode' => 'MNM-004', 'harga' => 12000]);
        Barang::create(['kategori_id' => $minuman->id, 'nama' => 'Air Mineral', 'deskripsi' => 'Air mineral botol 600ml', 'kode' => 'MNM-005', 'harga' => 4000]);

        // Snack
        Barang::create(['kategori_id' => $snack->id, 'nama' => 'Pisang Goreng', 'deskripsi' => 'Pisang goreng crispy 5 pcs', 'kode' => 'SNK-001', 'harga' => 10000]);
        Barang::create(['kategori_id' => $snack->id, 'nama' => 'Tahu Isi', 'deskripsi' => 'Tahu isi sayur goreng', 'kode' => 'SNK-002', 'harga' => 8000]);
        Barang::create(['kategori_id' => $snack->id, 'nama' => 'Risoles', 'deskripsi' => 'Risoles isi ragout ayam', 'kode' => 'SNK-003', 'harga' => 9000]);
        Barang::create(['kategori_id' => $snack->id, 'nama' => 'Kentang Goreng', 'deskripsi' => 'French fries porsi sedang', 'kode' => 'SNK-004', 'harga' => 13000]);
        Barang::create(['kategori_id' => $snack->id, 'nama' => 'Roti Bakar', 'deskripsi' => 'Roti bakar coklat keju', 'kode' => 'SNK-005', 'harga' => 14000]);
    }
}
