<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'nama',
    ];

    /**
     * Relasi: satu kategori punya banyak barang.
     */
    public function barang()
    {
        return $this->hasMany(Barang::class);
    }
}
