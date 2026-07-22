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

        // 5 Makanan
        Barang::create(['kategori_id' => $makanan->id, 'nama' => 'Nasi Goreng', 'deskripsi' => 'Nasi goreng telur & ayam suwir spesial kantin', 'harga' => 15000, 'gambar' => 'barang/nasi-goreng.jpg']);
        Barang::create(['kategori_id' => $makanan->id, 'nama' => 'Mie Goreng', 'deskripsi' => 'Mie goreng lezat dengan telur & sayuran', 'harga' => 13000, 'gambar' => 'barang/mie-goreng.jpg']);
        Barang::create(['kategori_id' => $makanan->id, 'nama' => 'Kentang Goreng', 'deskripsi' => 'Kentang goreng / French fries renyah', 'harga' => 12000, 'gambar' => 'barang/kentang-goreng.jpg']);
        Barang::create(['kategori_id' => $makanan->id, 'nama' => 'Pisang Goreng', 'deskripsi' => 'Pisang goreng crispy hangat 5 pcs', 'harga' => 10000, 'gambar' => 'barang/pisang-goreng.jpg']);
        Barang::create(['kategori_id' => $makanan->id, 'nama' => 'Ayam Geprek', 'deskripsi' => 'Ayam geprek renyah sambal bawang pedas', 'harga' => 16000, 'gambar' => 'barang/ayam-geprek.jpg']);

        // 3 Minuman
        Barang::create(['kategori_id' => $minuman->id, 'nama' => 'Air Mineral', 'deskripsi' => 'Air mineral botol 600ml dingin', 'harga' => 4000, 'gambar' => 'barang/air-mineral.jpg']);
        Barang::create(['kategori_id' => $minuman->id, 'nama' => 'Kopi Hitam', 'deskripsi' => 'Kopi hitam tubruk mantap khas kantin', 'harga' => 7000, 'gambar' => 'barang/kopi-hitam.jpg']);
        Barang::create(['kategori_id' => $minuman->id, 'nama' => 'Es Teh Manis', 'deskripsi' => 'Es teh manis dingin & segar', 'harga' => 5000, 'gambar' => 'barang/es-teh-manis.jpg']);
    }
}
