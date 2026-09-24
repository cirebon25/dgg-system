<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartUsageHeader extends Model
{
    protected $guarded = [];

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PartUsageItem::class, 'part_usage_header_id');
    }
}
