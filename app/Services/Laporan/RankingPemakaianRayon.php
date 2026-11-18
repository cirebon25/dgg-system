<?php

namespace App\Services\Laporan;

use Illuminate\Support\Collection;

final class RankingPemakaianRayon
{
    private const TANPA_RAYON = 'Tanpa Rayon';

    /**
     * Mengelompokkan baris laporan per rayon.
     *
     * Setiap baris wajib memiliki: nama_rayon, pemakaian_bw, pemakaian_color,
     * pemakaian_total (boleh null), dan rata_rata.
     *
     * Hasil: daftar grup dengan properti nama, baris, sub_bw, sub_color, sub_total.
     * Rayon diurutkan alfabetis, "Tanpa Rayon" selalu di paling bawah.
     *
     * @param  Collection<int, object>  $records
     * @return Collection<int, object>
     */
    public function build(Collection $records): Collection
    {
        return $records
            ->groupBy(fn(object $row) => $row->nama_rayon ?: self::TANPA_RAYON)
            ->sortKeysUsing(fn($a, $b) => [$a === self::TANPA_RAYON, (string) $a]
                <=> [$b === self::TANPA_RAYON, (string) $b])
            ->map(fn(Collection $rows, $nama) => $this->bentukGrup((string) $nama, $rows))
            ->values();
    }

    private function bentukGrup(string $nama, Collection $rows): object
    {
        $urut = $rows
            ->sort(fn(object $a, object $b) => $this->kunciUrut($b) <=> $this->kunciUrut($a))
            ->values();

        return (object) [
            'nama'      => $nama,
            'baris'     => $urut,
            'sub_bw'    => $urut->sum(fn(object $r) => $r->pemakaian_bw ?? 0),
            'sub_color' => $urut->sum(fn(object $r) => $r->pemakaian_color ?? 0),
            'sub_total' => $urut->sum(fn(object $r) => $r->pemakaian_total ?? 0),
        ];
    }

    /**
     * Kunci pengurutan: pemakaian bulan ini (terbesar di atas, null di bawah),
     * lalu rata-rata sebagai pembanding. Ubah di sini jika urutan ingin berdasarkan rata-rata.
     */
    private function kunciUrut(object $row): array
    {
        return [$row->pemakaian_total ?? -1, $row->rata_rata];
    }
}