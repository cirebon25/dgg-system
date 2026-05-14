<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceLogSparepart extends Model
{
    use HasFactory;
    protected $guarded = [];

protected static function booted()
    {
        static::created(function ($item) {
            // 1. Ambil data induk (ServiceLog) beserta jalur mesin, deployment, dan customernya
            $serviceLog = \App\Models\ServiceLog::with(['machine.deployment.customer'])->find($item->service_log_id);

            if ($serviceLog) {
                $techId = $serviceLog->technician_id;

                if ($techId) {
                    // Cari atau buat Tas Teknisi
                    $techStock = \App\Models\TechnicianStock::where('technician_id', $techId)
                        ->where('sparepart_id', $item->sparepart_id)
                        ->first();

                    if ($techStock) {
                        $techStock->decrement('jumlah', $item->jumlah);
                    } else {
                        $techStock = \App\Models\TechnicianStock::create([
                            'technician_id' => $techId,
                            'sparepart_id' => $item->sparepart_id,
                            'jumlah' => -$item->jumlah,
                        ]);
                    }

                    // 2. JALUR PRESISI AMBIL DATA SESUAI RESOURCE AKANG
                    $machineObj = $serviceLog->machine;
                    
                    // Ambil serial_number dari mesin
                    $noSeri = $machineObj ? $machineObj->serial_number : '-';
                    
                    // Ambil nama_customer lewat jalur Machine -> Deployment -> Customer
                    $customerName = $machineObj?->deployment?->customer?->nama_customer ?? 'Unknown Customer';

                    // 3. CATAT KE HISTORI
                    \App\Models\TechnicianStockHistory::create([
                        'technician_id' => $techId,
                        'sparepart_id' => $item->sparepart_id,
                        'keluar' => $item->jumlah,
                        'saldo_akhir' => $techStock->jumlah,
                        'keterangan' => "Servis: {$customerName} (No Seri: {$noSeri})",
                    ]);
                }
            }
        });
    }

    public function serviceLog() { return $this->belongsTo(ServiceLog::class, 'service_log_id'); }
    public function sparepart() { return $this->belongsTo(Sparepart::class); }
}