<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UppercaseAttributes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deployment extends Model
{
    use HasFactory, SoftDeletes, UppercaseAttributes;

    protected $fillable = [
        'no_kontrak',
        'customer_id',
        'machine_id',
        'technician_id',
        'tanggal_instal',
        'keterangan',
        'counter_bw',
        'counter_color',
        'volt',
    ];

    protected $casts = [
        'tanggal_instal' => 'date',
    ];

    /**
     * Logika Otomatis: Sinkronisasi Status Mesin, Lokasi, Teknisi,
     * Auto Create Service Log & Auto Create Kontrak MRC
     */
    protected static function booted()
    {
        // 1. SAAT DEPLOYMENT BARU DIBUAT (CREATE)
        static::created(function ($deployment) {
            // A. UPDATE DATA MESIN
            if ($deployment->machine) {
                $deployment->machine->update([
                    'status'        => 'Rented',
                    'customer_id'   => $deployment->customer_id,
                    'technician_id' => $deployment->technician_id,
                ]);
            }

            // B. OTOMATIS MASUK KE SERVICE LOG (RN - INSTALASI AWAL)
            \App\Models\ServiceLog::create([
                'machine_id'      => $deployment->machine_id,
                'customer_id'     => $deployment->customer_id,
                'technician_id'   => $deployment->technician_id,
                'tanggal'         => $deployment->tanggal_instal,
                'tipe_kunjungan'  => 'RN',
                'counter_bw'      => $deployment->counter_bw ?? 0,
                'counter_color'   => $deployment->counter_color ?? 0,
                'usage_bw'        => 0,
                'usage_color'     => 0,
                'perbaikan'       => "INSTALASI UNIT BARU (RN) - NO KONTRAK: " . $deployment->no_kontrak,
                'kerusakan'       => 'PEMASANGAN AWAL',
            ]);

            // C. OTOMATIS BUAT KONTRAK MRC (DRAFT, admin lengkapi harga & kuota)
            MrcContract::buatDraft(
                $deployment->machine_id,
                $deployment->customer_id,
                $deployment->tanggal_instal,
                'AUTO DARI DEPLOY - NO KONTRAK: ' . $deployment->no_kontrak
            );
        });

        // 2. SAAT DATA DEPLOYMENT DIUBAH (UPDATE)
        static::updated(function ($deployment) {
            // Jika mesin diganti (Tukar Mesin lewat form Deploy)
            if ($deployment->wasChanged('machine_id')) {
                $oldMachineId = $deployment->getOriginal('machine_id');

                // Mesin LAMA balik ke Gudang + kontraknya dinonaktifkan
                if ($oldMachineId) {
                    Machine::find($oldMachineId)?->update([
                        'status'        => 'Ready',
                        'customer_id'   => null,
                        'technician_id' => null,
                    ]);

                    MrcContract::nonaktifkanUntukMesin($oldMachineId);
                }

                // Mesin BARU dikirim ke Customer + dibuatkan kontrak draft
                $deployment->machine?->update([
                    'status'        => 'Rented',
                    'customer_id'   => $deployment->customer_id,
                    'technician_id' => $deployment->technician_id,
                ]);

                MrcContract::buatDraft(
                    $deployment->machine_id,
                    $deployment->customer_id,
                    now(),
                    'AUTO DARI TUKAR MESIN - NO KONTRAK: ' . $deployment->no_kontrak
                );
            }

            // Jika hanya Teknisi atau Customer yang berubah di form Deploy
            if ($deployment->wasChanged(['technician_id', 'customer_id'])) {
                $deployment->machine?->update([
                    'customer_id'   => $deployment->customer_id,
                    'technician_id' => $deployment->technician_id,
                ]);

                // Customer berubah -> samakan customer di kontrak aktif
                if ($deployment->wasChanged('customer_id')) {
                    MrcContract::where('machine_id', $deployment->machine_id)
                        ->where('aktif', true)
                        ->update(['customer_id' => $deployment->customer_id]);
                }
            }
        });

        // 3. SAAT DEPLOYMENT DIHAPUS (Kembalikan Unit ke Gudang)
        static::deleted(function ($deployment) {
            if ($deployment->machine) {
                $deployment->machine->update([
                    'status'        => 'Ready',
                    'customer_id'   => null,
                    'technician_id' => null,
                ]);

                MrcContract::nonaktifkanUntukMesin($deployment->machine_id);
            }
        });
    }

    /**
     * RELASI
     */
    public function spareparts(): BelongsToMany
    {
        return $this->belongsToMany(Sparepart::class, 'deployment_sparepart')
            ->withPivot('jumlah')
            ->withTimestamps();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public function deploymentSpareparts()
    {
        return $this->hasMany(DeploymentSparepart::class, 'deployment_id');
    }
}
