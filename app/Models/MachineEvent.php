<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MachineEvent extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public const TIPE = [
        'masuk_stok'     => 'Masuk Stok',
        'kirim_customer' => 'Kirim ke Customer',
        'service'        => 'Service / Kerusakan',
        'ganti_part'     => 'Ganti Part',
        'pindah'         => 'Pindah Customer/Lokasi',
        'kembali_ws'     => 'Kembali ke WS',
        'perbaikan_ws'   => 'Perbaikan di WS',
        'afkir'          => 'Afkir',
        'catatan'        => 'Catatan',
    ];

    public const WARNA = [
        'masuk_stok'     => 'gray',
        'kirim_customer' => 'info',
        'service'        => 'warning',
        'ganti_part'     => 'danger',
        'pindah'         => 'info',
        'kembali_ws'     => 'success',
        'perbaikan_ws'   => 'success',
        'afkir'          => 'gray',
        'catatan'        => 'gray',
    ];

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

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function getTipeLabelAttribute(): string
    {
        return self::TIPE[$this->tipe] ?? $this->tipe;
    }

    // Teknisi utama + teknisi ke-2 (jika ada)
    public function getTeknisiNamaAttribute(): ?string
    {
        $nama = collect([
            $this->technician?->nama_technician,
            $this->teknisi_2,
        ])->filter()->implode(' / ');

        return $nama ?: null;
    }

    // ===== Sinkronisasi otomatis dari Service Log =====

    protected static function dataDariServiceLog(ServiceLog $log): array
    {
        $ket = collect([
            filled($log->kerusakan) ? 'Kerusakan: ' . $log->kerusakan : null,
            filled($log->perbaikan) ? 'Perbaikan: ' . $log->perbaikan : null,
        ])->filter()->implode(' → ');

        return [
            'machine_id'     => $log->machine_id,
            'tanggal'        => Carbon::parse($log->tanggal ?? $log->created_at ?? now())->toDateString(),
            'tipe_kunjungan' => filled($log->tipe_kunjungan) ? $log->tipe_kunjungan : null,
            'customer_id'    => $log->customer_id ?: Machine::find($log->machine_id)?->customer_id,
            'technician_id'  => $log->technician_id,
            'teknisi_2'      => filled($log->nama_teknisi_2) ? $log->nama_teknisi_2 : null,
            'counter'        => $log->counter_bw ?: null,
            'keterangan'     => $ket ?: null,
        ];
    }

    public static function syncServiceLog(ServiceLog $log): void
    {
        if (!$log->machine_id) return;

        $d = self::dataDariServiceLog($log);

        // Event Service / Kerusakan
        self::updateOrCreate(
            ['service_log_id' => $log->id, 'tipe' => 'service'],
            $d
        );

        // Samakan data event ganti part milik Service Log ini
        self::where('service_log_id', $log->id)
            ->where('tipe', 'ganti_part')
            ->update([
                'machine_id'     => $d['machine_id'],
                'tanggal'        => $d['tanggal'],
                'tipe_kunjungan' => $d['tipe_kunjungan'],
                'customer_id'    => $d['customer_id'],
                'technician_id'  => $d['technician_id'],
                'teknisi_2'      => $d['teknisi_2'],
            ]);

        // Pola lama: part tersimpan langsung di kolom service_logs.sparepart_id
        $punyaPivot = ServiceLogSparepart::where('service_log_id', $log->id)->exists();

        if ($log->sparepart_id && !$punyaPivot) {
            $sparepart = Sparepart::find($log->sparepart_id);

            self::updateOrCreate(
                [
                    'service_log_id'           => $log->id,
                    'tipe'                     => 'ganti_part',
                    'service_log_sparepart_id' => null,
                ],
                $d + [
                    'sparepart_id' => $log->sparepart_id,
                    'jumlah'       => $log->jumlah_sparepart ?: 1,
                    'keterangan'   => 'Ganti part: ' . ($sparepart?->nama_sparepart ?? '-'),
                    'counter'      => null,
                ]
            );
        } else {
            self::where('service_log_id', $log->id)
                ->where('tipe', 'ganti_part')
                ->whereNull('service_log_sparepart_id')
                ->delete();
        }
    }

    public static function syncPart(ServiceLogSparepart $item): void
    {
        $log = $item->serviceLog;
        if (!$log || !$log->machine_id) return;

        $sparepart = Sparepart::find($item->sparepart_id);
        $d = self::dataDariServiceLog($log);

        // Part sudah tercatat lewat pivot, hapus event pola lama agar tidak dobel
        self::where('service_log_id', $log->id)
            ->where('tipe', 'ganti_part')
            ->whereNull('service_log_sparepart_id')
            ->delete();

        self::updateOrCreate(
            ['service_log_sparepart_id' => $item->id],
            $d + [
                'tipe'           => 'ganti_part',
                'sparepart_id'   => $item->sparepart_id,
                'jumlah'         => $item->jumlah,
                'service_log_id' => $log->id,
                'keterangan'     => 'Ganti part: ' . ($sparepart?->nama_sparepart ?? '-'),
                'counter'        => null,
            ]
        );
    }
}