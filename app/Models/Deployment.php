<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Deployment extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_kontrak',
        'customer_id',
        'machine_id',
        'technician_id',
        'tanggal_instal',
        'keterangan',
        'counter_bw',
        'counter_color',
        'volt', // Pastikan kecil semua agar aman di database
    ];

    protected $casts = [
        'tanggal_instal' => 'date',
    ];

    /**
     * Logika Otomatis Sinkronisasi Status Mesin
     */
    protected static function booted()
    {
        // 1. Saat Deployment BARU DIBUAT -> Ubah Status Mesin jadi 'Rented'
        static::created(function ($deployment) {
            if ($deployment->machine) {
                $deployment->machine->update(['status' => 'Rented']);
            }
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

    /**
     * RELASI: Milik Customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * RELASI: Menggunakan Mesin
     */
    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    /**
     * RELASI: Dipasang oleh Teknisi
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }
}
