<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceLogSparepart extends Model
{
    protected $guarded = [];

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

                // B. POTONG STOK TAS TEKNISI
                $techId = $serviceLog->technician_id;
                if ($techId) {
                    $techStock = \App\Models\TechnicianStock::firstOrCreate(
                        ['technician_id' => $techId, 'sparepart_id' => $item->sparepart_id],
                        ['jumlah' => 0]
                    );
                    $techStock->decrement('jumlah', $item->jumlah);

                    // C. HISTORI TAS
                    $machine = \App\Models\Machine::find($serviceLog->machine_id);
                    $cust = $machine->customer->nama_customer ?? 'Unknown';
                    \App\Models\TechnicianStockHistory::create([
                        'technician_id' => $techId,
                        'sparepart_id'  => $item->sparepart_id,
                        'keluar'        => $item->jumlah,
                        'saldo_akhir'   => $techStock->jumlah,
                        'keterangan'    => "Rolling: {$cust} (SN: {$machine->serial_number})",
                    ]);
                }
            }
        });
    }

    public function sparepart() { return $this->belongsTo(Sparepart::class); }
}