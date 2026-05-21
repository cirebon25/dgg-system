<?php

namespace App\Exports\Sheets;

use App\Models\ServiceLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServiceLogSheet implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function title(): string
    {
        return 'Service Log';
    }

    public function query()
    {
        return ServiceLog::withTrashed()->with(['machine', 'technician', 'customer']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal',
            'Jam Mulai',
            'Jam Selesai',
            'Customer',
            'Serial Number',
            'Teknisi',
            'Teknisi 2',
            'Tipe Kunjungan',
            'Kerusakan',
            'Perbaikan',
            'Counter BW',
            'Usage BW',
            'Counter Color',
            'Usage Color',
            'Counter Scan',
            'Sparepart',
            'Jumlah Sparepart',
            'Dibuat',
            'Dihapus (Arsip)'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') : '-',
            $row->jam_mulai,
            $row->jam_selesai,
            $row->customer?->nama_customer ?? '-',
            $row->machine?->serial_number ?? '-',
            $row->technician?->nama_technician ?? '-',
            $row->nama_teknisi_2 ?? '-',
            $row->tipe_kunjungan,
            $row->kerusakan,
            $row->perbaikan,
            $row->counter_bw,
            $row->usage_bw,
            $row->counter_color,
            $row->usage_color,
            $row->counter_scan,
            $row->sparepart,
            $row->jumlah_sparepart,
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
