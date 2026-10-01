<?php

namespace App\Http\Controllers;

use App\Exports\StokGudangExport;
use App\Models\Deployment;
use App\Models\Machine;
use App\Models\MachineAirRo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PrintMesinController extends Controller
{
    private const DEPO = 'Cirebon';

    // Route: /cetak-alokasi-customer -> cetak.alokasi-customer
    public function alokasiCustomer()
    {
        $data = Deployment::with(['customer', 'machine'])->get();

        return view('print.alokasi-customer', compact('data'));
    }

    // Route: /mesin/{id}/cetak-qr -> mesin.cetak-qr
    public function cetakQr($id)
    {
        $machine = Machine::findOrFail($id);

        $qrCode = QrCode::size(100)
            ->margin(0)
            ->generate(route('mesin.histori', ['id' => $machine->id]));

        return view('print.qr-mesin', compact('machine', 'qrCode'));
    }

    // Route: /mesin/{id}/histori -> mesin.histori
    public function histori($id)
    {
        $machine = Machine::findOrFail($id);

        $deployments = DB::table('deployments')
            ->join('customers', 'deployments.customer_id', '=', 'customers.id')
            ->leftJoin('technicians', 'deployments.technician_id', '=', 'technicians.id')
            ->where('deployments.machine_id', $id)
            ->select(
                'deployments.*',
                'customers.nama_customer',
                'customers.kota',
                'technicians.nama_technician'
            )
            ->orderByDesc('deployments.created_at')
            ->get();

        $serviceLogs = collect();
        if (Schema::hasTable('service_logs')) {
            $serviceLogs = DB::table('service_logs')
                ->leftJoin('technicians', 'service_logs.technician_id', '=', 'technicians.id')
                ->where('service_logs.machine_id', $id)
                ->select('service_logs.*', 'technicians.nama_technician')
                ->orderByDesc('service_logs.created_at')
                ->get();
        }

        return view('print.histori-mesin', [
            'machine'     => $machine,
            'deployments' => $deployments,
            'serviceLogs' => $serviceLogs,
            'autoPrint'   => false, // halaman ini dibuka di HP lewat QR, bukan untuk dicetak
        ]);
    }

    // Route: /cetak-stok-gudang -> cetak.stok-gudang
    public function stokGudang()
    {
        return view('print.stok-gudang', $this->dataStokGudang());
    }

    public function stokGudangExcel()
    {
        return Excel::download(
            new StokGudangExport(self::DEPO, now()),
            'stock-mesin-' . now()->format('Ymd') . '.xlsx'
        );
    }

    public function stokGudangPdf()
    {
        $pdf = Pdf::loadView('print.stok-gudang', $this->dataStokGudang())
            ->setPaper('a4', 'portrait');

        return $pdf->download('stock-mesin-' . now()->format('Ymd') . '.pdf');
    }

    // Route: /cetak-alokasi-mesin -> cetak.alokasi
    public function alokasiMesin()
    {
        $rows = DB::table('machines')
            ->join('deployments', 'machines.id', '=', 'deployments.machine_id')
            ->join('customers', 'deployments.customer_id', '=', 'customers.id')
            ->join('rayons', 'customers.rayon_id', '=', 'rayons.id')
            ->where('machines.status', 'Rented')
            // DB::table() tidak otomatis memfilter soft-delete
            ->whereNull('deployments.deleted_at')
            ->whereNull('customers.deleted_at')
            ->select(
                'rayons.nama_rayon',
                'customers.kota',
                'machines.tipe_model',
                DB::raw('count(*) as qty')
            )
            ->groupBy('rayons.nama_rayon', 'customers.kota', 'machines.tipe_model')
            ->orderBy('rayons.nama_rayon')
            ->orderBy('customers.kota')
            ->get();

        // Susun struktur rayon -> kota -> tipe, lengkap dengan total & jumlah baris
        $rayons = $rows->groupBy('nama_rayon')->map(function ($itemsRayon, $namaRayon) {
            $kotas = $itemsRayon->groupBy('kota')->map(fn($items, $kota) => [
                'nama'  => $kota,
                'items' => $items,
                'total' => $items->sum('qty'),
            ])->values();

            return [
                'nama'  => $namaRayon,
                'kotas' => $kotas,
                'total' => $kotas->sum('total'),
                // 1 baris nama kota + n baris tipe + 1 baris total kota
                'baris' => $kotas->sum(fn($k) => $k['items']->count() + 2),
            ];
        })->values();

        return view('print.alokasi-mesin', [
            'rayons'     => $rayons,
            'maxBaris'   => $rayons->max('baris') ?? 0,
            'grandTotal' => $rayons->sum('total'),
        ]);
    }

    // Route: /cetak-surat-jalan/{id} -> cetak.surat-jalan
    public function suratJalan($id)
    {
        $d = Deployment::with(['machine', 'customer'])->findOrFail($id);

        $parts = DB::table('deployment_sparepart')
            ->join('spareparts', 'deployment_sparepart.sparepart_id', '=', 'spareparts.id')
            ->where('deployment_sparepart.deployment_id', $id)
            ->select('spareparts.nama_sparepart', 'spareparts.code_part', 'deployment_sparepart.jumlah')
            ->get();

        $nomor = 'SJ/FC/CRB/' . $d->created_at->format('dmy') . '/' . str_pad($d->id, 3, '0', STR_PAD_LEFT);

        return view('print.surat-jalan', [
            'd'           => $d,
            'parts'       => $parts,
            'nomor'       => $nomor,
            'targetBaris' => 8,
        ]);
    }

    public function trackingMesin($id)
    {
        $machine  = Machine::with('customer')->findOrFail($id);
        $timeline = collect();

        // 1. Semua deployment (aktif maupun sudah ditarik)
        $deployments = DB::table('deployments')
            ->join('customers', 'deployments.customer_id', '=', 'customers.id')
            ->leftJoin('technicians', 'deployments.technician_id', '=', 'technicians.id')
            ->where('deployments.machine_id', $id)
            ->select(
                'deployments.id',
                'deployments.tanggal_instal',
                'deployments.tanggal_tarik',
                'deployments.deleted_at',
                'deployments.counter_bw',
                'deployments.counter_color',
                'deployments.no_kontrak',
                'customers.nama_customer',
                'customers.kota',
                'customers.alamat',
                'technicians.nama_technician'
            )
            ->orderBy('deployments.tanggal_instal')
            ->get();

        foreach ($deployments as $dep) {
            $timeline->push([
                'tanggal' => $dep->tanggal_instal,
                'tipe'    => 'RENTAL',
                'icon'    => '📦',
                'warna'   => 'green',
                'judul'   => 'Dipasang ke Customer',
                'detail'  => $dep->nama_customer . ' — ' . $dep->kota,
                'sub'     => 'Teknisi: ' . ($dep->nama_technician ?? '-')
                    . ' | Counter Awal BW: ' . number_format($dep->counter_bw)
                    . ' / CL: ' . number_format($dep->counter_color)
                    . ($dep->no_kontrak ? ' | Kontrak: ' . $dep->no_kontrak : ''),
            ]);

            if ($dep->tanggal_tarik || $dep->deleted_at) {
                $timeline->push([
                    'tanggal' => $dep->tanggal_tarik ?? Carbon::parse($dep->deleted_at)->toDateString(),
                    'tipe'    => 'TARIK',
                    'icon'    => '🔙',
                    'warna'   => 'orange',
                    'judul'   => 'Ditarik dari Customer',
                    'detail'  => $dep->nama_customer . ' — ' . $dep->kota,
                    'sub'     => '',
                ]);
            }
        }

        // 2. Rolling — mesin ini jadi mesin LAMA (diambil dari customer)
        $rollingLama = DB::table('machine_replacements')
            ->join('customers', 'machine_replacements.customer_id', '=', 'customers.id')
            ->join('machines as m_new', 'machine_replacements.new_machine_id', '=', 'm_new.id')
            ->leftJoin('technicians', 'machine_replacements.technician_id', '=', 'technicians.id')
            ->where('machine_replacements.old_machine_id', $id)
            ->select(
                'machine_replacements.tanggal',
                'machine_replacements.keterangan',
                'machine_replacements.counter_bw_final',
                'machine_replacements.counter_color_final',
                'customers.nama_customer',
                'customers.kota',
                'm_new.serial_number as sn_baru',
                'technicians.nama_technician'
            )
            ->get();

        foreach ($rollingLama as $r) {
            $timeline->push([
                'tanggal' => $r->tanggal,
                'tipe'    => 'ROLLING_KELUAR',
                'icon'    => '🔄',
                'warna'   => 'red',
                'judul'   => 'Rolling — Mesin Diganti (Keluar)',
                'detail'  => 'Diganti dari ' . $r->nama_customer . ' oleh SN Baru: ' . $r->sn_baru,
                'sub'     => 'Counter Akhir BW: ' . number_format($r->counter_bw_final)
                    . ' / CL: ' . number_format($r->counter_color_final)
                    . ' | Teknisi: ' . ($r->nama_technician ?? '-')
                    . ($r->keterangan ? ' | ' . $r->keterangan : ''),
            ]);
        }

        // 3. Rolling — mesin ini jadi mesin BARU (pengganti)
        $rollingBaru = DB::table('machine_replacements')
            ->join('customers', 'machine_replacements.customer_id', '=', 'customers.id')
            ->join('machines as m_old', 'machine_replacements.old_machine_id', '=', 'm_old.id')
            ->leftJoin('technicians', 'machine_replacements.technician_id', '=', 'technicians.id')
            ->where('machine_replacements.new_machine_id', $id)
            ->select(
                'machine_replacements.tanggal',
                'machine_replacements.keterangan',
                'customers.nama_customer',
                'customers.kota',
                'm_old.serial_number as sn_lama',
                'technicians.nama_technician'
            )
            ->get();

        foreach ($rollingBaru as $r) {
            $timeline->push([
                'tanggal' => $r->tanggal,
                'tipe'    => 'ROLLING_MASUK',
                'icon'    => '✅',
                'warna'   => 'blue',
                'judul'   => 'Rolling — Masuk sebagai Pengganti',
                'detail'  => 'Menggantikan SN: ' . $r->sn_lama . ' di ' . $r->nama_customer,
                'sub'     => 'Teknisi: ' . ($r->nama_technician ?? '-') . ($r->keterangan ? ' | ' . $r->keterangan : ''),
            ]);
        }

        $timeline = $timeline->sortBy('tanggal')->values();

        return view('print.tracking-mesin', compact('machine', 'timeline'));
    }

    /**
     * Data bersama untuk stok gudang (halaman cetak & PDF).
     */
    private function dataStokGudang(): array
    {
        // Mesin Photo Copy
        $stocks = Machine::select(
            'tipe_model',
            'status',
            'asal_mesin',
            'volt',
            'kaset',
            'finisher',
            'double_scan',
            DB::raw('count(*) as total_unit'),
            DB::raw('SUM(IF(kaset = 4, 1, 0)) as kaset_4_count'),
            DB::raw('GROUP_CONCAT(serial_number ORDER BY serial_number SEPARATOR ", ") as list_sn')
        )
            ->whereIn('status', ['Ready', 'Refurbish'])
            ->groupBy('tipe_model', 'status', 'asal_mesin', 'volt', 'finisher', 'double_scan')
            ->orderBy('tipe_model')
            ->orderByRaw("FIELD(status, 'Ready', 'Refurbish') asc")
            ->get();

        // Mesin Air RO (Ready & Perbaikan, belum di-deploy)
        $machineAirRos = MachineAirRo::select(
            'serial_number',
            'tipe_mesin',
            'status',
            DB::raw('count(*) as total_unit')
        )
            ->whereIn('status', ['Ready', 'Perbaikan'])
            ->whereNull('customer_ro_id')
            ->groupBy('serial_number', 'tipe_mesin', 'status')
            ->orderBy('tipe_mesin')
            ->get();

        return [
            'stocks'        => $stocks,
            'machineAirRos' => $machineAirRos,
            'depo'          => self::DEPO,
            'tanggal'       => now(),
            'dibuatOleh'    => null,
            'diketahuiOleh' => null,
        ];
    }
}
