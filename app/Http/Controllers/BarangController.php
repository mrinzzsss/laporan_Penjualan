<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    /**
     * Tampilkan daftar barang.
     */
    public function index(Request $request)
    {
        $barang = Barang::query()
            ->with('kategori')
            ->when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.barang.index', compact('barang'));
    }

    /**
     * Tampilkan form tambah barang.
     */
    public function create()
    {
        $kategoriList = Kategori::orderBy('nama')->get();

        return view('admin.barang.create', compact('kategoriList'));
    }

    /**
     * Simpan barang baru, termasuk upload gambar ke storage (disk public).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'kode' => ['nullable', 'string', 'max:50', 'unique:barang,kode'],
            'kategori_id' => ['nullable', 'exists:kategori,id'],
            'harga' => ['required', 'integer', 'min:0'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('gambar')) {
            // Disimpan di storage/app/public/barang, diakses lewat symlink storage:link
            $validated['gambar'] = $request->file('gambar')->store('barang', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        Barang::create($validated);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail barang.
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
     * Update barang, termasuk replace gambar di storage jika ada upload baru.
     */
    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'kode' => ['nullable', 'string', 'max:50', 'unique:barang,kode,' . $barang->id],
            'kategori_id' => ['nullable', 'exists:kategori,id'],
            'harga' => ['required', 'integer', 'min:0'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama supaya storage tidak menumpuk file yatim
            if ($barang->gambar) {
                Storage::disk('public')->delete($barang->gambar);
            }

            $validated['gambar'] = $request->file('gambar')->store('barang', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $barang->update($validated);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    /**
     * Hapus barang beserta gambarnya dari storage.
     */
    public function destroy(Barang $barang)
    {
        if ($barang->gambar) {
            Storage::disk('public')->delete($barang->gambar);
        }

        $barang->delete();

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}
