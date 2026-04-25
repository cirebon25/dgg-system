<?php

namespace App\Filament\Widgets;

use App\Models\Deployment;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class DeploymentChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Penempatan Mesin Bulanan';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Loop untuk 12 bulan dalam tahun ini
        for ($i = 1; $i <= 12; $i++) {
            // Ambil nama bulan (Jan, Feb, dst)
            $month = Carbon::create(date('Y'), $i, 1);
            $labels[] = $month->translatedFormat('M');

            // Hitung jumlah penempatan berdasarkan kolom tanggal_instal
            $data[] = Deployment::whereYear('tanggal_instal', date('Y'))
                ->whereMonth('tanggal_instal', $i)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Unit Terpasang ' . date('Y'),
                    'data' => $data,
                    'backgroundColor' => 'rgba(251, 191, 36, 0.2)',
                    'borderColor' => '#fbbf24',
                    'fill' => 'start',
                    'tension' => 0.4, // Membuat garis sedikit melengkung agar halus
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}