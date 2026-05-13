<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ArchivedCustomerExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * WAJIB ADA RETURN DI SINI BOSS!
    */
    public function collection()
    {
        // Kita ambil data customer yang diarsip
        $data = Customer::onlyTrashed()->with(['machines' => function($q) {
            $q->withTrashed();
        }])->get();

        return $data; // <--- PASTIKAN BARIS INI ADA!
    }

    public function headings(): array
    {
        return ['NAMA CUSTOMER', 'KOTA', 'TOTAL UNIT', 'DAFTAR SN MESIN', 'TGL DIARSIP'];
    }

    public function map($cust): array
    {
        return [
            $cust->nama_customer,
            $cust->kota,
            $cust->machines->count() . ' Unit',
            $cust->machines->pluck('serial_number')->implode(', '),
            $cust->deleted_at ? $cust->deleted_at->format('d/m/Y H:i') : '-',
        ];
    }
}