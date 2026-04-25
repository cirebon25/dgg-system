<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deployment extends Model
{
    protected $fillable = ['customer_id', 'machine_id', 'technician_id', 'tanggal_instal', 'tanggal_tarik', 'keterangan'];

    // --- BAGIAN OTOMATISASI MULAI DI SINI ---
    protected static function booted()
    {
        // 1. Saat data Penempatan dibuat (Save)
        static::created(function ($deployment) {
            $deployment->machine->update(['status' => 'Rented']);
        });

        // 2. Saat data Penempatan dihapus (Delete)
        static::deleted(function ($deployment) {
            $deployment->machine->update(['status' => 'Ready']);
        });
    }
    // --- SELESAI ---

    public function customer() { return $this->belongsTo(Customer::class); }
    public function machine() { return $this->belongsTo(Machine::class); }
    public function technician() { return $this->belongsTo(Technician::class); }
}