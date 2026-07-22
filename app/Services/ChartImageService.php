<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChartImageService
{
    private array $paletteHex = [
        '#38bdf8', '#fb923c', '#a78bfa', '#34d399', '#f472b6', '#facc15', '#60a5fa', '#fb7185'
    ];

    /**
     * Generate Line Chart (Tren Penjualan per Barang) dan simpan sebagai PNG di storage/app/public/charts.
     */
    public function lineChartToFile(array $months, array $items, string $title = ''): string
    {
        $width = 700;
        $height = 320;
        $img = imagecreatetruecolor($width, $height);

        // Colors
        $bg = imagecolorallocate($img, 255, 255, 255);
        $textColor = imagecolorallocate($img, 30, 41, 59); // slate-800
        $subTextColor = imagecolorallocate($img, 100, 116, 139); // slate-500
        $gridColor = imagecolorallocate($img, 226, 232, 240); // slate-200

        imagefill($img, 0, 0, $bg);

        // Header Title
        if ($title) {
            imagestring($img, 4, 20, 15, $title, $textColor);
        }

        $margin = ['top' => 50, 'bottom' => 50, 'left' => 50, 'right' => 20];
        $chartW = $width - $margin['left'] - $margin['right'];
        $chartH = $height - $margin['top'] - $margin['bottom'];

        // Find max value
        $maxVal = 1;
        foreach ($items as $item) {
            foreach ($months as $m) {
                $val = $item['data'][$m] ?? 0;
                if ($val > $maxVal) {
                    $maxVal = $val;
                }
            }
        }
        $maxVal = (int) (ceil($maxVal / 5) * 5); // Round to neat upper bound

        // Draw grid lines & Y labels
        $ySteps = 4;
        for ($i = 0; $i <= $ySteps; $i++) {
            $yVal = ($maxVal / $ySteps) * $i;
            $yPos = $margin['top'] + $chartH - ($i / $ySteps * $chartH);
            imageline($img, $margin['left'], (int)$yPos, $width - $margin['right'], (int)$yPos, $gridColor);
            imagestring($img, 2, 10, (int)$yPos - 7, (string)(int)$yVal, $subTextColor);
        }

        // X Labels
        $numMonths = count($months);
        $colWidth = $numMonths > 1 ? $chartW / ($numMonths - 1) : $chartW;

        foreach ($months as $idx => $m) {
            $xPos = $margin['left'] + ($idx * $colWidth);
            imageline($img, (int)$xPos, $margin['top'], (int)$xPos, $margin['top'] + $chartH, $gridColor);
            imagestring($img, 2, (int)$xPos - 15, $height - $margin['bottom'] + 10, $m, $subTextColor);
        }

        // Draw Data Lines & Dots
        foreach ($items as $itemIdx => $item) {
            $colorHex = $this->paletteHex[$itemIdx % count($this->paletteHex)];
            $lineColor = $this->allocateHex($img, $colorHex);

            $prevX = null;
            $prevY = null;

            foreach ($months as $idx => $m) {
                $val = $item['data'][$m] ?? 0;
                $xPos = $margin['left'] + ($idx * $colWidth);
                $yPos = $margin['top'] + $chartH - (($val / $maxVal) * $chartH);

                imagefilledellipse($img, (int)$xPos, (int)$yPos, 6, 6, $lineColor);

                if ($prevX !== null && $prevY !== null) {
                    imagesetthickness($img, 2);
                    imageline($img, (int)$prevX, (int)$prevY, (int)$xPos, (int)$yPos, $lineColor);
                    imagesetthickness($img, 1);
                }

                $prevX = $xPos;
                $prevY = $yPos;
            }
        }

        return $this->saveAndReturnRelativePath($img, 'line');
    }

    /**
     * Generate Bar Chart (Barang Paling Laku) dan simpan sebagai PNG di storage/app/public/charts.
     */
    public function barChartToFile(array $data, string $title = ''): string
    {
        $width = 700;
        $height = 320;
        $img = imagecreatetruecolor($width, $height);

        $bg = imagecolorallocate($img, 255, 255, 255);
        $textColor = imagecolorallocate($img, 30, 41, 59);
        $subTextColor = imagecolorallocate($img, 100, 116, 139);
        $gridColor = imagecolorallocate($img, 226, 232, 240);
        $barColor = imagecolorallocate($img, 56, 189, 248); // sky-400

        imagefill($img, 0, 0, $bg);

        if ($title) {
            imagestring($img, 4, 20, 15, $title, $textColor);
        }

        $margin = ['top' => 50, 'bottom' => 60, 'left' => 50, 'right' => 20];
        $chartW = $width - $margin['left'] - $margin['right'];
        $chartH = $height - $margin['top'] - $margin['bottom'];

        $count = count($data);
        if ($count === 0) {
            imagestring($img, 3, 50, 100, 'Belum ada data barang.', $subTextColor);
            return $this->saveAndReturnRelativePath($img, 'bar');
        }

        $maxVal = max(array_merge([1], array_map(fn($d) => $d['value'], $data)));
        $maxVal = (int) (ceil($maxVal / 5) * 5);

        // Draw Y Grid
        $ySteps = 4;
        for ($i = 0; $i <= $ySteps; $i++) {
            $yVal = ($maxVal / $ySteps) * $i;
            $yPos = $margin['top'] + $chartH - ($i / $ySteps * $chartH);
            imageline($img, $margin['left'], (int)$yPos, $width - $margin['right'], (int)$yPos, $gridColor);
            imagestring($img, 2, 10, (int)$yPos - 7, (string)(int)$yVal, $subTextColor);
        }

        // Draw Bars
        $barWidth = (int) (($chartW / $count) * 0.6);
        $gap = (int) (($chartW / $count) * 0.4);

        foreach ($data as $idx => $d) {
            $val = $d['value'];
            $label = $d['label'];

            $barH = ($val / $maxVal) * $chartH;
            $x1 = (int) ($margin['left'] + ($idx * ($barWidth + $gap)) + ($gap / 2));
            $y1 = (int) ($margin['top'] + $chartH - $barH);
            $x2 = (int) ($x1 + $barWidth);
            $y2 = (int) ($margin['top'] + $chartH);

            imagefilledrectangle($img, $x1, $y1, $x2, $y2, $barColor);

            // Bar value on top
            imagestring($img, 2, $x1 + (int)($barWidth / 4), max($margin['top'], $y1 - 15), (string)$val, $textColor);

            // X Label
            $truncatedLabel = strlen($label) > 10 ? substr($label, 0, 8) . '..' : $label;
            imagestring($img, 2, $x1 - 5, $height - $margin['bottom'] + 10, $truncatedLabel, $subTextColor);
        }

        return $this->saveAndReturnRelativePath($img, 'bar');
    }

    /**
     * Generate Pie Chart (Distribusi Pendapatan per Kategori) dan simpan sebagai PNG di storage/app/public/charts.
     */
    public function pieChartToFile(array $data, string $title = ''): string
    {
        $width = 700;
        $height = 320;
        $img = imagecreatetruecolor($width, $height);

        $bg = imagecolorallocate($img, 255, 255, 255);
        $textColor = imagecolorallocate($img, 30, 41, 59);
        $subTextColor = imagecolorallocate($img, 100, 116, 139);

        imagefill($img, 0, 0, $bg);

        if ($title) {
            imagestring($img, 4, 20, 15, $title, $textColor);
        }

        $totalVal = array_sum(array_map(fn($d) => $d['value'], $data));
        if ($totalVal <= 0) {
            imagestring($img, 3, 50, 100, 'Belum ada data kategori.', $subTextColor);
            return $this->saveAndReturnRelativePath($img, 'pie');
        }

        $centerX = 200;
        $centerY = 170;
        $pieWidth = 240;
        $pieHeight = 240;

        $startAngle = 0;
        $legendY = 80;

        foreach ($data as $idx => $d) {
            $val = $d['value'];
            $label = $d['label'];
            $pct = round(($val / $totalVal) * 100, 1);
            $sliceAngle = ($val / $totalVal) * 360;
            $endAngle = $startAngle + $sliceAngle;

            $colorHex = $this->paletteHex[$idx % count($this->paletteHex)];
            $sliceColor = $this->allocateHex($img, $colorHex);

            // Draw filled slice
            imagefilledarc(
                $img,
                $centerX,
                $centerY,
                $pieWidth,
                $pieHeight,
                (int) $startAngle,
                (int) $endAngle,
                $sliceColor,
                IMG_ARC_PIE
            );

            $startAngle = $endAngle;

            // Draw Legend box & label on right side
            if ($legendY < $height - 30) {
                imagefilledrectangle($img, 380, $legendY, 395, $legendY + 12, $sliceColor);
                $legendText = "{$label} ({$pct}%) - Rp " . number_format($val, 0, ',', '.');
                imagestring($img, 2, 405, $legendY, $legendText, $textColor);
                $legendY += 25;
            }
        }

        return $this->saveAndReturnRelativePath($img, 'pie');
    }

    /**
     * Hapus file-file grafik PNG fisik sementara dari storage disk public.
     */
    public function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /** Helper to save GD image to storage/app/public/charts and destroy GD handle. */
    private function saveAndReturnRelativePath($img, string $prefix): string
    {
        $dir = 'charts';
        if (! Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        $filename = "{$prefix}-" . Str::random(12) . '-' . time() . '.png';
        $relativePath = "{$dir}/{$filename}";
        $fullPath = Storage::disk('public')->path($relativePath);

        imagepng($img, $fullPath);
        imagedestroy($img);

        return $relativePath;
    }

    /** Helper to allocate RGB color from hex string like #38bdf8 */
    private function allocateHex($img, string $hex): int
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return imagecolorallocate($img, $r, $g, $b);
    }
}
