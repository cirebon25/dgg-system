<?php

namespace App\Services\Laporan;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class CounterPemakaianStats
{
    /**
     * Menambahkan atribut pemakaian bulanan ke setiap baris laporan (per mesin):
     * - counter_bw_lalu, counter_color_lalu     (int|null) counter akhir bulan lalu (baseline)
     * - counter_bw_ini,  counter_color_ini      (int|null) counter terakhir dalam bulan laporan
     * - pemakaian_bw, pemakaian_color, pemakaian_total (int|null)
     *
     * Baris laporan wajib memiliki properti machine_id.
     * Selalu menjalankan satu query, tidak bergantung jumlah mesin.
     */
    public function attach(Collection $records, int $bulan, int $tahun): Collection
    {
        if ($records->isEmpty()) {
            return $records;
        }

        $awalBulan = CarbonImmutable::create($tahun, $bulan, 1)->startOfMonth();
        $awalBerikut = $awalBulan->addMonthNoOverflow();

        $machineIds = $records
            ->pluck('machine_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $stats = $this->ringkasBulan($machineIds, $awalBulan, $awalBerikut);

        $records->each(function ($row) use ($stats) {
            $s = $stats->get($row->machine_id);

            $row->counter_bw_lalu    = $this->toInt($s?->counter_bw_lalu);
            $row->counter_color_lalu = $this->toInt($s?->counter_color_lalu);
            $row->counter_bw_ini     = $this->toInt($s?->counter_bw_ini);
            $row->counter_color_ini  = $this->toInt($s?->counter_color_ini);

            $row->pemakaian_bw    = $this->selisih($row->counter_bw_ini, $row->counter_bw_lalu);
            $row->pemakaian_color = $this->selisih($row->counter_color_ini, $row->counter_color_lalu);

            $row->pemakaian_total = ($row->pemakaian_bw === null || $row->pemakaian_color === null)
                ? null
                : $row->pemakaian_bw + $row->pemakaian_color;
        });

        return $records;
    }

    /**
     * Selisih counter bulan ini dan bulan lalu.
     * Mengembalikan null jika data tidak lengkap atau selisihnya negatif (indikasi reset/penggantian).
     */
    private function selisih(?int $ini, ?int $lalu): ?int
    {
        if ($ini === null || $lalu === null) {
            return null;
        }

        $selisih = $ini - $lalu;

        return $selisih >= 0 ? $selisih : null;
    }

    private function toInt(mixed $nilai): ?int
    {
        return $nilai === null ? null : (int) $nilai;
    }

    /**
     * Ringkasan pembacaan per mesin dalam satu bulan:
     * - baseline = MIN(counter_*_lalu), yaitu counter akhir bulan sebelumnya
     * - terakhir = MAX(counter_*), yaitu counter tertinggi dalam bulan laporan
     *
     * @param  array<int, int>  $machineIds
     * @return Collection<int|string, object>
     */
    private function ringkasBulan(array $machineIds, CarbonImmutable $awalBulan, CarbonImmutable $awalBerikut): Collection
    {
        if ($machineIds === []) {
            return collect();
        }

        return DB::table('meter_readings')
            ->select('machine_id')
            ->selectRaw('MIN(counter_bw_lalu) AS counter_bw_lalu')
            ->selectRaw('MIN(counter_color_lalu) AS counter_color_lalu')
            ->selectRaw('MAX(counter_bw) AS counter_bw_ini')
            ->selectRaw('MAX(counter_color) AS counter_color_ini')
            ->whereIn('machine_id', $machineIds)
            ->where('tanggal', '>=', $awalBulan->toDateString())
            ->where('tanggal', '<', $awalBerikut->toDateString())
            ->groupBy('machine_id')
            ->get()
            ->keyBy('machine_id');
    }
}