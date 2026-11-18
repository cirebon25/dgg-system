<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopUsageRequest;
use App\Models\MrcContract;
use App\Models\Rayon;
use App\Models\ServiceLog;
use App\Models\Technician;
use App\Services\Laporan\CounterPemakaianStats;
use App\Services\Laporan\PerformanceRayonReport;
use App\Services\Laporan\RankingPemakaianRayon;
use App\Services\Laporan\TopUsageQuery;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrintRayonController extends Controller
{
    // Selisih counter di atas batas ini dianggap tidak wajar (salah ketik / counter placeholder)
    private const LONJAKAN_MAKS = 150000;

    // Route: /cetak-rekap-rayon  (cetak.rekap-rayon) -> pakai view yang sama dengan serviceRayon
    public function rekapRayon(Request $request)
    {
        return $this->serviceRayon($request);
    }

    // Route: /cetak-service-rayon  (cetak.service-rayon)
    public function serviceRayon(Request $request)
    {
        $month = (int) $request->query('month', date('n'));
        $year  = (int) $request->query('year', date('Y'));

        $namaBulan = Carbon::create($year, $month, 1)->translatedFormat('F');

        $rayons = Rayon::with(['customers.deployments.machine'])->get();

        // Service bulan terpilih, per mesin (untuk tabel riwayat)
        $logsPerMesin = ServiceLog::with('technician')
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal')
            ->get()
            ->groupBy('machine_id');

        // Semua log yang punya counter sampai akhir tahun terpilih (untuk hitung pemakaian)
        $counterPerMesin = ServiceLog::whereDate('tanggal', '<=', $year . '-12-31')
            ->where(function ($q) {
                $q->where('counter_bw', '>', 0)->orWhere('counter_color', '>', 0);
            })
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get(['id', 'machine_id', 'tanggal', 'counter_bw', 'counter_color'])
            ->groupBy('machine_id');

        // Kontrak MRC aktif, per mesin
        $kontrakPerMesin = MrcContract::where('aktif', true)
            ->get()
            ->keyBy('machine_id');

        $rayonData = $rayons->map(function ($rayon) use ($logsPerMesin, $counterPerMesin, $kontrakPerMesin, $year) {
            $mesin = collect();

            foreach ($rayon->customers as $customer) {
                foreach ($customer->deployments as $dep) {
                    $machine = $dep->machine;

                    if (! $machine || $mesin->contains('id', $machine->id)) {
                        continue;
                    }

                    $mesin->push((object) [
                        'id'        => $machine->id,
                        'machine'   => $machine,
                        'customer'  => $customer,
                        'kontrak'   => $kontrakPerMesin->get($machine->id),
                        'logs'      => $logsPerMesin->get($machine->id, collect()),
                        'pemakaian' => $this->susunPemakaian($counterPerMesin->get($machine->id, collect()), $year),
                    ]);
                }
            }

            return (object) [
                'nama'  => $rayon->nama_rayon,
                'mesin' => $mesin,
            ];
        });

        return view('print.rekap-rayon', [
            'rayonData' => $rayonData,
            'month'     => $month,
            'year'      => $year,
            'namaBulan' => $namaBulan,
        ]);
    }

    // Route: /cetak-top-usage/pilih-bulan  (cetak.top-usage.pilih)
    public function topUsagePilih(Request $request)
    {
        $sekarang = now();

        return view('filament.pages.pilih-bulan-top-usage', [
            'daftarBulan' => collect(range(1, 12))->mapWithKeys(
                fn(int $nomor) => [$nomor => Carbon::create(null, $nomor, 1)->translatedFormat('F')]
            ),
            'daftarTahun'   => range($sekarang->year, $sekarang->year - 5),
            'bulanTerpilih' => $request->integer('month', $sekarang->month),
            'tahunTerpilih' => $request->integer('year', $sekarang->year),
        ]);
    }

    // Route: /cetak-top-usage  (cetak.top-usage)
    public function topUsage(
        TopUsageRequest $request,
        TopUsageQuery $query,
        CounterPemakaianStats $stats,
        RankingPemakaianRayon $ranking,
    ) {
        $sekarang = now();
        $month    = $request->integer('month', $sekarang->month);
        $year     = $request->integer('year', $sekarang->year);

        $awalBulan = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        $records = $query->get($awalBulan->copy()->endOfMonth());
        $records = $stats->attach($records, $month, $year);
        $groups  = $ranking->build($records);

        return view('filament.pages.cetak-top-usage', [
            'groups'        => $groups,
            'totalCustomer' => $records->count(),
            'bulan'         => $awalBulan->translatedFormat('F'),
            'tahun'         => $year,
        ]);
    }

    // Route: /print/technician-performance  (print.tech-performance)
    public function technicianPerformance(Request $request)
    {
        $month = $request->month ?? date('m');
        $year  = $request->year ?? date('Y');

        $technicians = Technician::all();
        $reportData  = [];

        foreach ($technicians as $tech) {
            $logs = ServiceLog::where('technician_id', $tech->id)
                ->whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

            $totalVisits = $logs->count();

            $breakdown = $logs->groupBy('tipe_kunjungan')->map(function ($items) use ($totalVisits) {
                $count = $items->count();

                return [
                    'count'      => $count,
                    'percentage' => $totalVisits > 0 ? round(($count / $totalVisits) * 100, 1) : 0,
                ];
            });

            if ($totalVisits > 0) {
                $reportData[] = (object) [
                    'nama'    => $tech->nama_technician,
                    'total'   => $totalVisits,
                    'details' => $breakdown,
                ];
            }
        }

        return view('print.technician-performance', compact('reportData', 'month', 'year'));
    }

    // Route: /print/performance-rayon  (print.performance-rayon)
    // Pengelompokan berdasarkan rayon CUSTOMER (bukan rayon teknisi), lihat PerformanceRayonReport
    public function performanceRayon(Request $request, PerformanceRayonReport $report)
    {
        $month = $request->month ?? date('m');
        $year  = $request->year ?? date('Y');

        return view('print.performance-rayon', $report->build($month, $year));
    }

    // Hitung pemakaian BW/Color per bulan (indeks 1..12) dari selisih counter service log.
    // - Counter pertama mesin = titik awal (pemakaian 0).
    // - Selisih dimasukkan ke bulan tanggal log yang bersangkutan.
    // - Kalau counter turun (reset / ganti unit / salah ketik) atau selisih melebihi
    //   LONJAKAN_MAKS, selisih dianggap 0 dan counter tetap jadi titik awal berikutnya.
    private function susunPemakaian($logs, int $year): array
    {
        $bw    = array_fill(1, 12, 0);
        $color = array_fill(1, 12, 0);

        $lastBw    = null;
        $lastColor = null;

        foreach ($logs as $log) {
            $tgl    = Carbon::parse($log->tanggal);
            $cBw    = (int) $log->counter_bw;
            $cColor = (int) $log->counter_color;

            $deltaBw = 0;
            if ($cBw > 0 && $lastBw !== null && $cBw >= $lastBw) {
                $selisih = $cBw - $lastBw;
                $deltaBw = $selisih <= self::LONJAKAN_MAKS ? $selisih : 0;
            }

            $deltaColor = 0;
            if ($cColor > 0 && $lastColor !== null && $cColor >= $lastColor) {
                $selisih    = $cColor - $lastColor;
                $deltaColor = $selisih <= self::LONJAKAN_MAKS ? $selisih : 0;
            }

            if ($cBw > 0) {
                $lastBw = $cBw;
            }
            if ($cColor > 0) {
                $lastColor = $cColor;
            }

            if ($tgl->year === $year) {
                $bw[$tgl->month]    += $deltaBw;
                $color[$tgl->month] += $deltaColor;
            }
        }

        return ['bw' => $bw, 'color' => $color];
    }
}