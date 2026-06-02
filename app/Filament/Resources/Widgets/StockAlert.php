<?php

namespace App\Filament\Resources\Widgets;

use App\Models\Customer;
use App\Models\Machine;
use App\Models\Sparepart;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockAlert extends BaseWidget
{
    // 1. URUTAN (Paling Atas)
    protected static ?int $sort = 1;

    // 2. ANTI-GLITCH (Biar enteng pas di-scroll)
    protected static bool $isLazy = true;

    // 3. LEBAR PENUH (Biar simetris di atas)
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Hitung stok kritis
        $lowStockCount = Sparepart::where('stok', '<=', 5)->count();

        // Tambahan: Ambil data mesin & customer biar Boss sekali lihat langsung tahu
        $totalMesin = Machine::count();
        $totalCustomer = Customer::count();

        return [
            // Stat 1: Total Mesin
            Stat::make('Total Unit Mesin', $totalMesin . ' Unit')
                ->description('Total Mesin Gudang dan  Customer DGG')
                ->color('info'),

            // Stat 2: Customer
            Stat::make('Total Customer', $totalCustomer . ' Customer')
                ->description('Unit yang tersebar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            // Stat 3: Sparepart Kritis (Perbaikan kode Boss)
            Stat::make('Sparepart Kritis', $lowStockCount . ' Item')
                ->description($lowStockCount > 0 ? 'Segera belanja stok!' : 'Stok gudang aman')
                ->descriptionIcon($lowStockCount > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($lowStockCount > 0 ? 'danger' : 'success')
                // Chart ini hanya muncul kalau ada barang kritis (visualisasi tren penurunan)
                ->chart($lowStockCount > 0 ? [7, 3, 5, 2, 4, 1] : [1, 1, 1]),
        ];
    }
}
