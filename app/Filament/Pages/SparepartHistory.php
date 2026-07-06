<?php

namespace App\Filament\Pages;

use App\Models\Sparepart;
use App\Models\SparepartEntry;
use App\Models\ServiceLog;
use App\Models\TechnicianStock;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class SparepartHistory extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static string $view = 'filament.pages.sparepart-history';
    protected static ?string $navigationLabel = 'History Sparepart';
    protected static ?string $title = 'Riwayat Mutasi Sparepart';
    protected static ?string $navigationGroup = 'Gudang & Stok';

    public $month;
    public $year;
    public $historyData;

    public function mount()
    {
        $this->month = Carbon::now()->format('m');
        $this->year = Carbon::now()->format('Y');
        $this->fetchData();
    }

    public function updated($property)
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        $m = $this->month;
        $y = $this->year;

        // =============================================
        // 1. MASUK — Stok masuk dari supplier
        // =============================================
        $masuk = \App\Models\SparepartEntry::with('sparepart')
            ->whereMonth('created_at', $m)
            ->whereYear('created_at', $y)
            ->get()
            ->map(fn($item) => (object)[
                'tipe'    => 'MASUK',
                'tanggal' => $item->created_at,
                'jumlah'  => $item->jumlah,
                'detail'  => 'DARI: ' . ($item->supplier ?? 'Supplier Tidak Diketahui'),
                'part'    => $item->sparepart->nama_sparepart ?? '-',
            ])
            ->toBase(); // <-- FIX: ubah dari Eloquent Collection ke base Collection

        // =============================================
        // 2. PINJAM — Gudang → Teknisi (masuk > 0)
        // =============================================
        $pinjam = \App\Models\TechnicianStockHistory::with(['sparepart', 'technician'])
            ->whereMonth('created_at', $m)
            ->whereYear('created_at', $y)
            ->where('masuk', '>', 0)
            ->get()
            ->map(fn($item) => (object)[
                'tipe'    => 'PINJAM',
                'tanggal' => $item->created_at,
                'jumlah'  => $item->masuk,
                'detail'  => 'KE TEKNISI: ' . ($item->technician->nama_technician ?? '-'),
                'part'    => $item->sparepart->nama_sparepart ?? '-',
            ])
            ->toBase(); // <-- FIX

        // =============================================
        // 3. PAKAI SERVIS — Teknisi → Customer (keluar > 0)
        // =============================================
        $pakai = \App\Models\TechnicianStockHistory::with(['sparepart', 'technician'])
            ->whereMonth('created_at', $m)
            ->whereYear('created_at', $y)
            ->where('keluar', '>', 0)
            ->get()
            ->map(fn($item) => (object)[
                'tipe'    => 'PAKAI',
                'tanggal' => $item->created_at,
                'jumlah'  => $item->keluar,
                'detail'  => $item->keterangan ?? 'SERVIS',
                'part'    => $item->sparepart->nama_sparepart ?? '-',
            ])
            ->toBase(); // <-- FIX

        // =============================================
        // 4. DEPLOY — Keluar via pemasangan mesin
        // =============================================
        $deploy = \Illuminate\Support\Facades\DB::table('deployment_sparepart as ds')
            ->join('deployments as d', 'd.id', '=', 'ds.deployment_id')
            ->join('spareparts as sp', 'sp.id', '=', 'ds.sparepart_id')
            ->join('customers as c', 'c.id', '=', 'd.customer_id')
            ->whereMonth('ds.created_at', $m)
            ->whereYear('ds.created_at', $y)
            ->whereNull('d.deleted_at')
            ->select('ds.jumlah', 'ds.created_at', 'sp.nama_sparepart', 'c.nama_customer', 'd.tanggal_instal')
            ->get()
            ->map(fn($item) => (object)[
                'tipe'    => 'DEPLOY',
                'tanggal' => $item->tanggal_instal ?? $item->created_at,
                'jumlah'  => $item->jumlah,
                'detail'  => 'INSTAL KE: ' . $item->nama_customer,
                'part'    => $item->nama_sparepart,
            ]);

        // =============================================
        // 5. ROLLING — Event ganti mesin (info saja)
        // =============================================
        $rolling = \Illuminate\Support\Facades\DB::table('machine_replacements as mr')
            ->join('customers as c', 'c.id', '=', 'mr.customer_id')
            ->join('machines as m_old', 'm_old.id', '=', 'mr.old_machine_id')
            ->join('machines as m_new', 'm_new.id', '=', 'mr.new_machine_id')
            ->whereMonth('mr.tanggal', $m)
            ->whereYear('mr.tanggal', $y)
            ->select('mr.tanggal', 'mr.keterangan', 'c.nama_customer', 'm_old.serial_number as sn_lama', 'm_new.serial_number as sn_baru')
            ->get()
            ->map(fn($item) => (object)[
                'tipe'    => 'ROLLING',
                'tanggal' => $item->tanggal,
                'jumlah'  => 0,
                'detail'  => $item->sn_lama . ' → ' . $item->sn_baru . ' | ' . $item->nama_customer,
                'part'    => '-',
            ]);

        // =============================================
        // 6. RETUR PART — Teknisi kembalikan part ke gudang
        // =============================================
        $returPart = \Illuminate\Support\Facades\DB::table('part_returns as pr')
            ->join('spareparts as sp', 'sp.id', '=', 'pr.sparepart_id')
            ->join('technicians as t', 't.id', '=', 'pr.technician_id')
            ->whereMonth('pr.created_at', $m)
            ->whereYear('pr.created_at', $y)
            ->select('pr.jumlah', 'pr.created_at', 'sp.nama_sparepart', 't.nama_technician')
            ->get()
            ->map(fn($item) => (object)[
                'tipe'    => 'RETUR',
                'tanggal' => $item->created_at,
                'jumlah'  => $item->jumlah,
                'detail'  => 'RETUR DARI: ' . $item->nama_technician,
                'part'    => $item->nama_sparepart,
            ]);

        // =============================================
        // GABUNG & URUTKAN
        // =============================================
        $this->historyData = $masuk
            ->concat($pinjam)
            ->concat($pakai)
            ->concat($deploy)
            ->concat($rolling)
            ->concat($returPart)
            ->sortByDesc('tanggal')
            ->values();
    }
}
