<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ServiceLogSparepart;
use App\Models\Sparepart;
use Illuminate\Http\Request;

class PartKeluarController extends Controller
{
    public function cetak(Request $request)
    {
        $data = $request->validate([
            'month'          => 'required|in:01,02,03,04,05,06,07,08,09,10,11,12',
            'year'           => 'required|integer|min:2024',
            'customer_id'    => 'nullable|exists:customers,id',
            'sparepart_ids'  => 'nullable|array',
            'sparepart_ids.*' => 'exists:spareparts,id',
        ]);

        $logs = ServiceLogSparepart::query()
            ->with(['sparepart', 'serviceLog.machine.customer'])
            ->whereHas('serviceLog', function ($q) use ($data) {
                // GANTI 'tanggal' dengan nama kolom tanggal di tabel service_logs
                $q->whereYear('tanggal', $data['year'])
                    ->whereMonth('tanggal', $data['month']);
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

        // Customer -> Part -> Mesin (No Seri)
        $perCustomer = $logs
            ->groupBy(fn($l) => $l->serviceLog?->machine?->customer?->id ?? 0)
            ->map(function ($group) {
                $customer = $group->first()->serviceLog?->machine?->customer;

                $parts = $group->groupBy('sparepart_id')
                    ->map(function ($g) {
                        $machines = $g
                            ->groupBy(fn($l) => $l->serviceLog?->machine_id ?? 0)
                            ->map(fn($m) => (object) [
                                'sn'    => $m->first()->serviceLog?->machine?->serial_number ?? '-',
                                'kali'  => $m->count(),
                                'total' => $m->sum('jumlah'),
                            ])
                            ->sortBy('sn')
                            ->values();

                        return (object) [
                            'sparepart' => $g->first()->sparepart,
                            'machines'  => $machines,
                            'total'     => $g->sum('jumlah'),
                        ];
                    })
                    ->sortBy(fn($p) => $p->sparepart->nama_sparepart ?? '')
                    ->values();

                return (object) [
                    'nama'  => $customer->nama_customer ?? 'Tanpa Customer',
                    'parts' => $parts,
                    'total' => $parts->sum('total'),
                ];
            })
            ->sortBy('nama')
            ->values();

        $namaBulan = [
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
            'perCustomer'    => $perCustomer,
            'bulan'          => $namaBulan,
            'tahun'          => $data['year'],
            'grand'          => $perCustomer->sum('total'),
            'filterCustomer' => isset($data['customer_id'])
                ? Customer::find($data['customer_id'])?->nama_customer
                : null,
            'filterParts'    => !empty($data['sparepart_ids'])
                ? Sparepart::whereIn('id', $data['sparepart_ids'])->pluck('nama_sparepart')->implode(', ')
                : null,
        ]);
    }
}