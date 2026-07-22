<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    /**
     * Tampilkan daftar transaksi penjualan.
     * Menggunakan groupBy('kode_transaksi') dan fungsi agregasi DB::raw()
     * untuk menggabungkan beberapa baris barang belanjaan menjadi 1 ringkasan nota.
     */
    public function index(Request $request)
    {
        $query = Transaksi::query()
            ->select([
                'kode_transaksi',
                DB::raw('MIN(id) as id'),
                DB::raw('MAX(tanggal) as tanggal'),
                DB::raw('COUNT(*) as jumlah_item'),
                DB::raw('SUM(subtotal) as total'), // Menghitung total belanja per kode nota
            ])
            ->when($request->search, function ($q, $search) {
                $q->where('kode_transaksi', 'like', "%{$search}%");
            })
            ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                $q->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            })
            ->groupBy('kode_transaksi') // Mengelompokkan per kode nota
            ->orderByDesc('tanggal');

        $transaksi = $query->paginate(10)->withQueryString();

        return view('kasir.transaksi.index', compact('transaksi'));
    }

    /**
     * Tampilkan form tambah transaksi penjualan.
     * Mengambil daftar barang aktif dikelompokkan per kategori untuk tampilan card katalog kasir.
     */
    public function create()
    {
        $barangList = Barang::active()->orderBy('nama')->get();

        $kategoriList = Kategori::with(['barang' => function ($q) {
            $q->active()->orderBy('nama');
        }])->get();

        $kodeAuto = Transaksi::generateNextKode();

        return view('kasir.transaksi.create', compact('barangList', 'kategoriList', 'kodeAuto'));
    }

    /**
     * Simpan nota transaksi baru.
     * Membungkus proses menyimpan beberapa item barang sekaligus menggunakan DB::transaction().
     * Tujuannya menjamin integritas data: jika salah satu item gagal disimpan, seluruh query dibatalkan (rollback).
     */
    public function store(Request $request)
    {
        // Validasi input nota & array barang belanjaan dari kasir
        $validated = $request->validate([
            'kode_transaksi' => ['required', 'string', 'max:50', 'unique:transaksi,kode_transaksi'],
            'tanggal' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'exists:barang,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        // DB::transaction menjamin semua item berhasil disimpan atau tidak sama sekali
        DB::transaction(function () use ($validated) {
            foreach ($validated['items'] as $item) {
                $barang = Barang::findOrFail($item['barang_id']);

                // Simpan setiap item barang sebagai 1 record baris di tabel transaksi
                Transaksi::create([
                    'kode_transaksi' => $validated['kode_transaksi'],
                    'barang_id' => $barang->id,
                    'user_id' => Auth::id(), // ID kasir yang menginput transaksi
                    'tanggal' => $validated['tanggal'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $barang->harga, // Snapshot harga barang saat ini
                    'subtotal' => $barang->harga * $item['jumlah'],
                ]);
            }
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    /**
     * Tampilkan detail rincian belanja per nota transaksi.
     */
    public function show(Transaksi $transaksi)
    {
        $items = Transaksi::with('barang')
            ->kode($transaksi->kode_transaksi)
            ->get();

        return view('kasir.transaksi.show', [
            'kodeTransaksi' => $transaksi->kode_transaksi,
            'tanggal' => $transaksi->tanggal,
            'user' => $transaksi->user,
            'items' => $items,
            'total' => $items->sum('subtotal'),
        ]);
    }

    /**
     * Tampilkan form edit transaksi.
     */
    public function edit(Transaksi $transaksi)
    {
        $items = Transaksi::kode($transaksi->kode_transaksi)->get();
        $barangList = Barang::active()->orderBy('nama')->get();

        $kategoriList = Kategori::with(['barang' => function ($q) {
            $q->active()->orderBy('nama');
        }])->get();

        return view('kasir.transaksi.edit', [
            'kodeTransaksi' => $transaksi->kode_transaksi,
            'tanggal' => $transaksi->tanggal,
            'items' => $items,
            'barangList' => $barangList,
            'kategoriList' => $kategoriList,
        ]);
    }

    /**
     * Update transaksi menggunakan strategi Delete-and-Recreate di dalam DB::transaction.
     * Baris barang lama dihapus sekaligus lalu digantikan dengan baris barang belanjaan baru.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        $kodeLama = $transaksi->kode_transaksi;

        $validated = $request->validate([
            'kode_transaksi' => ['required', 'string', 'max:50'],
            'tanggal' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'exists:barang,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        // Cek jika kode transaksi diubah, pastikan kode baru belum digunakan nota lain
        if ($validated['kode_transaksi'] !== $kodeLama) {
            $kodeSudahDipakai = Transaksi::kode($validated['kode_transaksi'])->exists();

            if ($kodeSudahDipakai) {
                return back()->withInput()->withErrors([
                    'kode_transaksi' => 'Kode transaksi sudah dipakai nota lain.',
                ]);
            }
        }

        // DB::transaction membungkus proses hapus baris lama & simpan baris baru secara atomik
        DB::transaction(function () use ($validated, $kodeLama) {
            // Hapus seluruh baris item barang lama dengan kode nota lama
            Transaksi::kode($kodeLama)->delete();

            // Masukkan ulang baris item barang belanjaan baru
            foreach ($validated['items'] as $item) {
                $barang = Barang::findOrFail($item['barang_id']);

                Transaksi::create([
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

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Hapus transaksi: Seluruh baris barang yang memiliki kode_transaksi yang sama dihapus sekaligus.
     */
    public function destroy(Transaksi $transaksi)
    {
        Transaksi::kode($transaksi->kode_transaksi)->delete();

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
