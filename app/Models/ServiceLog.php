<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceLog extends Model
{
    protected $guarded = [];

   protected static function booted()
    {
    static::created(function ($serviceLog) {
        // TAMBAHKAN PENGECEKAN: Hanya jalan jika sparepart_id ADA isinya
        if ($serviceLog->sparepart_id && $serviceLog->jumlah_sparepart > 0) {
            $sparepart = \App\Models\Sparepart::find($serviceLog->sparepart_id);
            if ($sparepart) {
                $sparepart->decrement('stok', $serviceLog->jumlah_sparepart);
            }
        }
    });
    }

    public function machine() { return $this->belongsTo(Machine::class); }
    public function technician() { return $this->belongsTo(Technician::class); }
    public function sparepart() { return $this->belongsTo(Sparepart::class); }
}