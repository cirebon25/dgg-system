<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparepartUsageRo extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function machineAirRo()
    {
        return $this->belongsTo(MachineAirRo::class);
    }

    public function sparepartRo()
    {
        return $this->belongsTo(SparepartRo::class);
    }
}
