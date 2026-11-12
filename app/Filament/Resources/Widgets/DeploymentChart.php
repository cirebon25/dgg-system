<?php

namespace App\Filament\Resources\Widgets;

use App\Models\Deployment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DeploymentChart extends ChartWidget
{
    /**
     * Palet sejalan dengan token di theme.css.
     * Chart.js tidak bisa membaca variabel CSS, sehingga nilainya ditulis langsung.
     */
    private const COLOR_ACCENT = '#8b5cf6';
    private const COLOR_ACCENT_FILL = 'rgba(139, 92, 246, 0.15)';
    private const COLOR_TEXT_MUTED = '#968caa';
    private const COLOR_GRID = 'rgba(38, 33, 53, 0.9)';

    protected static ?string $heading = 'Tren Penempatan Mesin Bulanan';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public ?string $filter = null;

    /**
     * Opsi tahun untuk dropdown di header widget, diurutkan dari yang terbaru.
     * Tahun berjalan selalu tersedia, meskipun belum ada data.
     *
     * @return array<string, string>
     */
    protected function getFilters(): ?array
    {
        $currentYear = (int) now()->year;

        $years = Deployment::query()
            ->selectRaw('DISTINCT YEAR(created_at) as year')
            ->pluck('year')
            ->map(fn($year) => (int) $year)
            ->push($currentYear)
            ->unique()
            ->sortDesc()
            ->values();

        return $years
            ->mapWithKeys(fn(int $year) => [(string) $year => (string) $year])
            ->all();
    }

    protected function getData(): array
    {
        $selectedYear = (int) ($this->filter ?? now()->year);

        $deployments = Deployment::query()
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', $selectedYear)
            ->whereNotIn('id', function ($query) {
                $query->select('deployment_id')
                    ->from('machine_replacements')
                    ->whereNotNull('deployment_id');
            })
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('count', 'month')
            ->toArray();

        $labels = [];
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = Carbon::createFromDate($selectedYear, $month, 1)
                ->locale('id')
                ->translatedFormat('M');
            $data[] = $deployments[$month] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => "Total Pemasangan Baru (Deploy Murni) {$selectedYear}",
                    'data' => $data,
                    'borderColor' => self::COLOR_ACCENT,
                    'backgroundColor' => self::COLOR_ACCENT_FILL,
                    'pointBackgroundColor' => self::COLOR_ACCENT,
                    'pointBorderColor' => self::COLOR_ACCENT,
                    'fill' => 'start',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'labels' => [
                        'color' => self::COLOR_TEXT_MUTED,
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => ['color' => self::COLOR_TEXT_MUTED],
                    'grid' => ['color' => self::COLOR_GRID],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'color' => self::COLOR_TEXT_MUTED,
                        'precision' => 0,
                    ],
                    'grid' => ['color' => self::COLOR_GRID],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
