<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicianStockHistory extends Model
{
    protected $guarded = [];

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }
}