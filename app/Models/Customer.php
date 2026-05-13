<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- 1. PANGGIL MANTRANYA

class Customer extends Model
{
    use HasFactory, SoftDeletes; // <--- 2. AKTIFKAN MANTRANYA DI SINI

    protected $fillable = ['rayon_id', 'nama_customer', 'kota', 'alamat', 'nomor_telp'];

    /**
     * Relasi: Customer dimiliki oleh satu Rayon
     */
    public function rayon(): BelongsTo
    {
        return $this->belongsTo(Rayon::class);
    }

    /**
     * Relasi: Customer memiliki banyak Pemasangan Mesin (Deployments)
     */
    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }

    /**
     * Relasi Langsung ke Mesin (Untuk menghitung populasi di menu Arsip)
     */
    public function machines(): HasMany
    {
        return $this->hasMany(Machine::class);
    }
}