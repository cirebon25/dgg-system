<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicianStock extends Model
{
    // Buka akses agar bisa ditambah/dikurangi secara otomatis
    protected $guarded = [];

    // Relasi ke tabel Sparepart
    public function sparepart() 
    { 
        return $this->belongsTo(Sparepart::class); 
    }
    
    // Relasi ke tabel Teknisi
    public function technician() 
    { 
        return $this->belongsTo(Technician::class); 
    }
}