<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerRo extends Model
{
    protected $table = 'customer_ros';

    protected $fillable = [
        'nama_customer',
        'lokasi',
        'keterangan',
    ];

    public function machineAirRos(): HasMany
    {
        return $this->hasMany(MachineAirRo::class);
    }
}
