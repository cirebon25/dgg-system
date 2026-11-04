<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDailyResult;
use App\Models\AttendanceEmployee;
use App\Models\AttendanceShift;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;

class RincianHarianController extends Controller
{
    public function __invoke(Request $request)
    {
        $dari   = Carbon::parse($request->query('dari') ?: now()->startOfMonth())->startOfDay();
        $sampai = Carbon::parse($request->query('sampai') ?: now())->startOfDay();

        if ($dari->gt($sampai)) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        $jumlahHari = (int) round(($sampai->timestamp - $dari->timestamp) / 86400) + 1;
        abort_if($jumlahHari > config('absensi.maks_hari', 31), 422, 'Rentang cetak maksimal ' . config('absensi.maks_hari', 31) . ' hari.');

        $pin = $request->query('pin');

        $employees = AttendanceEmployee::query()
            ->whereNotNull('pegawai_id')
            ->when($pin, fn($q, $p) => $q->where('pin', $p))
            ->orderByRaw('CAST(pin AS UNSIGNED)')
            ->get();

        $results = AttendanceDailyResult::query()
            ->whereBetween('tgl_shift', [$dari->toDateString(), $sampai->toDateString()])
            ->get()
            ->groupBy('pegawai_id');

        $shifts = AttendanceShift::all()->keyBy('jk_id');
        $dates  = collect(CarbonPeriod::create($dari, $sampai))->all();

        $durasiStd = (int) config('absensi.durasi_standar_menit', 540);

        $blocks = $employees->map(function ($emp) use ($results, $shifts, $dates, $durasiStd) {
            $byDate = ($results[$emp->pegawai_id] ?? collect())
                ->keyBy(fn($r) => $r->tgl_shift->toDateString());

            $rows = [];

            foreach ($dates as $d) {
                $r = $byDate[$d->toDateString()] ?? null;

                $hariKerja  = $r && $r->jk_id > 0;
                $shift      = $hariKerja ? ($shifts[$r->jk_id] ?? null) : null;
                $liburUmum  = (bool) ($r?->libur_umum);
                $liburRutin = (bool) ($r?->libur_rutin);
                $libur      = $liburUmum || $liburRutin;

                $jamMasuk = $jamKeluar = '';

                if ($shift) {
                    $masuk     = Carbon::createFromTimeString($shift->jam_masuk);
                    $jamMasuk  = $masuk->format('H:i');
                    $jamKeluar = $masuk->copy()->addMinutes($durasiStd)->format('H:i');
                } elseif ($libur) {
                    $jamMasuk = $jamKeluar = '00:00';
                }

                $rows[] = [
                    'tanggal'      => $d->format('l d/m/Y'),
                    'shift'        => $shift?->name ?? ($liburRutin ? 'Libur Rutin' : ($liburUmum ? 'Libur Umum' : '')),
                    'jam_masuk'    => $jamMasuk,
                    'scan_masuk'   => $r?->scan_in?->format('H:i') ?? '',
                    'cyan_masuk'   => $hariKerja && ! $r->scan_in,
                    'terlambat'    => $this->hm($r?->late_minute ?? 0),
                    'jam_keluar'   => $jamKeluar,
                    'scan_keluar'  => $r?->scan_out?->format('H:i') ?? '',
                    'cyan_keluar'  => $hariKerja && ! $r->scan_out,
                    'pulang_awal'  => $this->hm($r?->early_minute ?? 0),
                    'durasi'       => $this->hm($r?->durasi_minute ?? 0),
                    'ist_durasi'   => $this->hm($r?->break_minute ?? 0),
                    'ist_lebih'    => $this->hm($r?->break_ot_minute ?? 0),
                    'lembur_akhir' => $this->hm($r?->durasi_eot_minute ?? 0),
                    'libur_umum'   => $liburUmum,
                    'libur_rutin'  => $liburRutin,
                    'shift_lembur' => (bool) ($r?->jk_ot),
                    'keterangan'   => $r?->keterangan ?? '',
                ];
            }

            return ['emp' => $emp, 'rows' => $rows];
        });

        // Berapa karyawan muat dalam satu halaman (8 tanggal -> 3 karyawan, 31 tanggal -> 1 karyawan)
        $perHalaman = max(1, intdiv((int) config('absensi.baris_per_halaman', 36), count($dates) + 4));

        return view('cetak.rincian-harian', [
            'pages'      => $blocks->chunk($perHalaman)->values(),
            'dari'       => $dari,
            'sampai'     => $sampai,
            'perusahaan' => config('absensi.perusahaan'),
            'oleh'       => $request->user()?->name ?? 'admin',
            'dicetak'    => now(),
        ]);
    }

    private function hm(int $menit): string
    {
        return $menit > 0 ? sprintf('%02d:%02d', intdiv($menit, 60), $menit % 60) : '';
    }
}