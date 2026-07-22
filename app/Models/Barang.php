<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Barang extends Model
{
    use HasFactory;

    /**
     * $table menentukan nama tabel database secara eksplisit.
     */
    protected $table = 'barang';

    /**
     * $fillable berfungsi sebagai whitelist (daftar putih) kolom yang aman diisi via mass assignment.
     * Melindungi aplikasi dari Mass Assignment Vulnerability saat memproses form.
     */
    protected $fillable = [
        'kategori_id',
        'nama',
        'deskripsi',
        'harga',
        'gambar',
        'is_active',
    ];

    /**
     * $casts mengonversi harga ke integer dan is_active ke tipe boolean secara otomatis.
     */
    protected $casts = [
        'harga' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi Eloquent: Barang ini milik satu kategori (Many to 1).
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Relasi Eloquent: Satu barang bisa muncul di banyak baris transaksi (1 to Many).
     */
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    /**
     * Accessor Eloquent: Membuat atribut semu $barang->gambar_url yang dinamis
     * untuk mengubah path file di database menjadi URL link publik di storage.
     */
    public function getGambarUrlAttribute(): ?string
    {
        if (! $this->gambar) {
            return null;
        }

        if (str_starts_with($this->gambar, 'http://') || str_starts_with($this->gambar, 'https://')) {
            return $this->gambar;
        }

        return asset('storage/' . $this->gambar);
    }

    /**
     * Local Scope Eloquent: Membuat query filter reusable untuk mengambil hanya barang yang aktif (Barang::active()).
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
