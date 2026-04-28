<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Machine;
use App\Models\ServiceLog; // WAJIB ADA INI
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Mesin', Machine::count())
                ->description('Semua unit DGG')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('primary'),

            Stat::make('Mesin Tersewa', Machine::where('status', 'Rented')->count())
                ->description('Unit di lokasi customer')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Customer', Customer::count())
                ->description('Pelanggan terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            // Statistik Pemakaian Color
            Stat::make('Usage Color (' . Carbon::now()->format('M') . ')', 
                number_format(ServiceLog::whereMonth('tanggal', now()->month)->sum('usage_color')) . ' Lbr')
                ->description('Total cetak warna bulan ini')
                ->descriptionIcon('heroicon-m-presentation-chart-line')
                ->color('warning'),

            // Statistik Pemakaian BW
            Stat::make('Usage BW (' . Carbon::now()->format('M') . ')', 
                number_format(ServiceLog::whereMonth('tanggal', now()->month)->sum('usage_bw')) . ' Lbr')
                ->description('Total cetak hitam-putih')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),
        ];
    }
}