<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeterReading extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'machine_id',
        'customer_id',
        'technician_id',
        'tanggal',
        'counter_bw_lalu',
        'counter_bw',
        'usage_bw',
        'counter_color_lalu',
        'counter_color',
        'usage_color',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    /**
     * Kontrak MRC AKTIF milik mesin ini.
     * Bisa di-eager load: MeterReading::with('kontrak')
     */
    public function kontrak(): HasOne
    {
        return $this->hasOne(MrcContract::class, 'machine_id', 'machine_id')
            ->where('aktif', true);
    }

    /**
     * Kontrak yang berlaku PADA TANGGAL pembacaan meter.
     * Dipakai untuk data lama (kontrak sudah nonaktif / diganti).
     */
    public function kontrakBerlaku(): ?MrcContract
    {
        return MrcContract::where('machine_id', $this->machine_id)
            ->whereDate('tanggal_mulai', '<=', $this->tanggal)
            ->where(function ($q) {
                $q->whereNull('tanggal_selesai')
                    ->orWhereDate('tanggal_selesai', '>=', $this->tanggal);
            })
            ->latest('tanggal_mulai')
            ->first();
    }

    /**
     * Hitung tagihan bulan ini dari usage + harga/kuota kontrak.
     * Return null kalau mesin belum punya kontrak.
     */
    public function hitungTagihan(?MrcContract $kontrak = null): ?array
    {
        $kontrak ??= $this->kontrakBerlaku();

        if (! $kontrak) {
            return null;
        }

        return $kontrak->hitungTagihan(
            (int) $this->usage_bw,
            (int) $this->usage_color
        );
    }

    /**
     * Akses cepat: $reading->total_tagihan
     */
    public function getTotalTagihanAttribute(): int
    {
        return $this->hitungTagihan()['total'] ?? 0;
    }
}
