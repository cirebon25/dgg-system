<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Technician extends Model
{
    use HasFactory;

    protected $fillable = ['rayon_id', 'nama_technician', 'nomor_hp', 'is_active'];

    public function rayon()
    {
        return $this->belongsTo(Rayon::class);
    }

    public function rayons()
    {
        return $this->belongsToMany(Rayon::class, 'rayon_technician');
    }

    // Jembatan ke Kartu Stok
    public function technicianStocks()
    {
        return $this->hasMany(TechnicianStock::class);
    }

    // Jembatan ke Histori Tabungan
    public function histories()
    {
        return $this->hasMany(TechnicianStockHistory::class);
    }

    public function serviceLogs(): HasMany
    {
        return $this->hasMany(ServiceLog::class, 'technician_id');
    }
}
