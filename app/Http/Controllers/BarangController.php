<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    /**
     * Tampilkan daftar barang produk.
     * with('kategori') menggunakan Eager Loading agar query SQL efisien (mencegah N+1 Query Problem).
     */
    public function index(Request $request)
    {
        $barang = Barang::query()
            ->with('kategori') // Eager loading relasi kategori
            ->when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%"); // Filter pencarian nama
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.barang.index', compact('barang'));
    }

    /**
     * Tampilkan form tambah barang baru.
     */
    public function create()
    {
        $kategoriList = Kategori::orderBy('nama')->get(); // Data opsi dropdown kategori

        return view('admin.barang.create', compact('kategoriList'));
    }

    /**
     * Simpan barang baru ke database, termasuk mengunggah gambar ke storage disk public.
     * Storage::disk('public')->store() menyimpan file fisik ke storage/app/public/barang.
     */
    public function store(Request $request)
    {
        // Memvalidasi data input barang & file gambar (maksimal 2MB = 2048KB)
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'kategori_id' => ['nullable', 'exists:kategori,id'],
            'harga' => ['required', 'integer', 'min:0'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Cek jika pengguna meng-upload file gambar
        if ($request->hasFile('gambar')) {
            // Disimpan di storage/app/public/barang, diakses via URL publik lewat symlink storage:link
            $validated['gambar'] = $request->file('gambar')->store('barang', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        Barang::create($validated);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail barang beserta gambar & kategori.
     */
    public function show(Barang $barang)
    {
        $barang->load('kategori');

        return view('admin.barang.show', compact('barang'));
    }

    /**
     * Tampilkan form edit barang.
     */
    public function edit(Barang $barang)
    {
        $kategoriList = Kategori::orderBy('nama')->get();

        return view('admin.barang.edit', compact('barang', 'kategoriList'));
    }

    /**
     * Update data barang di database, dan ganti foto di storage jika mengunggah gambar baru.
     * Storage::disk('public')->delete() menghapus file foto lama dari disk server agar tidak menumpuk.
     */
    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'kategori_id' => ['nullable', 'exists:kategori,id'],
            'harga' => ['required', 'integer', 'min:0'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama dari disk public jika sebelumnya sudah ada foto
            if ($barang->gambar) {
                Storage::disk('public')->delete($barang->gambar);
            }

            // Simpan gambar baru
            $validated['gambar'] = $request->file('gambar')->store('barang', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $barang->update($validated);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    /**
     * Hapus barang dari database beserta file gambar fisiknya dari storage disk public.
     */
    public function destroy(Barang $barang)
    {
        if ($barang->gambar) {
            Storage::disk('public')->delete($barang->gambar); // Hapus foto dari server
        }

        $barang->delete(); // Hapus data dari tabel database

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}
