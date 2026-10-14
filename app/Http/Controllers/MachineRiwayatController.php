<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\MachineEvent;

class MachineRiwayatController extends Controller
{
    public function show(Machine $machine)
    {
        $events = MachineEvent::with(['customer', 'technician', 'sparepart'])
            ->where('machine_id', $machine->id)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        // Siklus: bertambah setiap kali mesin dikirim ke customer
        $siklus = 0;
        foreach ($events as $e) {
            if ($e->tipe === 'kirim_customer') {
                $siklus++;
            }
            $e->siklus = $siklus;
        }

        // Penanda tanggal kembar: tiap tanggal yang punya >1 event dapat nomor warna
        $kembar = $events
            ->groupBy(fn($e) => $e->tanggal->toDateString())
            ->filter(fn($g) => $g->count() > 1);

        $urutKembar = $kembar->keys()->values();

        foreach ($events as $e) {
            $k = $e->tanggal->toDateString();
            $e->grup_tgl = $kembar->has($k) ? $urutKembar->search($k) : null;
            $e->jml_tgl  = $kembar->has($k) ? $kembar[$k]->count() : 1;
        }

        // Service dengan tanggal dan isi persis sama = kemungkinan input dobel
        $dobel = $events
            ->where('tipe', 'service')
            ->groupBy(fn($e) => $e->tanggal->toDateString() . '|' . ($e->keterangan ?? '') . '|' . ($e->counter ?? ''))
            ->filter(fn($g) => $g->count() > 1);

        foreach ($dobel as $grup) {
            foreach ($grup as $e) {
                $e->dobel = true;
            }
        }

        $partDiganti = $events
            ->whereNotNull('sparepart_id')
            ->groupBy('sparepart_id')
            ->map(fn($g) => (object) [
                'nama'  => $g->first()->sparepart->nama_sparepart ?? '-',
                'total' => $g->sum('jumlah'),
            ])
            ->sortByDesc('total')
            ->values();

        return view('cetak.riwayat-mesin', [
            'machine'      => $machine->load('customer'),
            'events'       => $events,
            'partDiganti'  => $partDiganti,
            'totalService' => $events->where('tipe', 'service')->count(),
            'statusAkhir'  => $events->last()?->tipe_label ?? '-',
            'tanggalAwal'  => $events->first()?->tanggal,
            'jumlahKembar' => $kembar->count(),
            'jumlahDobel'  => $dobel->count(),
        ]);
    }
}