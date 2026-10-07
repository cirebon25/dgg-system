<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;

final class TechnicianStockReportService
{
    /**
     * @param  Collection  $groupedHistories  Histori dalam periode, di-group per technician_id
     * @param  Collection|null  $carryOver    Baris histori terakhir part yang tidak bergerak di periode,
     *                                        di-group per technician_id
     */
    public function build(Collection $groupedHistories, ?Collection $carryOver = null): Collection
    {
        $carryOver ??= collect();

        return $groupedHistories->keys()
            ->merge($carryOver->keys())
            ->unique()
            ->values()
            ->map(function ($key) use ($groupedHistories, $carryOver): array {
                $items = $groupedHistories->get($key, collect())
                    ->sortBy([['created_at', 'asc'], ['id', 'asc']])
                    ->values();

                $idle = $carryOver->get($key, collect());

                $parts = $items
                    ->groupBy('sparepart_id')
                    ->map(fn(Collection $rows): array => $this->summarizePart($rows))
                    ->concat($idle->map(fn($history): array => $this->summarizeIdlePart($history)))
                    ->sortBy('nama')
                    ->values();

                $reference = $items->first() ?? $idle->first();

                return [
                    'technician' => $reference->technician->nama_technician ?? 'Tanpa Teknisi',
                    'parts'      => $parts,
                    'mutations'  => $items->map(fn($history): array => [
                        'waktu'      => $history->created_at,
                        'part'       => $history->sparepart->nama_sparepart ?? '-',
                        'masuk'      => (int) $history->masuk,
                        'keluar'     => (int) $history->keluar,
                        'saldo'      => (int) $history->saldo_akhir,
                        'jenis'      => $this->resolveType($history),
                        'keterangan' => (string) $history->keterangan,
                    ]),
                    'totals'     => $this->summarizeTotals($parts),
                ];
            })
            ->sortBy('technician')
            ->values();
    }

    private function summarizePart(Collection $rows): array
    {
        $first = $rows->first();
        $last  = $rows->last();

        $masuk  = (int) $rows->sum('masuk');
        $keluar = (int) $rows->sum('keluar');
        $retur  = (int) $rows
            ->filter(fn($history): bool => $this->isRetur($history))
            ->sum('keluar');

        $saldoAwal  = (int) $first->saldo_akhir - (int) $first->masuk + (int) $first->keluar;
        $saldoAkhir = (int) $last->saldo_akhir;
        $selisih    = $saldoAkhir - ($saldoAwal + $masuk - $keluar);

        return [
            'nama'         => $first->sparepart->nama_sparepart ?? '-',
            'saldo_awal'   => $saldoAwal,
            'dipinjam'     => $masuk,
            'retur'        => $retur,
            'terpakai'     => $keluar - $retur,
            'total_keluar' => $keluar,
            'saldo_akhir'  => $saldoAkhir,
            'selisih'      => $selisih,
        ];
    }

    /**
     * Part yang ada di tas tetapi tidak bergerak selama periode.
     */
    private function summarizeIdlePart(object $history): array
    {
        $saldo = (int) $history->saldo_akhir;

        return [
            'nama'         => $history->sparepart->nama_sparepart ?? '-',
            'saldo_awal'   => $saldo,
            'dipinjam'     => 0,
            'retur'        => 0,
            'terpakai'     => 0,
            'total_keluar' => 0,
            'saldo_akhir'  => $saldo,
            'selisih'      => 0,
        ];
    }

    private function summarizeTotals(Collection $parts): array
    {
        return [
            'jenis_part'   => $parts->count(),
            'saldo_awal'   => (int) $parts->sum('saldo_awal'),
            'dipinjam'     => (int) $parts->sum('dipinjam'),
            'retur'        => (int) $parts->sum('retur'),
            'terpakai'     => (int) $parts->sum('terpakai'),
            'total_keluar' => (int) $parts->sum('total_keluar'),
            'saldo_akhir'  => (int) $parts->sum('saldo_akhir'),
            'part_selisih' => $parts->filter(fn(array $part): bool => $part['selisih'] !== 0)->count(),
        ];
    }

    private function resolveType(object $history): string
    {
        if ((int) $history->masuk > 0) {
            return 'Pinjam';
        }

        return $this->isRetur($history) ? 'Retur' : 'Pakai';
    }

    /**
     * Satu-satunya tempat aturan "apa itu retur" didefinisikan.
     */
    private function isRetur(object $history): bool
    {
        return (int) $history->keluar > 0
            && str_contains(mb_strtolower((string) $history->keterangan), 'retur');
    }
}
