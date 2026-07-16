<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MachineAirRo extends Model
{
    protected $table = 'machine_air_ros';

    protected $fillable = [
        'customer_ro_id',
        'serial_number',
        'tipe_mesin',
        'status',
        'keterangan',
    ];

    public function customerRo(): BelongsTo
    {
        return $this->belongsTo(CustomerRo::class);
    }
}
