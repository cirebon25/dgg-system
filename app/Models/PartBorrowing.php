<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartBorrowing extends Model
{
    protected $guarded = [];

    // KOSONGKAN booted() — semua logika stok ditangani
    // di CreatePartBorrowing::afterCreate() agar tidak dobel

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}