<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id', 'technician_id', 'tanggal', 'jam_mulai', 'jam_selesai',
        'tipe_kunjungan', 'counter_bw', 'usage_bw', 'counter_color', 'usage_color',
        'kerusakan', 'perbaikan', 'sparepart_id', 'jumlah_sparepart', 'customer_id',
    ];

    protected $casts = [
        'tanggal' => 'date', // Sesuaikan nama kolomnya (tadi di fillable 'tanggal')
    ];

    protected static function booted()
    {
        static::created(function ($serviceLog) {
            // JEMBATAN OTOMATIS: Update angka pemakaian part setiap ada servis baru
            // Kita ambil semua catatan kesehatan part untuk mesin ini
            $healthRecords = \App\Models\MachinePartHealth::where('machine_id', $serviceLog->machine_id)->get();
            
            foreach ($healthRecords as $health) {
                // Pemakaian = Counter Sekarang (BW) - Counter saat terakhir ganti
                // Boss bisa ganti ke counter_color jika partnya spesifik warna
                $currentCounter = $serviceLog->counter_bw ?? 0; 
                $usage = $currentCounter - $health->last_replaced_counter;
                
                $health->update([
                    'current_usage' => $usage > 0 ? $usage : 0
                ]);
            }
        });
    }

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