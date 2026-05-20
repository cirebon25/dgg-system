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

    // --- RELASI ---
    public function sparepartEntries()
    {
        return $this->hasMany(SparepartEntry::class);
    }

    public function partBorrowings()
    {
        return $this->hasMany(PartBorrowing::class);
    }

    public function partReturns()
    {
        return $this->hasMany(PartReturn::class);
    }

    public function deployments()
    {
        return $this->belongsToMany(Deployment::class, 'deployment_sparepart', 'sparepart_id', 'deployment_id')
            ->withPivot('jumlah')
            ->withTimestamps();
    }

    // --- ACCESSOR REAL-TIME ---

    // 1. Total Masuk dari Supplier
    public function getCalculatedSaldoMasukAttribute(): int
    {
        return (int) $this->sparepartEntries()->sum('jumlah');
    }

    // 2. Total Keluar BERSIH = (Pinjam + Deploy) - Retur
    public function getCalculatedSaldoKeluarAttribute(): int
    {
        $pinjamTeknisi  = (int) $this->partBorrowings()->sum('jumlah');
        $terpasangMesin = (int) DB::table('deployment_sparepart')->where('sparepart_id', $this->id)->sum('jumlah');
        $retur          = (int) $this->partReturns()->sum('jumlah');

        // Keluar bersih = semua keluar dikurangi yang dikembalikan
        return ($pinjamTeknisi + $terpasangMesin) - $retur;
    }

    // 3. Stok Gudang = Total Masuk - Total Keluar Bersih
    public function getCalculatedStokAttribute(): int
    {
        return $this->calculated_saldo_masuk - $this->calculated_saldo_keluar;
    }
}