<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;

class TechnicianStockHistory extends Model
{
    use UppercaseAttributes;
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
