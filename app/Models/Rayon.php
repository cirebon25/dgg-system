<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rayon extends Model
{
    use HasFactory;

    // Bersihkan technician_id karena sudah pakai tabel pivot
    protected $fillable = ['nama_rayon']; 

    /**
     * Relasi ke Customer
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Relasi ke Tim Teknisi (Many-to-Many)
     * Pastikan nama tabel pivot 'rayon_technician' sudah dibuat di database
     */
    public function technicians(): BelongsToMany
    {
        return $this->belongsToMany(Technician::class, 'rayon_technician');
    }
}