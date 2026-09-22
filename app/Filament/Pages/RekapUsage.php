<?php

namespace App\Filament\Pages;

use App\Exports\RekapUsageExport;
use App\Models\Deployment;
use App\Models\ServiceLog;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RekapUsage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Ranking Pemakaian';
    protected static ?string $title = 'Ranking Pemakaian Mesin';
    // protected static ?string $navigationGroup = 'Laporan'; // Komentari baris ini agar tidak muncul di grup navigasi
    protected static string $view = 'filament.pages.rekap-usage';
    // protected static ?int $navigationSort = 16;

    // Tambahkan fungsi ini agar halamannya tidak muncul sama sekali di sidebar/menu navigasi
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    public string $month;
    public string $year;

    public function mount(): void
    {
        $this->month = date('m');
        $this->year  = date('Y');
    }

    public function updatedMonth(): void {}
    public function updatedYear(): void {}

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $filename = 'Rekap_Pemakaian_' . $this->getNamaBulan($this->month) . '_' . $this->year . '.xlsx';
                    return Excel::download(
                        new RekapUsageExport($this->month, $this->year),
                        $filename
                    );
                }),

            Action::make('print')
                ->label('Cetak')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(fn() => $this->dispatch('printPage')),
        ];
    }

    public function getUsageData()
    {
        $month = $this->month;
        $year  = $this->year;

        $monthlyUsage = ServiceLog::query()
            ->select(
                'machine_id',
                DB::raw('SUM(usage_bw) as monthly_bw'),
                DB::raw('SUM(usage_color) as monthly_color')
            )
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->groupBy('machine_id');

        $lifetimeUsage = ServiceLog::query()
            ->select(
                'machine_id',
                DB::raw('SUM(usage_bw) as life_bw'),
                DB::raw('SUM(usage_color) as life_color'),
                DB::raw('COUNT(*) as total_kunjungan')
            )
            ->groupBy('machine_id');

        return Deployment::query()
            ->join('customers', 'deployments.customer_id', '=', 'customers.id')
            ->join('machines', 'deployments.machine_id', '=', 'machines.id')
            ->join('rayons', 'customers.rayon_id', '=', 'rayons.id')
            ->leftJoin('technicians', 'customers.technician_id', '=', 'technicians.id')
            ->leftJoinSub($monthlyUsage, 'monthly', function ($join) {
                $join->on('deployments.machine_id', '=', 'monthly.machine_id');
            })
            ->leftJoinSub($lifetimeUsage, 'lifetime', function ($join) {
                $join->on('deployments.machine_id', '=', 'lifetime.machine_id');
            })
            ->select(
                'customers.nama_customer',
                'rayons.nama_rayon',
                'technicians.nama_technician',
                'machines.serial_number',
                'machines.tipe_model',
                'deployments.tanggal_instal',
                DB::raw('COALESCE(monthly.monthly_bw, 0) as total_bw'),
                DB::raw('COALESCE(monthly.monthly_color, 0) as total_color'),
                DB::raw('COALESCE(monthly.monthly_bw, 0) + COALESCE(monthly.monthly_color, 0) as total_bulan'),
                DB::raw('COALESCE(lifetime.life_bw, 0) as total_bw_life'),
                DB::raw('COALESCE(lifetime.life_color, 0) as total_color_life'),
                DB::raw('COALESCE(lifetime.life_bw, 0) + COALESCE(lifetime.life_color, 0) as total_hidup'),
                DB::raw('COALESCE(lifetime.total_kunjungan, 0) as total_kunjungan'),
                DB::raw('TIMESTAMPDIFF(MONTH, deployments.tanggal_instal, NOW()) + 1 as lama_pasang')
            )
            ->orderByDesc(DB::raw('COALESCE(monthly.monthly_bw, 0) + COALESCE(monthly.monthly_color, 0)'))
            ->get()
            ->map(function ($item) {
                $item->rata_rata = round($item->total_hidup / ($item->lama_pasang ?: 1));
                return $item;
            });
    }

    public function getNamaBulan(string $month): string
    {
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        return $bulan[$month] ?? $month;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(['admin']) ?? false;
    }
}
