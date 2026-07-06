<?php

namespace App\Filament\Resources\Widgets;

use App\Models\Deployment;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeploymentChart extends ChartWidget
{
    protected static ?string $heading = '📈 Tren Penempatan Mesin Bulanan';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $currentYear = date('Y');

        // Hanya hitung deployment yang BUKAN hasil Rolling.
        // Deployment hasil rolling akan punya id yang tercatat sebagai
        // 'deployment_id' di tabel machine_replacements (lihat GantiMesin::submit()).
        $deployments = Deployment::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as count')
        )
            ->whereYear('created_at', $currentYear)
            ->whereNotIn('id', function ($query) {
                $query->select('deployment_id')
                    ->from('machine_replacements')
                    ->whereNotNull('deployment_id');
            })
            ->groupBy(DB::raw('MONTH(created_at)'))
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
                    'label' => 'Total Pemasangan Baru (Deploy Murni) ' . $currentYear,
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
