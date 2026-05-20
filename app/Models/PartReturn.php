<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartReturn extends Model
{
    protected $guarded = [];

    // KOSONGKAN booted() — semua logika stok ditangani
    // di CreatePartReturn::afterCreate() agar tidak dobel
    // Filament menggunakan saveQuietly() sehingga booted() tidak terpanggil dari web

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}