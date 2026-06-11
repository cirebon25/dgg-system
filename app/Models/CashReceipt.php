<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashReceipt extends Model
{
    use HasFactory;

    protected $table = 'cash_receipts';

    protected $fillable = [
        'tanggal',
        'no_bukti',
        'sumber_dana',
        'jumlah',
        'keterangan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            // 1. Pengaman No Bukti Otomatis
            if (empty($model->no_bukti)) {
                $count = static::whereYear('tanggal', now()->year)->count() + 1;
                $romawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
                $bulan = $romawi[now()->month - 1];
                $tahun = now()->format('y');
                
                $model->no_bukti = 'KM-' . str_pad($count, 3, '0', STR_PAD_LEFT) . '/' . $bulan . '/' . $tahun;
            }

            // 2. Pengaman Sumber Dana
            if (empty($model->sumber_dana)) {
                $model->sumber_dana = 'KAS UTAMA';
            }

            // REVISI NYATA: Pengaman kolumn Keterangan jika terkirim kosong dari sistem otomatis
            if (empty($model->keterangan)) {
                $model->keterangan = 'Penerimaan Kas Masuk (' . $model->sumber_dana . ')';
            }
        });
    }
}