<?php

namespace App\Exports\Sheets;

use App\Models\Machine;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MachineSheet implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Machine';
    }

    public function query()
    {
        return \App\Models\Machine::query()->with(['customer.technician']);
    }

    public function headings(): array
    {
        return ['ID', 'Serial Number', 'Tipe Model', 'Volt', 'Finisher', 'Cover', 'Kaset', 'Double Scan', 'Status', 'Keterangan Awal', 'Customer', 'Teknisi', 'Dibuat', 'Diupdate', 'Dihapus (Arsip)'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->serial_number,
            $row->tipe_model,
            $row->volt,
            $row->finisher,
            $row->cover,
            $row->kaset,
            $row->double_scan,
            $row->status,
            $row->keterangan_awal,
            $row->customer?->nama_customer ?? '-',
            $row->technician?->nama_technician ?? '-',
            $row->created_at?->format('d/m/Y H:i'),
            $row->updated_at?->format('d/m/Y H:i'),
            $row->deleted_at ? $row->deleted_at->format('d/m/Y H:i') . ' (ARSIP)' : 'Aktif',
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
