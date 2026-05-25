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
        // 1. MASUK (Dari Supplier/Import)
        $masuk = SparepartEntry::whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->get()->map(fn($item) => (object)[
                'tipe' => 'MASUK', 'tanggal' => $item->created_at, 'jumlah' => $item->jumlah,
                'detail' => 'DARI: ' . ($item->supplier ?? 'Gudang'), 'part' => $item->sparepart->nama_sparepart ?? '-'
            ]);

        // 2. PINJAM (Ke Teknisi)
        $pinjam = TechnicianStock::whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->get()->map(fn($item) => (object)[
                'tipe' => 'PINJAM', 'tanggal' => $item->created_at, 'jumlah' => $item->jumlah,
                'detail' => 'KE TEKNISI: ' . ($item->technician->nama_technician ?? 'Tdk Diketahui'), 'part' => $item->sparepart->nama_sparepart ?? '-'
            ]);

        // 3. PAKAI (Ke Customer)
        $pakai = ServiceLog::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->whereNotNull('sparepart_id')
            ->get()->map(fn($item) => (object)[
                'tipe' => 'PAKAI', 'tanggal' => $item->tanggal, 'jumlah' => $item->jumlah_sparepart,
                'detail' => 'CUSTOMER: ' . ($item->customer->nama_customer ?? 'Mesin Internal'), 'part' => $item->sparepart->nama_sparepart ?? '-'
            ]);

        $this->historyData = $masuk->concat($pinjam)->concat($pakai)->sortByDesc('tanggal');
    }
}