<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashMutationItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi balik ke data kuitansi utama
    public function cashMutation(): BelongsTo
    {
        return $this->belongsTo(CashMutation::class);
    }
}