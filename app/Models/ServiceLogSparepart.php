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
