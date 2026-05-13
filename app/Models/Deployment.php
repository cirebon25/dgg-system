<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- 1. SUNTIK MANTRANYA DI SINI

class Deployment extends Model
{
    use HasFactory, SoftDeletes; // <--- 2. AKTIFKAN MANTRANYA DI SINI

    protected $fillable = [
        'no_kontrak',
        'customer_id',
        'machine_id',
        'technician_id',
        'tanggal_instal',
        'keterangan',
        'counter_bw',
        'counter_color',
        'volt', 
    ];

    protected $casts = [
        'tanggal_instal' => 'date',
    ];

    /**
     * Logika Otomatis: Sinkronisasi Status Mesin & Auto Create Service Log
     */
    protected static function booted()
    {
        // 1. SAAT DEPLOYMENT BARU DIBUAT
        static::created(function ($deployment) {
            // A. Ubah Status Mesin jadi 'Rented'
            if ($deployment->machine) {
                $deployment->machine->update(['status' => 'Rented']);
            }

            // B. OTOMATIS MASUK KE SERVICE LOG (RN)
            \App\Models\ServiceLog::create([
                'machine_id'      => $deployment->machine_id,
                'customer_id'     => $deployment->customer_id,
                'technician_id'   => $deployment->technician_id,
                'tanggal'         => $deployment->tanggal_instal,
                'tipe_kunjungan'  => 'RN',
                'counter_bw'      => $deployment->counter_bw ?? 0,
                'counter_color'   => $deployment->counter_color ?? 0,
                'usage_bw'        => 0,
                'usage_color'     => 0,
                'perbaikan'       => "INSTALASI UNIT BARU (RN) - NO KONTRAK: " . $deployment->no_kontrak,
                'kerusakan'       => 'PEMASANGAN AWAL',
            ]);
        });

        // 2. Saat Deployment DIUPDATE
        static::updated(function ($deployment) {
            if ($deployment->wasChanged('machine_id')) {
                // Mesin lama balik jadi Ready
                $oldMachineId = $deployment->getOriginal('machine_id');
                if ($oldMachineId) {
                    Machine::find($oldMachineId)?->update(['status' => 'Ready']);
                }
                // Mesin baru jadi Rented
                $deployment->machine?->update(['status' => 'Rented']);
            }
        });

        // 3. Saat Deployment DIHAPUS -> Kembalikan Status Mesin jadi 'Ready'
        static::deleted(function ($deployment) {
            if ($deployment->machine) {
                $deployment->machine->update(['status' => 'Ready']);
            }
        });
    }

    /**
     * RELASI: Sparepart yang disertakan (Many-to-Many)
     */
    public function spareparts(): BelongsToMany
    {
        return $this->belongsToMany(Sparepart::class, 'deployment_sparepart')
            ->withPivot('jumlah')
            ->withTimestamps();
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function machine(): BelongsTo { return $this->belongsTo(Machine::class); }
    public function technician(): BelongsTo { return $this->belongsTo(Technician::class); }
}