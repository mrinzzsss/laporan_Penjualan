<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'barang_id',
        'user_id',
        'tanggal',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
        'harga_satuan' => 'integer',
        'subtotal' => 'integer',
    ];

    /**
     * Relasi: baris transaksi ini merujuk ke satu barang.
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Relasi: baris transaksi dicatat oleh satu user (kasir/admin).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: filter transaksi dalam rentang tanggal tertentu.
     * Dipakai untuk laporan tren.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Scope: filter transaksi berdasarkan kode transaksi (satu nota).
     */
    public function scopeKode($query, string $kodeTransaksi)
    {
        return $query->where('kode_transaksi', $kodeTransaksi);
    }
}
