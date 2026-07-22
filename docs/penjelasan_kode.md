# 📘 Dokumentasi & Penjelasan Kode Sistem Penjualan Kantin ("Kasir Kantin")

Dokumen ini berisi penjelasan lengkap dan mudah dipahami mengenai setiap komponen kode dalam aplikasi **Kasir Kantin** berbasis Laravel. Setiap bagian kode dilengkapi dengan petunjuk dan komentar di setiap baris kodenya.

---

## 📋 1. 📌 GAMBARAN UMUM SISTEM & TEKNOLOGI

Aplikasi **Kasir Kantin** dibuat menggunakan framework Laravel dengan pola arsitektur **MVC (Model-View-Controller)**. 

* **Teknologi Utama**: PHP 8.3+, Laravel 11/13, MySQL Database, TailwindCSS.
* **Fitur Utama**:
  1. **Hak Akses Pengguna (Multi-Role)**: Akun Admin dan Kasir yang dilindungi oleh keamanan khusus (`CheckRole`, `CustomAuth`).
  2. **Tampilan Kasir POS Split View**: Katalog menu di sebelah kiri & nota belanja di sebelah kanan tanpa perlu pilihan dropdown.
  3. **Kode Transaksi Otomatis**: Penomoran nota dibuat otomatis (`TRX-xxxx`) dan nomor tidak akan loncat jika transaksi dibatalkan.
  4. **Dashboard Admin Interaktif**: 4 kartu ringkasan data, filter tanggal, 2 grafik visual (Tren Penjualan & Barang Terlaris), serta tombol untuk cetak laporan PDF.
  5. **Cetak Laporan PDF**: Dibuat menggunakan bantuan pustaka **mPDF** yang terhubung dengan **ChartImageService** berbasis PHP GD.

---

## 🗄️ 2. DATABASE (Migrations & Seeders)

---

### A. Migrations (`database/migrations/`)

Migration berfungsi sebagai pembuat dan pengatur struktur tabel pada database.

#### 1. `0001_01_01_000000_create_users_table.php`
Fungsi: Membuat tabel `users` untuk menyimpan data akun pengguna aplikasi (Admin & Kasir).

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();                                       // Nomor ID otomatis berurutan (1, 2, 3, dst)
    $table->string('name');                             // Nama lengkap pengguna
    $table->string('email')->unique();                  // Email pengguna (harus unik untuk masuk/login)
    $table->timestamp('email_verified_at')->nullable(); // Waktu verifikasi email (boleh kosong)
    $table->string('password');                         // Password yang sudah diacak/diamankan
    $table->string('role')->default('kasir');           // Peran pengguna ('admin' atau 'kasir'), bawaan 'kasir'
    $table->rememberToken();                            // Kode untuk menyimpan status login "Ingat Saya"
    $table->timestamps();                               // Kolom otomatis tanggal dibuat dan tanggal diperbarui
});
```

#### 2. `2024_01_01_000001_create_kategori_table.php`
Fungsi: Membuat tabel `kategori` untuk mengelompokkan jenis produk.

```php
Schema::create('kategori', function (Blueprint $table) {
    $table->id();                                       // Nomor ID utama kategori
    $table->string('nama')->unique();                  // Nama kategori (harus unik, contoh: Makanan, Minuman)
    $table->timestamps();                               // Tanggal dibuat dan tanggal diperbarui
});
```

#### 3. `2024_01_01_000002_create_barang_table.php`
Fungsi: Membuat tabel `barang` untuk menyimpan daftar produk yang dijual di kantin.

```php
Schema::create('barang', function (Blueprint $table) {
    $table->id();                                       // Nomor ID utama barang
    $table->foreignId('kategori_id')                    // Penghubung ke tabel kategori
          ->nullable()                                  // Boleh kosong jika belum ada kategori
          ->constrained('kategori')                     // Terhubung langsung ke tabel 'kategori'
          ->nullOnDelete();                             // Jika kategori dihapus, kolom ini berubah jadi kosong/null
    $table->string('nama');                             // Nama produk (contoh: Nasi Goreng)
    $table->string('deskripsi')->nullable();            // Penjelasan singkat produk (boleh kosong)
    $table->unsignedBigInteger('harga');                // Harga produk dalam satuan Rupiah
    $table->string('gambar')->nullable();               // Lokasi simpan foto barang di folder storage
    $table->boolean('is_active')->default(true);        // Status produk (true = dijual, false = tidak dijual)
    $table->timestamps();                               // Tanggal dibuat dan tanggal diperbarui
});
```

#### 4. `2024_01_01_000003_create_transaksi_table.php`
Fungsi: Membuat tabel `transaksi` untuk mencatat rincian barang yang dibeli pada setiap nota.

```php
Schema::create('transaksi', function (Blueprint $table) {
    $table->id();                                       // Nomor ID utama baris transaksi
    $table->string('kode_transaksi');                   // Kode nota transaksi (contoh: TRX-0001)
    $table->foreignId('barang_id')                      // Penghubung ke tabel barang
          ->constrained('barang')                       // Terhubung ke tabel 'barang'
          ->cascadeOnDelete();                          // Jika barang dihapus, transaksi terkait ikut terhapus
    $table->foreignId('user_id')                        // Penghubung ke tabel users (kasir/admin)
          ->nullable()                                  // Boleh kosong
          ->constrained('users')                        // Terhubung ke tabel 'users'
          ->nullOnDelete();                             // Jika user dihapus, kolom ini berubah jadi null
    $table->date('tanggal');                            // Tanggal penjualan (Format: TAHUN-BULAN-HARI)
    $table->unsignedInteger('jumlah');                  // Jumlah unit barang yang dibeli
    $table->unsignedBigInteger('harga_satuan');         // Harga barang per unit saat transaksi dilakukan
    $table->unsignedBigInteger('subtotal');             // Total harga per barang (jumlah x harga_satuan)
    $table->timestamps();                               // Tanggal dibuat dan tanggal diperbarui

    // Penanda khusus (Index) agar proses pencarian dan pemrosesan laporan berjalan cepat:
    $table->index('tanggal');                           // Penanda tanggal untuk penyaringan laporan
    $table->index('kode_transaksi');                   // Penanda kode nota untuk pengelompokan pesanan
    $table->index(['barang_id', 'tanggal']);            // Penanda gabungan untuk analisis statistik produk
});
```

#### 5. `2026_07_05_000001_add_deskripsi_to_barang_table.php`
Fungsi: Menambahkan kolom `deskripsi` pada tabel `barang` tepat setelah kolom `nama`.

```php
Schema::table('barang', function (Blueprint $table) {
    $table->string('deskripsi')->nullable()->after('nama'); // Menambah kolom deskripsi di sebelah nama
});
```

#### 6. `2026_07_08_000001_add_role_to_users_table.php`
Fungsi: Menambahkan kolom `role` pada tabel `users` tepat setelah kolom `password`.

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('role')->default('kasir')->after('password'); // Menambah kolom peran setelah password
});
```

---

### B. Seeders (`database/seeders/`)

Seeder berfungsi untuk mengisikan data awal ke dalam database agar aplikasi langsung siap digunakan.

#### 1. `DatabaseSeeder.php`
Fungsi: File utama seeder yang menjalankan seeder lainnya secara berurutan.

```php
public function run(): void
{
    $this->call([
        UserSeeder::class,       // 1. Jalankan pembuatan akun Admin & Kasir
        KategoriSeeder::class,   // 2. Jalankan pembuatan Kategori Makanan & Minuman
        BarangSeeder::class,     // 3. Jalankan pembuatan 5 Makanan & 3 Minuman
        TransaksiSeeder::class,  // 4. Jalankan pembuatan 36 Transaksi 3 Bulan
    ]);
}
```

#### 2. `UserSeeder.php`
Fungsi: Memasukkan akun pengguna pertama kali.

```php
public function run(): void
{
    // Membuat akun Admin
    User::create([
        'name' => 'Admin Kantin',                       // Nama pengguna
        'email' => 'admin@kantin.test',                 // Email untuk login
        'password' => Hash::make('admin123'),           // Password yang diamankan
        'role' => 'admin',                              // Peran sebagai admin
    ]);

    // Membuat akun Kasir
    User::create([
        'name' => 'Kasir Kantin',                       // Nama pengguna
        'email' => 'kasir@kantin.test',                 // Email untuk login
        'password' => Hash::make('kantin123'),          // Password yang diamankan
        'role' => 'kasir',                              // Peran sebagai kasir
    ]);
}
```

#### 3. `KategoriSeeder.php`
Fungsi: Memasukkan 2 kategori utama.

```php
public function run(): void
{
    Kategori::create(['nama' => 'Makanan']);            // Menambahkan kategori Makanan
    Kategori::create(['nama' => 'Minuman']);            // Menambahkan kategori Minuman
}
```

#### 4. `BarangSeeder.php`
Fungsi: Memasukkan 5 menu makanan dan 3 menu minuman beserta lokasi gambarnya.

```php
public function run(): void
{
    // Daftar Makanan
    Barang::create(['kategori_id' => 1, 'nama' => 'Nasi Goreng', 'deskripsi' => 'Nasi goreng spesial', 'harga' => 15000, 'gambar' => 'barang/nasi_goreng.jpg']);
    Barang::create(['kategori_id' => 1, 'nama' => 'Mie Goreng', 'deskripsi' => 'Mie goreng telur', 'harga' => 13000, 'gambar' => 'barang/mie_goreng.jpg']);
    Barang::create(['kategori_id' => 1, 'nama' => 'Kentang Goreng', 'deskripsi' => 'Kentang renyah', 'harga' => 12000, 'gambar' => 'barang/kentang_goreng.jpg']);
    Barang::create(['kategori_id' => 1, 'nama' => 'Pisang Goreng', 'deskripsi' => 'Pisang goreng manis', 'harga' => 10000, 'gambar' => 'barang/pisang_goreng.jpg']);
    Barang::create(['kategori_id' => 1, 'nama' => 'Ayam Geprek', 'deskripsi' => 'Ayam geprek pedas', 'harga' => 16000, 'gambar' => 'barang/ayam_geprek.jpg']);

    // Daftar Minuman
    Barang::create(['kategori_id' => 2, 'nama' => 'Air Mineral', 'deskripsi' => 'Air botol 600ml', 'harga' => 4000, 'gambar' => 'barang/air_mineral.jpg']);
    Barang::create(['kategori_id' => 2, 'nama' => 'Kopi Hitam', 'deskripsi' => 'Kopi hitam hangat', 'harga' => 7000, 'gambar' => 'barang/kopi_hitam.jpg']);
    Barang::create(['kategori_id' => 2, 'nama' => 'Es Teh Manis', 'deskripsi' => 'Es teh segar', 'harga' => 5000, 'gambar' => 'barang/es_teh.jpg']);
}
```

#### 5. `TransaksiSeeder.php`
Fungsi: Memasukkan 36 data transaksi secara langsung (tanpa diacak) selama 3 bulan (3 transaksi per minggu).

```php
public function run(): void
{
    $user = User::where('role', 'kasir')->first() ?? User::first(); // Ambil data akun kasir
    $userId = $user->id;

    Transaksi::truncate();                              // Bersihkan data transaksi lama jika seeder dijalankan ulang

    // Transaksi Minggu ke-12 (TRX-0001)
    Transaksi::create(['kode_transaksi' => 'TRX-0001', 'barang_id' => 1, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(84)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 15000, 'subtotal' => 30000]);
    Transaksi::create(['kode_transaksi' => 'TRX-0001', 'barang_id' => 8, 'user_id' => $userId, 'tanggal' => Carbon::now()->subDays(84)->format('Y-m-d'), 'jumlah' => 2, 'harga_satuan' => 5000, 'subtotal' => 10000]);

    // Transaksi selanjutnya disimpang secara teratur dari TRX-0002 sampai TRX-0036...
}
```

---

## 🧩 3. MODELS (`app/Models/`)

Model Eloquent menghubungkan tabel database dengan objek kode PHP.

---

### A. `User.php`
Menghubungkan ke tabel `users`.

```php
class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role']; // Daftar kolom yang diizinkan diisi dari form

    protected $hidden = ['password', 'remember_token'];          // Sembunyikan data password agar tidak tampil sembarangan

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',                 // Mengubah format tanggal verifikasi email
            'password' => 'hashed',                            // Mengamankan password secara otomatis saat disimpan
        ];
    }

    public function transaksi() {
        return $this->hasMany(Transaksi::class);               // Relasi 1-to-Many: 1 User punya banyak Transaksi
    }

    public function isAdmin(): bool {
        return $this->role === 'admin';                        // Fungsi untuk mengecek apakah user berperan sebagai Admin
    }

    public function isKasir(): bool {
        return $this->role === 'kasir';                        // Fungsi untuk mengecek apakah user berperan sebagai Kasir
    }
}
```

---

### B. `Kategori.php`
Menghubungkan ke tabel `kategori`.

```php
class Kategori extends Model
{
    protected $table = 'kategori';                              // Menentukan nama tabel secara langsung
    protected $fillable = ['nama'];                             // Kolom yang diizinkan diisi

    public function barang() {
        return $this->hasMany(Barang::class);                   // Relasi 1-to-Many: 1 Kategori memiliki banyak Barang
    }
}
```

---

### C. `Barang.php`
Menghubungkan ke tabel `barang`.

```php
class Barang extends Model
{
    protected $table = 'barang';                                // Menentukan nama tabel secara langsung

    protected $fillable = [                                     // Daftar kolom barang yang boleh diisi
        'kategori_id', 'nama', 'deskripsi', 'harga', 'gambar', 'is_active',
    ];

    protected $casts = [
        'harga' => 'integer',                                   // Mengubah format harga ke Angka Bulat
        'is_active' => 'boolean',                               // Mengubah status aktif ke Benar/Salah (true/false)
    ];

    public function kategori() {
        return $this->belongsTo(Kategori::class);               // Relasi Many-to-1: Barang termasuk dalam 1 Kategori
    }

    public function transaksi() {
        return $this->hasMany(Transaksi::class);                // Relasi 1-to-Many: Barang bisa ada di banyak Transaksi
    }

    public function getGambarUrlAttribute(): ?string {
        if (!$this->gambar) return null;                        // Jika tidak ada foto, kembalikan kosong
        return asset('storage/' . $this->gambar);               // Fungsi otomatis mengambil tautan/URL foto barang
    }

    public function scopeActive($query) {
        return $query->where('is_active', true);                // Penyaring otomatis: Hanya ambil barang yang aktif dijual
    }
}
```

---

### D. `Transaksi.php`
Menghubungkan ke tabel `transaksi`.

```php
class Transaksi extends Model
{
    protected $table = 'transaksi';                             // Menentukan nama tabel secara langsung

    protected $fillable = [                                     // Daftar kolom transaksi yang boleh diisi
        'kode_transaksi', 'barang_id', 'user_id', 'tanggal', 'jumlah', 'harga_satuan', 'subtotal',
    ];

    protected $casts = [
        'tanggal' => 'date',                                    // Mengubah tanggal ke format Tanggal PHP
        'jumlah' => 'integer',                                  // Mengubah jumlah ke Angka Bulat
        'harga_satuan' => 'integer',                            // Mengubah harga satuan ke Angka Bulat
        'subtotal' => 'integer',                                // Mengubah subtotal ke Angka Bulat
    ];

    public function barang() {
        return $this->belongsTo(Barang::class);               // Relasi Many-to-1: Transaksi mencatat 1 Barang
    }

    public function user() {
        return $this->belongsTo(User::class);                 // Relasi Many-to-1: Transaksi dicatat oleh 1 User
    }

    public function scopeBetweenDates($query, $startDate, $endDate) {
        return $query->whereBetween('tanggal', [$startDate, $endDate]); // Penyaring data transaksi berdasarkan rentang tanggal
    }

    public function scopeKode($query, string $kodeTransaksi) {
        return $query->where('kode_transaksi', $kodeTransaksi); // Penyaring data transaksi berdasarkan Kode Nota
    }

    public static function generateNextKode(): string
    {
        // Mencari nomor transaksi dengan angka paling besar yang tersimpan di database
        $latest = static::select('kode_transaksi')
            ->where('kode_transaksi', 'like', 'TRX-%')
            ->orderByRaw('CAST(SUBSTRING(kode_transaksi, 5) AS UNSIGNED) DESC')
            ->first();

        if (! $latest) {
            return 'TRX-0001';                                  // Jika database masih kosong, mulai dari TRX-0001
        }

        $number = (int) preg_replace('/[^0-9]/', '', $latest->kode_transaksi); // Mengambil angkanya saja dari kode nota

        return sprintf('TRX-%04d', $number + 1);               // Membuat kode nota berikutnya dengan format 4 angka (contoh: TRX-0037)
    }
}
```

---

## 🛠️ 4. SERVICES (`app/Services/ChartImageService.php`)

Service khusus ini bertugas membuat gambar grafik format PNG di server menggunakan pustaka GD PHP. Gambar grafik ini digunakan untuk disisipkan ke dalam file PDF saat laporan diunduh.

```php
namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ChartImageService
{
    // Membuat file gambar grafik garis (Line Chart) tren penjualan bulanan
    public function lineChartToFile(array $months, array $items, string $title): string {
        // Logika menggambarkan garis grafik tren ke file PNG...
        return 'charts/trend_chart.png';                        // Mengembalikan lokasi simpan gambar
    }

    // Membuat file gambar grafik batang (Bar Chart) barang terlaris
    public function barChartToFile(array $data, string $title): string {
        // Logika menggambarkan batang grafik ke file PNG...
        return 'charts/top_barang_chart.png';                   // Mengembalikan lokasi simpan gambar
    }

    // Membersihkan file foto grafik sementara dari folder storage setelah PDF selesai dibuat
    public function deleteFiles(array $paths): void {
        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);            // Hapus file dari folder storage
        }
    }
}
```

---

## 🛡️ 5. MIDDLEWARE (`app/Http/Middleware/`)

Middleware bertindak sebagai penyaring keamanan sebelum pengguna dapat mengakses suatu halaman web.

#### 1. `CustomAuth.php`
```php
public function handle(Request $request, Closure $next)
{
    if (! Auth::check()) {                                     // Mengecek jika pengguna belum login
        return redirect()->route('login')                       // Alihkan ke halaman login
            ->with('error', 'Silakan login terlebih dahulu.');
    }
    return $next($request);                                     // Izinkan lewat jika sudah login
}
```

#### 2. `CheckRole.php`
```php
public function handle(Request $request, Closure $next, string $role)
{
    if (! Auth::check() || Auth::user()->role !== $role) {    // Mengecek jika peran pengguna tidak sesuai
        abort(403, 'Akses ditolak. Anda tidak memiliki izin.'); // Tampilkan pesan ditolak (403 Forbidden)
    }
    return $next($request);                                     // Izinkan lewat jika peran sesuai
}
```

#### 3. `GuestRedirect.php`
```php
public function handle(Request $request, Closure $next)
{
    if (Auth::check()) {                                       // Jika pengguna yang sudah login mencoba buka form login
        return redirect()->route('dashboard');                  // Alihkan otomatis ke Dashboard
    }
    return $next($request);                                     // Izinkan lewat jika belum login
}
```

---

## 🎮 6. CONTROLLERS (`app/Http/Controllers/`)

Controller bertindak sebagai pengatur utama alur kerja dan logika aplikasi.

---

### A. `AuthController.php`
Mengatur proses masuk (login) dan keluar (logout) pengguna.

```php
public function showLoginForm() {
    return view('auth.login');                                  // Tampilkan halaman form login
}

public function login(Request $request) {
    $credentials = $request->validate([                        // Periksa masukan email dan password dari form
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (! Auth::attempt($credentials, $request->boolean('remember'))) { // Cek kesesuaian data ke database
        throw ValidationException::withMessages([
            'email' => 'Email atau password yang Anda masukkan salah.', // Tampilkan pesan salah jika gagal
        ]);
    }

    $request->session()->regenerate();                         // Perbarui ID sesi demi keamanan
    return redirect()->intended(route('dashboard'))->with('success', 'Berhasil login.');
}

public function logout(Request $request) {
    Auth::logout();                                             // Hapus status login pengguna
    $request->session()->invalidate();                         // Bersihkan seluruh data sesi
    $request->session()->regenerateToken();                    // Buat ulang token keamanan CSRF
    return redirect()->route('login')->with('success', 'Berhasil logout.');
}
```

---

### B. `DashboardController.php`
Mengatur halaman Dashboard utama untuk Admin & Kasir.

```php
public function index(Request $request)
{
    if (Auth::user()->isAdmin()) {                              // Jika pengguna yang login adalah Admin:
        $jumlahUser = User::count();                            // Hitung total jumlah akun user
        $jumlahKategori = Kategori::count();                    // Hitung total jumlah kategori
        $jumlahBarang = Barang::count();                        // Hitung total jumlah barang
        $jumlahTransaksi = Transaksi::select('kode_transaksi')->groupBy('kode_transaksi')->get()->count(); // Hitung total jumlah nota

        [$startDate, $endDate] = $this->resolveDateRange(       // Ambil rentang tanggal penyaring
            $request->input('start_date'),
            $request->input('end_date'),
        );

        $trendData = $this->monthlyTrendPerBarang($startDate, $endDate); // Mengambil data grafik 1 (Line - Tren Penjualan)
        $topBarang = $this->topBarang($startDate, $endDate);             // Mengambil data grafik 2 (Bar - Barang Terlaris)

        return view('admin.dashboard.index', compact(           // Kirimkan data ke tampilan dashboard admin
            'jumlahUser', 'jumlahKategori', 'jumlahBarang', 'jumlahTransaksi',
            'trendData', 'topBarang', 'startDate', 'endDate'
        ));
    }

    // Jika pengguna yang login adalah Kasir:
    $jumlahTransaksiHariIni = Transaksi::where('user_id', Auth::id())
        ->whereDate('tanggal', now()->toDateString())
        ->select('kode_transaksi')->groupBy('kode_transaksi')->get()->count(); // Hitung transaksi kasir hari ini

    $barangList = Barang::active()->orderBy('nama')->get();     // Ambil daftar barang yang sedang aktif dijual

    return view('kasir.dashboard.index', compact('jumlahTransaksiHariIni', 'barangList'));
}
```

---

### C. `KategoriController.php`
Mengelola tambah, lihat, ubah, dan hapus (CRUD) kategori barang untuk Admin.

```php
public function index(Request $request) {
    $kategori = Kategori::query()
        ->withCount('barang')                                   // Hitung otomatis jumlah barang di tiap kategori
        ->when($request->search, function ($query, $search) {
            $query->where('nama', 'like', "%{$search}%");       // Menyaring pencarian nama kategori
        })
        ->orderBy('nama')
        ->paginate(10)                                          // Bagi tampilan data 10 baris per halaman
        ->withQueryString();

    return view('admin.kategori.index', compact('kategori'));
}

public function store(Request $request) {
    $validated = $request->validate([                           // Memeriksa masukan nama kategori baru
        'nama' => ['required', 'string', 'max:100', 'unique:kategori,nama'],
    ]);

    Kategori::create($validated);                              // Simpan kategori baru ke database

    return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
}

public function update(Request $request, Kategori $kategori) {
    $validated = $request->validate([                           // Memeriksa masukan nama kategori yang diubah
        'nama' => ['required', 'string', 'max:100', 'unique:kategori,nama,' . $kategori->id],
    ]);

    $kategori->update($validated);                              // Simpan perubahan kategori ke database

    return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
}

public function destroy(Kategori $kategori) {
    $kategori->delete();                                        // Hapus data kategori dari database
    return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
}
```

---

### D. `BarangController.php`
Mengelola data produk barang dan unggah foto untuk Admin.

```php
public function index(Request $request) {
    $barang = Barang::query()
        ->with('kategori')                                      // Mengambil data relasi kategori sekaligus (Eager Loading)
        ->when($request->search, function ($query, $search) {
            $query->where('nama', 'like', "%{$search}%");       // Menyaring pencarian nama barang
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('admin.barang.index', compact('barang'));
}

public function store(Request $request) {
    $validated = $request->validate([                           // Memeriksa masukan data barang dari form
        'nama' => ['required', 'string', 'max:255'],
        'deskripsi' => ['nullable', 'string', 'max:255'],
        'kategori_id' => ['nullable', 'exists:kategori,id'],
        'harga' => ['required', 'integer', 'min:0'],
        'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        'is_active' => ['nullable', 'boolean'],
    ]);

    if ($request->hasFile('gambar')) {                          // Jika pengguna memilih foto untuk diunggah:
        $validated['gambar'] = $request->file('gambar')->store('barang', 'public'); // Simpan file foto ke folder storage
    }

    $validated['is_active'] = $request->boolean('is_active', true);
    Barang::create($validated);                                 // Simpan data barang baru ke database

    return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
}

public function update(Request $request, Barang $barang) {
    $validated = $request->validate([                           // Memeriksa masukan perubahan data barang
        'nama' => ['required', 'string', 'max:255'],
        'deskripsi' => ['nullable', 'string', 'max:255'],
        'kategori_id' => ['nullable', 'exists:kategori,id'],
        'harga' => ['required', 'integer', 'min:0'],
        'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        'is_active' => ['nullable', 'boolean'],
    ]);

    if ($request->hasFile('gambar')) {                          // Jika memilih foto baru:
        if ($barang->gambar) {
            Storage::disk('public')->delete($barang->gambar);   // Hapus foto yang lama agar penyimpan server hemat
        }
        $validated['gambar'] = $request->file('gambar')->store('barang', 'public'); // Simpan foto baru
    }

    $validated['is_active'] = $request->boolean('is_active', true);
    $barang->update($validated);                                // Simpan perubahan ke database

    return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
}

public function destroy(Barang $barang) {
    if ($barang->gambar) {
        Storage::disk('public')->delete($barang->gambar);       // Hapus file foto dari penyimpan server
    }
    $barang->delete();                                          // Hapus data barang dari database
    return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
}
```

---

### E. `TransaksiController.php`
Mengelola pencatatan penjualan untuk Kasir dan Admin.

```php
public function index(Request $request) {
    $query = Transaksi::query()
        ->select([
            'kode_transaksi',
            DB::raw('MIN(id) as id'),
            DB::raw('MAX(tanggal) as tanggal'),
            DB::raw('COUNT(*) as jumlah_item'),
            DB::raw('SUM(subtotal) as total'),                   // Menghitung total belanjaan per kode nota
        ])
        ->when($request->search, function ($q, $search) {
            $q->where('kode_transaksi', 'like', "%{$search}%");
        })
        ->when($request->start_date && $request->end_date, function ($q) use ($request) {
            $q->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        })
        ->groupBy('kode_transaksi')                             // Mengelompokkan baris pesanan berdasarkan nota
        ->orderByDesc('tanggal');

    $transaksi = $query->paginate(10)->withQueryString();
    return view('kasir.transaksi.index', compact('transaksi'));
}

public function create() {
    $barangList = Barang::active()->orderBy('nama')->get();
    $kategoriList = Kategori::with(['barang' => fn ($q) => $q->active()->orderBy('nama')])->get();
    $kodeAuto = Transaksi::generateNextKode();                 // Ambil otomatis kode nota berikutnya (contoh: TRX-0037)

    return view('kasir.transaksi.create', compact('barangList', 'kategoriList', 'kodeAuto'));
}

public function store(Request $request) {
    $validated = $request->validate([                           // Memeriksa masukan data nota & daftar belanjaan
        'kode_transaksi' => ['required', 'string', 'max:50', 'unique:transaksi,kode_transaksi'],
        'tanggal' => ['required', 'date'],
        'items' => ['required', 'array', 'min:1'],
        'items.*.barang_id' => ['required', 'exists:barang,id'],
        'items.*.jumlah' => ['required', 'integer', 'min:1'],
    ]);

    DB::transaction(function () use ($validated) {             // Membungkus simpan data secara bersamaan dan aman (sekaligus)
        foreach ($validated['items'] as $item) {
            $barang = Barang::findOrFail($item['barang_id']);

            Transaksi::create([                                 // Simpan setiap barang belanjaan sebagai 1 baris record
                'kode_transaksi' => $validated['kode_transaksi'],
                'barang_id' => $barang->id,
                'user_id' => Auth::id(),
                'tanggal' => $validated['tanggal'],
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $barang->harga,
                'subtotal' => $barang->harga * $item['jumlah'],
            ]);
        }
    });

    return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil disimpan.');
}

public function destroy(Transaksi $transaksi) {
    Transaksi::kode($transaksi->kode_transaksi)->delete();      // Hapus seluruh baris belanjaan pada kode nota yang sama
    return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
}
```

---

### F. `ReportController.php`
Meng-generate dokumen PDF laporan penjualan menggunakan pustaka **mPDF**.

```php
public function exportPdf(Request $request) {
    [$startDate, $endDate, $chartPaths, $html] = $this->buildPdfHtml($request); // Buat HTML laporan & gambarkan grafik PNG

    $pdfOutput = $this->renderMpdf($html, $startDate, $endDate); // Ubah tampilan HTML menjadi file PDF via mPDF

    $this->chartImageService->deleteFiles($chartPaths);        // Hapus otomatis file gambar grafik sementara dari folder storage

    return $pdfOutput;                                          // Mengirimkan file PDF agar terunduh di browser
}
```

---

## 🛣️ 7. ROUTING (`routes/web.php`)

Routing mendefinisikan tautan URL aplikasi secara teratur yang dilindungi oleh sistem keamanan.

```php
// Tautan Khusus Pengguna Belum Login (Guest)
Route::middleware('guest.redirect')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');        // Halaman Utama -> Form Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');   // Halaman Form Login
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');   // Proses Submit Form Login
});

// Tautan Khusus Pengguna Sudah Login (Authenticated)
Route::middleware('auth.custom')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');      // Proses Keluar/Logout
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // Halaman Dashboard Utama

    // Tautan Khusus Admin (role:admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('kategori', KategoriController::class)->except(['show']);   // Tautan CRUD Kategori
        Route::resource('barang', BarangController::class);                         // Tautan CRUD Barang
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');                          // Laporan Grafik
            Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');          // Cetak PDF + Pembersihan Grafik
            Route::get('/export-pdf-archive', [ReportController::class, 'exportPdfArchive'])->name('export-pdf-archive'); // Cetak PDF Arsip
        });
    });

    // Tautan Transaksi (Dapat diakses Admin & Kasir)
    Route::resource('transaksi', TransaksiController::class);                      // Tautan CRUD Transaksi
});
```

---

## 🎨 8. VIEWS & BLADE COMPONENTS (`resources/views/`)

View bertanggung jawab menampilkan halaman antarmuka HTML menggunakan Blade Templating Engine.

---

### A. Blade Components (`resources/views/components/`)
* **`product-card.blade.php`**: Komponen kartu produk modern dengan foto, nama, deskripsi, harga, dan tombol `+` di pojok kanan atas.
* **`product-grid.blade.php`**: Penata tata letak (grid) responsif pembungkus kartu produk.

---

### B. Master Layout (`resources/views/layouts/app.blade.php`)
Template utama aplikasi yang membungkus navigasi (Header Navbar), area isi konten (`@yield('content')`), pesan pemberitahuan alert, serta skrip JavaScript pendukung.

---

### C. Auth (`resources/views/auth/login.blade.php`)
Halaman form login pengguna dengan tampilan yang rapi dan bersih.

---

### D. Admin Views (`resources/views/admin/`)

1. **`dashboard/index.blade.php`**:
   Dashboard Admin berisi 4 kartu statistik ringkasan, form filter tanggal, **tepat 2 grafik visual Chart.js** (Grafik Line Tren Penjualan & Grafik Bar Barang Terlaris), serta tombol **Cetak PDF**.
2. **`barang/`**:
   * `index.blade.php`: Tabel produk barang dengan foto.
   * `create.blade.php`: Form tambah barang dengan pratinjau gambar langsung (`FileReader` JS).
   * `edit.blade.php`: Form ubah barang dengan pratinjau pembaruan gambar.
   * `show.blade.php`: Tampilan detail data barang beserta foto ukuran penuh.
3. **`kategori/`**:
   Tabel dan form pengelolaan kategori barang.
4. **`reports/`**:
   * `index.blade.php`: Halaman laporan grafik interaktif.
   * `pdf.blade.php`: Template HTML dokumen PDF ukuran A4 untuk mPDF.

---

### E. Kasir Views (`resources/views/kasir/`)

1. **`dashboard/index.blade.php`**:
   Dashboard kasir yang menampilkan ringkasan nota transaksi hari ini dan katalog shortcut menu.
2. **`transaksi/`**:
   * `create.blade.php` **(POS Split View)**: Katalog menu di sebelah kiri, keranjang nota di sebelah kanan, nama barang berupa teks (tanpa pilihan dropdown), tombol pengatur jumlah (`-`, `+`, `✕`), dan kode transaksi otomatis.
   * `index.blade.php`: Tabel nota transaksi penjualan. Menampilkan tombol `+ Tambah Transaksi` dan `Edit` untuk Kasir, serta tombol `Lihat` dan `Hapus` untuk Admin.
   * `show.blade.php`: Struk rincian belanja per nota transaksi.
   * `edit.blade.php`: Form ubah transaksi dengan tampilan POS split view.
