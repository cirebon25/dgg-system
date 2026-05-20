<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceLog extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'machine_id',
        'customer_id',
        'technician_id',
        'tanggal',
        'tipe_kunjungan',
        'counter_bw',
        'counter_color',
        'kerusakan',
        'perbaikan',
        'nama_teknisi_2',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * RELASI KE TEKNISI (HANYA BOLEH ADA SATU DI SINI BOSS!)
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'technician_id');
    }

    /**
     * RELASI KE CUSTOMER
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * RELASI KE MESIN
     */
    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    /**
     * RELASI KE PENGGUNAAN SPAREPART
     */
    public function serviceLogSpareparts(): HasMany
    {
        return $this->hasMany(ServiceLogSparepart::class, 'service_log_id');
    }
}
