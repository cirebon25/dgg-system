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
        'stok_minimum',
        'auto_order',
        'harga_beli',
        'keterangan',
    ];

    protected $casts = [
        'auto_order' => 'boolean',
    ];

    // Helper: tampilkan nama lengkap + alias untuk dropdown
    public function getLabelLengkapAttribute(): string
    {
        if ($this->nama_alias) {
            return "{$this->nama_sparepart} ({$this->nama_alias})";
        }
        return $this->nama_sparepart;
    }

    // Scope: stok <= stok minimum DAN ikut order otomatis
    public function scopeStokMenipis($query)
    {
        return $query
            ->where('auto_order', true)
            ->whereColumn('stok', '<=', 'stok_minimum');
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

    public function orderPartItems()
    {
        return $this->hasMany(\App\Models\OrderPartItem::class, 'sparepart_id');
    }
}
