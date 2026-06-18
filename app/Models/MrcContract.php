<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MrcContract extends Model
{
    protected $fillable = [
        'machine_id', 'customer_id', 'harga_sewa',
        'free_bw', 'harga_bw', 'free_color', 'harga_color',
        'tanggal_mulai', 'tanggal_selesai', 'aktif', 'keterangan',
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

    // Hitung tagihan berdasarkan usage
    public function hitungTagihan(int $usageBw, int $usageColor): array
    {
        $hargaSewa   = (int) $this->harga_sewa;

        $kelebihaBw    = max(0, $usageBw    - $this->free_bw);
        $kelebihaColor = max(0, $usageColor - $this->free_color);

        $biayaBw    = $kelebihaBw    * (int) $this->harga_bw;
        $biayaColor = $kelebihaColor * (int) $this->harga_color;
        $total      = $hargaSewa + $biayaBw + $biayaColor;

        return [
            'harga_sewa'      => $hargaSewa,
            'usage_bw'        => $usageBw,
            'free_bw'         => $this->free_bw,
            'kelebihan_bw'    => $kelebihaBw,
            'harga_bw'        => (int) $this->harga_bw,
            'biaya_bw'        => $biayaBw,
            'usage_color'     => $usageColor,
            'free_color'      => $this->free_color,
            'kelebihan_color' => $kelebihaColor,
            'harga_color'     => (int) $this->harga_color,
            'biaya_color'     => $biayaColor,
            'total'           => $total,
        ];
    }
}