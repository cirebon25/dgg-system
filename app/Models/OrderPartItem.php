<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPartItem extends Model
{
    protected $fillable = ['order_part_id', 'sparepart_id', 'stok_saat_ini', 'jumlah_order', 'keterangan'];

    public function orderPart()
    {
        return $this->belongsTo(OrderPart::class);
    }

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}
