<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceLogSparepart;

class SparepartOutflowController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month', date('m'));
        $year  = $request->query('year',  date('Y'));

        $usages = ServiceLogSparepart::with([
            'sparepart',
            'serviceLog',
            'serviceLog.machine',
            'serviceLog.machine.deployment',
            'serviceLog.machine.deployment.customer',
            'serviceLog.technician',
            'serviceLog.technician.rayon',
        ])
            ->whereHas('serviceLog', function ($q) use ($month, $year) {
                $q->whereMonth('tanggal', (int) $month)
                  ->whereYear('tanggal',  (int) $year);
            })
            ->get();

        $flat = $usages->map(function ($item) {
            $log        = $item->serviceLog;
            $machine    = optional($log)->machine;
            $deploy     = optional($machine)->deployment;
            $customer   = optional($deploy)->customer;
            $technician = optional($log)->technician;
            $rayon      = optional($technician)->rayon;

            return (object) [
                'tanggal'         => optional($log)->tanggal,
                'nama_customer'   => optional($customer)->nama_customer
                                     ?? optional($customer)->nama ?? '-',
                'tipe_model'      => optional($machine)->tipe_model
                                     ?? optional($machine)->tipe ?? '-',
                'serial_number'   => optional($machine)->serial_number
                                     ?? optional($machine)->no_seri ?? '-',
                'usage_bw'        => optional($log)->usage_bw
                                     ?? optional($log)->pemakaian_bw ?? 0,
                'usage_color'     => optional($log)->usage_color
                                     ?? optional($log)->pemakaian_color ?? 0,
                'counter_bw'      => optional($log)->counter_bw
                                     ?? optional($log)->counter_akhir_bw ?? 0,
                'counter_color'   => optional($log)->counter_color
                                     ?? optional($log)->counter_akhir_color ?? 0,
                'nama_part'       => optional($item->sparepart)->nama_sparepart
                                     ?? optional($item->sparepart)->nama_part ?? '-',
                'jumlah_part'     => $item->jumlah ?? $item->qty ?? 1,
                'nama_technician' => optional($technician)->nama_technician
                                     ?? optional($technician)->nama ?? '-',
                'nama_rayon'      => optional($rayon)->nama_rayon
                                     ?? optional($rayon)->nama ?? 'TIDAK DIKETAHUI',
                '_visit_key'      => optional($log)->tanggal
                                     . '|' . (optional($machine)->serial_number ?? optional($machine)->no_seri ?? '')
                                     . '|' . (optional($technician)->nama_technician ?? optional($technician)->nama ?? ''),
            ];
        });

        $groupedUsages = $flat->groupBy('nama_rayon');

        return view('print.sparepart-outflow', compact('groupedUsages', 'month', 'year'));
    }
}