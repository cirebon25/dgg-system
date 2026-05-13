<?php

namespace App\Exports;

use App\Models\ServiceLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ArchivedServiceLogExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Ambil Log yang diarsip lengkap dengan data Customer & Mesin
        return ServiceLog::onlyTrashed()->with(['customer', 'machine'])->get();
    }

    public function headings(): array
    {
        return ['TANGGAL', 'CUSTOMER', 'SN MESIN', 'TIPE', 'PERBAIKAN'];
    }

    public function map($log): array
    {
        return [
            $log->tanggal->format('d/m/Y'),
            $log->customer->nama_customer ?? 'N/A',
            $log->machine->serial_number ?? 'N/A',
            $log->tipe_kunjungan,
            $log->perbaikan,
        ];
    }
}