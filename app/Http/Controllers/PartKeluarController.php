<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ServiceLogSparepart;
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

        $logs = ServiceLogSparepart::query()
            ->with(['sparepart', 'serviceLog.machine.customer'])
            ->whereHas('serviceLog', function ($q) use ($data, $setahun) {
                // GANTI 'tanggal' dengan nama kolom tanggal di tabel service_logs
                $q->whereYear('tanggal', $data['year']);

                if (!$setahun) {
                    $q->whereMonth('tanggal', $data['month']);
                }
            })
            ->when($data['customer_id'] ?? null, function ($q, $id) {
                // GANTI 'customer_id' jika nama kolom di tabel machines berbeda
                $q->whereHas('serviceLog.machine', fn($m) => $m->where('customer_id', $id));
            })
            ->when(
                !empty($data['sparepart_ids']),
                fn($q) => $q->whereIn('sparepart_id', $data['sparepart_ids'])
            )
            ->get();

        // Per part -> ringkasan per customer + rincian transaksi
        $perPart = $logs
            ->groupBy('sparepart_id')
            ->map(function ($g) {
                $transaksi = $g->map(fn($l) => (object) [
                    'tanggal'  => $l->serviceLog?->tanggal,
                    'customer' => $l->serviceLog?->machine?->customer?->nama_customer ?? 'Tanpa Customer',
                    'sn'       => $l->serviceLog?->machine?->serial_number ?? '-',
                    'qty'      => $l->jumlah,
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