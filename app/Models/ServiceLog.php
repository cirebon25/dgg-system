<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- 1. PANGGIL MANTRANYA

class ServiceLog extends Model
{
    use HasFactory, SoftDeletes; // <--- 2. PASANG MANTRANYA DI SINI

    protected $fillable = [
        'machine_id', 
        'customer_id', 
        'technician_id', 
        'nama_teknisi_2',
        'tanggal', 
        'jam_mulai', 
        'jam_selesai',
        'tipe_kunjungan', 
        'counter_bw', 
        'usage_bw', 
        'counter_color', 
        'usage_color',
        'kerusakan', 
        'perbaikan', 
        'sparepart_id', 
        'jumlah_sparepart',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relasi Teknisi Utama
    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technician_id');
    }

    // Relasi Teknisi Kedua (Partner) - Pastikan nama kolom di DB sesuai (technician_2_id)
    public function technician2(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technician_2_id');
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function machine(): BelongsTo { return $this->belongsTo(Machine::class); }
    public function serviceLogSpareparts() { return $this->hasMany(ServiceLogSparepart::class); }
    public function deployment()
    {
        return $this->belongsTo(Deployment::class);
    }

}