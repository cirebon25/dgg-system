<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UppercaseAttributes;

class MachineReturn extends Model
{
    use HasFactory, SoftDeletes, UppercaseAttributes;

    protected $fillable = [
        'machine_id',
        'dari_lokasi',
        'ke_lokasi',
        'tanggal_retur',
        'kondisi_saat_retur',
        'keterangan_kerusakan',
        'catatan_tambahan',
        'dikirim_oleh',
        'status_retur',
        'tanggal_selesai_servis',
        'tanggal_kembali',
        'hasil_servis',
    ];

    protected $casts = [
        'tanggal_retur'          => 'date',
        'tanggal_selesai_servis' => 'date',
        'tanggal_kembali'        => 'date',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    protected static function booted(): void
    {
        // Saat retur baru dibuat -> status mesin jadi Returned
        static::created(function (self $return) {
            Machine::where('id', $return->machine_id)
                ->update(['status' => 'Returned']);
        });

        // Saat status_retur diupdate -> sinkron status mesin
        static::updated(function (self $return) {
            if ($return->wasChanged('status_retur')) {
                $newStatus = match ($return->status_retur) {
                    'Kembali ke Cirebon' => 'Ready',
                    default              => 'Returned',
                };
                Machine::where('id', $return->machine_id)
                    ->update(['status' => $newStatus]);
            }
        });

        // Saat di-delete -> rollback ke Ready
        static::deleted(function (self $return) {
            Machine::where('id', $return->machine_id)
                ->update(['status' => 'Ready']);
        });
    }
public function machineReturns()
{
    return $this->hasMany(MachineReturn::class);
}
    }
