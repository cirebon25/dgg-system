<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartBorrowing extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($borrowing) {
            // 1. Potong Stok Gudang Utama
            \App\Models\Sparepart::find($borrowing->sparepart_id)?->decrement('stok', $borrowing->jumlah);

            // 2. Tambah Stok ke Tas Teknisi (Pakai firstOrCreate biar anti-gagal)
            $techStock = \App\Models\TechnicianStock::firstOrCreate(
                ['technician_id' => $borrowing->technician_id, 'sparepart_id' => $borrowing->sparepart_id],
                ['jumlah' => 0]
            );
            $techStock->increment('jumlah', $borrowing->jumlah);
            
            \App\Models\TechnicianStockHistory::create([
                    'technician_id' => $borrowing->technician_id,
                    'sparepart_id' => $borrowing->sparepart_id,
                    'masuk' => $borrowing->jumlah,
                    'saldo_akhir' => $techStock->jumlah,
                    'keterangan' => 'Pinjam Part dari Gudang',
            ]);
       
         });
    }

    public function technician() { return $this->belongsTo(Technician::class); }
    public function sparepart() { return $this->belongsTo(Sparepart::class); }
}