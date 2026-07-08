@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="text-xl font-semibold text-slate-800 mb-6">Dashboard Admin</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500 mb-1">Jumlah User</div>
            <div class="text-3xl font-bold text-slate-800">{{ $jumlahUser }}</div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500 mb-1">Jumlah Kategori</div>
            <div class="text-3xl font-bold text-slate-800">{{ $jumlahKategori }}</div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500 mb-1">Jumlah Barang</div>
            <div class="text-3xl font-bold text-slate-800">{{ $jumlahBarang }}</div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500 mb-1">Jumlah Transaksi</div>
            <div class="text-3xl font-bold text-slate-800">{{ $jumlahTransaksi }}</div>
        </div>

    </div>
</div>
@endsection
