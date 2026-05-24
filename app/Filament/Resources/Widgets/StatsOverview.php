<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
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
        $currentMonthName = Carbon::now()->translatedFormat('F');

        return [
            Stat::make('Total Mesin', number_format(Machine::count(), 0, ',', '.'))
                ->description('Semua unit armada DGG')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('primary'),

            Stat::make('Mesin Tersewa', number_format(Machine::where('status', 'Rented')->count(), 0, ',', '.'))
                ->description('Unit aktif di lokasi customer')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Customer', number_format(Customer::count(), 0, ',', '.'))
                ->description('Pelanggan terdaftar aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make("Usage Color ($currentMonthName)", number_format(ServiceLog::whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->sum('usage_color'), 0, ',', '.') . ' Lbr')
                ->description('Total cetak warna bulan ini')
                ->descriptionIcon('heroicon-m-presentation-chart-line')
                ->color('warning'),

            Stat::make("Usage BW ($currentMonthName)", number_format(ServiceLog::whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->sum('usage_bw'), 0, ',', '.') . ' Lbr')
                ->description('Total cetak hitam-putih')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),
        ];
    }
}