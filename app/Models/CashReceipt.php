<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashReceipt extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'cash_receipts';

    // Kolom yang dapat diisi massal (sesuai field form Filament)
    protected $fillable = [
        'tanggal',
        'no_bukti',
        'sumber_dana',
        'jumlah',
        'keterangan',
        'dibuat_oleh',
    ];

    // Format tipe data kolom
    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
    ];
}