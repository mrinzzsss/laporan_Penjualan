@extends('layouts.app')

@section('title', 'Barang')

@section('content')

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Barang</h1>
            <p class="text-sm text-slate-500">Kelola daftar barang yang dijual.</p>
        </div>
        <a href="{{ route('barang.create') }}"
           class="inline-flex items-center gap-2 bg-sky-400 hover:bg-sky-500 text-white text-sm font-medium px-4 py-2 rounded-lg transition w-fit">
            + Tambah Barang
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 mb-4">
        <form method="GET" action="{{ route('barang.index') }}" class="p-4 flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang..."
                   class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
            <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-4 py-2 rounded-lg transition">
                Cari
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Gambar</th>
                    <th class="px-4 py-3 font-medium">Nama</th>
                    <th class="px-4 py-3 font-medium">Kategori</th>
                    <th class="px-4 py-3 font-medium">Harga</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($barang as $item)
                    <tr>
                        <td class="px-4 py-3">
                            @if ($item->gambar_url)
                                <img src="{{ $item->gambar_url }}" alt="{{ $item->nama }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-xs">N/A</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $item->nama }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $item->kategori->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-600">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @if ($item->is_active)
                                <span class="px-2 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-medium">Aktif</span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-medium">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('barang.edit', $item) }}" class="text-sky-500 hover:text-sky-600 font-medium">Edit</a>
                                <form method="POST" action="{{ route('barang.destroy', $item) }}" onsubmit="return confirm('Hapus barang ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-600 font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $barang->links() }}
    </div>

@endsection
