<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MrcContract extends Model
{
    protected $fillable = [
        'machine_id',
        'customer_id',
        'harga_sewa',
        'free_bw',
        'harga_bw',
        'free_color',
        'harga_color',
        'tanggal_mulai',
        'tanggal_selesai',
        'aktif',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'aktif'           => 'boolean',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Pemakaian bulanan (meter reading) milik mesin kontrak ini.
     */
    public function meterReadings(): HasMany
    {
        return $this->hasMany(MeterReading::class, 'machine_id', 'machine_id');
    }

    /**
     * SCOPE: kontrak aktif yang harganya belum diisi (draft).
     */
    public function scopeBelumDiisi($query)
    {
        return $query->where('aktif', true)
            ->where(fn($q) => $q->whereNull('harga_sewa')->orWhere('harga_sewa', 0));
    }

    /**
     * SCOPE: kontrak aktif tapi mesinnya sudah tidak berstatus Rented (ditarik).
     */
    public function scopeMesinDitarik($query)
    {
        return $query->where('aktif', true)
            ->whereHas('machine', fn($q) => $q->where('status', '!=', 'Rented'));
    }

    /**
     * SCOPE: semua kontrak yang perlu perhatian admin (untuk badge sidebar).
     */
    public function scopePerluPerhatian($query)
    {
        return $query->where('aktif', true)->where(function ($q) {
            $q->whereNull('harga_sewa')
                ->orWhere('harga_sewa', 0)
                ->orWhereHas('machine', fn($m) => $m->where('status', '!=', 'Rented'));
        });
    }

    /**
     * SCOPE: kontrak yang berlaku pada tanggal tertentu.
     */
    public function scopeBerlakuPada($query, $tanggal)
    {
        return $query->whereDate('tanggal_mulai', '<=', $tanggal)
            ->where(function ($q) use ($tanggal) {
                $q->whereNull('tanggal_selesai')
                    ->orWhereDate('tanggal_selesai', '>=', $tanggal);
            });
    }

    public function isMesinDitarik(): bool
    {
        return $this->aktif && $this->machine && $this->machine->status !== 'Rented';
    }

    // Hitung tagihan berdasarkan usage
    public function hitungTagihan(int $usageBw, int $usageColor): array
    {
        $hargaSewa = (int) $this->harga_sewa;

        $kelebihaBw    = max(0, $usageBw    - (int) $this->free_bw);
        $kelebihaColor = max(0, $usageColor - (int) $this->free_color);

        $biayaBw    = $kelebihaBw    * (int) $this->harga_bw;
        $biayaColor = $kelebihaColor * (int) $this->harga_color;
        $total      = $hargaSewa + $biayaBw + $biayaColor;

        return [
            'harga_sewa'      => $hargaSewa,
            'usage_bw'        => $usageBw,
            'free_bw'         => (int) $this->free_bw,
            'kelebihan_bw'    => $kelebihaBw,
            'harga_bw'        => (int) $this->harga_bw,
            'biaya_bw'        => $biayaBw,
            'usage_color'     => $usageColor,
            'free_color'      => (int) $this->free_color,
            'kelebihan_color' => $kelebihaColor,
            'harga_color'     => (int) $this->harga_color,
            'biaya_color'     => $biayaColor,
            'total'           => $total,
        ];
    }

    /**
     * Buat kontrak draft untuk mesin (kalau belum ada kontrak aktif).
     * Admin tinggal melengkapi harga & kuota.
     */
    public static function buatDraft(int $machineId, ?int $customerId, $tanggalMulai = null, ?string $keterangan = null): self
    {
        return static::firstOrCreate(
            ['machine_id' => $machineId, 'aktif' => true],
            [
                'customer_id'   => $customerId,
                'tanggal_mulai' => $tanggalMulai ?? now(),
                'keterangan'    => $keterangan,
                'harga_sewa'    => 0,
                'free_bw'       => 0,
                'harga_bw'      => 0,
                'free_color'    => 0,
                'harga_color'   => 0,
            ]
        );
    }

    /**
     * Nonaktifkan kontrak aktif milik mesin (mesin ditarik / ditukar).
     * Mengembalikan jumlah kontrak yang dinonaktifkan.
     */
    public static function nonaktifkanUntukMesin(int $machineId): int
    {
        return static::where('machine_id', $machineId)
            ->where('aktif', true)
            ->update([
                'aktif'           => false,
                'tanggal_selesai' => now(),
            ]);
    }
}
