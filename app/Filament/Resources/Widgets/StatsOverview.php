<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Machine;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Mesin', Machine::count())
                ->description('Semua unit di gudang & sewa')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('primary'),

            Stat::make('Mesin Tersewa', Machine::where('status', 'Rented')->count())
                ->description('Unit di lokasi customer')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Customer', Customer::count())
                ->description('Pelanggan terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}