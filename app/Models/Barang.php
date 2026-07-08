<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'kategori_id',
        'nama',
        'deskripsi',
        'kode',
        'harga',
        'gambar',
        'is_active',
    ];

    protected $casts = [
        'harga' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi: barang ini milik satu kategori (boleh kosong).
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Relasi: satu barang bisa muncul di banyak baris transaksi.
     */
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    /**
     * Accessor: URL publik gambar barang dari storage.
     * Pakai: $barang->gambar_url
     */
    public function getGambarUrlAttribute(): ?string
    {
        if (! $this->gambar) {
            return null;
        }

        return Storage::disk('public')->url($this->gambar);
    }

    /**
     * Scope: hanya barang yang aktif (masih dijual).
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
