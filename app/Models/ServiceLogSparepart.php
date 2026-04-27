<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceLogSparepart extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_log_id',
        'sparepart_id',
        'jumlah',
    ];

    // Logika Otomatis: Begitu sparepart disimpan di Service Log, stok gudang langsung berkurang
    protected static function booted()
    {
        static::created(function ($item) {
            $sparepart = Sparepart::find($item->sparepart_id);
            if ($sparepart) {
                $sparepart->decrement('stok', $item->jumlah);
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