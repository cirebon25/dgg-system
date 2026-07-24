<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StokGudangExport implements WithMultipleSheets
{
    protected $depo;
    protected $tanggal;

    public function __construct($depo = 'Cirebon', $tanggal = null)
    {
        $this->depo = $depo;
        $this->tanggal = $tanggal ?? now();
    }

    public function sheets(): array
    {
        return [
            'Photo Copy'   => new StokPhotoCopySheet($this->depo, $this->tanggal),
            'Dispenser RO' => new StokDispenserRoSheet($this->depo, $this->tanggal),
        ];
    }
}
