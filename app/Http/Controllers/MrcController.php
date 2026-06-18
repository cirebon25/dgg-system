<?php

namespace App\Http\Controllers;

use App\Models\ServiceLog;
use App\Models\Machine;
use Illuminate\Http\Request;

class MrcController extends Controller
{
    public function rekap(Request $request)
{
    $month = $request->get('month', date('m'));
    $year  = $request->get('year',  date('Y'));

    // Gunakan pluck untuk lookup cepat, bukan load semua kolom
    $mrcLogs = ServiceLog::select([
            'id', 'machine_id', 'tanggal',
            'counter_bw', 'usage_bw',
            'counter_color', 'usage_color'
        ])
        ->where('is_mrc', true)
        ->whereMonth('tanggal', $month)
        ->whereYear('tanggal',  $year)
        ->get()
        ->keyBy('machine_id');

    $machines = Machine::select([
            'id', 'serial_number', 'tipe_model', 'customer_id'
        ])
        ->with([
            'customer:id,nama_customer,technician_id',
            'customer.technician:id,nama_technician',
        ])
        ->where('status', 'Rented')
        ->orderBy('customer_id')
        ->get()
        ->sortBy(fn($machine) => isset($mrcLogs[$machine->id]) ? 0 : 1)
        ->values();

    $bulanNama = [
        '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
        '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
        '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
        '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
    ];

    return view('reports.mrc-rekap', [
        'machines' => $machines,
        'mrcLogs'  => $mrcLogs,
        'bulan'    => $bulanNama[$month] ?? $month,
        'tahun'    => $year,
        'month'    => $month,
        'year'     => $year,
    ]);
}

public function tagihan(Request $request)
{
    $month = $request->get('month', date('m'));
    $year  = $request->get('year',  date('Y'));

    $mrcLogs = ServiceLog::select([
            'id', 'machine_id', 'tanggal',
            'usage_bw', 'usage_color'
        ])
        ->where('is_mrc', true)
        ->whereMonth('tanggal', $month)
        ->whereYear('tanggal',  $year)
        ->get()
        ->keyBy('machine_id');

    $contracts = \App\Models\MrcContract::select([
            'id', 'machine_id', 'customer_id',
            'harga_sewa', 'free_bw', 'harga_bw',
            'free_color', 'harga_color'
        ])
        ->with([
            'machine:id,serial_number,tipe_model',
            'customer:id,nama_customer',
        ])
        ->where('aktif', true)
        ->get();

    $tagihans = $contracts->map(function ($contract) use ($mrcLogs) {
        $log        = $mrcLogs[$contract->machine_id] ?? null;
        $usageBw    = $log ? (int) $log->usage_bw    : 0;
        $usageColor = $log ? (int) $log->usage_color : 0;
        $tagihan    = $contract->hitungTagihan($usageBw, $usageColor);

        return [
            'contract' => $contract,
            'log'      => $log,
            'tagihan'  => $tagihan,
        ];
    });

    $bulanNama = [
        '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
        '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
        '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
        '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
    ];

    return view('reports.mrc-tagihan', [
        'tagihans' => $tagihans,
        'bulan'    => $bulanNama[$month] ?? $month,
        'tahun'    => $year,
    ]);
}
}