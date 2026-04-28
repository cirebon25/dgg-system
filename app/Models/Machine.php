<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Ini Import (Sudah Benar)

class Machine extends Model
{
    // WAJIB TAMBAHKAN BARIS INI DI SINI:
    use HasFactory; 
    
    protected $fillable = [
        'serial_number',
        'tipe_model',
        'status',
        'keterangan_awal'
    ];

    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }

    public function customer() { 
        return $this->belongsTo(Customer::class); 
    }
    
    public function deployment() { 
        return $this->hasOne(Deployment::class); 
    }

    public function serviceLogs()
    {
        return $this->hasMany(ServiceLog::class);
    }

    public static function getGloballySearchableAttributes(): array
    {
        // Saya sesuaikan ke 'tipe_model' agar pencarian global tidak eror
        return ['serial_number', 'tipe_model']; 
    }
}