<?php

// app/Models/MachineWithdrawal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MachineWithdrawal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'machine_id',
        'customer_id',
        'tanggal_tarik',
        'alasan_penarikan',
        'kondisi_akhir',
    ];

    protected $casts = [
        'tanggal_tarik' => 'date',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    protected static function booted(): void
    {
        static::created(function (self $withdrawal) {
            // 1. Update status mesin jadi Ready dan lepas customer
            Machine::where('id', $withdrawal->machine_id)
                ->update([
                    'status'      => 'Ready',
                    'customer_id' => null,
                ]);

            // 2. Soft-delete semua Deployment aktif untuk mesin ini
            Deployment::where('machine_id', $withdrawal->machine_id)
                ->each(fn(Deployment $d) => $d->delete());
        });

        static::deleted(function (self $withdrawal) {
            // Rollback: kembalikan status mesin jadi Rented
            // dan pasang kembali customer_id dari data withdrawal
            Machine::where('id', $withdrawal->machine_id)
                ->update([
                    'status'      => 'Rented',
                    'customer_id' => $withdrawal->customer_id,
                ]);

            // Restore Deployment yang di-soft-delete saat penarikan
            Deployment::onlyTrashed()
                ->where('machine_id', $withdrawal->machine_id)
                ->restore();
        });
    }
}
