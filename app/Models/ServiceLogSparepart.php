<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ServiceLog;

class ServiceLogSparepart extends Model
{
    use HasFactory, UppercaseAttributes;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($item) {
            $serviceLog = \App\Models\ServiceLog::find($item->service_log_id);
            if (!$serviceLog) return;

            $techId = $serviceLog->technician_id;
            if (!$techId) return;

            // Cek stok teknisi cukup sebelum dipotong
            $techStock = \App\Models\TechnicianStock::firstOrCreate(
                ['technician_id' => $techId, 'sparepart_id' => $item->sparepart_id],
                ['jumlah' => 0]
            );

            if ($techStock->jumlah < $item->jumlah) {
                \Illuminate\Support\Facades\Log::warning('Stok teknisi tidak cukup', [
                    'technician_id' => $techId,
                    'sparepart_id'  => $item->sparepart_id,
                    'stok_ada'      => $techStock->jumlah,
                    'dibutuhkan'    => $item->jumlah,
                ]);
                return; // Tidak potong jika tidak cukup
            }

            // Potong stok teknisi saja, gudang tidak bergerak
            $techStock->decrement('jumlah', $item->jumlah);

            // Catat histori
            $machine = \App\Models\Machine::find($serviceLog->machine_id);
            $cust    = $machine?->customer?->nama_customer ?? 'Unknown';
            $sn      = $machine?->serial_number ?? '-';

            \App\Models\TechnicianStockHistory::create([
                'technician_id' => $techId,
                'sparepart_id'  => $item->sparepart_id,
                'masuk'         => 0,
                'keluar'        => $item->jumlah,
                'saldo_akhir'   => $techStock->fresh()->jumlah,
                'keterangan'    => "Service: {$cust} (SN: {$sn})",
            ]);
        });

        // PENTING (perbaikan 30 Juni 2026): saat baris pivot ini dihapus -- baik
        // karena admin edit Service Log (Filament Repeater menghapus item lama
        // sebelum membuat item baru), maupun karena dihapus manual -- saldo yang
        // SUDAH TERLANJUR DIPOTONG harus dikembalikan ke kartu stok teknisi.
        // Tanpa ini, edit Service Log akan membuat saldo hilang permanen karena
        // potongan lama tidak pernah dikembalikan.
        static::deleting(function ($item) {
            $serviceLog = \App\Models\ServiceLog::find($item->service_log_id);
            if (!$serviceLog) return;

            $techId = $serviceLog->technician_id;
            if (!$techId) return;

            $techStock = \App\Models\TechnicianStock::firstOrCreate(
                ['technician_id' => $techId, 'sparepart_id' => $item->sparepart_id],
                ['jumlah' => 0]
            );

            // Kembalikan saldo sebesar jumlah yang dulu dipotong oleh baris ini
            $techStock->increment('jumlah', $item->jumlah);

            $machine = \App\Models\Machine::find($serviceLog->machine_id);
            $cust    = $machine?->customer?->nama_customer ?? 'Unknown';
            $sn      = $machine?->serial_number ?? '-';

            \App\Models\TechnicianStockHistory::create([
                'technician_id' => $techId,
                'sparepart_id'  => $item->sparepart_id,
                'masuk'         => $item->jumlah,
                'keluar'        => 0,
                'saldo_akhir'   => $techStock->fresh()->jumlah,
                'keterangan'    => "Koreksi/Edit Service: {$cust} (SN: {$sn}) — saldo dikembalikan",
            ]);
        });
    }

    public function serviceLog(): BelongsTo
    {
        return $this->belongsTo(ServiceLog::class, 'service_log_id');
    }

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class, 'sparepart_id');
    }
}
