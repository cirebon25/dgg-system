<?php

namespace App\Exports\Sheets;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerSheet implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Customer';
    }

    public function query()
    {
        return Customer::withTrashed()->with(['rayon', 'technician']);
    }

    public function headings(): array
    {
        return ['ID', 'Nama Customer', 'Kota', 'Alamat', 'No. Telp', 'Rayon', 'Teknisi', 'Dibuat', 'Diupdate', 'Dihapus (Arsip)'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->nama_customer,
            $row->kota,
            $row->alamat,
            $row->nomor_telp,
            $row->rayon?->nama_rayon ?? '-',
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
