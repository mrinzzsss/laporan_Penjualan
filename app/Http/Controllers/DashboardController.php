<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Dashboard simpel: cuma nunjukin jumlah data di tiap tabel.
     * Tidak bergantung ke ReportService (sengaja dilepas, biar tidak
     * error kalau service itu belum/tidak dibuat).
     */
    public function index()
    {
        $jumlahUser = User::count();
        $jumlahKategori = Kategori::count();
        $jumlahBarang = Barang::count();
        $jumlahTransaksi = Transaksi::count();

        return view('dashboard', compact(
            'jumlahUser',
            'jumlahKategori',
            'jumlahBarang',
            'jumlahTransaksi',
        ));
    }
}
