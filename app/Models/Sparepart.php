<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
    ];

    // --- RELASI-RELASI PENDUKUNG ---
    public function sparepartEntries()
    {
        return $this->hasMany(SparepartEntry::class);
    }

    public function partBorrowings()
    {
        return $this->hasMany(PartBorrowing::class);
    }

    // 🌟 RELASI BARU: Hubungan langsung ke transaksi Pemasangan Mesin (Deployment)
    public function deployments()
    {
        return $this->belongsToMany(Deployment::class, 'deployment_sparepart', 'sparepart_id', 'deployment_id')
            ->withPivot('jumlah')
            ->withTimestamps();
    }

    // --- MANTRANYA DI SINI BOSS (REAL-TIME ACCESSOR - GUDANG AKURAT) ---

    // 1. Hitung Otomatis Total Saldo Masuk dari Inputan Supplier
    public function getCalculatedSaldoMasukAttribute(): int
    {
        return (int) $this->sparepartEntries()->sum('jumlah');
    }

    // 2. Hitung Otomatis Total Keluar (Pinjam Teknisi + Terpasang di Mesin Deploy)
    public function getCalculatedSaldoKeluarAttribute(): int
    {
        // A. Hitung pengeluaran dari pinjaman teknisi
        $pinjamTeknisi = (int) $this->partBorrowings()->sum('jumlah');

        // B. Hitung pengeluaran dari pemasangan unit mesin baru (Deployment)
        $terpasangMesin = (int) DB::table('deployment_sparepart')->where('sparepart_id', $this->id)->sum('jumlah');

        // Gabungkan kedua pengeluaran gudang
        return $pinjamTeknisi + $terpasangMesin;
    }

    // 3. Sisa Stok Gudang Pusat Saat Ini = Total Masuk - Total Keluar
    public function getCalculatedStokAttribute(): int
    {
        return $this->calculated_saldo_masuk - $this->calculated_saldo_keluar;
    }
}
