<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    protected static function booted()
    {
        static::created(function ($withdrawal) {
            // 1. Update status mesin jadi Ready
            Machine::where('id', $withdrawal->machine_id)
                ->update([
                    'status'      => 'Ready',
                    'customer_id' => null,
                ]);

            // 2. Soft-delete Deployment aktif yang terkait mesin ini
            Deployment::where('machine_id', $withdrawal->machine_id)
                ->whereNull('deleted_at')
                ->each(fn($d) => $d->delete());
        });

        static::deleted(function ($withdrawal) {
            // Rollback: kembalikan status mesin jadi Rented
            Machine::where('id', $withdrawal->machine_id)
                ->update(['status' => 'Rented']);

            // Restore Deployment yang di-soft-delete
            Deployment::withTrashed()
                ->where('machine_id', $withdrawal->machine_id)
                ->each(fn($d) => $d->restore());
        });
    }
}
