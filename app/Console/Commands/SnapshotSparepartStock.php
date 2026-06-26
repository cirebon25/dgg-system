<?php

namespace App\Console\Commands;

use App\Models\Sparepart;
use App\Models\SparepartStockSnapshot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SnapshotSparepartStock extends Command
{
    protected $signature = 'sparepart:snapshot {--bulan=} {--tahun=}';
    protected $description = 'Ambil snapshot stok akhir semua sparepart untuk bulan tertentu (default: bulan lalu)';

    public function handle(): int
    {
        // Default: snapshot untuk bulan LALU (karena dijalankan tanggal 1, artinya
        // stok yang dipotret adalah stok akhir di hari terakhir bulan sebelumnya)
        $now    = now();
        $bulan  = $this->option('bulan') ?? $now->copy()->subMonth()->month;
        $tahun  = $this->option('tahun') ?? $now->copy()->subMonth()->year;

        $spareparts = Sparepart::all();
        $count = 0;

        foreach ($spareparts as $sp) {
            SparepartStockSnapshot::updateOrCreate(
                [
                    'sparepart_id' => $sp->id,
                    'bulan'        => $bulan,
                    'tahun'        => $tahun,
                ],
                [
                    'stok_akhir'  => $sp->stok,
                    'snapshot_at' => $now,
                ]
            );
            $count++;
        }

        $this->info("Snapshot stok berhasil disimpan untuk {$count} sparepart, periode {$bulan}/{$tahun}.");
        Log::info("Sparepart stock snapshot dijalankan untuk periode {$bulan}/{$tahun}, {$count} item.");

        return self::SUCCESS;
    }
}