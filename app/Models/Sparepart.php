<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sparepart extends Model
{
    use HasFactory;

    // SEMUA KOLOM DISATUKAN DI SINI (HANYA BOLEH ADA SATU $fillable)
    protected $fillable = [
        'nama_sparepart',
        'code_part',
        'no_part',
        'saldo_masuk',
        'saldo_keluar',
        'stok',
        'harga_beli',
        'keterangan',
    ];

    // Relasi ke Service Log Sparepart
    public function serviceLogSpareparts()
    {
        return $this->hasMany(ServiceLogSparepart::class);
    }

    // Logika Hitung Sisa Saldo (Stok) Otomatis
    protected static function booted()
    {
        static::saving(function ($sparepart) {
            // Sisa Saldo (stok) = Masuk - Keluar
            $masuk = $sparepart->saldo_masuk ?? 0;
            $keluar = $sparepart->saldo_keluar ?? 0;
            
            $sparepart->stok = $masuk - $keluar;
        });
    }
}