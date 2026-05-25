<?php

namespace App\Exports\Sheets;

use App\Models\Sparepart;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SparepartSheet implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function title(): string { return 'Sparepart'; }

    public function query()
    {
        return Sparepart::query();
    }

    public function headings(): array
    {
        return ['ID', 'Nama Sparepart', 'Kode Part', 'No. Part', 'Stok', 'Harga Beli', 'Saldo Masuk', 'Saldo Keluar', 'Dibuat', 'Diupdate'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->nama_sparepart,
            $row->code_part,
            $row->no_part,
            $row->stok,
            number_format($row->harga_beli, 0, ',', '.'),
            $row->saldo_masuk,
            $row->saldo_keluar,
            $row->created_at?->format('d/m/Y H:i'),
            $row->updated_at?->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            ],
        ];
    }
}