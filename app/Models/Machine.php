<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- 1. PANGGIL MANTRANYA

class Machine extends Model
{
    use HasFactory, SoftDeletes; // <--- 2. AKTIFKAN MANTRANYA DI SINI

    protected $fillable = [
        'serial_number',
        'tipe_model',
        'status',
        'keterangan_awal',
        'volt',
        'finisher',
        'cover',
        'kaset',
        'rayon_id',    
        'customer_id', 
    ];

    // --- RELASI KE RAYON (WAJIB ADA UNTUK LAPORAN ALOKASI) ---
    public function rayon()
    {
        return $this->belongsTo(Rayon::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }

    public function deployment()
    {
        return $this->hasOne(Deployment::class);
    }

    public function serviceLogs()
    {
        return $this->hasMany(ServiceLog::class);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['serial_number', 'tipe_model'];
    }
}