<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;

class MachineReplacement extends Model
{
    use HasFactory, UppercaseAttributes;

    protected $fillable = [
        'customer_id',
        'old_machine_id',
        'new_machine_id',
        'deployment_id',
        'technician_id',
        'tanggal',
        'keterangan',
    ];

    // Relasi ke Mesin Baru (Penting untuk Observer ambil SN)
    public function machine_new()
    {
        return $this->belongsTo(Machine::class, 'new_machine_id');
    }

    // Relasi ke Mesin Lama
    public function machine_old()
    {
        return $this->belongsTo(Machine::class, 'old_machine_id');
    }

    // Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi ke Teknisi
    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    // Relasi ke Deployment baru (untuk menarik sparepart yang dipakai saat rolling)
    public function deployment()
    {
        return $this->belongsTo(Deployment::class);
    }
}