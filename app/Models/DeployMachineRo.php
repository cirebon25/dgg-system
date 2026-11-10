<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeployMachineRo extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        // Saat deploy dibuat / diubah
        static::saved(function (DeployMachineRo $deploy) {
            // Kalau mesin diganti saat edit, lepas mesin lama
            if ($deploy->wasChanged('machine_air_ro_id') && $deploy->getOriginal('machine_air_ro_id')) {
                MachineAirRo::whereKey($deploy->getOriginal('machine_air_ro_id'))
                    ->update(['status' => 'Ready', 'customer_ro_id' => null]);
            }

            $mesin = $deploy->machineAirRo;
            if (! $mesin) {
                return;
            }

            if ($deploy->status_deploy === 'Terpasang') {
                $mesin->update([
                    'status'         => 'Rental',
                    'customer_ro_id' => $deploy->customer_ro_id,
                ]);
            } else { // Ditarik
                $mesin->update([
                    'status'         => 'Ready',
                    'customer_ro_id' => null,
                ]);
            }
        });

        // Saat data deploy dihapus, mesin kembali Ready
        static::deleted(function (DeployMachineRo $deploy) {
            if ($deploy->status_deploy === 'Terpasang') {
                MachineAirRo::whereKey($deploy->machine_air_ro_id)
                    ->update(['status' => 'Ready', 'customer_ro_id' => null]);
            }
        });
    }

    public function machineAirRo()
    {
        return $this->belongsTo(MachineAirRo::class);
    }

    public function customerRo()
    {
        return $this->belongsTo(CustomerRo::class);
    }
}
