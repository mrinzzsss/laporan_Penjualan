@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')

    <div class="mb-6">
        <a href="{{ route('transaksi.index') }}" class="text-sm text-sky-500 hover:text-sky-600 font-medium">&larr; Kembali ke daftar transaksi</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 max-w-2xl">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-lg font-bold text-slate-800">{{ $kodeTransaksi }}</h1>
                <p class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }} &middot; oleh {{ $user->name ?? '-' }}</p>
            </div>
            <a href="{{ route('transaksi.edit', $items->first()->id) }}" class="text-sky-500 hover:text-sky-600 text-sm font-medium">Edit</a>
        </div>

        <table class="w-full text-sm mb-4">
            <thead class="text-slate-500 text-left border-b border-slate-100">
                <tr>
                    <th class="py-2 font-medium">Barang</th>
                    <th class="py-2 font-medium text-right">Qty</th>
                    <th class="py-2 font-medium text-right">Harga</th>
                    <th class="py-2 font-medium text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($items as $item)
                    <tr>
                        <td class="py-2 text-slate-800">{{ $item->barang->nama ?? 'Barang dihapus' }}</td>
                        <td class="py-2 text-right text-slate-600">{{ $item->jumlah }}</td>
                        <td class="py-2 text-right text-slate-600">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="py-2 text-right text-slate-800 font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right text-sm pt-2 border-t border-slate-200">
            Total: <span class="font-semibold text-slate-800 text-base">Rp {{ number_format($total, 0, ',', '.') }}</span>
        </div>
    </div>

@endsection
