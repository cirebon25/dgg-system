<?php

namespace App\Filament\Widgets;

use App\Models\Sparepart;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockAlert extends BaseWidget
{
    // Mengatur urutan agar tampil di paling atas (opsional)
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Menghitung jumlah item yang stoknya 5 atau kurang
        $lowStockCount = Sparepart::where('stok', '<=', 5)->count();

        return [
            Stat::make('Sparepart Kritis', $lowStockCount . ' Item')
                ->description($lowStockCount > 0 ? 'Segera lakukan pengadaan stok!' : 'Stok semua aman')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'danger' : 'success')
                // Menambahkan efek grafik (opsional)
                ->chart($lowStockCount > 0 ? [7, 3, 5, 2, 4, 1] : [1, 1, 1]),
        ];
    }
}