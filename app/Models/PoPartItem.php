<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoPartItem extends Model
{
    protected $fillable = [
        'po_part_id', 'sparepart_id', 'nama_part',
        'merk_type', 'kode_part', 'jumlah', 'keterangan',
    ];

    public function poPart(): BelongsTo
    {
        return $this->belongsTo(PoPart::class, 'po_part_id');
    }

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class, 'sparepart_id');
    }
}