<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Rayon;
use App\Models\ServiceLog;
use App\Models\Technician;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrintRayonController extends Controller
{
    // dipindah dari: Route::get('/cetak-rekap-rayon', ...)->name('cetak.rekap-rayon')
    public function rekapRayon(Request $request)
    {
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));

        $rayons = Rayon::with(['customers.deployments.machine'])->get();

        $namaBulan = Carbon::createFromFormat('m', $month)->translatedFormat('F');

        $html = "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Helvetica', sans-serif; font-size: 11px; padding: 10px; }
                header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
                .rayon-box { margin-bottom: 30px; }
                .rayon-title { background: #333; color: #fff; padding: 8px; font-weight: bold; font-size: 14px; margin-bottom: 5px; }
                table { width: 100%; border-collapse: collapse; }
                th { background: #f2f2f2; border: 1px solid #000; padding: 8px; text-transform: uppercase; }
                td { border: 1px solid #000; padding: 8px; vertical-align: top; }
                .text-center { text-align: center; }
                .empty { color: #999; font-style: italic; }
            </style>
        </head>
        <body onload='window.print()'>
            <header>
                <h1 style='margin:0;'>DGG SYSTEM - MONITORING UNIT</h1>
                <h2 style='margin:5px 0;'>LAPORAN KINERJA MESIN PER RAYON</h2>
                <p>Periode: $namaBulan $year</p>
            </header>";

        foreach ($rayons as $rayon) {
            $html .= "<div class='rayon-box'>
                        <div class='rayon-title'>📍 RAYON: " . strtoupper($rayon->nama_rayon) . "</div>
                        <table>
                            <thead>
                                <tr>
                                    <th width='30'>NO</th>
                                    <th width='200'>NAMA CUSTOMER & KOTA</th>
                                    <th width='150'>SN / MODEL</th>
                                    <th>HISTORI SERVICE ($namaBulan)</th>
                                </tr>
                            </thead>
                            <tbody>";

            $i = 1;
            foreach ($rayon->customers as $customer) {
                foreach ($customer->deployments as $dep) {
                    $machine = $dep->machine;
                    if (! $machine) {
                        continue;
                    }

                    $logs = ServiceLog::where('machine_id', $machine->id)
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)
                        ->get();

                    $html .= "<tr>
                                <td class='text-center'>$i</td>
                                <td><b>{$customer->nama_customer}</b><br>{$customer->kota}</td>
                                <td><b>{$machine->serial_number}</b><br>{$machine->tipe_model}</td>
                                <td>";

                    if ($logs->isEmpty()) {
                        $html .= "<span class='empty'>- Unit Normal (Tidak Ada Tindakan) -</span>";
                    } else {
                        $html .= "<ul style='margin:0; padding-left:15px;'>";
                        foreach ($logs as $log) {
                            $tgl = date('d/m', strtotime($log->created_at));
                            $html .= "<li><b>[$tgl]</b> {$log->keluhan} -> <i>{$log->tindakan}</i></li>";
                        }
                        $html .= "</ul>";
                    }

                    $html .= "</td></tr>";
                    $i++;
                }
            }

            if ($i == 1) {
                $html .= "<tr><td colspan='4' class='text-center empty'>Tidak ada mesin terpasang di wilayah ini.</td></tr>";
            }

            $html .= "</tbody></table></div>";
        }

        $html .= "</body></html>";

        return response($html);
    }

    // dipindah dari: Route::get('/cetak-service-rayon', ...)->name('cetak.service-rayon')
    public function serviceRayon(Request $request)
    {
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));

        $rayons = Rayon::with(['customers.deployments.machine'])->get();
        $namaBulan = Carbon::createFromFormat('m', $month)->translatedFormat('F');

        return view('filament.pages.cetak-rekap-rayon', [
            'rayons' => $rayons,
            'month' => $month,
            'year' => $year,
            'namaBulan' => $namaBulan,
        ]);
    }

    // dipindah dari: Route::get('/cetak-top-usage', ...)->name('cetak.top-usage')
    public function topUsage(Request $request)
    {
        $month = $request->month;
        $year = $request->year;

        $records = ServiceLog::query()
            ->join('machines', 'service_logs.machine_id', '=', 'machines.id')
            ->join('deployments', 'machines.id', '=', 'deployments.machine_id')
            ->join('customers', 'deployments.customer_id', '=', 'customers.id')
            ->select(
                'customers.nama_customer',
                'machines.serial_number',
                DB::raw('SUM(usage_bw) as total_bw'),
                DB::raw('SUM(usage_color) as total_color'),
                DB::raw('SUM(usage_bw + usage_color) as total_semua')
            )
            ->whereMonth('service_logs.tanggal', $month)
            ->whereYear('service_logs.tanggal', $year)
            ->groupBy('customers.nama_customer', 'machines.serial_number')
            ->orderBy('total_semua', 'desc')->get();

        return view('filament.pages.cetak-top-usage', [
            'records' => $records,
            'bulan' => Carbon::create()->month($month)->translatedFormat('F'),
            'tahun' => $year,
        ]);
    }

    // dipindah dari: Route::get('/print/technician-performance', ...)->name('print.tech-performance')
    public function technicianPerformance(Request $request)
    {
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        $technicians = Technician::all();
        $reportData = [];

        foreach ($technicians as $tech) {
            $logs = ServiceLog::where('technician_id', $tech->id)
                ->whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->get();

            $totalVisits = $logs->count();

            $breakdown = $logs->groupBy('tipe_kunjungan')->map(function ($items) use ($totalVisits) {
                $count = $items->count();
                return [
                    'count' => $count,
                    'percentage' => $totalVisits > 0 ? round(($count / $totalVisits) * 100, 1) : 0
                ];
            });

            if ($totalVisits > 0) {
                $reportData[] = (object)[
                    'nama' => $tech->nama_technician,
                    'total' => $totalVisits,
                    'details' => $breakdown
                ];
            }
        }

        return view('print.technician-performance', compact('reportData', 'month', 'year'));
    }

    // PERBAIKAN (24 Juni 2026):
    // Sebelumnya laporan dikelompokkan berdasarkan rayon TEKNISI (Technician::rayon_id).
    // Ini salah untuk teknisi yang tugasnya lintas wilayah (contoh: petugas pencatat
    // counter MRC yang harus tetap diberi 1 rayon saat input data, padahal dia jalan
    // ke semua kota). Akibatnya kunjungan ke kota di luar rayon resminya ikut
    // tercampur ke grup rayon teknisi tersebut.
    //
    // Sekarang dikelompokkan berdasarkan rayon CUSTOMER (customers.rayon_id) --
    // setiap kunjungan otomatis masuk ke grup rayon sesuai kota customer yang
    // sebenarnya dikunjungi, bukan rayon resmi si teknisi. Ini otomatis benar
    // untuk teknisi lintas wilayah tanpa perlu rayon khusus atau koreksi manual.
    public function performanceRayon(Request $request)
    {
        $month = $request->month ?? date('m');
        $year  = $request->year  ?? date('Y');

        // Ambil semua service log bulan ini, sertakan customer (dengan rayon-nya) dan technician
        $logs = ServiceLog::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->with(['customer.rayon', 'technician'])
            ->get();

        // TAMBAHAN: kumpulan machine_id yang SUDAH dikunjungi RM bulan ini -- dipakai
        // untuk menghitung "RM Tertunda" (mesin Rented yang belum di-RM sama sekali
        // bulan ini). Diambil dari log yang sama supaya konsisten dengan data tabel.
        $machineIdSudahRm = $logs->where('tipe_kunjungan', 'RM')->pluck('machine_id')->unique();

        $tipeKolom = ['CM', 'RM', 'RN', 'RR', 'Mesin', 'RM Tertunda'];

        $hariKerja = 0;
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $day = Carbon::create($year, $month, $d)->dayOfWeek;
            if ($day !== Carbon::SUNDAY) $hariKerja++;
        }

        // Kelompokkan LOG berdasarkan rayon CUSTOMER, bukan rayon teknisi
        $logsByRayon = $logs->groupBy(fn($log) => $log->customer?->rayon?->nama_rayon ?? 'Tanpa Rayon');

        // Bentuk ulang struktur agar tetap kompatibel dengan blade performance-rayon
        // yang mengharapkan $reportData[$namaRayon] = koleksi "technician-like" dengan
        // properti ->serviceLogs dan ->nama_technician.
        $reportData = $logsByRayon->map(function ($logsInRayon) {
            // Kelompokkan log dalam rayon ini per teknisi, supaya jumlah teknisi
            // (untuk rata-rata kunjungan/hari) tetap dihitung dengan benar.
            return $logsInRayon
                ->groupBy(fn($log) => $log->technician_id)
                ->map(function ($logsPerTechnician) {
                    $technician = $logsPerTechnician->first()->technician;
                    return (object) [
                        'nama_technician' => $technician?->nama_technician ?? '-',
                        'serviceLogs'     => $logsPerTechnician,
                    ];
                })
                ->values();
        });

        // TAMBAHAN: kumpulkan unit mesin terpasang (status Rented) per RAYON + KOTA.
        // PENTING: nama kota yang sama (misal "Cirebon") bisa muncul di beberapa rayon
        // berbeda, karena rayon ditentukan oleh customers.rayon_id per customer --
        // bukan oleh nama kotanya. Jika dihitung per kota saja (tanpa rayon), maka kota
        // yang sama akan menunjukkan angka identik di setiap grup rayon, padahal mesin
        // di tiap rayon itu benar-benar unit yang berbeda.
        // Disimpan sebagai koleksi mesin (bukan cuma angka) agar bisa dicocokkan dengan
        // $machineIdSudahRm untuk menghitung "RM Tertunda".
        $mesinPerRayonKota = Machine::where('status', 'Rented')
            ->with('customer.rayon')
            ->get()
            ->groupBy(fn($m) => ($m->customer?->rayon?->nama_rayon ?? 'Tanpa Rayon') . '||' . ($m->customer?->kota ?? 'Tanpa Kota'));

        $jumlahMesinPerRayonKota = $mesinPerRayonKota->map(fn($group) => $group->count());

        // RM Tertunda per rayon+kota = mesin di grup itu yang machine_id-nya TIDAK ada
        // di $machineIdSudahRm (belum pernah dikunjungi RM bulan ini).
        $rmTertundaPerRayonKota = $mesinPerRayonKota->map(
            fn($group) => $group->filter(fn($m) => !$machineIdSudahRm->contains($m->id))->count()
        );

        return view('print.performance-rayon', compact(
            'reportData',
            'month',
            'year',
            'tipeKolom',
            'hariKerja',
            'jumlahMesinPerRayonKota',
            'rmTertundaPerRayonKota'
        ));
    }
}