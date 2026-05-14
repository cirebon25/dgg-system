<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicianStockHistory extends Model
{
    protected $guarded = [];

    // Jembatan ke Part
    public function sparepart() { 
        return $this->belongsTo(Sparepart::class); 
    }

    // Jembatan ke Teknisi (TAMBAHKAN INI)
    public function technician() {
        return $this->belongsTo(Technician::class);
    }
}