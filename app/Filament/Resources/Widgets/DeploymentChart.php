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

    // Menyimpan value filter yang dipilih user (key dari getFilters())
    public ?string $filter = null;

    /**
     * Menyediakan opsi dropdown tahun di header widget.
     * Filament otomatis merender <select> dan menyimpan pilihan di $this->filter.
     */
    protected function getFilters(): ?array
    {
        $years = Deployment::query()
            ->selectRaw('DISTINCT YEAR(created_at) as year')
            ->orderByDesc('year')
            ->pluck('year', 'year')
            ->map(fn($year) => (string) $year)
            ->toArray();

        $currentYear = date('Y');

        // Jaga-jaga: kalau tahun berjalan belum punya data sama sekali,
        // tetap tampilkan sebagai opsi supaya user bisa lihat chart kosong
        if (! isset($years[$currentYear])) {
            $years = [$currentYear => $currentYear] + $years;
        }

        return $years;
    }

    protected function getData(): array
    {
        // Ambil tahun aktif dari filter, fallback ke tahun sekarang
        $selectedYear = $this->filter ?? date('Y');

        $deployments = Deployment::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as count')
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

        $data = [];
        $labels = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create((int) $selectedYear, $i, 1)->translatedFormat('M');
            $data[] = $deployments[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pemasangan Baru (Deploy Murni) ' . $selectedYear,
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
