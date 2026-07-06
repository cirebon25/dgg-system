<?php

namespace App\Exports;

use App\Models\Deployment;
use App\Models\ServiceLog;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapUsageExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $month;
    protected $year;

    // Menangkap parameter bulan dan tahun yang dikirim dari halaman Filament
    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year  = $year;
    }

    /**
     * 🌟 KUNCI QUERY UTUH: Mengambil data ranking pemakaian persis seperti di aplikasi
     */
    public function collection()
    {
        $monthlyUsage = ServiceLog::query()
            ->select(
                'machine_id',
                DB::raw('SUM(usage_bw) as monthly_bw'),
                DB::raw('SUM(usage_color) as monthly_color')
            )
            ->whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->groupBy('machine_id');

        $lifetimeUsage = ServiceLog::query()
            ->select(
                'machine_id',
                DB::raw('SUM(usage_bw) as life_bw'),
                DB::raw('SUM(usage_color) as life_color'),
                DB::raw('COUNT(*) as total_kunjungan')
            )
            ->groupBy('machine_id');

        return Deployment::query()
            ->join('customers', 'deployments.customer_id', '=', 'customers.id')
            ->join('machines', 'deployments.machine_id', '=', 'machines.id')
            ->join('rayons', 'customers.rayon_id', '=', 'rayons.id')
            ->leftJoin('technicians', 'customers.technician_id', '=', 'technicians.id')
            ->leftJoinSub($monthlyUsage, 'monthly', function ($join) {
                $join->on('deployments.machine_id', '=', 'monthly.machine_id');
            })
            ->leftJoinSub($lifetimeUsage, 'lifetime', function ($join) {
                $join->on('deployments.machine_id', '=', 'lifetime.machine_id');
            })
            ->select(
                'customers.nama_customer',
                'rayons.nama_rayon',
                'technicians.nama_technician',
                'machines.serial_number',
                'machines.tipe_model',
                'deployments.tanggal_instal',
                DB::raw('COALESCE(monthly.monthly_bw, 0) as total_bw'),
                DB::raw('COALESCE(monthly.monthly_color, 0) as total_color'),
                DB::raw('COALESCE(monthly.monthly_bw, 0) + COALESCE(monthly.monthly_color, 0) as total_bulan'),
                DB::raw('COALESCE(lifetime.life_bw, 0) + COALESCE(lifetime.life_color, 0) as total_hidup'),
                DB::raw('COALESCE(lifetime.total_kunjungan, 0) as total_kunjungan'),
                DB::raw('TIMESTAMPDIFF(MONTH, deployments.tanggal_instal, NOW()) + 1 as lama_pasang')
            )
            ->orderBy(DB::raw('total_bw + total_color'), 'desc')
            ->get()
            ->map(function ($item) {
                // Menghitung nilai rata-rata pemakaian bulanan secara realtime
                $item->rata_rata = round($item->total_hidup / ($item->lama_pasang ?: 1));
                return $item;
            });
    }

    /**
     * 🌟 STRUKTUR KOLOM EXCEL: Menentukan judul baris teratas file Excel
     */
    public function headings(): array
    {
        return [
            'Nama Pelanggan',
            'Rayon',
            'Teknisi Utama',
            'Serial Number',
            'Tipe Model',
            'Tanggal Instal',
            'Usage BW (Bulan Ini)',
            'Usage Color (Bulan Ini)',
            'Total (Bulan Ini)',
            'Total Lifetime',
            'Total Kunjungan',
            'Lama Pasang (Bulan)',
            'Rata-rata / Bulan',
        ];
    }

    /**
     * 🌟 DATA MAPPING: Memetakan setiap baris data database ke kolom Excel
     */
    public function map($item): array
    {
        return [
            $item->nama_customer,
            $item->nama_rayon,
            $item->nama_technician ?? 'Belum Diset',
            $item->serial_number,
            $item->tipe_model,
            $item->tanggal_instal,
            $item->total_bw,
            $item->total_color,
            $item->total_bulan,
            $item->total_hidup,
            $item->total_kunjungan,
            $item->lama_pasang . ' Bulan',
            $item->rata_rata,
        ];
    }

    /**
     * 🌟 DESAIN BADGE & HEADER: Bikin baris judul otomatis tebal biar kelihatan rapi
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}