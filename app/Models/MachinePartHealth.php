<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MachinePartHealth extends Model
{
    use HasFactory, UppercaseAttributes;

    // Nama tabelnya (pastikan sesuai dengan migrasi)
    protected $table = 'machine_part_health';

    // Izin kolom yang boleh diisi otomatis
    protected $fillable = [
        'machine_id',
        'sparepart_id',
        'last_replaced_counter',
        'last_replaced_at',
        'current_usage',
    ];

    /**
     * Relasi ke data Mesin
     */
    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    /**
     * Relasi ke data Sparepart
     */
    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }
}
