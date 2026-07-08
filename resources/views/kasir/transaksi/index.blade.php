@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Transaksi Penjualan</h1>
            <p class="text-sm text-slate-500">Daftar nota transaksi yang sudah dicatat.</p>
        </div>
        <a href="{{ route('transaksi.create') }}"
           class="inline-flex items-center gap-2 bg-sky-400 hover:bg-sky-500 text-white text-sm font-medium px-4 py-2 rounded-lg transition w-fit">
            + Tambah Transaksi
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 mb-4">
        <form method="GET" action="{{ route('transaksi.index') }}" class="p-4 grid sm:grid-cols-4 gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode transaksi..."
                   class="sm:col-span-2 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
            <input type="date" name="end_date" value="{{ request('end_date') }}"
                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
            <button type="submit" class="sm:col-span-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-4 py-2 rounded-lg transition w-fit">
                Filter
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Kode Transaksi</th>
                    <th class="px-4 py-3 font-medium">Tanggal</th>
                    <th class="px-4 py-3 font-medium">Jumlah Item</th>
                    <th class="px-4 py-3 font-medium">Total</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($transaksi as $item)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $item->kode_transaksi }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $item->jumlah_item }} barang</td>
                        <td class="px-4 py-3 text-slate-600">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('transaksi.show', $item->id) }}" class="text-slate-500 hover:text-slate-700 font-medium">Lihat</a>
                                <a href="{{ route('transaksi.edit', $item->id) }}" class="text-sky-500 hover:text-sky-600 font-medium">Edit</a>
                                <form method="POST" action="{{ route('transaksi.destroy', $item->id) }}" onsubmit="return confirm('Hapus transaksi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-600 font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $transaksi->links() }}
    </div>

@endsection
