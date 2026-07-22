<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * $fillable berfungsi sebagai whitelist (daftar putih) kolom yang boleh diisi
     * secara mass-assignment (misal saat User::create($request->all())).
     * Tujuannya melindungi aplikasi dari celah Mass Assignment Vulnerability agar
     * pengguna tidak bisa menyisipkan kolom ilegal secara sembarangan.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * $hidden berfungsi menyembunyikan kolom sensitif dari respon JSON atau Array
     * agar data seperti password atau token tidak bocor saat dikirim ke frontend/API.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * $casts berfungsi mengonversi tipe data kolom secara otomatis dari database ke tipe PHP.
     * 'password' => 'hashed' otomatis meng-hash password dengan bcrypt saat diisi/di-update.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi Eloquent: Satu user (kasir/admin) bisa menginput banyak baris transaksi penjualan (1 to Many).
     */
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }

    /**
     * Helper method: Mengecek apakah user yang sedang aktif memiliki role Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Helper method: Mengecek apakah user yang sedang aktif memiliki role Kasir.
     */
    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }
}
