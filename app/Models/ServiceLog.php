<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id', 
        'customer_id', // SUDAH BERSIH & DITAMBAHKAN
        'technician_id', 
        'tanggal', 
        'jam_mulai', 
        'jam_selesai',
        'tipe_kunjungan', 
        'counter_bw', 
        'usage_bw', 
        'counter_color', 
        'usage_color',
        'kerusakan', 
        'perbaikan', 
        'sparepart_id', 
        'jumlah_sparepart',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected static function booted()
    {
        static::created(function ($serviceLog) {
            // JEMBATAN OTOMATIS: Update angka pemakaian part setiap ada servis baru
            $healthRecords = \App\Models\MachinePartHealth::where('machine_id', $serviceLog->machine_id)->get();

            foreach ($healthRecords as $health) {
                // Pemakaian = Counter Sekarang (BW) - Counter saat terakhir ganti
                $currentCounter = $serviceLog->counter_bw ?? 0;
                $usage = $currentCounter - $health->last_replaced_counter;

                $health->update([
                    'current_usage' => $usage > 0 ? $usage : 0,
                ]);
            }
        });
    }

    // RELASI KE CUSTOMER (Wajib ada buat Laporan Tukar Guling)
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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