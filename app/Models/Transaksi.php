<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    /**
     * $table menentukan nama tabel database secara eksplisit.
     */
    protected $table = 'transaksi';

    /**
     * $fillable berfungsi sebagai whitelist (daftar putih) kolom yang aman diisi via mass assignment.
     * Melindungi aplikasi dari celah keamanan Mass Assignment Vulnerability.
     */
    protected $fillable = [
        'kode_transaksi',
        'barang_id',
        'user_id',
        'tanggal',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    /**
     * $casts mengonversi kolom tanggal ke objek Date PHP, serta jumlah/harga ke integer.
     */
    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
        'harga_satuan' => 'integer',
        'subtotal' => 'integer',
    ];

    /**
     * Relasi Eloquent: Baris transaksi ini merujuk ke satu barang (Many to 1).
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Relasi Eloquent: Baris transaksi dicatat oleh satu user/kasir (Many to 1).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Local Scope Eloquent: Filter transaksi dalam rentang tanggal tertentu untuk query laporan (Transaksi::betweenDates($start, $end)).
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Local Scope Eloquent: Filter transaksi berdasarkan kode transaksi / nota (Transaksi::kode($kode)).
     */
    public function scopeKode($query, string $kodeTransaksi)
    {
        return $query->where('kode_transaksi', $kodeTransaksi);
    }

    /**
     * Generate Kode Transaksi otomatis secara dinamis (misal: TRX-0037).
     * Mengambil angka tertinggi dari nota yang tersimpan di database.
     * Jika transaksi terakhir dihapus / batal simpan, nomor tidak loncat.
     */
    public static function generateNextKode(): string
    {
        $latest = static::select('kode_transaksi')
            ->where('kode_transaksi', 'like', 'TRX-%')
            ->orderByRaw('CAST(SUBSTRING(kode_transaksi, 5) AS UNSIGNED) DESC')
            ->first();

        if (! $latest) {
            return 'TRX-0001';
        }

        $number = (int) preg_replace('/[^0-9]/', '', $latest->kode_transaksi);

        return sprintf('TRX-%04d', $number + 1);
    }
}
