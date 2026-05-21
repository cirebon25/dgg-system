<?php

namespace App\Exports\Sheets;

use App\Models\Technician;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TechnicianSheet implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function title(): string { return 'Technician'; }

    public function query()
    {
        return Technician::with('rayon');
    }

    public function headings(): array
    {
        return ['ID', 'Nama Teknisi', 'Rayon', 'Nomor HP', 'Dibuat', 'Diupdate'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->nama_technician,
            $row->rayon?->nama_rayon ?? '-',
            $row->nomor_hp,
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