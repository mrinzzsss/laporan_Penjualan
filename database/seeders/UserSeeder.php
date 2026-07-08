<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed 1 akun admin default untuk login & jadi pencatat transaksi dummy.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@kantin.test'],
            [
                'name' => 'Admin Kantin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
