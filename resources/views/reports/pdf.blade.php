<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1e293b; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .subtitle { font-size: 11px; color: #64748b; margin-bottom: 16px; }
        .section-title { font-size: 14px; font-weight: bold; margin-top: 18px; margin-bottom: 8px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        .chart-img { width: 100%; max-width: 700px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        th { background-color: #f8fafc; color: #64748b; }
        td.right, th.right { text-align: right; }
        .footer { margin-top: 24px; font-size: 9px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>

    <h1>Laporan Penjualan</h1>
    <div class="subtitle">
        Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        &middot; Dicetak: {{ $generatedAt }}
    </div>

    {{--
        Chart.js (dipakai di halaman web reports/index) berjalan lewat JavaScript/canvas,
        yang TIDAK bisa dirender oleh mpdf. Jadi di sini chart yang sama digambar ulang
        di server (ChartImageService, pakai GD) jadi file PNG fisik di storage/app/public/charts,
        baru file itu ditempel sebagai <img src="path/absolut/ke/file.png">.
    --}}

    <div class="section-title">Tren Penjualan per Barang</div>
    <img class="chart-img" src="{{ $trendChartImage }}" alt="Grafik tren penjualan">

    <div class="section-title">Barang Paling Laku</div>
    <img class="chart-img" src="{{ $topBarangChartImage }}" alt="Grafik barang paling laku">

    <div class="section-title">Distribusi Pendapatan per Kategori</div>
    <img class="chart-img" src="{{ $kategoriChartImage }}" alt="Grafik distribusi kategori">

    <div class="section-title">Detail Barang Terlaris</div>
    <table>
        <thead>
            <tr>
                <th>Barang</th>
                <th>Kategori</th>
                <th class="right">Unit Terjual</th>
                <th class="right">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topBarang as $row)
                <tr>
                    <td>{{ $row->barang_nama }}</td>
                    <td>{{ $row->kategori_nama ?? '-' }}</td>
                    <td class="right">{{ $row->total_jumlah }}</td>
                    <td class="right">Rp {{ number_format($row->total_pendapatan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Tidak ada data transaksi di periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Distribusi per Kategori</div>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th class="right">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($revenueByKategori as $row)
                <tr>
                    <td>{{ $row->kategori_nama }}</td>
                    <td class="right">Rp {{ number_format($row->total_pendapatan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Laporan ini dibuat otomatis oleh sistem.</div>

</body>
</html>
