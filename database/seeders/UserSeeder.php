<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed 1 akun admin (CRUD kategori/barang/laporan) dan
     * 1 akun kasir (input transaksi) untuk login & data dummy.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@kantin.test'],
            [
                'name' => 'Admin Kantin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'kasir@kantin.test'],
            [
                'name' => 'Kasir Kantin',
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ]
        );
    }
}
