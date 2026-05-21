<?php

namespace App\Exports\Sheets;

use App\Models\MachineReplacement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MachineReplacementSheet implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function title(): string { return 'Machine Replacement'; }

    public function query()
    {
        return MachineReplacement::with(['customer', 'technician', 'oldMachine', 'newMachine']);
    }

    public function headings(): array
    {
        return ['ID', 'Customer', 'Mesin Lama (SN)', 'Mesin Baru (SN)', 'Teknisi', 'Tanggal', 'Keterangan', 'Dibuat', 'Diupdate'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->customer?->nama_customer ?? '-',
            $row->oldMachine?->serial_number ?? '-',
            $row->newMachine?->serial_number ?? '-',
            $row->technician?->nama_technician ?? '-',
            $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') : '-',
            $row->keterangan,
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