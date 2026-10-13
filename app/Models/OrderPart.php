<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPart extends Model
{
    protected $fillable = ['no_order', 'tanggal', 'status', 'catatan', 'created_by'];

    protected $casts = ['tanggal' => 'date'];

    public const STATUS_AKTIF = ['draft', 'diajukan', 'dipesan'];

    public function items()
    {
        return $this->hasMany(OrderPartItem::class);
    }

    public static function generateNumber(): string
    {
        $prefix = 'OP-' . date('Ym') . '-';
        $count  = static::where('no_order', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Buat draft dari sparepart yang stoknya <= stok_minimum,
     * kecuali yang sudah ada di order yang masih aktif.
     */
    public static function createDraftFromLowStock(): ?self
    {
        $sudahDiorder = OrderPartItem::whereHas('orderPart', fn($q) => $q->whereIn('status', self::STATUS_AKTIF))
            ->pluck('sparepart_id');

        $low = Sparepart::stokMenipis()->whereNotIn('id', $sudahDiorder)->get();

        if ($low->isEmpty()) {
            return null;
        }

        $order = static::create([
            'no_order'   => static::generateNumber(),
            'tanggal'    => now(),
            'status'     => 'draft',
            'catatan'    => 'Dibuat otomatis dari stok menipis',
            'created_by' => auth()->id(),
        ]);

        foreach ($low as $s) {
            $order->items()->create([
                'sparepart_id'  => $s->id,
                'stok_saat_ini' => $s->stok,
                // usulan: isi sampai 2x stok minimum (bisa diedit)
                'jumlah_order'  => max(($s->stok_minimum * 2) - $s->stok, 1),
            ]);
        }

        return $order;
    }
}
