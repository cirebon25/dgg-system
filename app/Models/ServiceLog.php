<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogInputan;
use App\Models\Concerns\DetectsDuplicate;


class ServiceLog extends Model
{
    use DetectsDuplicate;
    use SoftDeletes;
    use HasFactory, UppercaseAttributes;

    protected $fillable = [
        'machine_id',
        'customer_id',
        'technician_id',
        'tanggal',
        'tipe_kunjungan',
        'counter_bw',
        'counter_color',
        'usage_bw',
        'usage_color',
        'kerusakan',
        'perbaikan',
        'nama_teknisi_2',
        'is_mrc',
        'counter_bw_lalu',
        'counter_color_lalu',
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

    public static function duplicateKeys(): array
    {
        return ['customer_id', 'machine_id', 'sparepart_id', 'counter_bw', 'counter_color'];
    }

    public static function duplicateDateKeys(): array
    {
        return ['tanggal'];
    }

    public function scopeBukanMrc($query)
    {
        return $query->where(fn($q) => $q->where('is_mrc', false)->orWhereNull('is_mrc'));
    }
}
