<?php

namespace App\Filament\Resources\Widgets;

use App\Models\Machine;
use App\Models\ServiceLog;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $now = Carbon::now();
        $currentMonthName = $now->translatedFormat('F');

        // Mengambil total penggunaan dari ServiceLog bulan ini
        // Kita tidak memfilter sparepart_id agar usage tetap terhitung
        $usageBW = ServiceLog::whereYear('tanggal', $now->year)
            ->whereMonth('tanggal', $now->month)
            ->sum('usage_bw');

        $usageColor = ServiceLog::whereYear('tanggal', $now->year)
            ->whereMonth('tanggal', $now->month)
            ->sum('usage_color');

        return [
            Stat::make('Total Mesin ( Gudang )', number_format(Machine::where('status', 'Ready')->count(), 0, ',', '.'))
                ->description('Unit mesin siap digunakan')
                ->color('success'),

            Stat::make('Mesin Tersewa', number_format(Machine::where('status', 'Rented')->count(), 0, ',', '.'))
                ->description('Unit aktif di lokasi customer')
                ->color('success'),

            Stat::make("Usage Color ($currentMonthName)", number_format($usageColor, 0, ',', '.') . ' Lbr')
                ->description('Total cetak warna bulan ini')
                ->color('warning'),

            Stat::make("Usage BW ($currentMonthName)", number_format($usageBW, 0, ',', '.') . ' Lbr')
                ->description('Total cetak BW bulan ini')
                ->color('gray'),
        ];
    }
}
