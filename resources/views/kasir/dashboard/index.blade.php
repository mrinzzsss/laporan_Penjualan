@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="text-xl font-semibold text-slate-800 mb-6">Dashboard Kasir</h1>

    <p class="text-slate-600 mb-6">Selamat datang, {{ auth()->user()->name }}. Silakan input transaksi penjualan.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500 mb-1">Transaksi Saya Hari Ini</div>
            <div class="text-3xl font-bold text-slate-800">{{ $jumlahTransaksiHariIni }}</div>
        </div>
    </div>

    <a href="{{ route('transaksi.create') }}"
       class="inline-block bg-sky-400 hover:bg-sky-500 text-white font-medium px-5 py-2.5 rounded-lg mb-8">
        + Input Transaksi Baru
    </a>

    <div>
        <h2 class="text-sm font-semibold text-slate-700 mb-3">Menu / Produk</h2>

        {{--
            Klik "+" di kartu produk langsung menuju halaman Tambah Transaksi
            dengan barang tersebut sudah otomatis dipilihkan (lihat script di create.blade.php).
        --}}
        <x-product-grid
            :barang-list="$barangList"
            :add-url-for="fn ($barang) => route('transaksi.create', ['barang' => $barang->id])"
        />
    </div>
</div>
@endsection
