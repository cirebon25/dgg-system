<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deployment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'machine_id',
        'technician_id',
        'tanggal_instal',
        'keterangan',
    ];

    // INI POSISI YANG BENAR BOSS, DI LUAR FUNGSI
    protected $casts = [
        'tanggal_instal' => 'date', 
    ];

    // Logika Otomatis Sinkronisasi Status Mesin
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

    // Relasi
    public function customer() { return $this->belongsTo(Customer::class); }
    public function machine() { return $this->belongsTo(Machine::class); }
    public function technician() { return $this->belongsTo(Technician::class); }
}