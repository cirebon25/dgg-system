<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    protected $guarded = [];

    protected $casts = ['scan_at' => 'datetime'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(AttendanceEmployee::class, 'pin', 'pin');
    }
}