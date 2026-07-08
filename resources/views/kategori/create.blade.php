@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')

    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Tambah Kategori</h1>
        <p class="text-sm text-slate-500">Buat kategori baru untuk mengelompokkan barang.</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 max-w-md">
        <form method="POST" action="{{ route('kategori.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kategori</label>
                <input type="text" name="nama" value="{{ old('nama') }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-sky-400 hover:bg-sky-500 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Simpan
                </button>
                <a href="{{ route('kategori.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection
