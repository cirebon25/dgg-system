<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceLog extends Model
{
    use HasFactory;

    // Daftar kolom yang boleh diisi
    protected $fillable = [
    'machine_id', 'technician_id', 'tanggal', 'jam_mulai', 'jam_selesai',
    'tipe_kunjungan', 'counter_bw', 'usage_bw', 'counter_color', 'usage_color',
    'kerusakan', 'perbaikan', 'sparepart_id', 'jumlah_sparepart'
    ];

    // Format otomatis tanggal
    protected $casts = [
        'tanggal_service' => 'date',
    ];

    // Logika Otomatis saat data dibuat
    protected static function booted()
    {
        static::created(function ($serviceLog) {
            // Jika teknisi memilih sparepart dari list, stok di gudang otomatis berkurang
            if ($serviceLog->sparepart_id && $serviceLog->jumlah_sparepart > 0) {
                $sparepart = \App\Models\Sparepart::find($serviceLog->sparepart_id);
                if ($sparepart) {
                    $sparepart->decrement('stok', $serviceLog->jumlah_sparepart);
                }
            }
        });
    }

    // Relasi ke tabel lain
    public function machine(): BelongsTo 
    { 
        return $this->belongsTo(Machine::class); 
    }

    public function technician(): BelongsTo 
    { 
        return $this->belongsTo(Technician::class); 
    }

    public function sparepart(): BelongsTo 
    { 
        return $this->belongsTo(Sparepart::class); 
    }

    public function serviceLogSpareparts()
    {
    return $this->hasMany(ServiceLogSparepart::class);
    }

}