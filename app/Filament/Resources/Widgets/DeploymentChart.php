<?php

namespace App\Filament\Widgets;

use App\Models\Deployment;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeploymentChart extends ChartWidget
{
    protected static ?string $heading = '📈 Tren Penempatan Mesin Bulanan';

    // Urutan posisi widget di dashboard (Baris ke-2 setelah Stats Overview)
    protected static ?int $sort = 2;

    // Memaksa widget menggunakan seluruh lebar grid (Full Kiri Kanan)
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $currentYear = date('Y');

        // Ambil data penempatan setahun sekaligus (lebih hemat memori)
        $deployments = Deployment::select(
            DB::raw('MONTH(tanggal_instal) as month'),
            DB::raw('count(*) as count')
        )
            ->whereYear('tanggal_instal', $currentYear)
            ->groupBy(DB::raw('MONTH(tanggal_instal)'))
            ->pluck('count', 'month')
            ->toArray();

        $data = [];
        $labels = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create($currentYear, $i, 1)->translatedFormat('M');
            $data[] = $deployments[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Unit Terpasang ' . $currentYear,
                    'data' => $data,
                    'backgroundColor' => 'rgba(251, 191, 36, 0.1)',
                    'borderColor' => '#fbbf24',
                    'fill' => 'start',
                    'tension' => 0.3,
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
