<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MachineReplacement extends Model
{
    protected $table = 'machine_replacements';

    protected $fillable = [
        'customer_id',
        'old_machine_id',
        'new_machine_id',
        'deployment_id',
        'technician_id',
        'tanggal',
        'keterangan',
        'counter_bw_final',
        'counter_color_final',
    ];

    protected $casts = [
        'tanggal'                => 'date',
        'counter_bw_final'       => 'integer',
        'counter_color_final'    => 'integer',
        'created_at'             => 'datetime',
        'updated_at'             => 'datetime',
    ];

    // ✅ Relasi
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function oldMachine(): BelongsTo
    {
        return $this->belongsTo(Machine::class, 'old_machine_id');
    }

    public function newMachine(): BelongsTo
    {
        return $this->belongsTo(Machine::class, 'new_machine_id');
    }

    public function deployment(): BelongsTo
    {
        return $this->belongsTo(Deployment::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }
}
