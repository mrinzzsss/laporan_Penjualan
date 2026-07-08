<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard beda tampilan tergantung role:
     * - admin: ringkasan jumlah data semua tabel.
     * - kasir: shortcut buat input transaksi + jumlah transaksi miliknya hari ini.
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $jumlahUser = User::count();
            $jumlahKategori = Kategori::count();
            $jumlahBarang = Barang::count();
            $jumlahTransaksi = Transaksi::count();

            return view('admin.dashboard.index', compact(
                'jumlahUser',
                'jumlahKategori',
                'jumlahBarang',
                'jumlahTransaksi',
            ));
        }

        $jumlahTransaksiHariIni = Transaksi::where('user_id', Auth::id())
            ->whereDate('tanggal', now()->toDateString())
            ->count();

        // Daftar produk aktif untuk shortcut "tambah transaksi" di dashboard,
        // gambar+nama+deskripsi diambil langsung dari tabel barang.
        $barangList = Barang::active()->orderBy('nama')->get();

        return view('kasir.dashboard.index', compact('jumlahTransaksiHariIni', 'barangList'));
    }
}
