<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman Dashboard utama.
     * Untuk Admin: menampilkan ringkasan data statistik, filter tanggal, tombol export PDF,
     * serta 3 grafik interaktif (Tren Penjualan, Barang Terlaris, Distribusi Kategori).
     * Untuk Kasir: menampilkan ringkasan transaksi hari ini & shortcut katalog menu.
     */
    public function index(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $jumlahUser = User::count();
            $jumlahKategori = Kategori::count();
            $jumlahBarang = Barang::count();
            $jumlahTransaksi = Transaksi::select('kode_transaksi')->groupBy('kode_transaksi')->get()->count();

            [$startDate, $endDate] = $this->resolveDateRange(
                $request->input('start_date'),
                $request->input('end_date'),
            );

            $trendData = $this->monthlyTrendPerBarang($startDate, $endDate);
            $topBarang = $this->topBarang($startDate, $endDate);
            $revenueByKategori = $this->revenueByKategori($startDate, $endDate);

            return view('admin.dashboard.index', [
                'jumlahUser' => $jumlahUser,
                'jumlahKategori' => $jumlahKategori,
                'jumlahBarang' => $jumlahBarang,
                'jumlahTransaksi' => $jumlahTransaksi,
                'trendData' => $trendData,
                'topBarang' => $topBarang,
                'revenueByKategori' => $revenueByKategori,
                'startDate' => $startDate->toDateString(),
                'endDate' => $endDate->toDateString(),
            ]);
        }

        $jumlahTransaksiHariIni = Transaksi::where('user_id', Auth::id())
            ->whereDate('tanggal', now()->toDateString())
            ->select('kode_transaksi')
            ->groupBy('kode_transaksi')
            ->get()
            ->count();

        $barangList = Barang::active()->orderBy('nama')->get();

        return view('kasir.dashboard.index', compact('jumlahTransaksiHariIni', 'barangList'));
    }

    private function resolveDateRange(?string $start, ?string $end): array
    {
        $endDate = $end ? Carbon::parse($end)->endOfDay() : Carbon::now()->endOfDay();
        $startDate = $start ? Carbon::parse($start)->startOfDay() : $endDate->copy()->subMonths(3)->startOfMonth();

        return [$startDate, $endDate];
    }

    private function monthlyTrendPerBarang(Carbon $startDate, Carbon $endDate): array
    {
        $rows = Transaksi::query()
            ->join('barang', 'barang.id', '=', 'transaksi.barang_id')
            ->selectRaw("DATE_FORMAT(transaksi.tanggal, '%Y-%m') as bulan, barang.nama as barang_nama, SUM(transaksi.jumlah) as total_jumlah")
            ->whereBetween('transaksi.tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('bulan', 'barang.nama')
            ->orderBy('bulan')
            ->get();

        $months = [];
        $cursor = $startDate->copy()->startOfMonth();
        while ($cursor->lessThanOrEqualTo($endDate)) {
            $months[] = $cursor->format('Y-m');
            $cursor->addMonth();
        }

        $items = [];
        foreach ($rows as $row) {
            if (! isset($items[$row->barang_nama])) {
                $items[$row->barang_nama] = [
                    'name' => $row->barang_nama,
                    'data' => [],
                ];
            }
            $items[$row->barang_nama]['data'][$row->bulan] = (int) $row->total_jumlah;
        }

        return [
            'months' => $months,
            'items' => array_values($items),
        ];
    }

    private function topBarang(Carbon $startDate, Carbon $endDate, int $limit = 10)
    {
        return Transaksi::query()
            ->join('barang', 'barang.id', '=', 'transaksi.barang_id')
            ->leftJoin('kategori', 'kategori.id', '=', 'barang.kategori_id')
            ->selectRaw('barang.nama as barang_nama, kategori.nama as kategori_nama, SUM(transaksi.jumlah) as total_jumlah, SUM(transaksi.subtotal) as total_pendapatan')
            ->whereBetween('transaksi.tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('barang.id', 'barang.nama', 'kategori.nama')
            ->orderByDesc('total_jumlah')
            ->limit($limit)
            ->get();
    }

    private function revenueByKategori(Carbon $startDate, Carbon $endDate)
    {
        return Transaksi::query()
            ->join('barang', 'barang.id', '=', 'transaksi.barang_id')
            ->leftJoin('kategori', 'kategori.id', '=', 'barang.kategori_id')
            ->selectRaw("COALESCE(kategori.nama, 'Tanpa Kategori') as kategori_nama, SUM(transaksi.subtotal) as total_pendapatan")
            ->whereBetween('transaksi.tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('kategori_nama')
            ->orderByDesc('total_pendapatan')
            ->get();
    }
}
