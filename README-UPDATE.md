# Update: Card Menu per Kategori + Seeder Manual (Laporan Penjualan)

Project ini beda struktur dari kasus "laporan-kopi" sebelumnya:
- Kategori sudah jadi **tabel terpisah** (`kategori`), bukan kolom string di barang.
- Transaksi berbasis **nota** (`kode_transaksi`): 1 nota bisa berisi banyak baris barang.
- **Cuma 1 role** user (tanpa admin/kasir) dan auth manual (bukan Breeze/Jetstream).
- CRUD transaksi (index/create/edit/show/destroy) **sudah lengkap** dari awal —
  jadi tidak perlu dibuat ulang seperti kasus kopi.

Karena strukturnya beda, penerapan syarat sebelumnya disesuaikan:

## 1. Kolom Deskripsi di Barang
Migration baru `2026_07_05_000001_add_deskripsi_to_barang_table.php` menambah
kolom `deskripsi` (nullable) di tabel `barang`, dipakai untuk teks singkat di
card menu. Ditambahkan juga ke `$fillable` model `Barang`, validasi
`BarangController`, dan form create/edit barang.

## 2. Seeder Manual (Tanpa Loop)
- **`BarangSeeder`**: sebelumnya pakai `Barang::factory()->count(15)->create()`
  (data acak dari faker). Sekarang ditulis manual, 15 barang dengan deskripsi
  jelas, dikelompokkan 5 Makanan + 5 Minuman + 5 Snack.
- **`TransaksiSeeder`**: sebelumnya pakai `while` loop generate 3 bulan data
  acak. Sekarang manual, **7 nota transaksi** tersebar di **2 minggu
  terakhir** (3-4 nota/minggu), tiap nota 1-3 baris barang — sama seperti pola
  di project laporan-kopi.
- `UserSeeder` dan `KategoriSeeder` tidak diubah (sudah simpel dari awal).

## 3. Card Menu per Kategori di Halaman Tambah Transaksi
Sebelumnya form tambah transaksi cuma punya dropdown `<select>` barang biasa.
Sekarang di atas form ditambahkan panel **"Pilih dari Menu"**:

- Barang ditampilkan sebagai **card** (gambar, nama, deskripsi, harga),
  dikelompokkan per **kategori** (Makanan, Minuman, Snack — sesuai data di
  tabel `kategori`).
- Tiap kelompok kategori bisa **di-scroll horizontal**, denah card diatur
  supaya kelihatan maksimal 5 card sekaligus, sisanya swipe ke kanan.
- Tombol **"+"** di pojok kanan atas card membuka **modal kecil** untuk isi
  jumlah, lalu otomatis menambahkan baris ke keranjang nota yang sudah ada
  (fungsi `addItemRow()` yang sebelumnya sudah ada di `create.blade.php`) —
  jadi tidak mengubah cara nota disimpan, cuma mempercepat cara memilih
  barangnya.

### Perubahan Controller
`TransaksiController@create` sekarang juga mengirim `$kategoriList` (kategori
beserta relasi barang aktifnya) ke view, selain `$barangList` yang sudah ada.

## 4. Yang TIDAK Diterapkan (Karena Tidak Relevan di Project Ini)
- **Navbar "Index"/"History"**: tidak diterapkan karena di sini transaksi
  memang cuma satu alur (`transaksi.index` = daftar nota, sudah berfungsi
  sebagai riwayat). Tidak ada pemisahan peran kasir/admin seperti di
  laporan-kopi.
- **CRUD transaksi khusus admin**: tidak perlu dibuat terpisah karena
  `TransaksiController` di project ini sudah CRUD penuh dari awal (tidak ada
  pembatasan role).

## Langkah Setelah Update
```bash
php artisan migrate      # jalankan migration deskripsi baru
php artisan db:seed      # re-seed data (drop dulu tabel lama kalau perlu fresh)
php artisan route:clear
php artisan view:clear
```

Kalau database sudah terisi data lama dan mau full reset ke data baru:
```bash
php artisan migrate:fresh --seed
```

---

## 5. Perbaikan Error `Target class [App\Services\ReportService] does not exist`

Ternyata folder `app/Services` gak ikut ke-zip pas project di-export sebelumnya,
padahal `DashboardController` dan `ReportController` manggil `ReportService` &
`ChartImageService` dari situ. Ini bukan masalah routing.

**Yang sudah diperbaiki:**
- `DashboardController` — dirombak jadi dashboard simpel, cuma hitung jumlah
  data 4 tabel (User, Kategori, Barang, Transaksi). Sudah tidak bergantung ke
  `ReportService` sama sekali.
- `ReportController` — logic yang tadinya ada di `ReportService`
  (`resolveDateRange`, `monthlyTrendPerBarang`, `topBarang`,
  `revenueByKategori`) dipindahkan langsung jadi private method di dalam
  controller ini. Jadi dependency ke `ReportService` sudah dihapus total,
  method publiknya (`index`, `exportPdf`, `exportPdfArchive`) tetap sama
  seperti sebelumnya.

**Yang MASIH perlu dibuat:**
- `ChartImageService` — dipakai di `ReportController` buat gambar ulang
  chart jadi file PNG statis (pakai GD) waktu export PDF. File ini belum
  dibuat ulang, jadi halaman `/reports/export-pdf` dan
  `/reports/export-pdf-archive` masih akan error `Target class
  [App\Services\ChartImageService] does not exist` kalau dicoba sekarang.
  Halaman `/reports` (tampilan chart interaktif di browser) sudah aman
  karena itu tidak pakai `ChartImageService` (chart-nya digambar Chart.js
  di browser, bukan di server).
