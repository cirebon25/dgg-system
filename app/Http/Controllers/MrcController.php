<?php

namespace App\Http\Controllers;

use App\Models\ServiceLog;
use App\Models\Machine;
use Illuminate\Http\Request;

class MrcController extends Controller
{
    public function print(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year  = $request->get('year',  date('Y'));

        $logs = ServiceLog::with(['machine', 'customer', 'technician'])
            ->where('tipe_kunjungan', 'MRC')
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal',  $year)
            ->orderBy('tanggal')
            ->get();

        $bulanNama = [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];

        return view('reports.mrc-print', [
            'logs'  => $logs,
            'bulan' => $bulanNama[$month] ?? $month,
            'tahun' => $year,
        ]);
    }

    public function tagihan(Request $request)
{
    $month = $request->get('month', date('m'));
    $year  = $request->get('year',  date('Y'));

    // Ambil semua kontrak aktif beserta mesin & customer
    $contracts = \App\Models\MrcContract::with(['machine', 'customer'])
        ->where('aktif', true)
        ->get();

    // Ambil semua MRC log bulan ini, key by machine_id
    $mrcLogs = \App\Models\ServiceLog::where('tipe_kunjungan', 'MRC')
        ->whereMonth('tanggal', $month)
        ->whereYear('tanggal',  $year)
        ->get()
        ->keyBy('machine_id');

    // Hitung tagihan per kontrak
    $tagihans = $contracts->map(function ($contract) use ($mrcLogs) {
        $log      = $mrcLogs[$contract->machine_id] ?? null;
        $usageBw    = $log ? (int) $log->usage_bw    : 0;
        $usageColor = $log ? (int) $log->usage_color : 0;
        $tagihan  = $contract->hitungTagihan($usageBw, $usageColor);

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

    public function rekap(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year  = $request->get('year',  date('Y'));

        // $mrcLogs harus didefinisikan DULU sebelum dipakai di sortBy
        $mrcLogs = ServiceLog::where('tipe_kunjungan', 'MRC')
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal',  $year)
            ->get()
            ->keyBy('machine_id');

        $machines = Machine::with(['customer.technician'])
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

        return view('reports.mrc-print', [
            'machines' => $machines,
            'mrcLogs'  => $mrcLogs,
            'bulan'    => $bulanNama[$month] ?? $month,
            'tahun'    => $year,
            'month'    => $month,
            'year'     => $year,
        ]);
    }
}