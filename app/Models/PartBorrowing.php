<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;

class PartBorrowing extends Model
{
    use UppercaseAttributes;
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
