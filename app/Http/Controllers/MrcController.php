<?php

namespace App\Http\Controllers;

use App\Models\ServiceLog;
use App\Models\Machine;
use App\Models\MrcContract;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MrcController extends Controller
{
    /**
     * Array referensi nama bulan dalam Bahasa Indonesia.
     */
    protected array $bulanNama = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];

    /**
     * Print Log MRC umum.
     */
    public function print(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year  = $request->get('year',  date('Y'));

        $logs = ServiceLog::with(['machine', 'customer', 'technician'])
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal',  $year)
            ->orderBy('tanggal')
            ->get();

        return view('reports.mrc-print', [
            'logs'  => $logs,
            'bulan' => $this->bulanNama[$month] ?? $month,
            'tahun' => $year,
        ]);
    }

    /**
     * Rekap MRC (Menampilkan data mesin rental dan log MRC pada bulan/tahun tertentu).
     */
    public function rekap(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year  = $request->get('year',  date('Y'));

        $mrcLogs = ServiceLog::select([
            'id',
            'machine_id',
            'tanggal',
            'counter_bw',
            'usage_bw',
            'counter_color',
            'usage_color'
        ])
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal',  $year)
            ->get()
            ->keyBy('machine_id');

        $machines = Machine::select([
            'id',
            'serial_number',
            'tipe_model',
            'customer_id'
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

        return view('reports.mrc-rekap', [
            'machines' => $machines,
            'mrcLogs'  => $mrcLogs,
            'bulan'    => $this->bulanNama[$month] ?? $month,
            'tahun'    => $year,
            'month'    => $month,
            'year'     => $year,
        ]);
    }

    /**
     * Cetak Rekap MRC (Alias / Endpoint yang dipanggil dari action Filament).
     */
    public function cetakRekap(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year  = $request->get('year',  date('Y'));

        $mrcLogs = ServiceLog::with(['machine.customer', 'technician'])
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal',  $year)
            ->get();

        return view('exports.mrc-rekap', [
            'mrcLogs' => $mrcLogs,
            'bulan'   => $this->bulanNama[$month] ?? $month,
            'tahun'   => $year,
            'month'   => $month,
            'year'    => $year,
        ]);
    }

    /**
     * Kalkulasi Tagihan & Usage Bulanan (Counter Lalu vs Counter Ini & Selisih).
     */
    public function tagihan(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year  = $request->get('year',  date('Y'));

        $contracts = MrcContract::select([
            'id',
            'machine_id',
            'customer_id',
            'harga_sewa',
            'free_bw',
            'harga_bw',
            'free_color',
            'harga_color'
        ])
            ->with([
                'machine:id,serial_number,tipe_model',
                'customer:id,nama_customer',
            ])
            ->where('aktif', true)
            ->get();

        $tanggalAwalBulan = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfMonth();

        $tagihans = $contracts->map(function ($contract) use ($month, $year, $tanggalAwalBulan) {
            // Log MRC bulan ini (paling akhir di bulan tersebut)
            $logSekarang = ServiceLog::where('machine_id', $contract->machine_id)
                ->whereMonth('tanggal', $month)
                ->whereYear('tanggal',  $year)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            // Log MRC sebelum bulan ini (paling akhir sebelum periode ini) sebagai counter bulan lalu
            $logSebelumnya = ServiceLog::where('machine_id', $contract->machine_id)
                ->where('tanggal', '<', $tanggalAwalBulan)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $usageBw    = 0;
            $usageColor = 0;

            $isMrcTercatat = $logSekarang !== null;
            $adaBaseline   = $logSebelumnya !== null;

            if ($isMrcTercatat && $adaBaseline) {
                $bwLalu    = $logSebelumnya->counter_bw    ?? 0;
                $colorLalu = $logSebelumnya->counter_color ?? 0;

                $usageBw    = max(0, (int) $logSekarang->counter_bw    - (int) $bwLalu);
                $usageColor = max(0, (int) $logSekarang->counter_color - (int) $colorLalu);
            }

            // Pastikan method hitungTagihan tersedia di model MrcContract
            $tagihan = method_exists($contract, 'hitungTagihan')
                ? $contract->hitungTagihan($usageBw, $usageColor)
                : 0;

            return [
                'contract'              => $contract,
                'log'                   => $logSekarang,
                'tagihan'               => $tagihan,
                'usage_bw'              => $usageBw,
                'usage_color'           => $usageColor,
                'is_mrc_tercatat'       => $isMrcTercatat,
                'is_baseline_pertama'   => $isMrcTercatat && !$adaBaseline,
                'counter_bw_lalu'       => $isMrcTercatat ? (int) ($logSebelumnya->counter_bw ?? 0) : null,
                'counter_bw_akhir'      => $isMrcTercatat ? (int) $logSekarang->counter_bw : null,
                'counter_color_lalu'    => $isMrcTercatat ? (int) ($logSebelumnya->counter_color ?? 0) : null,
                'counter_color_akhir'   => $isMrcTercatat ? (int) $logSekarang->counter_color : null,
            ];
        });

        return view('reports.mrc-tagihan', [
            'tagihans' => $tagihans,
            'bulan'    => $this->bulanNama[$month] ?? $month,
            'tahun'    => $year,
            'month'    => $month,
            'year'     => $year,
        ]);
    }

    /**
     * Cetak Semua Kontrak MRC.
     */
    public function cetakSemua()
    {
        $contracts = MrcContract::with(['machine.customer', 'customer'])->get();

        return view('exports.mrc-all', compact('contracts'));
    }
}
