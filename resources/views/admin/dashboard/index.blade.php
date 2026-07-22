@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">

    {{-- Header & Tombol Generate PDF --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Dashboard Admin</h1>
            <p class="text-sm text-slate-500">Ringkasan statistik penjualan & grafik performa kantin.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.export-pdf', request()->query()) }}" target="_blank"
               class="inline-flex items-center gap-2 bg-sky-400 hover:bg-sky-500 text-white text-sm font-medium px-4 py-2 rounded-lg transition w-fit shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6v-8z"/>
                </svg>
                Cetak PDF
            </a>
            <a href="{{ route('reports.export-pdf-archive', request()->query()) }}" target="_blank"
               title="Chart PNG-nya disimpan permanen di storage (tidak dihapus otomatis)"
               class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-4 py-2 rounded-lg transition w-fit border border-slate-200">
                Cetak PDF (Arsip)
            </a>
        </div>
    </div>

    {{-- Kartu Ringkasan Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Jumlah User</div>
            <div class="text-3xl font-bold text-slate-800">{{ number_format($jumlahUser) }}</div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Jumlah Kategori</div>
            <div class="text-3xl font-bold text-slate-800">{{ number_format($jumlahKategori) }}</div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Jumlah Barang</div>
            <div class="text-3xl font-bold text-slate-800">{{ number_format($jumlahBarang) }}</div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Total Transaksi</div>
            <div class="text-3xl font-bold text-slate-800">{{ number_format($jumlahTransaksi) }}</div>
        </div>

    </div>

    {{-- Filter Tanggal --}}
    <div class="bg-white rounded-xl border border-slate-200 mb-6">
        <form method="GET" action="{{ route('dashboard') }}" class="p-4 grid sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-4 py-2 rounded-lg transition w-full">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- 2 Grafik Utama Dashboard --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Grafik 1: Tren Penjualan per Barang (Line) --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <h2 class="font-semibold text-slate-800 mb-1">Tren Penjualan per Barang</h2>
            <p class="text-xs text-slate-500 mb-4">Jumlah unit terjual per bulan per barang.</p>
            <div class="relative" style="height: 320px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Grafik 2: Barang Paling Laku (Bar) --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <h2 class="font-semibold text-slate-800 mb-1">Barang Paling Laku</h2>
            <p class="text-xs text-slate-500 mb-4">Ranking barang berdasarkan unit terjual.</p>
            <div class="relative" style="height: 320px;">
                <canvas id="topBarangChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Tabel Ringkasan Barang Terlaris --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-800 text-sm">Rincian Barang Terlaris</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Barang</th>
                    <th class="px-4 py-3 font-medium">Kategori</th>
                    <th class="px-4 py-3 font-medium text-right">Unit Terjual</th>
                    <th class="px-4 py-3 font-medium text-right">Pendapatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($topBarang as $row)
                    <tr>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $row->barang_nama }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $row->kategori_nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-slate-600">{{ number_format($row->total_jumlah) }}</td>
                        <td class="px-4 py-3 text-right text-slate-800 font-medium">Rp {{ number_format($row->total_pendapatan, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada data transaksi di rentang ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const trendData = @json($trendData);
    const topBarang = @json($topBarang);

    const palette = ['#38bdf8', '#fb923c', '#a78bfa', '#34d399', '#f472b6', '#facc15', '#60a5fa', '#fb7185'];

    // Grafik 1: Line Chart - Tren per barang per bulan
    const months = trendData.months;
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: trendData.items.map((item, index) => ({
                label: item.name,
                data: months.map(m => item.data[m] ?? 0),
                borderColor: palette[index % palette.length],
                backgroundColor: palette[index % palette.length],
                tension: 0.3,
                fill: false,
            })),
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true } },
        },
    });

    // Grafik 2: Bar Chart - Barang paling laku
    new Chart(document.getElementById('topBarangChart'), {
        type: 'bar',
        data: {
            labels: topBarang.map(r => r.barang_nama),
            datasets: [{
                label: 'Unit Terjual',
                data: topBarang.map(r => r.total_jumlah),
                backgroundColor: '#38bdf8',
                borderRadius: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } },
        },
    });
</script>
@endpush
