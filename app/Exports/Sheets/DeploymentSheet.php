<?php

namespace App\Exports\Sheets;

use App\Models\Deployment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeploymentSheet implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Deployment';
    }

    public function query()
    {
        return Deployment::withTrashed()->with(['customer', 'machine', 'technician']);
    }

    public function headings(): array
    {
        return ['ID', 'Customer', 'Serial Number', 'No. Kontrak', 'Volt', 'Teknisi', 'Tgl Instal', 'Tgl Tarik', 'Counter BW', 'Counter Color', 'Keterangan', 'Dibuat', 'Dihapus (Arsip)'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->customer?->nama_customer ?? '-',
            $row->machine?->serial_number ?? '-',
            $row->no_kontrak,
            $row->volt,
            $row->technician?->nama_technician ?? '-',
            $row->tanggal_instal ? \Carbon\Carbon::parse($row->tanggal_instal)->format('d/m/Y') : '-',
            $row->tanggal_tarik  ? \Carbon\Carbon::parse($row->tanggal_tarik)->format('d/m/Y')  : '-',
            $row->counter_bw,
            $row->counter_color,
            $row->keterangan,
            $row->created_at?->format('d/m/Y H:i'),
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
