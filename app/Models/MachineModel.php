<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Tambahkan biar aman kalau terhapus

class Machine extends Model
{
    use HasFactory, SoftDeletes;

    // 1. DAFTAR KOLOM YANG BOLEH DIISI (PINTU MASUK DATA)
    protected $fillable = [
        'serial_number', 
        'tipe_model', 
        'brand', 
        'status', 
        'customer_id',    // Wajib ada untuk logika Deploy
        'technician_id',  // Wajib ada untuk grouping PR 1
        'keterangan_awal',
        'volt',
        'finisher',
        'cover',
        'kaset',
        'double_scan',
    ];

    /**
     * RELASI KE CUSTOMER (Lokasi Mesin Saat Ini)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * RELASI KE TEKNISI (Penanggung Jawab Rayon)
     */
    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    /**
     * RELASI KE RIWAYAT PEMASANGAN (Deployment)
     */
    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }

    /**
     * RELASI KE LAPORAN SERVIS
     */
    public function serviceLogs()
    {
        return $this->hasMany(ServiceLog::class);
    }
}