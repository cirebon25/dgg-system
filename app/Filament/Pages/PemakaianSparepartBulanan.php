<?php

namespace App\Filament\Pages;

use App\Models\Sparepart;
use App\Models\SparepartStockSnapshot;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class PemakaianSparepartBulanan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Pemakaian per Bulan';
    protected static ?string $navigationGroup = 'Laporan';
    protected static string  $view            = 'filament.pages.pemakaian-sparepart-bulanan';
    protected static ?string $title           = 'Pemakaian Sparepart per Bulan';

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

        $snapshots = SparepartStockSnapshot::where(function ($q) use ($tahun) {
            $q->where('tahun', $tahun)
                ->orWhere(function ($q2) use ($tahun) {
                    $q2->where('tahun', $tahun - 1)->where('bulan', 12);
                });
        })
            ->get()
            ->groupBy('sparepart_id');

        $spareparts = Sparepart::orderBy('nama_sparepart')->get();

        $matrix = [];
        $totalPerBulan = array_fill(1, 12, 0);

        foreach ($spareparts as $sp) {
            $snapsForThis = $snapshots[$sp->id] ?? collect();
            $snapsByBulanTahun = $snapsForThis->keyBy(fn($s) => $s->tahun . '-' . $s->bulan);

            $row = [];

            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $akhir = $snapsByBulanTahun[$tahun . '-' . $bulan]->stok_akhir ?? null;

                if ($bulan === 1) {
                    $awal = $snapsByBulanTahun[($tahun - 1) . '-12']->stok_akhir ?? null;
                } else {
                    $awal = $snapsByBulanTahun[$tahun . '-' . ($bulan - 1)]->stok_akhir ?? null;
                }

                if ($awal !== null && $akhir !== null) {
                    $pemakaian = max(0, $awal - $akhir);
                } else {
                    $pemakaian = null;
                }

                $row[$bulan] = $pemakaian;

                if ($pemakaian !== null && $pemakaian > 0) {
                    $totalPerBulan[$bulan] += $pemakaian;
                }
            }

            if (collect($row)->filter(fn($v) => $v !== null)->isNotEmpty()) {
                $matrix[] = [
                    'sparepart' => $sp,
                    'bulanan'   => $row,
                    'total'     => collect($row)->filter()->sum(),
                ];
            }
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
