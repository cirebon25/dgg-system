<?php

namespace App\Exports;

use App\Models\MachineAirRo;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class StokDispenserRoSheet implements FromView, ShouldAutoSize, WithTitle, WithEvents
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
        $machineAirRos = MachineAirRo::select(
            'serial_number',
            'tipe_mesin',
            'status',
            \DB::raw('count(*) as total_unit')
        )
            ->whereIn('status', ['Ready', 'Perbaikan'])
            ->whereNull('customer_ro_id')
            ->groupBy('serial_number', 'tipe_mesin', 'status')
            ->orderBy('tipe_mesin', 'asc')
            ->get();

        return view('excel.stok-dispenser-ro', [
            'machineAirRos' => $machineAirRos,
            'depo'          => $this->depo,
            'tanggal'       => $this->tanggal,
        ]);
    }

    public function title(): string
    {
        return 'Dispenser RO';
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
