<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Wajib untuk fitur Arsip/Trashed
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * DAFTAR KOLOM YANG BOLEH DIISI (FILLABLE)
     * Tanpa technician_id di sini, data teknisi tidak akan pernah tersimpan!
     */
    protected $fillable = [
        'nama_customer',
        'alamat',
        'no_telp',
        'kota',
        'rayon_id',
        'technician_id', // 🌟 Kunci Utama agar Save Teknisi Berhasil
    ];

    /**
     * RELASI KE TEKNISI (Teknisi Utama)
     * Ini yang bikin 'technician.nama_technician' di Resource bisa tampil
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technician_id');
    }

    /**
     * RELASI KE RAYON
     */
    public function rayon(): BelongsTo
    {
        return $this->belongsTo(Rayon::class, 'rayon_id');
    }

    /**
     * RELASI KE UNIT TERPASANG (Machine)
     * Sesuaikan nama 'deployments' jika di tabel/resource Akang menggunakan nama itu
     */
    public function deployments(): HasMany
    {
        // Jika di database nama tabelnya 'machines', pastikan modelnya Machine
        return $this->hasMany(Machine::class, 'customer_id');
    }

    // Jika Akang menggunakan nama 'machines' di tempat lain, buatkan cadangannya:
    public function machines(): HasMany
    {
        return $this->hasMany(Machine::class, 'customer_id');
    }
}