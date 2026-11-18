<?php

namespace App\Services\Laporan;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class CounterPemakaianStats
{
    /**
     * Menambahkan atribut pemakaian bulanan ke setiap baris laporan (per mesin),
     * bersumber dari tabel service_logs (kolom tanggal bertipe DATE):
     *
     * - counter_bw_lalu, counter_color_lalu : baseline, yaitu counter kunjungan terakhir sebelum bulan laporan
     * - counter_bw_ini,  counter_color_ini  : counter kunjungan terakhir dalam bulan laporan (null = belum ada service)
     * - pemakaian_bw, pemakaian_color, pemakaian_total (int|null)
     *
     * Baris laporan wajib memiliki properti machine_id.
     * Selalu menjalankan tiga query, tidak bergantung jumlah mesin.
     */
    public function attach(Collection $records, int $bulan, int $tahun): Collection
    {
        if ($records->isEmpty()) {
            return $records;
        }

        $awal    = CarbonImmutable::create($tahun, $bulan, 1)->startOfMonth();
        $berikut = $awal->addMonthNoOverflow();

        $awalTanggal    = $awal->toDateString();
        $berikutTanggal = $berikut->toDateString();

        $machineIds = $records
            ->pluck('machine_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $sebelumnya = $this->ambilSatu($machineIds, null, $awalTanggal, 'DESC');
        $pertama    = $this->ambilSatu($machineIds, $awalTanggal, $berikutTanggal, 'ASC');
        $terakhir   = $this->ambilSatu($machineIds, $awalTanggal, $berikutTanggal, 'DESC');

        $records->each(function ($row) use ($sebelumnya, $pertama, $terakhir) {
            $prev  = $sebelumnya->get($row->machine_id);
            $first = $pertama->get($row->machine_id);
            $last  = $terakhir->get($row->machine_id);

            $row->counter_bw_ini    = $this->toInt($last?->counter_bw);
            $row->counter_color_ini = $last === null ? null : $this->toInt($last->counter_color ?? 0);

            $row->counter_bw_lalu    = $this->baseline($prev, $first, 'counter_bw', 'counter_bw_lalu');
            $row->counter_color_lalu = $this->baseline($prev, $first, 'counter_color', 'counter_color_lalu');

            $row->pemakaian_bw    = $this->selisih($row->counter_bw_ini, $row->counter_bw_lalu);
            $row->pemakaian_color = $this->selisih($row->counter_color_ini, $row->counter_color_lalu);

            $row->pemakaian_total = ($row->pemakaian_bw === null || $row->pemakaian_color === null)
                ? null
                : $row->pemakaian_bw + $row->pemakaian_color;
        });

        return $records;
    }

    /**
     * Baseline dari kunjungan terakhir sebelum bulan laporan.
     * Jika tidak ada (mesin baru), pakai counter_*_lalu pada kunjungan pertama bulan laporan, selama bernilai > 0.
     */
    private function baseline(?object $prev, ?object $first, string $kolom, string $kolomLalu): ?int
    {
        if ($prev !== null) {
            return $this->toInt($prev->{$kolom} ?? 0);
        }

        $lalu = $first?->{$kolomLalu};

        if ($lalu === null || (int) $lalu <= 0) {
            return null;
        }

        return (int) $lalu;
    }

    /**
     * Selisih counter akhir dan baseline.
     * Null jika salah satu tidak tersedia, atau selisihnya negatif (indikasi reset/penggantian mesin).
     */
    private function selisih(?int $akhir, ?int $baseline): ?int
    {
        if ($akhir === null || $baseline === null) {
            return null;
        }

        $selisih = $akhir - $baseline;

        return $selisih >= 0 ? $selisih : null;
    }

    private function toInt(mixed $nilai): ?int
    {
        return $nilai === null ? null : (int) $nilai;
    }

    /**
     * Satu kunjungan per mesin dalam rentang tanggal.
     * $dari = null : tanpa batas bawah (dipakai untuk mencari kunjungan sebelum bulan laporan)
     * $sampai      : batas atas (eksklusif), format Y-m-d
     * $arah        : 'DESC' untuk kunjungan terakhir, 'ASC' untuk kunjungan pertama
     *
     * @param  array<int, int>  $machineIds
     * @return Collection<int|string, object>
     */
    private function ambilSatu(array $machineIds, ?string $dari, string $sampai, string $arah): Collection
    {
        if ($machineIds === []) {
            return collect();
        }

        $sub = DB::table('service_logs')
            ->select('machine_id', 'counter_bw', 'counter_color', 'counter_bw_lalu', 'counter_color_lalu')
            ->selectRaw("ROW_NUMBER() OVER (PARTITION BY machine_id ORDER BY tanggal {$arah}, id {$arah}) AS rn")
            ->whereNull('deleted_at')
            ->whereIn('machine_id', $machineIds)
            ->where('tanggal', '<', $sampai);

        if ($dari !== null) {
            $sub->where('tanggal', '>=', $dari);
        }

        return DB::query()
            ->fromSub($sub, 't')
            ->where('rn', 1)
            ->get()
            ->keyBy('machine_id');
    }
}