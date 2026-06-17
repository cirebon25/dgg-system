<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;

class Sparepart extends Model
{
    use HasFactory, UppercaseAttributes;

    protected $fillable = [
        'nama_sparepart',
        'nama_alias',
        'code_part',
        'no_part',
        'stok',
        'harga_beli',
        'keterangan',
    ];

    // Helper: tampilkan nama lengkap + alias untuk dropdown
    public function getLabelLengkapAttribute(): string
    {
        if ($this->nama_alias) {
            return "{$this->nama_sparepart} ({$this->nama_alias})";
        }
        return $this->nama_sparepart;
    }

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

        public function technicianStocks()
{
    return $this->hasMany(\App\Models\TechnicianStock::class, 'sparepart_id');
}
    }