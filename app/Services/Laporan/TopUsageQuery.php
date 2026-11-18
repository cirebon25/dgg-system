<?php

namespace App\Services\Laporan;

use App\Models\MeterReading;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TopUsageQuery
{
    public function get(Carbon $akhirBulan): Collection
    {
        return MeterReading::query()
            ->join('machines', 'machines.id', '=', 'meter_readings.machine_id')
            ->join('customers', 'customers.id', '=', 'meter_readings.customer_id')
            ->leftJoin('rayons', 'rayons.id', '=', 'customers.rayon_id')
            ->where('meter_readings.tanggal', '<=', $akhirBulan)
            ->groupBy(
                'meter_readings.machine_id',
                'machines.serial_number',
                'customers.id',
                'customers.nama_customer',
                'rayons.nama_rayon'
            )
            ->selectRaw("
                meter_readings.machine_id,
                customers.nama_customer,
                machines.serial_number,
                rayons.nama_rayon,

                COALESCE(MAX(meter_readings.counter_bw) - MIN(meter_readings.counter_bw_lalu), 0) AS total_bw,
                COALESCE(MAX(meter_readings.counter_color) - MIN(meter_readings.counter_color_lalu), 0) AS total_color,

                PERIOD_DIFF(DATE_FORMAT(?, '%Y%m'), DATE_FORMAT(MIN(meter_readings.tanggal), '%Y%m')) + 1 AS jumlah_bulan
            ", [
                $akhirBulan->toDateString(),
            ])
            ->get()
            ->each(function ($row) {
                $row->total_semua = $row->total_bw + $row->total_color;
                $row->rata_rata   = round($row->total_semua / ($row->jumlah_bulan ?: 1));
            });
    }
}