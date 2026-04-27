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

    // INI KABEL OTOMATISNYA BOSS
    protected static function booted()
    {
        // 1. Saat Deployment BARU DIBUAT -> Ubah Status Mesin jadi 'Rented'
        static::created(function ($deployment) {
            $deployment->machine->update(['status' => 'Rented']);
        });

        // 2. Saat Deployment DIUPDATE -> Pastikan status sinkron
        static::updated(function ($deployment) {
            if ($deployment->wasChanged('machine_id')) {
                // Mesin lama balik jadi Ready
                Machine::find($deployment->getOriginal('machine_id'))->update(['status' => 'Ready']);
                // Mesin baru jadi Rented
                $deployment->machine->update(['status' => 'Rented']);
            }
        });

        // 3. Saat Deployment DIHAPUS -> Kembalikan Status Mesin jadi 'Ready'
        static::deleted(function ($deployment) {
            if ($deployment->machine) {
                $deployment->machine->update(['status' => 'Ready']);
            }
        });
    }

    public function customer() { return $this->belongsTo(Customer::class); }
    public function machine() { return $this->belongsTo(Machine::class); }
    public function technician() { return $this->belongsTo(Technician::class); }
}