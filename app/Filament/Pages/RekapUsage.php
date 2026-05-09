<?php

namespace App\Filament\Pages;

use App\Models\Deployment;
use App\Models\ServiceLog;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class RekapUsage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Ranking Pemakaian';

    protected static ?string $title = 'Customer Pemakaian Terbanyak';

    protected static ?string $navigationGroup = 'Laporan';

    protected static string $view = 'filament.pages.rekap-usage';

    public $month;

    public $year;

    public function mount(): void
    {
        $this->month = date('m');
        $this->year = date('Y');
    }

    public function getUsageData()
    {
        // 1. Ambil pemakaian KHUSUS bulan & tahun yang dipilih (Untuk Kolom BW & Color)
        $monthlyUsage = ServiceLog::query()
            ->select('machine_id',
                DB::raw('SUM(usage_bw) as monthly_bw'),
                DB::raw('SUM(usage_color) as monthly_color'))
            ->whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->groupBy('machine_id');

        // 2. Ambil TOTAL pemakaian seumur hidup (Untuk menghitung Rata-rata)
        $lifetimeUsage = ServiceLog::query()
            ->select('machine_id',
                DB::raw('SUM(usage_bw) as life_bw'),
                DB::raw('SUM(usage_color) as life_color'))
            ->groupBy('machine_id');

        // 3. Gabungkan semua ke Pemasangan & Customer
        return Deployment::query()
            ->join('customers', 'deployments.customer_id', '=', 'customers.id')
            ->join('machines', 'deployments.machine_id', '=', 'machines.id')
            ->leftJoinSub($monthlyUsage, 'monthly', function ($join) {
                $join->on('deployments.machine_id', '=', 'monthly.machine_id');
            })
            ->leftJoinSub($lifetimeUsage, 'lifetime', function ($join) {
                $join->on('deployments.machine_id', '=', 'lifetime.machine_id');
            })
            ->select(
                'customers.nama_customer',
                'machines.serial_number',
                'machines.tipe_model',
                'deployments.tanggal_instal',
                DB::raw('COALESCE(monthly.monthly_bw, 0) as total_bw'),
                DB::raw('COALESCE(monthly.monthly_color, 0) as total_color'),
                DB::raw('COALESCE(lifetime.life_bw, 0) + COALESCE(lifetime.life_color, 0) as total_hidup'),
                DB::raw('TIMESTAMPDIFF(MONTH, deployments.tanggal_instal, NOW()) + 1 as lama_pasang')
            )
            ->orderBy(DB::raw('total_bw + total_color'), 'desc') // Urutkan dari yang terbanyak bulan ini
            ->get()
            ->map(function ($item) {
                // Rata-rata = Total Seumur Hidup / Lama Pasang
                $item->rata_rata = $item->total_hidup / ($item->lama_pasang ?: 1);

                return $item;
            });
    }
}
