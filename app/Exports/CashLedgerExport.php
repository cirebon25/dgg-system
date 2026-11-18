<?php

namespace App\Exports;

use App\Models\CashLedger;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashLedgerExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnFormatting,
    ShouldAutoSize
{
    protected float $saldo;
    protected int $no = 0;

    public function __construct(protected int $bulan, protected int $tahun)
    {
        $this->saldo = (float) CashLedger::saldoAwalBulan($tahun, $bulan);
    }

    public function collection()
    {
        return CashLedger::with(['cashMutation.items', 'cashReceipt'])
            ->whereYear('tanggal', $this->tahun)
            ->whereMonth('tanggal', $this->bulan)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'No. Surat',
            'Uraian Transaksi',
            'Pemasukan Kas',
            'Pengeluaran Kas',
            'Sisa Saldo',
        ];
    }

    public function map($row): array
    {
        $this->saldo += (float) $row->uang_masuk - (float) $row->uang_keluar;

        if ($row->cashMutation && $row->cashMutation->items->isNotEmpty()) {
            $uraian = $row->cashMutation->items->pluck('uraian')->filter()->implode(', ');
        } elseif ($row->cashReceipt) {
            $uraian = $row->cashReceipt->uraian ?? $row->cashReceipt->keterangan ?? '-';
        } else {
            $uraian = $row->keterangan ?? '-';
        }

        return [
            ++$this->no,
            $row->tanggal->format('d/m/Y'),
            $row->uang_masuk > 0 ? '-' : ($row->no_surat ?: '-'),
            $uraian,
            (float) $row->uang_masuk,
            (float) $row->uang_keluar,
            $this->saldo,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}