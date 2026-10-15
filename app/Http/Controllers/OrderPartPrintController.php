<?php

namespace App\Http\Controllers;

use App\Models\OrderPart;
use App\Models\Sparepart;

class OrderPartPrintController extends Controller
{
    // Nama parameter ($orderPart) harus sama dengan {orderPart} di route
    public function show(OrderPart $orderPart)
    {
        $orderPart->load('items');

        // Ambil sparepart langsung berdasarkan sparepart_id,
        // jadi tidak tergantung ada/tidaknya relasi sparepart() di model item
        $spareparts = Sparepart::whereIn('id', $orderPart->items->pluck('sparepart_id'))
            ->get()
            ->keyBy('id');

        return view('print.order-part', [
            'order'      => $orderPart,
            'spareparts' => $spareparts,
        ]);
    }
}