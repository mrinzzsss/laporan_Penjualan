@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')

    <div class="mb-6">
        <a href="{{ route('barang.index') }}" class="text-sm text-sky-500 hover:text-sky-600 font-medium">&larr; Kembali ke daftar barang</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 max-w-xl">
        <div class="flex gap-4 items-start mb-4">
            @if ($barang->gambar_url)
                <img src="{{ $barang->gambar_url }}" alt="{{ $barang->nama }}" class="w-20 h-20 rounded-lg object-cover border border-slate-200">
            @else
                <div class="w-20 h-20 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-xs">N/A</div>
            @endif

            <div>
                <h1 class="text-lg font-bold text-slate-800">{{ $barang->nama }}</h1>
                <p class="text-sm text-slate-500">{{ $barang->kategori->nama ?? 'Tanpa kategori' }}</p>
                @if ($barang->is_active)
                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-xs font-medium">Aktif</span>
                @else
                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-medium">Nonaktif</span>
                @endif
            </div>
        </div>

        <dl class="divide-y divide-slate-100 text-sm">
            <div class="flex justify-between py-2">
                <dt class="text-slate-500">Harga</dt>
                <dd class="text-slate-800 font-medium">Rp {{ number_format($barang->harga, 0, ',', '.') }}</dd>
            </div>
            <div class="flex justify-between py-2">
                <dt class="text-slate-500">Total Terjual</dt>
                <dd class="text-slate-800 font-medium">{{ $barang->transaksi->sum('jumlah') }} unit</dd>
            </div>
        </dl>

        <div class="flex gap-3 pt-4">
            <a href="{{ route('barang.edit', $barang) }}" class="bg-sky-400 hover:bg-sky-500 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                Edit Barang
            </a>
        </div>
    </div>

@endsection
