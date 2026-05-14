<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartReturn extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($return) {
            // 1. Balikkan ke Gudang Utama
            \App\Models\Sparepart::find($return->sparepart_id)?->increment('stok', $return->jumlah);

            // 2. Kurangi dari Tas Teknisi (Pakai firstOrCreate biar anti-gagal)
            $techStock = \App\Models\TechnicianStock::firstOrCreate(
                ['technician_id' => $return->technician_id, 'sparepart_id' => $return->sparepart_id],
                ['jumlah' => 0]
            );
            $techStock->decrement('jumlah', $return->jumlah);

            \App\Models\TechnicianStockHistory::create([
                'technician_id' => $return->technician_id,
                'sparepart_id' => $return->sparepart_id,
                'keluar' => $return->jumlah,
                'saldo_akhir' => $techStock->jumlah,
                'keterangan' => 'Retur Part ke Gudang',
            ]);
        });
    }

    public function technician() { return $this->belongsTo(Technician::class); }
    public function sparepart() { return $this->belongsTo(Sparepart::class); }
}