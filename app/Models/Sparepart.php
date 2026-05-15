<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_sparepart',
        'code_part',
        'no_part',
        'stok',
        'harga_beli',
        'keterangan',
        // Kolom stok, saldo_masuk, saldo_keluar dibiarkan di DB tapi kalkulasinya kita handle via Accessor bawah ini
    ];

    // --- RELASI-RELASI PENDUKUNG ---
    public function sparepartEntries() {
        return $this->hasMany(SparepartEntry::class);
    }

    public function partBorrowings() {
        return $this->hasMany(PartBorrowing::class);
    }

    // --- MANTRANYA DI SINI BOSS (REAL-TIME ACCESSOR) ---
    
    // 1. Hitung Otomatis Total Saldo Masuk dari Inputan Supplier
    public function getCalculatedSaldoMasukAttribute(): int
    {
        return (int) $this->sparepartEntries()->sum('jumlah');
    }

    // 2. Hitung Otomatis Total Keluar (Barang yang dipinjam Teknisi)
    public function getCalculatedSaldoKeluarAttribute(): int
    {
        return (int) $this->partBorrowings()->sum('jumlah');
    }

    // 3. Sisa Stok Gudang Pusat Saat Ini = Total Masuk - Total Keluar
    public function getCalculatedStokAttribute(): int
    {
        return $this->calculated_saldo_masuk - $this->calculated_saldo_keluar;
    }
}