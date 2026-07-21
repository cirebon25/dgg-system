<?php

namespace App\Filament\Resources\Widgets;

use App\Models\Deployment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class YearlyDeploymentChart extends ChartWidget
{
    protected static ?string $heading = '📈 Tren Penempatan Mesin Tahunan';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    
    public ?string $filter = null;

    public function __construct()
    {
        $this->filter = (string) date('Y');
    }

    protected function getFilters(): ?array
    {
        $currentYear = (int) date('Y');
        $filters = [];
        for ($year = $currentYear; $year >= $currentYear - 5; $year--) {
            $filters[$year] = (string) $year;
        }
        return $filters;
    }

    protected function getData(): array
    {
        $endYear = filter_var($this->filter ?? date('Y'), FILTER_VALIDATE_INT) ?: (int) date('Y');
        $startYear = $endYear - 4;

        $deployments = Deployment::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('count(*) as count')
        )
            ->whereBetween(DB::raw('YEAR(created_at)'), [$startYear, $endYear])
            ->whereNotIn('id', function ($query) {
                $query->select('deployment_id')->from('machine_replacements')->whereNotNull('deployment_id');
            })
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->pluck('count', 'year')
            ->toArray();

        $data = [];
        $labels = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $labels[] = (string) $year;
            $data[] = $deployments[$year] ?? 0;
        }

        return [
            'datasets' => [[
                'label' => "Total Pemasangan Baru (Tren {$startYear} - {$endYear})",
                'data' => $data,
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'borderColor' => '#3b82f6',
                'fill' => 'start',
                'tension' => 0.3,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    // [KENAPA] Mengarah ke view kustom khusus widget dashboard yang menggunakan $this
    protected static string $view = 'filament.widgets.yearly-deployment-chart-with-print';
}