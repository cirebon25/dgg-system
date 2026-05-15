<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use App\Models\ServiceLogSparepart; // 🌟 WAJIB ADA INI BOSS!
use App\Models\ServiceLog;

class ServiceLogSparepart extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * LOGIKA POTONG STOK OTOMATIS (Gudang & Tas Teknisi)
     */
    protected static function booted()
    {
        static::created(function ($item) {
            $serviceLog = \App\Models\ServiceLog::find($item->service_log_id);
            if ($serviceLog) {
                // A. POTONG STOK GUDANG UTAMA
                $mainPart = \App\Models\Sparepart::find($item->sparepart_id);
                if ($mainPart) {
                    $mainPart->decrement('stok', $item->jumlah);
                }

                // B. POTONG STOK TAS TEKNISI (Rolling Stock)
                $techId = $serviceLog->technician_id;
                if ($techId) {
                    $techStock = \App\Models\TechnicianStock::firstOrCreate(
                        ['technician_id' => $techId, 'sparepart_id' => $item->sparepart_id],
                        ['jumlah' => 0]
                    );
                    $techStock->decrement('jumlah', $item->jumlah);

                    // C. CATAT HISTORI TAS
                    $machine = \App\Models\Machine::find($serviceLog->machine_id);
                    $cust = $machine->customer->nama_customer ?? 'Unknown';
                    \App\Models\TechnicianStockHistory::create([
                        'technician_id' => $techId,
                        'sparepart_id'  => $item->sparepart_id,
                        'keluar'        => $item->jumlah,
                        'saldo_akhir'   => $techStock->fresh()->jumlah,
                        'keterangan'    => "Service: {$cust} (SN: {$machine->serial_number})",
                    ]);
                }
            }
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