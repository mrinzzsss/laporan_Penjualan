<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    /**
     * Tampilkan daftar transaksi, dikelompokkan per nota (kode_transaksi).
     * Karena tabel "transaksi" flat (1 baris = 1 barang), daftar di sini
     * di-group supaya satu nota tetap tampil sebagai satu baris.
     */
    public function index(Request $request)
    {
        $query = Transaksi::query()
            ->select([
                'kode_transaksi',
                DB::raw('MIN(id) as id'),
                DB::raw('MAX(tanggal) as tanggal'),
                DB::raw('COUNT(*) as jumlah_item'),
                DB::raw('SUM(subtotal) as total'),
            ])
            ->when($request->search, function ($q, $search) {
                $q->where('kode_transaksi', 'like', "%{$search}%");
            })
            ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                $q->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            })
            ->groupBy('kode_transaksi')
            ->orderByDesc('tanggal');

        $transaksi = $query->paginate(10)->withQueryString();

        return view('transaksi.index', compact('transaksi'));
    }

    /**
     * Tampilkan form tambah transaksi.
     */
    public function create()
    {
        $barangList = Barang::active()->orderBy('nama')->get();

        // Dikelompokkan per kategori untuk ditampilkan sebagai card menu
        // di atas form (mempermudah pemilihan barang tanpa scroll dropdown panjang).
        $kategoriList = \App\Models\Kategori::with(['barang' => function ($q) {
            $q->active()->orderBy('nama');
        }])->get();

        return view('transaksi.create', compact('barangList', 'kategoriList'));
    }

    /**
     * Simpan transaksi baru. Satu nota (kode_transaksi) bisa berisi banyak barang,
     * masing-masing disimpan sebagai satu baris terpisah di tabel transaksi.
     * Format input yang diharapkan dari form:
     * kode_transaksi, tanggal, items[][barang_id], items[][jumlah]
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_transaksi' => ['required', 'string', 'max:50', 'unique:transaksi,kode_transaksi'],
            'tanggal' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'exists:barang,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($validated) {
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
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    /**
     * Tampilkan detail transaksi (semua baris dengan kode_transaksi yang sama).
     */
    public function show(Transaksi $transaksi)
    {
        $items = Transaksi::with('barang')
            ->kode($transaksi->kode_transaksi)
            ->get();

        return view('transaksi.show', [
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

        return view('transaksi.edit', [
            'kodeTransaksi' => $transaksi->kode_transaksi,
            'tanggal' => $transaksi->tanggal,
            'items' => $items,
            'barangList' => $barangList,
        ]);
    }

    /**
     * Update transaksi: baris lama (kode_transaksi lama) dihapus, diganti baris baru.
     * Lebih sederhana & aman daripada sinkronisasi parsial per baris.
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

        // Tabel transaksi flat, satu kode_transaksi bisa punya banyak baris.
        // Jadi tidak bisa pakai rule unique bawaan (yang cuma bisa exclude 1 baris).
        // Kalau kode diganti, pastikan kode barunya belum dipakai nota lain secara manual.
        if ($validated['kode_transaksi'] !== $kodeLama) {
            $kodeSudahDipakai = Transaksi::kode($validated['kode_transaksi'])->exists();

            if ($kodeSudahDipakai) {
                return back()->withInput()->withErrors([
                    'kode_transaksi' => 'Kode transaksi sudah dipakai nota lain.',
                ]);
            }
        }

        DB::transaction(function () use ($validated, $kodeLama) {
            Transaksi::kode($kodeLama)->delete();

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
     * Hapus transaksi: semua baris dengan kode_transaksi yang sama dihapus sekaligus.
     */
    public function destroy(Transaksi $transaksi)
    {
        Transaksi::kode($transaksi->kode_transaksi)->delete();

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
