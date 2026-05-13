<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActiveCustomerExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Otomatis hanya mengambil data yang AKTIF (deleted_at IS NULL)
        return Customer::with(['machines', 'rayon'])->get();
    }

    public function headings(): array
    {
        return ['NAMA CUSTOMER', 'KOTA', 'RAYON', 'TOTAL UNIT AKTIF', 'DAFTAR SN MESIN'];
    }

    public function map($cust): array
    {
        return [
            $cust->nama_customer,
            $cust->kota,
            $cust->rayon->nama_rayon ?? '-',
            $cust->machines->count() . ' Unit',
            $cust->machines->pluck('serial_number')->implode(', '),
        ];
    }
}