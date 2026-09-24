<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Exception;

class PartUsageItem extends Model
{
    protected $guarded = [];

    public function header(): BelongsTo
    {
        return $this->belongsTo(PartUsageHeader::class, 'part_usage_header_id');
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }

    protected static function booted()
    {
        static::created(function ($item) {
            DB::transaction(function () use ($item) {
                $header = $item->header;
                $sparepartName = Sparepart::find($item->sparepart_id)->nama_sparepart ?? 'Sparepart';

                if ($header->sumber_stok === 'gudang') {
                    // Potong Stok Gudang Pusat
                    $sparepart = Sparepart::lockForUpdate()->find($item->sparepart_id);
                    if (!$sparepart || $sparepart->stok < $item->jumlah) {
                        throw new Exception("❌ Stok Gudang untuk '{$sparepartName}' tidak mencukupi! Sisa di gudang: " . ($sparepart->stok ?? 0));
                    }
                    $sparepart->decrement('stok', $item->jumlah);
                } else {
                    // Potong Stok Tas Teknisi
                    $techStock = TechnicianStock::where('technician_id', $header->technician_id)
                        ->where('sparepart_id', $item->sparepart_id)
                        ->first();

                    if (!$techStock || $techStock->jumlah < $item->jumlah) {
                        throw new Exception("❌ Stok di Tas Teknisi untuk '{$sparepartName}' tidak mencukupi! Sisa di tas: " . ($techStock->jumlah ?? 0));
                    }

                    $techStock->decrement('jumlah', $item->jumlah);
                }
            });
        });
    }
}
