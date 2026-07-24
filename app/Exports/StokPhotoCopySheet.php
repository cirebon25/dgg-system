<?php

namespace App\Exports;

use App\Models\Machine;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class StokPhotoCopySheet implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    protected $depo;
    protected $tanggal;

    public function __construct($depo, $tanggal)
    {
        $this->depo = $depo;
        $this->tanggal = $tanggal;
    }

    public function view(): \Illuminate\Contracts\View\View
    {
        $stocks = Machine::select(
            'tipe_model',
            'status',
            'asal_mesin',
            'volt',
            'kaset',
            'finisher',
            'double_scan',
            DB::raw('count(*) as total_unit'),
            DB::raw('SUM(IF(kaset = 4, 1, 0)) as kaset_4_count'),
            DB::raw('GROUP_CONCAT(serial_number ORDER BY serial_number SEPARATOR ", ") as list_sn')
        )
            ->whereIn('status', ['Ready', 'Refurbish'])
            ->groupBy('tipe_model', 'status', 'asal_mesin', 'volt', 'finisher', 'double_scan')
            ->orderBy('tipe_model', 'asc')
            ->orderByRaw("FIELD(status, 'Ready', 'Refurbish') asc")
            ->get();

        return view('excel.stok-photo-copy', [
            'stocks'  => $stocks,
            'depo'    => $this->depo,
            'tanggal' => $this->tanggal,
        ]);
    }

    public function title(): string
    {
        return 'Photo Copy';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
                $event->sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $event->sheet->getPageMargins()->setLeft(0.5);
                $event->sheet->getPageMargins()->setRight(0.5);
                $event->sheet->getPageMargins()->setTop(0.5);
                $event->sheet->getPageMargins()->setBottom(0.5);
            },
        ];
    }
}
