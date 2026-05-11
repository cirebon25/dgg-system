<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceLogSparepart extends Model
{
    use HasFactory;

    // Pastikan ada titik koma (;) di akhir baris ini
    protected $fillable = ['service_log_id', 'sparepart_id', 'jumlah'];

    protected static function booted()
    {
        // 1. SAAT INPUT BARANG BARU
        static::created(function ($item) {
            $sparepart = $item->sparepart;
            if ($sparepart) {
                // Update Stok (Logika Boss)
                $sparepart->saldo_keluar += $item->jumlah;
                $sparepart->save();

                // Update Jembatan Kesehatan Mesin (Logika Baru)
                $serviceLog = $item->serviceLog;
                if ($serviceLog) {
                    \App\Models\MachinePartHealth::updateOrInsert(
                        [
                            'machine_id' => $serviceLog->machine_id,
                            'sparepart_id' => $item->sparepart_id,
                        ],
                        [
                            'last_replaced_counter' => $serviceLog->counter_akhir ?? 0,
                            'last_replaced_at' => $serviceLog->tanggal,
                            'current_usage' => 0, // Reset karena part baru dipasang
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        });

        // 2. SAAT JUMLAH BARANG DIEDIT
        static::updated(function ($item) {
            $sparepart = $item->sparepart;
            if ($sparepart) {
                $selisih = $item->jumlah - $item->getOriginal('jumlah');
                $sparepart->saldo_keluar += $selisih;
                $sparepart->save();
            }
        });

        // 3. SAAT DATA SERVIS DIHAPUS
        static::deleted(function ($item) {
            $sparepart = $item->sparepart;
            if ($sparepart) {
                $sparepart->saldo_keluar -= $item->jumlah;
                $sparepart->save();

                // Opsional: Jika data dihapus, mungkin Boss ingin reset health-nya juga?
                // Biasanya dibiarkan saja agar tetap ada record penggantian terakhir.
            }
        });
    }

    public function serviceLog()
    {
        return $this->belongsTo(ServiceLog::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}
