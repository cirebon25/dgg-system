<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartReplacement extends Model
{
    protected $fillable = [
        'machine_id',
        'sparepart_id',
        'service_log_id',
        'counter_saat_ganti',
        'counter_sebelumnya',
        'selisih',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function serviceLog(): BelongsTo
    {
        return $this->belongsTo(ServiceLog::class);
    }
}
