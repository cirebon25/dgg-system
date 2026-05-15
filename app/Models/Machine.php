<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'serial_number',
        'tipe_model',
        'status',
        'keterangan_awal',
        'volt',
        'finisher',
        'cover',
        'kaset',
        'double_scan',
    ];

    // Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi ke ServiceLogs
    public function serviceLogs()
    {
        return $this->hasMany(ServiceLog::class);
    }
}