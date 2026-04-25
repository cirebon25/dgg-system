<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    // TAMBAHKAN INI BIAR NGGAK ERROR FORMAT()
    protected $casts = [
        'tanggal' => 'date',
    ];

    protected static function booted()
    {
        static::created(function ($serviceLog) {
            if ($serviceLog->sparepart_id && $serviceLog->jumlah_sparepart > 0) {
                $sparepart = Sparepart::find($serviceLog->sparepart_id);
                if ($sparepart) {
                    $sparepart->decrement('stok', $serviceLog->jumlah_sparepart);
                }
            }
        });
    }

    public function machine(): BelongsTo { return $this->belongsTo(Machine::class); }
    public function technician(): BelongsTo { return $this->belongsTo(Technician::class); }
    public function sparepart(): BelongsTo { return $this->belongsTo(Sparepart::class); }
}