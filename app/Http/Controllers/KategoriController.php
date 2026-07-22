<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Tampilkan daftar kategori barang.
     * withCount('barang') menghitung otomatis jumlah barang per kategori.
     * paginate(10)->withQueryString() membagi data per 10 baris dan mempertahankan query search pada URL.
     */
    public function index(Request $request)
    {
        $kategori = Kategori::query()
            ->withCount('barang') // Menghitung jumlah barang terkait per kategori
            ->when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%"); // Filter pencarian nama kategori
            })
            ->orderBy('nama')
            ->paginate(10) // Paginasi 10 item per halaman
            ->withQueryString(); // Mempertahankan parameter URL saat klik halaman selanjutnya

        return view('admin.kategori.index', compact('kategori'));
    }

    /**
     * Tampilkan form tambah kategori baru.
     */
    public function create()
    {
        return view('admin.kategori.create');
    }

    /**
     * Simpan kategori baru ke database.
     * $request->validate memvalidasi agar nama kategori wajib diisi, string, maksimal 100 karakter, dan belum pernah ada di tabel kategori.
     */
    public function store(Request $request)
    {
        // Memvalidasi data input dari form tambah kategori
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:kategori,nama'],
        ]);

        Kategori::create($validated); // Memasukkan data ter-validasi ke database

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit kategori.
     * Menggunakan Route Model Binding (Kategori $kategori otomatis mencari ID sesuai URL).
     */
    public function edit(Kategori $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    /**
     * Update data kategori di database.
     * Rule 'unique:kategori,nama,'.$kategori->id mengizinkan nama yang sama jika tidak mengubah nama kategori itu sendiri.
     */
    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:kategori,nama,' . $kategori->id],
        ]);

        $kategori->update($validated);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori dari database.
     * Barang yang terkait otomatis berubah menjadi "Tanpa Kategori" (kategori_id = null).
     */
    public function destroy(Kategori $kategori)
    {
        $kategori->delete();

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
