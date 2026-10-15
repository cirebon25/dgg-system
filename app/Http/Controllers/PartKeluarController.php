<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PartUsageHeader;
use App\Models\ServiceLogSparepart;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PartKeluarController extends Controller
{
    public function cetak(Request $request)
    {
        $data = $request->validate([
            'month'           => 'required|in:all,01,02,03,04,05,06,07,08,09,10,11,12',
            'year'            => 'required|integer|min:2024',
            'customer_id'     => 'nullable|exists:customers,id',
            'sparepart_ids'   => 'nullable|array',
            'sparepart_ids.*' => 'exists:spareparts,id',
        ]);

        $setahun = $data['month'] === 'all';

        // ===== SUMBER 1: SERVICE LOG =====
        $logs = ServiceLogSparepart::query()
            ->with(['sparepart', 'serviceLog.machine.customer'])
            ->whereHas('serviceLog', function ($q) use ($data, $setahun) {
                $q->whereYear('tanggal', $data['year']);

                if (!$setahun) {
                    $q->whereMonth('tanggal', $data['month']);
                }
            })
            ->when($data['customer_id'] ?? null, function ($q, $id) {
                $q->whereHas('serviceLog.machine', fn($m) => $m->where('customer_id', $id));
            })
            ->when(
                !empty($data['sparepart_ids']),
                fn($q) => $q->whereIn('sparepart_id', $data['sparepart_ids'])
            )
            ->get();

        $rows = $logs->map(fn($l) => (object) [
            'sparepart_id' => $l->sparepart_id,
            'sparepart'    => $l->sparepart,
            'tanggal'      => $l->serviceLog?->tanggal ? Carbon::parse($l->serviceLog->tanggal) : null,
            'customer'     => $l->serviceLog?->machine?->customer?->nama_customer ?? 'Tanpa Customer',
            'sn'           => $l->serviceLog?->machine?->serial_number ?? '-',
            'qty'          => $l->jumlah,
        ]);

        // ===== SUMBER 2: PEMAKAIAN PART WS (tas + gudang) =====
        // Kalau hanya mau yang dari gudang, tambahkan: ->where('sumber_stok', 'gudang')
        $wsRows = PartUsageHeader::query()
            ->with(['items.sparepart', 'items.machine.customer'])
            ->whereYear('tanggal', $data['year'])
            ->when(!$setahun, fn($q) => $q->whereMonth('tanggal', $data['month']))
            ->get()
            ->flatMap(function ($header) {
                return $header->items->map(fn($item) => (object) [
                    'sparepart_id' => $item->sparepart_id,
                    'sparepart'    => $item->sparepart,
                    'tanggal'      => Carbon::parse($header->tanggal),
                    'customer'     => $item->machine?->customer?->nama_customer ?? 'Tanpa Customer',
                    'customer_id'  => $item->machine?->customer?->getKey(),
                    'sn'           => $item->machine?->serial_number ?? '-',
                    'qty'          => $item->jumlah,
                ]);
            })
            ->filter(fn($r) => $r->sparepart)
            ->when(
                !empty($data['sparepart_ids']),
                fn($c) => $c->filter(fn($r) => in_array($r->sparepart_id, $data['sparepart_ids']))
            )
            ->when(
                $data['customer_id'] ?? null,
                fn($c, $id) => $c->filter(fn($r) => (int) $r->customer_id === (int) $id)
            );

        $rows = $rows->concat($wsRows);

        // ===== Per part -> ringkasan per customer + rincian transaksi =====
        $perPart = $rows
            ->groupBy('sparepart_id')
            ->map(function ($g) {
                $transaksi = $g->map(fn($r) => (object) [
                    'tanggal'  => $r->tanggal,
                    'customer' => $r->customer,
                    'sn'       => $r->sn,
                    'qty'      => $r->qty,
                ])->sortBy('tanggal')->values();

                $ringkas = $transaksi
                    ->groupBy('customer')
                    ->map(fn($t, $nama) => (object) [
                        'customer'  => $nama,
                        'total'     => $t->sum('qty'),
                        'transaksi' => $t->count(),
                    ])
                    ->sortByDesc('total')
                    ->values();

                return (object) [
                    'sparepart' => $g->first()->sparepart,
                    'transaksi' => $transaksi,
                    'ringkas'   => $ringkas,
                    'total'     => $transaksi->sum('qty'),
                ];
            })
            ->sortBy(fn($p) => $p->sparepart->nama_sparepart ?? '')
            ->values();

        $namaBulan = $setahun
            ? 'Januari - Desember'
            : [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember',
            ][$data['month']];

        return view('cetak.part-keluar', [
            'perPart'        => $perPart,
            'bulan'          => $namaBulan,
            'tahun'          => $data['year'],
            'filterCustomer' => isset($data['customer_id'])
                ? Customer::find($data['customer_id'])?->nama_customer
                : null,
        ]);
    }
}