<?php

namespace App\Filament\Pages;

use App\Models\Sparepart;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class PemakaianSparepartBulanan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Pemakaian per Bulan';
    protected static ?string $navigationGroup = 'Gudang & Stok';
    protected static string  $view            = 'filament.pages.pemakaian-sparepart-bulanan';
    protected static ?string $title           = 'Pemakaian Sparepart per Bulan';
    protected static ?int $navigationSort = 10;

    public ?array $data = [];
    public array $matrix = [];
    public array $bulanList = [];
    public array $totalPerBulan = [];

    public function mount(): void
    {
        $this->form->fill([
            'tahun' => date('Y'),
        ]);
        $this->loadMatrix(date('Y'));
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('tahun')
                ->label('Tahun')
                ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                ->live()
                ->afterStateUpdated(fn($state) => $this->loadMatrix($state)),
        ])->statePath('data');
    }

    public function loadMatrix($tahun): void
    {
        $this->bulanList = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        // Semua sparepart, tanpa terkecuali
        $spareparts = Sparepart::orderBy('nama_sparepart')->get();

        // Sumber 1: sparepart dipakai saat servis ke customer
        $serviceUsage = DB::table('service_log_spareparts')
            ->join('service_logs', 'service_logs.id', '=', 'service_log_spareparts.service_log_id')
            ->whereNull('service_logs.deleted_at')
            ->whereYear('service_logs.tanggal', $tahun)
            ->selectRaw('service_log_spareparts.sparepart_id, MONTH(service_logs.tanggal) as bulan, SUM(service_log_spareparts.jumlah) as total')
            ->groupBy('service_log_spareparts.sparepart_id', DB::raw('MONTH(service_logs.tanggal)'))
            ->get();

        // Sumber 2: sparepart terpasang saat deployment/instalasi mesin baru ke customer
        $deploymentUsage = DB::table('deployment_sparepart')
            ->join('deployments', 'deployments.id', '=', 'deployment_sparepart.deployment_id')
            ->whereNull('deployments.deleted_at')
            ->whereYear('deployments.tanggal_instal', $tahun)
            ->selectRaw('deployment_sparepart.sparepart_id, MONTH(deployments.tanggal_instal) as bulan, SUM(deployment_sparepart.jumlah) as total')
            ->groupBy('deployment_sparepart.sparepart_id', DB::raw('MONTH(deployments.tanggal_instal)'))
            ->get();

        // Gabungkan: [sparepart_id][bulan] => total
        $usageMap = [];

        foreach ($serviceUsage as $row) {
            $usageMap[$row->sparepart_id][$row->bulan] = ($usageMap[$row->sparepart_id][$row->bulan] ?? 0) + $row->total;
        }

        foreach ($deploymentUsage as $row) {
            $usageMap[$row->sparepart_id][$row->bulan] = ($usageMap[$row->sparepart_id][$row->bulan] ?? 0) + $row->total;
        }

        $matrix = [];
        $totalPerBulan = array_fill(1, 12, 0);

        foreach ($spareparts as $sp) {
            $row = [];
            $totalRow = 0;

            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $jumlah = (int) ($usageMap[$sp->id][$bulan] ?? 0);
                $row[$bulan] = $jumlah;
                $totalRow += $jumlah;
                $totalPerBulan[$bulan] += $jumlah;
            }

            $matrix[] = [
                'sparepart' => $sp,
                'bulanan'   => $row,
                'total'     => $totalRow,
            ];
        }

        $this->matrix = $matrix;
        $this->totalPerBulan = $totalPerBulan;
    }

    public function cetak()
    {
        $tahun = $this->data['tahun'] ?? date('Y');
        return redirect()->route('sparepart.pemakaian-matrix', ['tahun' => $tahun]);
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(['admin', 'manager']) ?? false;
    }
}