<?php

namespace App\Services\Laporan;

use App\Models\Machine;
use App\Models\ServiceLog;
use Carbon\Carbon;

class PerformanceRayonReport
{
    private const TIPE_KOLOM = ['CM', 'RM', 'RN', 'RR', 'Mesin', 'RM Tertunda'];

    /**
     * @return array data siap kirim ke view print.performance-rayon
     */
    public function build(string|int $month, string|int $year): array
    {
        $logs = ServiceLog::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->with(['customer.rayon', 'technician'])
            ->get();

        // machine_id yang sudah di-RM bulan ini (untuk hitung "RM Tertunda")
        $machineIdSudahRm = $logs->where('tipe_kunjungan', 'RM')->pluck('machine_id')->unique();

        // Kelompokkan log berdasarkan rayon CUSTOMER, lalu per teknisi
        $reportData = $logs
            ->groupBy(fn($log) => $log->customer?->rayon?->nama_rayon ?? 'Tanpa Rayon')
            ->map(function ($logsInRayon) {
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

        // Mesin Rented per RAYON + KOTA (kota yang sama bisa ada di beberapa rayon)
        $mesinPerRayonKota = Machine::where('status', 'Rented')
            ->with('customer.rayon')
            ->get()
            ->groupBy(fn($m) => ($m->customer?->rayon?->nama_rayon ?? 'Tanpa Rayon')
                . '||' . ($m->customer?->kota ?? 'Tanpa Kota'));

        $jumlahMesinPerRayonKota = $mesinPerRayonKota->map(fn($group) => $group->count());

        $rmTertundaPerRayonKota = $mesinPerRayonKota->map(
            fn($group) => $group->filter(fn($m) => ! $machineIdSudahRm->contains($m->id))->count()
        );

        return [
            'reportData'              => $reportData,
            'month'                   => $month,
            'year'                    => $year,
            'tipeKolom'               => self::TIPE_KOLOM,
            'hariKerja'               => $this->hitungHariKerja((int) $month, (int) $year),
            'jumlahMesinPerRayonKota' => $jumlahMesinPerRayonKota,
            'rmTertundaPerRayonKota'  => $rmTertundaPerRayonKota,
        ];
    }

    // Hari kerja = semua hari kecuali Minggu
    private function hitungHariKerja(int $month, int $year): int
    {
        $hariKerja   = 0;
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        for ($d = 1; $d <= $daysInMonth; $d++) {
            if (Carbon::create($year, $month, $d)->dayOfWeek !== Carbon::SUNDAY) {
                $hariKerja++;
            }
        }

        return $hariKerja;
    }
}