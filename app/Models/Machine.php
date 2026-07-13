<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use HasFactory, UppercaseAttributes, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'serial_number',
        'tipe_model',
        'status',
        'keterangan_awal',
        'volt',
        'finisher',
        'cover',
        'kaset',
        'double_scan',
        'technician_id',
        'counter_bw',
        'counter_color',
        'last_rolled_at',
    ];

    // ✅ EXCLUDE fields dari uppercase (hanya di model, bukan di trait)
    protected $uppercaseExcept = [
        'counter_bw',
        'counter_color',
        'last_rolled_at',
        'double_scan',
        'status',
        'customer_id',
        'technician_id',
    ];

    protected $casts = [
        'counter_bw'    => 'integer',
        'counter_color' => 'integer',
        'last_rolled_at' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    // ────── RELASI ──────

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function serviceLogs()
    {
        return $this->hasMany(ServiceLog::class);
    }

    public function deployment()
    {
        return $this->hasOne(Deployment::class, 'machine_id');
    }

    public function oldReplacements()
    {
        return $this->hasMany(MachineReplacement::class, 'old_machine_id');
    }

    public function newReplacements()
    {
        return $this->hasMany(MachineReplacement::class, 'new_machine_id');
    }

    public function getDisplayStatusAttribute(): string
    {
        return match (strtolower($this->status)) {
            'ready' => 'Ex Luar',
            default => $this->status,
        };
    }
}
