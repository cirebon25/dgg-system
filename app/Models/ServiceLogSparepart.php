<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceLogSparepart extends Model
{
    // Pastikan ada titik koma (;) di akhir baris ini
    protected $fillable = ['service_log_id', 'sparepart_id', 'jumlah'];

    protected static function booted()
    {
        // Logika saat input barang baru
        static::created(function ($item) {
            $sparepart = $item->sparepart;
            if ($sparepart) {
                $sparepart->saldo_keluar += $item->jumlah;
                $sparepart->save();
            }
        });

        // Logika saat jumlah barang diedit
        static::updated(function ($item) {
            $sparepart = $item->sparepart;
            if ($sparepart) {
                $selisih = $item->jumlah - $item->getOriginal('jumlah');
                $sparepart->saldo_keluar += $selisih;
                $sparepart->save();
            }
        });

        // Logika saat data servis dihapus
        static::deleted(function ($item) {
            $sparepart = $item->sparepart;
            if ($sparepart) {
                $sparepart->saldo_keluar -= $item->jumlah;
                $sparepart->save();
            }
        });
    }

    public function serviceLog()
    {
        return $this->belongsTo(ServiceLog::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}