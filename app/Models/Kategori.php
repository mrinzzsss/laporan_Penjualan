<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    /**
     * $table berfungsi menentukan nama tabel database secara eksplisit yang diwakili model ini.
     */
    protected $table = 'kategori';

    /**
     * $fillable berfungsi sebagai whitelist (daftar putih) kolom yang aman diisi via mass assignment.
     * Melindungi kolom dari celah keamanan Mass Assignment Vulnerability.
     */
    protected $fillable = [
        'nama',
    ];

    /**
     * Relasi Eloquent: Satu kategori memiliki banyak barang (1 to Many).
     */
    public function barang()
    {
        return $this->hasMany(Barang::class);
    }
}
