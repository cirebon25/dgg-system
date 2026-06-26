<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrintRekapSparepartController extends Controller
{
    // dipindah dari: Route::get('/cetak-rekap-sparepart', ...)->name('cetak.rekap-sparepart')
    public function index(Request $request)
    {
        $month = str_pad($request->query('bulan', date('m')), 2, '0', STR_PAD_LEFT);
        $year  = trim($request->query('tahun', date('Y')));

        $masterUsages = collect();

        /*
        |--------------------------------------------------------------------------
        | KANAL 1 — SERVICE LOG SPAREPART
        |--------------------------------------------------------------------------
        */

        $serviceLogs = DB::table('service_logs')

            ->leftJoin('customers', 'service_logs.customer_id', '=', 'customers.id')

            ->leftJoin('machines', 'service_logs.machine_id', '=', 'machines.id')

            ->leftJoin('technicians', 'service_logs.technician_id', '=', 'technicians.id')

            ->leftJoin('rayons', 'technicians.rayon_id', '=', 'rayons.id')

            // DETAIL SPAREPART SERVICE
            ->join(
                'service_log_spareparts',
                'service_logs.id',
                '=',
                'service_log_spareparts.service_log_id'
            )

            // MASTER SPAREPART
            ->join(
                'spareparts',
                'service_log_spareparts.sparepart_id',
                '=',
                'spareparts.id'
            )

            ->whereYear('service_logs.tanggal', $year)

            ->whereMonth('service_logs.tanggal', (int) $month)

            ->select([

                'service_logs.tanggal',

                'customers.nama_customer',

                'machines.tipe_model',
                'machines.serial_number',

                'spareparts.nama_sparepart',
                'spareparts.nama_alias',

                // QTY SPAREPART
                'service_log_spareparts.jumlah',

                'service_logs.usage_bw',
                'service_logs.usage_color',

                'service_logs.counter_bw',
                'service_logs.counter_color',

                'technicians.nama_technician',

                'rayons.nama_rayon'
            ])

            ->orderBy('service_logs.tanggal', 'asc')

            ->get();

        foreach ($serviceLogs as $log) {

            $masterUsages->push((object)[

                'tanggal' => $log->tanggal,

                'nama_customer' => $log->nama_customer ?? '-',

                'tipe_model' => $log->tipe_model ?? '-',

                'serial_number' => $log->serial_number ?? '-',

                'nama_part' => ($log->nama_alias ? $log->nama_alias : ($log->nama_sparepart ?? '-')) . ' (' . ($log->jumlah ?? 0) . ' pcs)',
                'usage_bw' => $log->usage_bw ?? 0,

                'usage_color' => $log->usage_color ?? 0,

                'counter_bw' => $log->counter_bw ?? 0,

                'counter_color' => $log->counter_color ?? 0,

                'nama_technician' => $log->nama_technician ?? '-',

                'nama_rayon' => $log->nama_rayon ?? 'WILAYAH LAIN'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | KANAL 2 — DEPLOYMENT SPAREPART
        |--------------------------------------------------------------------------
        */

        $deploymentLogs = DB::table('deployments')

            ->leftJoin('customers', 'deployments.customer_id', '=', 'customers.id')

            ->leftJoin('machines', 'deployments.machine_id', '=', 'machines.id')

            ->leftJoin('technicians', 'deployments.technician_id', '=', 'technicians.id')

            ->leftJoin('rayons', 'technicians.rayon_id', '=', 'rayons.id')

            // DETAIL DEPLOYMENT SPAREPART
            ->join(
                'deployment_sparepart',
                'deployments.id',
                '=',
                'deployment_sparepart.deployment_id'
            )

            // MASTER SPAREPART
            ->join(
                'spareparts',
                'deployment_sparepart.sparepart_id',
                '=',
                'spareparts.id'
            )

            ->whereYear('deployments.tanggal_instal', $year)

            ->whereMonth('deployments.tanggal_instal', (int) $month)

            ->select([

                'deployments.tanggal_instal as tanggal',

                'customers.nama_customer',

                'machines.tipe_model',
                'machines.serial_number',

                'spareparts.nama_sparepart',
                'spareparts.nama_alias',

                // QTY
                'deployment_sparepart.jumlah',

                'deployments.counter_bw',
                'deployments.counter_color',

                'technicians.nama_technician',

                'rayons.nama_rayon'
            ])

            ->orderBy('deployments.tanggal_instal', 'asc')

            ->get();

        foreach ($deploymentLogs as $log) {

            $masterUsages->push((object)[

                'tanggal' => $log->tanggal,

                'nama_customer' => $log->nama_customer ?? '-',

                'tipe_model' => $log->tipe_model ?? '-',

                'serial_number' => $log->serial_number ?? '-',

                'nama_part' => ($log->nama_alias ? $log->nama_alias : ($log->nama_sparepart ?? '-')) . ' (' . ($log->jumlah ?? 0) . ' pcs) [New Install]',


                'usage_bw' => 0,

                'usage_color' => 0,

                'counter_bw' => $log->counter_bw ?? 0,

                'counter_color' => $log->counter_color ?? 0,

                'nama_technician' => $log->nama_technician ?? '-',

                'nama_rayon' => $log->nama_rayon ?? 'WILAYAH LAIN'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | GROUPING RAYON
        |--------------------------------------------------------------------------
        */

        $groupedUsages = $masterUsages
            ->sortBy('tanggal')
            ->groupBy('nama_rayon');

        return view('print.sparepart-outflow')
            ->with('groupedUsages', $groupedUsages)
            ->with('month', $month)
            ->with('year', $year);
    }

    // sparepart traking bulanan
    public function pemakaianBulanan(\Illuminate\Http\Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));

        // Hitung bulan & tahun sebelumnya
        $tanggalIni = \Carbon\Carbon::createFromDate($tahun, $bulan, 1);
        $bulanLalu  = $tanggalIni->copy()->subMonth()->month;
        $tahunLalu  = $tanggalIni->copy()->subMonth()->year;

        // Ambil semua sparepart beserta snapshot bulan ini & bulan lalu
        $spareparts = \App\Models\Sparepart::all();

        $snapshotBulanIni = \App\Models\SparepartStockSnapshot::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('sparepart_id');

        $snapshotBulanLalu = \App\Models\SparepartStockSnapshot::where('bulan', $bulanLalu)
            ->where('tahun', $tahunLalu)
            ->get()
            ->keyBy('sparepart_id');

        $data = $spareparts->map(function ($sp) use ($snapshotBulanIni, $snapshotBulanLalu) {
            $stokAwal  = $snapshotBulanLalu[$sp->id]->stok_akhir ?? null;
            $stokAkhir = $snapshotBulanIni[$sp->id]->stok_akhir ?? $sp->stok; // fallback stok realtime kalau snapshot bulan ini belum ada

            $pemakaian = ($stokAwal !== null) ? max(0, $stokAwal - $stokAkhir) : null;

            return [
                'sparepart'  => $sp,
                'stok_awal'  => $stokAwal,
                'stok_akhir' => $stokAkhir,
                'pemakaian'  => $pemakaian,
            ];
        })->filter(fn($d) => $d['pemakaian'] !== null && $d['pemakaian'] > 0)
            ->sortByDesc('pemakaian')
            ->values();

        $bulanNama = [
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

        return view('reports.sparepart-pemakaian-bulanan', [
            'data'  => $data,
            'bulan' => $bulanNama[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? $bulan,
            'tahun' => $tahun,
        ]);
    }

    public function pemakaianMatrix(\Illuminate\Http\Request $request)
    {
        $tahun = $request->query('tahun', date('Y'));

        $snapshots = \App\Models\SparepartStockSnapshot::where(function ($q) use ($tahun) {
            $q->where('tahun', $tahun)
                ->orWhere(function ($q2) use ($tahun) {
                    $q2->where('tahun', $tahun - 1)->where('bulan', 12);
                });
        })
            ->get()
            ->groupBy('sparepart_id');

        $spareparts = \App\Models\Sparepart::orderBy('nama_sparepart')->get();

        $matrix = [];
        $totalPerBulan = array_fill(1, 12, 0);

        foreach ($spareparts as $sp) {
            $snapsForThis = $snapshots[$sp->id] ?? collect();
            $snapsByBulanTahun = $snapsForThis->keyBy(fn($s) => $s->tahun . '-' . $s->bulan);

            $row = [];
            foreach (range(1, 12) as $bulan) {
                $akhir = $snapsByBulanTahun[$tahun . '-' . $bulan]->stok_akhir ?? null;
                $awal  = $bulan === 1
                    ? ($snapsByBulanTahun[($tahun - 1) . '-12']->stok_akhir ?? null)
                    : ($snapsByBulanTahun[$tahun . '-' . ($bulan - 1)]->stok_akhir ?? null);

                $pemakaian = ($awal !== null && $akhir !== null) ? max(0, $awal - $akhir) : null;
                $row[$bulan] = $pemakaian;

                if ($pemakaian !== null) {
                    $totalPerBulan[$bulan] += $pemakaian;
                }
            }

            if (collect($row)->filter(fn($v) => $v !== null)->isNotEmpty()) {
                $matrix[] = [
                    'sparepart' => $sp,
                    'bulanan'   => $row,
                    'total'     => collect($row)->filter()->sum(),
                ];
            }
        }

        $bulanLabel = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];

        return view('reports.sparepart-pemakaian-matrix', compact('matrix', 'totalPerBulan', 'bulanLabel', 'tahun'));
    }
}
