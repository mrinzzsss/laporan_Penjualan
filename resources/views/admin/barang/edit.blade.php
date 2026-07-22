@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')

    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Edit Barang</h1>
        <p class="text-sm text-slate-500">Perbarui detail {{ $barang->nama }}.</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 max-w-2xl">
        <form method="POST" action="{{ route('barang.update', $barang) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Barang</label>
                <input type="text" name="nama" value="{{ old('nama', $barang->nama) }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <input type="text" name="deskripsi" value="{{ old('deskripsi', $barang->deskripsi) }}" placeholder="opsional, tampil di card menu"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                @error('deskripsi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                <select name="kategori_id"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                    <option value="">Tanpa kategori</option>
                    @foreach ($kategoriList as $kategori)
                        <option value="{{ $kategori->id }}" {{ old('kategori_id', $barang->kategori_id) == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Harga (Rp)</label>
                <input type="number" name="harga" value="{{ old('harga', $barang->harga) }}" required min="0"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                @error('harga') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Gambar Barang</label>

                <div id="preview-container" class="mb-3 {{ $barang->gambar_url ? '' : 'hidden' }}">
                    <img id="image-preview" src="{{ $barang->gambar_url ?? '#' }}" alt="{{ $barang->nama }}" class="w-24 h-24 rounded-lg object-cover border border-slate-200 shadow-sm">
                </div>

                <input type="file" name="gambar" id="gambar-input" accept="image/*" onchange="previewImage(event)"
                       class="w-full text-sm text-slate-600 border border-slate-300 rounded-lg px-3 py-2 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:bg-sky-50 file:text-sky-600 file:text-sm">
                <p class="text-xs text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengganti gambar. Format JPG/PNG/WEBP, maks 2MB.</p>
                @error('gambar') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $barang->is_active) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-sky-400 focus:ring-sky-400">
                <label for="is_active" class="ml-2 text-sm text-slate-600">Barang aktif (dijual)</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-sky-400 hover:bg-sky-500 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('barang.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image-preview');
                const container = document.getElementById('preview-container');
                preview.src = e.target.result;
                container.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
