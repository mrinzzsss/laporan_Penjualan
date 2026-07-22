<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Seed 3 kategori dasar: Makanan, Minuman, Snack.
     */
    public function run(): void
    {
        collect(['Makanan', 'Minuman'])
            ->each(fn ($nama) => Kategori::firstOrCreate(['nama' => $nama]));
    }
}
