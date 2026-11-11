<?php

namespace App\Filament\Resources\Widgets;

use App\Models\Customer;
use App\Models\Machine;
use App\Models\MachineReturn;
use App\Models\Sparepart;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockAlert extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = true;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Hitung stok kritis
        $lowStockCount = Sparepart::where('stok', '<=', 5)->count();

        // Hanya mesin yang masih dipegang DGG (Ready + Rented), Returned tidak dihitung
        $totalMesin = Machine::whereIn('status', ['Ready', 'Rented'])->count();
        $totalCustomer = Customer::count();

        // Retur bulan ini & bulan lalu
        $returBulanIni = MachineReturn::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $bulanLalu = now()->subMonthNoOverflow();
        $returBulanLalu = MachineReturn::whereMonth('created_at', $bulanLalu->month)
            ->whereYear('created_at', $bulanLalu->year)
            ->count();

        // Data retur per bulan (Jan - Des tahun ini) untuk grafik mini
        $perBulan = MachineReturn::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $chartRetur = collect(range(1, now()->month))
            ->map(fn($m) => (int) ($perBulan[$m] ?? 0))
            ->all();

        // Selisih dengan bulan lalu
        $selisih = $returBulanIni - $returBulanLalu;
        $deskripsiRetur = 'Bulan lalu: ' . $returBulanLalu . ' unit';
        if ($selisih > 0) {
            $deskripsiRetur .= " (naik {$selisih})";
        } elseif ($selisih < 0) {
            $deskripsiRetur .= ' (turun ' . abs($selisih) . ')';
        }

        return [
            // Stat 1: Total Mesin (tanpa Returned)
            Stat::make('Total Unit Mesin', $totalMesin . ' Unit')
                ->description('Total Mesin Gudang dan Customer DGG')
                ->color('info'),

            // Stat 2: Customer
            Stat::make('Total Customer', $totalCustomer . ' Customer')
                ->description('Unit yang tersebar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            // Stat 3: Retur per bulan
            Stat::make('Retur ' . now()->translatedFormat('F Y'), $returBulanIni . ' Unit')
                ->description($deskripsiRetur)
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->chart(count($chartRetur) > 1 ? $chartRetur : [0, $returBulanIni])
                ->color('warning'),

            // Stat 4: Sparepart Kritis
            Stat::make('Sparepart Kritis', $lowStockCount . ' Item')
                ->description($lowStockCount > 0 ? 'Segera belanja stok!' : 'Stok gudang aman')
                ->descriptionIcon($lowStockCount > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($lowStockCount > 0 ? 'danger' : 'success')
                ->chart($lowStockCount > 0 ? [7, 3, 5, 2, 4, 1] : [1, 1, 1]),
        ];
    }
}
