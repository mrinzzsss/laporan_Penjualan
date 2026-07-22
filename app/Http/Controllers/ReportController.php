<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Services\ChartImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class ReportController extends Controller
{
    /**
     * Dependency Injection via Constructor: Menginjeksikan ChartImageService
     * untuk membuat file gambar grafik PNG fisik di server yang akan ditaruh dalam file PDF.
     */
    public function __construct(
        private readonly ChartImageService $chartImageService,
    ) {
    }

    /**
     * Tampilkan halaman grafik laporan penjualan interaktif (menggunakan Chart.js).
     * Rentang tanggal default: 3 bulan terakhir.
     */
    public function index(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
        );

        $trendData = $this->monthlyTrendPerBarang($startDate, $endDate);
        $topBarang = $this->topBarang($startDate, $endDate);
        $revenueByKategori = $this->revenueByKategori($startDate, $endDate);

        return view('admin.reports.index', [
            'trendData' => $trendData,
            'topBarang' => $topBarang,
            'revenueByKategori' => $revenueByKategori,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
        ]);
    }

    /**
     * Meng-generate dokumen PDF laporan penjualan.
     * Grafik digambar ulang di server sebagai file PNG fisik di folder storage disk public,
     * disisipkan ke dalam HTML Blade PDF, dikonversi menjadi dokumen PDF oleh mPDF,
     * lalu file gambar PNG sementara tersebut dihapus dari disk public (cleanup).
     */
    public function exportPdf(Request $request)
    {
        [$startDate, $endDate, $chartPaths, $html] = $this->buildPdfHtml($request);

        $pdfOutput = $this->renderMpdf($html, $startDate, $endDate);

        // Hapus file fisik grafik PNG dari disk public agar tidak memenuhi storage server
        $this->chartImageService->deleteFiles($chartPaths);

        return $pdfOutput;
    }

    /**
     * Meng-generate PDF laporan sama seperti exportPdf(), tetapi file grafik PNG-nya
     * tidak dihapus dari disk public agar tersimpan sebagai arsip file di server.
     */
    public function exportPdfArchive(Request $request)
    {
        [$startDate, $endDate, , $html] = $this->buildPdfHtml($request);

        return $this->renderMpdf($html, $startDate, $endDate);
    }

    /**
     * Susun ulang data laporan + generate chart sebagai file PNG di storage,
     * lalu render view PDF (masih HTML string, belum jadi PDF).
     */
    private function buildPdfHtml(Request $request): array
    {
        [$startDate, $endDate] = $this->resolveDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
        );

        $trendData = $this->monthlyTrendPerBarang($startDate, $endDate);
        $topBarang = $this->topBarang($startDate, $endDate);
        $revenueByKategori = $this->revenueByKategori($startDate, $endDate);

        $trendChartPath = $this->chartImageService->lineChartToFile(
            $trendData['months'],
            $trendData['items'],
            'Tren Penjualan per Barang (Unit per Bulan)',
        );

        $topBarangChartPath = $this->chartImageService->barChartToFile(
            $topBarang->map(fn ($row) => [
                'label' => $row->barang_nama,
                'value' => (int) $row->total_jumlah,
            ])->all(),
            'Barang Paling Laku',
        );

        $kategoriChartPath = $this->chartImageService->pieChartToFile(
            $revenueByKategori->map(fn ($row) => [
                'label' => $row->kategori_nama,
                'value' => (int) $row->total_pendapatan,
            ])->all(),
            'Distribusi Pendapatan per Kategori',
        );

        $chartPaths = [$trendChartPath, $topBarangChartPath, $kategoriChartPath];

        $html = view('admin.reports.pdf', [
            'trendData' => $trendData,
            'topBarang' => $topBarang,
            'revenueByKategori' => $revenueByKategori,
            'trendChartImage' => Storage::disk('public')->path($trendChartPath),
            'topBarangChartImage' => Storage::disk('public')->path($topBarangChartPath),
            'kategoriChartImage' => Storage::disk('public')->path($kategoriChartPath),
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'generatedAt' => now()->format('d-m-Y H:i'),
        ])->render();

        return [$startDate, $endDate, $chartPaths, $html];
    }

    /** Render HTML jadi response PDF inline lewat mpdf. */
    private function renderMpdf(string $html, $startDate, $endDate)
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        $mpdf->WriteHTML($html);

        $filename = 'laporan-penjualan-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.pdf';

        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Tentukan rentang tanggal laporan. Kalau tidak diisi lewat query string,
     * default-nya 3 bulan terakhir sampai hari ini.
     */
    private function resolveDateRange(?string $start, ?string $end): array
    {
        $endDate = $end ? Carbon::parse($end)->endOfDay() : Carbon::now()->endOfDay();
        $startDate = $start ? Carbon::parse($start)->startOfDay() : $endDate->copy()->subMonths(3)->startOfMonth();

        return [$startDate, $endDate];
    }

    /**
     * Tren penjualan per barang, dikelompokkan per bulan.
     * Return: ['months' => ['2026-05', '2026-06', ...], 'items' => [['name' => ..., 'data' => ['2026-05' => 12, ...]], ...]]
     */
    private function monthlyTrendPerBarang(Carbon $startDate, Carbon $endDate): array
    {
        $rows = Transaksi::query()
            ->join('barang', 'barang.id', '=', 'transaksi.barang_id')
            ->selectRaw("DATE_FORMAT(transaksi.tanggal, '%Y-%m') as bulan, barang.nama as barang_nama, SUM(transaksi.jumlah) as total_jumlah")
            ->whereBetween('transaksi.tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('bulan', 'barang.nama')
            ->orderBy('bulan')
            ->get();

        // Susun daftar bulan berurutan dari startDate s/d endDate (biar bulan yang kosong tetap muncul di chart).
        $months = [];
        $cursor = $startDate->copy()->startOfMonth();
        while ($cursor->lessThanOrEqualTo($endDate)) {
            $months[] = $cursor->format('Y-m');
            $cursor->addMonth();
        }

        // Kelompokkan per nama barang.
        $items = [];
        foreach ($rows as $row) {
            if (! isset($items[$row->barang_nama])) {
                $items[$row->barang_nama] = [
                    'name' => $row->barang_nama,
                    'data' => [],
                ];
            }
            $items[$row->barang_nama]['data'][$row->bulan] = (int) $row->total_jumlah;
        }

        return [
            'months' => $months,
            'items' => array_values($items),
        ];
    }

    /** Ranking barang paling laku (berdasarkan total unit terjual) dalam rentang tanggal. */
    private function topBarang(Carbon $startDate, Carbon $endDate, int $limit = 10)
    {
        return Transaksi::query()
            ->join('barang', 'barang.id', '=', 'transaksi.barang_id')
            ->leftJoin('kategori', 'kategori.id', '=', 'barang.kategori_id')
            ->selectRaw('barang.nama as barang_nama, kategori.nama as kategori_nama, SUM(transaksi.jumlah) as total_jumlah, SUM(transaksi.subtotal) as total_pendapatan')
            ->whereBetween('transaksi.tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('barang.id', 'barang.nama', 'kategori.nama')
            ->orderByDesc('total_jumlah')
            ->limit($limit)
            ->get();
    }

    /** Total pendapatan dikelompokkan per kategori, dalam rentang tanggal. */
    private function revenueByKategori(Carbon $startDate, Carbon $endDate)
    {
        return Transaksi::query()
            ->join('barang', 'barang.id', '=', 'transaksi.barang_id')
            ->leftJoin('kategori', 'kategori.id', '=', 'barang.kategori_id')
            ->selectRaw("COALESCE(kategori.nama, 'Tanpa Kategori') as kategori_nama, SUM(transaksi.subtotal) as total_pendapatan")
            ->whereBetween('transaksi.tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('kategori_nama')
            ->orderByDesc('total_pendapatan')
            ->get();
    }
}
