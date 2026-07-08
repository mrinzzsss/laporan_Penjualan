# Update: Role Admin/Kasir + Pemisahan Folder View

## Ringkasan perubahan

1. **Kolom `role` di tabel users** — migration baru, plain string (bukan enum), default `kasir`.
2. **User model** — tambah `role` ke `$fillable`, tambah helper `isAdmin()` dan `isKasir()`.
3. **Middleware baru `CheckRole`** — alias `role`, dipakai sebagai `middleware('role:admin')` / `middleware('role:kasir')`.
4. **Routes dipisah per role**:
   - `role:admin` → `kategori.*`, `barang.*`, `reports.*`
   - `role:kasir` → `transaksi.*`
   - `dashboard` tetap satu route, tapi kontennya beda tergantung role (lihat `DashboardController`).
5. **View dipisah folder**:
   - `resources/views/admin/kategori/...`
   - `resources/views/admin/barang/...`
   - `resources/views/admin/reports/...`
   - `resources/views/admin/dashboard/index.blade.php`
   - `resources/views/kasir/transaksi/...`
   - `resources/views/kasir/dashboard/index.blade.php`
6. **Navbar** (`layouts/app.blade.php`) — menu Kategori/Barang/Laporan hanya muncul untuk admin, menu Transaksi hanya muncul untuk kasir. Nama user juga menampilkan role-nya.
7. **UserSeeder** — sekarang bikin 2 akun:
   - `admin@kantin.test` / `password` (role: admin)
   - `kasir@kantin.test` / `password` (role: kasir)

## File yang ditambahkan
- `database/migrations/2026_07_08_000001_add_role_to_users_table.php`
- `app/Http/Middleware/CheckRole.php`
- `resources/views/admin/dashboard/index.blade.php`
- `resources/views/kasir/dashboard/index.blade.php`

## File yang diubah
- `app/Models/User.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/KategoriController.php` (path view)
- `app/Http/Controllers/BarangController.php` (path view)
- `app/Http/Controllers/ReportController.php` (path view)
- `app/Http/Controllers/TransaksiController.php` (path view)
- `database/seeders/UserSeeder.php`
- `routes/web.php`
- `resources/views/layouts/app.blade.php`

## File yang dipindah (folder)
- `resources/views/kategori/*` → `resources/views/admin/kategori/*`
- `resources/views/barang/*` → `resources/views/admin/barang/*`
- `resources/views/reports/*` → `resources/views/admin/reports/*`
- `resources/views/transaksi/*` → `resources/views/kasir/transaksi/*`
- `resources/views/dashboard.blade.php` dihapus, diganti 2 file di atas

## Langkah yang perlu Jo lakukan secara manual

### 1. Daftarkan middleware alias `role`

File `bootstrap/app.php` tidak ikut ter-upload di zip ini, jadi tambahkan sendiri baris berikut
di bagian `->withMiddleware()` (sejajar dengan alias `auth.custom` dan `guest.redirect` yang sudah ada):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'auth.custom' => \App\Http\Middleware\EnsureUserIsAuthenticated::class,
        'guest.redirect' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'role' => \App\Http\Middleware\CheckRole::class, // <-- tambahkan ini
    ]);
})
```

### 2. Jalankan migration & seeder ulang

```bash
php artisan migrate:fresh --seed
```

Kalau tidak mau reset data, cukup migrate kolom baru lalu update role user lama manual:

```bash
php artisan migrate
```

lalu isi kolom `role` untuk user yang sudah ada (via tinker atau DB client), misalnya:

```bash
php artisan tinker
>>> App\Models\User::where('email', 'admin@kantin.test')->update(['role' => 'admin']);
```

### 3. Login untuk testing

- **Admin**: `admin@kantin.test` / `password` → bisa akses Kategori, Barang, Laporan.
- **Kasir**: `kasir@kantin.test` / `password` → hanya bisa akses Transaksi.

Kalau user login dengan role yang tidak sesuai route (misal kasir coba akses `/kategori`), akan kena `403 Forbidden` dari middleware `CheckRole`.
