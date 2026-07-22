<?php

namespace App\Filament\Resources\Widgets;

use App\Models\Deployment;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class YearlyDeploymentChart extends Widget
{
    protected static ?string $heading = '📈 Tren Penempatan Mesin Tahunan';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    // Mengarahkan ke view blade kustom untuk tabel
    protected static string $view = 'filament.widgets.yearly-deployment-chart-with-print';

    public ?string $filter = null;

    public function mount(): void
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

    // Method untuk mengambil data tabel berdasarkan filter tahun
    public function getTableData(): array
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

        $reportData = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $reportData[$year] = $deployments[$year] ?? 0;
        }

        return [
            'data' => $reportData,
            'startYear' => $startYear,
            'endYear' => $endYear,
        ];
    }
}
