<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoPart extends Model
{
    protected $fillable = ['no_po', 'tanggal', 'keterangan'];

    protected $casts = ['tanggal' => 'date'];

    public function items(): HasMany
    {
        return $this->hasMany(PoPartItem::class, 'po_part_id');
    }

    // Auto generate no_po: PO-BDG-20260617-001
    public static function generateNoPo(): string
    {
        $prefix = 'PO-BDG-' . date('Ymd');
        $last = static::where('no_po', 'like', $prefix . '%')
            ->orderByDesc('no_po')
            ->value('no_po');

        $seq = $last ? (int) substr($last, -3) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}