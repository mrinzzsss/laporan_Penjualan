# 🛒 Penjualan Kantin

---

## Pendahuluan

Selamat pagi / siang / sore,

Perkenalkan, saya [**Joel justin Adrian**], mahasiswa [**Jurusan sistem Informasi**].

Pada kesempatan hari ini, saya ingin mempresentasikan project UAS mata kuliah **Pemrograman Web Lanjut** yang telah saya kerjakan.

Project ini bernama **"Penjualan Kantin"** — sebuah aplikasi web berbasis Laravel yang saya rancang untuk membantu pengelolaan penjualan di kantin secara digital, mulai dari pencatatan transaksi oleh kasir hingga pembuatan laporan penjualan oleh admin.

---

## 1. Web Ini Untuk Siapa?

Web ini dibuat untuk **kantin** yang ingin mencatat dan memantau penjualan secara digital.

Ada **2 pengguna** yang bisa menggunakan web ini:

- 👤 **Admin** — mengelola data barang, kategori, dan melihat laporan penjualan
- 👤 **Kasir** — mencatat transaksi penjualan setiap harinya

---

## 2. Apa yang Bisa Dilakukan?

### Kasir
- Login ke web
- Melihat daftar transaksi yang sudah dibuat
- **Tambah transaksi baru** (pilih barang + jumlah)
- Edit dan hapus transaksi
- Lihat detail per nota transaksi

### Admin
- Login ke web
- Kelola **kategori barang** (tambah, edit, hapus)
- Kelola **barang** (tambah, edit, hapus, upload foto)
- Lihat **laporan penjualan** dalam bentuk grafik interaktif
- **Export laporan ke PDF** (bisa disimpan atau dicetak)

---

## 3. Use Case

### Admin

| No | Yang Bisa Dilakukan |
|---|---|
| 1 | Tambah / Edit / Hapus Kategori Barang |
| 2 | Tambah / Edit / Hapus / Lihat Barang |
| 3 | Upload foto barang |
| 4 | Lihat laporan grafik penjualan |
| 5 | Export laporan ke PDF |

### Kasir

| No | Yang Bisa Dilakukan |
|---|---|
| 1 | Lihat daftar transaksi |
| 2 | Tambah transaksi baru |
| 3 | Edit transaksi |
| 4 | Hapus transaksi |
| 5 | Lihat detail nota transaksi |

---

## 4. Penjelasan File Utama

### 📁 Database (Migrations)
Berisi struktur tabel yang dibuat di database.

| Tabel | Isi |
|---|---|
| `users` | Menyimpan data pengguna: nama, email, password, dan role (admin/kasir) |
| `kategori` | Menyimpan nama kategori barang |
| `barang` | Menyimpan nama, harga, foto, status aktif, dan kategori barang |
| `transaksi` | Menyimpan setiap barang yang terjual beserta jumlah dan harganya per nota |

---

### 🧩 Models
Berisi aturan dan relasi antar data.

| Model | Fungsi |
|---|---|
| `User` | Data pengguna, bisa cek apakah dia admin atau kasir |
| `Kategori` | Data kategori, terhubung ke banyak barang |
| `Barang` | Data barang, terhubung ke kategori dan transaksi |
| `Transaksi` | Data penjualan, terhubung ke barang dan user yang input |

---

### 🎮 Controllers
Berisi logika / proses yang terjadi di balik layar.

| Controller | Fungsi |
|---|---|
| `AuthController` | Proses login dan logout |
| `DashboardController` | Menampilkan halaman utama sesuai role (admin/kasir) |
| `KategoriController` | Proses tambah, edit, hapus kategori |
| `BarangController` | Proses tambah, edit, hapus barang + upload foto |
| `TransaksiController` | Proses tambah, edit, hapus transaksi |
| `ReportController` | Menampilkan grafik laporan + export ke PDF |

---

### 🖼️ Views
Berisi tampilan halaman yang dilihat pengguna.

| Folder View | Isi |
|---|---|
| `auth/` | Halaman login |
| `admin/dashboard/` | Dashboard admin (ringkasan jumlah data) |
| `admin/kategori/` | Halaman CRUD kategori |
| `admin/barang/` | Halaman CRUD barang |
| `admin/reports/` | Halaman laporan grafik + template PDF |
| `kasir/dashboard/` | Dashboard kasir (shortcut input transaksi) |
| `kasir/transaksi/` | Halaman CRUD transaksi |

---

## Penutup

Demikian presentasi project **Penjualan Kantin** dari saya.
Aplikasi ini dibangun menggunakan **Laravel** dengan fitur autentikasi, manajemen data, dan laporan PDF.

Terima kasih atas perhatiannya. Saya siap menerima pertanyaan. 🙏
