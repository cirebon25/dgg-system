<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeployMachineRo extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function machineAirRo()
    {
        return $this->belongsTo(MachineAirRo::class);
    }

    public function customerRo()
    {
        return $this->belongsTo(CustomerRo::class);
    }
}
