<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;

class SparepartEntry extends Model
{
    use UppercaseAttributes;
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($entry) {
            // AMBIL DATA MASTER SPAREPART
            $sparepart = \App\Models\Sparepart::find($entry->sparepart_id);

            if ($sparepart) {
                // 1. TAMBAH STOK DI MASTER
                $sparepart->increment('stok', $entry->jumlah);
                
                // 2. TAMBAH SALDO MASUK DI MASTER (UNTUK REKAP)
                $sparepart->increment('saldo_masuk', $entry->jumlah);
            }
        });
    }

    public function sparepart() {
        return $this->belongsTo(Sparepart::class);
    }
}
