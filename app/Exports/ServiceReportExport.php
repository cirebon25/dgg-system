<?php

namespace App\Exports;

use App\Models\Rayon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ServiceReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        // Ambil data deployment beserta relasi customer, machine, dan service logs pada bulan/tahun tertentu
        return \App\Models\Deployment::with([
            'customer.rayon',
            'machine.serviceLogs' => function ($query) {
                $query->whereMonth('tanggal', $this->month)
                    ->whereYear('tanggal', $this->year)
                    ->orderBy('tanggal', 'asc');
            },
        ])->get();
    }

    public function headings(): array
    {
        return [
            'Rayon',
            'Kota',
            'Nama Customer',
            'Alamat',
            'Tipe Mesin',
            'No Seri',
            'Tanggal Pasang',
            'Total RM',
            'Total CM',
            'Total TN',
            'Total RN',
            'Total RR',
            'Status RM',
            'No Kontrak',
        ];
    }

    public function map($deployment): array
    {
        $customer = $deployment->customer;
        $machine = $deployment->machine;
        $logs = $machine ? $machine->serviceLogs : collect();

        $hasRM = $logs->contains(fn($log) => strtoupper($log->tipe_kunjungan) === 'RM');

        return [
            $customer?->rayon?->nama_rayon ?? '-',
            $customer?->kota ?? '-',
            $customer?->nama_customer ?? '-',
            $customer?->alamat ?? '-',
            $machine?->tipe_model ?? '-',
            $machine?->serial_number ?? '-',
            $deployment->tanggal_instal ? \Carbon\Carbon::parse($deployment->tanggal_instal)->format('Y-m-d') : '-',
            $logs->where('tipe_kunjungan', 'RM')->count(),
            $logs->where('tipe_kunjungan', 'CM')->count(),
            $logs->where('tipe_kunjungan', 'TN')->count(),
            $logs->where('tipe_kunjungan', 'RN')->count(),
            $logs->where('tipe_kunjungan', 'RR')->count(),
            $hasRM ? 'SUDAH RM' : 'BLM RM',
            $deployment->no_kontrak ?? ($machine?->no_kontrak ?? '-'),
        ];
    }
}