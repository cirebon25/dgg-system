<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Deployment;
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

        $setahun      = $data['month'] === 'all';
        $year         = $data['year'];
        $month        = $data['month'];
        $customerId   = $data['customer_id'] ?? null;
        $sparepartIds = $data['sparepart_ids'] ?? [];

        // ===== SUMBER 1: SERVICE LOG =====
        $logs = ServiceLogSparepart::query()
            ->with(['sparepart', 'serviceLog.machine.customer'])
            ->whereHas('serviceLog', function ($q) use ($year, $month, $setahun) {
                $q->whereYear('tanggal', $year);
                if (!$setahun) {
                    $q->whereMonth('tanggal', $month);
                }
            })
            ->when($customerId, function ($q, $id) {
                $q->whereHas('serviceLog.machine', fn($m) => $m->where('customer_id', $id));
            })
            ->when(!empty($sparepartIds), function ($q) use ($sparepartIds) {
                $q->whereIn('sparepart_id', $sparepartIds);
            })
            ->get();

        $rows = $logs->map(fn($l) => (object) [
            'sparepart_id' => $l->sparepart_id,
            'sparepart'    => $l->sparepart,
            'tanggal'      => $l->serviceLog?->tanggal ? Carbon::parse($l->serviceLog->tanggal) : null,
            'customer'     => $l->serviceLog?->machine?->customer?->nama_customer ?? 'Tanpa Customer',
            'customer_id'  => $l->serviceLog?->machine?->customer?->getKey(),
            'sn'           => $l->serviceLog?->machine?->serial_number ?? '-',
            'qty'          => $l->jumlah,
        ]);

        // ===== SUMBER 2: PEMAKAIAN PART WORKSHOP (PartUsageHeader) =====
        $wsHeaders = PartUsageHeader::query()
            ->with(['items.sparepart', 'items.machine.customer'])
            ->whereYear('tanggal', $year)
            ->when(!$setahun, fn($q) => $q->whereMonth('tanggal', $month))
            ->when($customerId, function ($q, $id) {
                $q->whereHas('items.machine', fn($m) => $m->where('customer_id', $id));
            })
            ->when(!empty($sparepartIds), function ($q) use ($sparepartIds) {
                $q->whereHas('items', fn($i) => $i->whereIn('sparepart_id', $sparepartIds));
            })
            ->get();

        $wsRows = $wsHeaders->flatMap(function ($header) use ($sparepartIds, $customerId) {
            return $header->items
                ->filter(function ($item) use ($sparepartIds, $customerId) {
                    if (!$item->sparepart) return false;
                    if (!empty($sparepartIds) && !in_array($item->sparepart_id, $sparepartIds)) return false;
                    if ($customerId && (int) optional($item->machine?->customer)->getKey() !== (int) $customerId) return false;
                    return true;
                })
                ->map(fn($item) => (object) [
                    'sparepart_id' => $item->sparepart_id,
                    'sparepart'    => $item->sparepart,
                    'tanggal'      => Carbon::parse($header->tanggal),
                    'customer'     => $item->machine?->customer?->nama_customer ?? 'Tanpa Customer',
                    'customer_id'  => $item->machine?->customer?->getKey(),
                    'sn'           => $item->machine?->serial_number ?? '-',
                    'qty'          => $item->jumlah,
                ]);
        });

        $rows = $rows->concat($wsRows);

        // ===== SUMBER 3: DEPLOYMENT SPAREPARTS (Instalasi Unit Baru) =====
        $deployments = Deployment::query()
            ->with(['spareparts', 'customer', 'machine'])
            ->whereYear('tanggal_instal', $year)
            ->when(!$setahun, fn($q) => $q->whereMonth('tanggal_instal', $month))
            ->when($customerId, fn($q, $id) => $q->where('customer_id', $id))
            ->when(!empty($sparepartIds), function ($q) use ($sparepartIds) {
                $q->whereHas('spareparts', fn($s) => $s->whereIn('spareparts.id', $sparepartIds));
            })
            ->get();

        $deploymentRows = $deployments->flatMap(function ($deployment) use ($sparepartIds, $customerId) {
            return $deployment->spareparts
                ->filter(function ($sparepart) use ($sparepartIds, $customerId, $deployment) {
                    if (!empty($sparepartIds) && !in_array($sparepart->id, $sparepartIds)) return false;
                    if ($customerId && (int) $deployment->customer_id !== (int) $customerId) return false;
                    return true;
                })
                ->map(fn($sparepart) => (object) [
                    'sparepart_id' => $sparepart->id,
                    'sparepart'    => $sparepart,
                    'tanggal'      => $deployment->tanggal_instal ? Carbon::parse($deployment->tanggal_instal) : null,
                    'customer'     => $deployment->customer?->nama_customer ?? 'Tanpa Customer',
                    'customer_id'  => $deployment->customer_id,
                    'sn'           => $deployment->machine?->serial_number ?? '-',
                    'qty'          => $sparepart->pivot->jumlah,
                ]);
        });

        $rows = $rows->concat($deploymentRows);

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
            : Carbon::create()->month((int)$month)->translatedFormat('F');

        return view('cetak.part-keluar', [
            'perPart'        => $perPart,
            'bulan'          => $namaBulan,
            'tahun'          => $year,
            'filterCustomer' => $customerId ? Customer::find($customerId)?->nama_customer : null,
        ]);
    }
}
