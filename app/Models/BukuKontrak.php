<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BukuKontrak extends Model
{
    protected $fillable = [
        'no_kontrak',
        'customer_id',
        'tgl_dtg',
        'technician_id',
        'tgl_dibawa',
        'status',
        'tgl_status',
        'keterangan',
    ];

    protected $casts = [
        'tgl_dtg'    => 'date',
        'tgl_dibawa' => 'date',
        'tgl_status' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public static function statusLabels(): array
    {
        return [
            'depo_cirebon'   => 'Di Depo Cirebon',
            'dibawa_teknisi' => 'Dibawa Teknisi',
            'di_customer'    => 'Di Customer',
            'selesai'        => 'Sudah Selesai',
        ];
    }
}
