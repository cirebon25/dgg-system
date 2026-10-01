<?php

namespace App\Filament\Pages;

use App\Models\SparepartEntry;
use App\Models\Technician;
use App\Models\TechnicianStockHistory;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SparepartHistory extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static string $view = 'filament.pages.sparepart-history';
    protected static ?string $navigationLabel = 'History Sparepart';
    protected static ?string $title = 'Riwayat Mutasi Sparepart';
    protected static ?string $navigationGroup = 'Gudang & Stok';
    protected static ?int $navigationSort = 8;

    private const PER_PAGE = 30;

    /**
     * Kata kunci untuk mendeteksi baris "keluar" di technician_stock_histories
     * yang sebenarnya RETUR, bukan PAKAI permanen. Solusi sementara berbasis
     * teks — solusi permanen: kolom enum eksplisit di migrasi, diisi oleh
     * kode yang menulis baris tsb (lihat catatan arsitektur).
     */
    private const KATA_KUNCI_RETUR = 'retur';

    /**
     * Granularitas pembulatan waktu untuk mencocokkan satu kejadian retur
     * yang ditulis ke technician_stock_histories DAN part_returns sekaligus.
     * Dibulatkan ke menit (bukan detik) untuk toleransi jeda antar-insert.
     */
    private const FORMAT_SIGNATURE_WAKTU = 'Y-m-d H:i';

    private const NAMA_HARI = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    private const NAMA_BULAN = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    private const URUTAN_KATEGORI = ['PINJAM', 'PAKAI', 'DEPLOY', 'RETUR', 'MASUK', 'ROLLING'];

    public string $month;
    public string $year;
    public ?int $technician_id = null;
    public int $displayLimit = self::PER_PAGE;
    public string $groupBy = 'tanggal';

    /** @var Collection<int, object> */
    public Collection $historyData;

    public function mount(): void
    {
        $this->month = Carbon::now()->format('m');
        $this->year = Carbon::now()->format('Y');
        $this->technician_id = null;
        $this->fetchData();
    }

    public function updatedMonth(): void
    {
        $this->resetAndRefresh();
    }

    public function updatedYear(): void
    {
        $this->resetAndRefresh();
    }

    public function updatedTechnicianId(): void
    {
        $this->resetAndRefresh();
    }

    public function setGroupBy(string $mode): void
    {
        $this->groupBy = in_array($mode, ['tanggal', 'teknisi', 'kategori'], true) ? $mode : 'tanggal';
    }

    public function loadMore(): void
    {
        $this->displayLimit += self::PER_PAGE;
    }

    private function resetAndRefresh(): void
    {
        $this->displayLimit = self::PER_PAGE;
        $this->fetchData();
    }

    private function adalahKeteranganRetur(?string $keterangan): bool
    {
        return $keterangan !== null && str_contains(strtolower($keterangan), self::KATA_KUNCI_RETUR);
    }

    /**
     * Signature untuk mencocokkan satu kejadian retur yang ditulis ke dua
     * tabel berbeda (technician_stock_histories & part_returns) dalam
     * request yang sama.
     */
    private function buatSignatureRetur(int $sparepartId, ?int $technicianId, int $jumlah, $createdAt): string
    {
        $waktu = Carbon::parse($createdAt)->format(self::FORMAT_SIGNATURE_WAKTU);

        return "{$sparepartId}|{$technicianId}|{$jumlah}|{$waktu}";
    }

    public function fetchData(): void
    {
        $m = max(1, min(12, (int) $this->month));
        $y = max(2020, min((int) Carbon::now()->format('Y') + 1, (int) $this->year));

        $validTechnicianIds = Technician::pluck('id')->all();
        $techId = ($this->technician_id && in_array($this->technician_id, $validTechnicianIds, true))
            ? $this->technician_id
            : null;

        // 1. MASUK — Stok masuk dari supplier / cabang lain
        $masuk = SparepartEntry::with('sparepart')
            ->whereMonth('created_at', $m)
            ->whereYear('created_at', $y)
            ->get()
            ->map(fn($item) => (object) [
                'tipe'         => 'MASUK',
                'tanggal'      => $item->created_at,
                'jumlah'       => $item->jumlah,
                'part'         => $item->sparepart->nama_sparepart ?? '-',
                'nama_teknisi' => null,
                'keterangan'   => 'Diterima dari: ' . ($item->supplier ?? 'Supplier tidak diketahui'),
            ]);

        // 2. PINJAM — Gudang → Teknisi (masih aset perusahaan, belum terpakai)
        $pinjamQuery = TechnicianStockHistory::with(['sparepart', 'technician'])
            ->whereMonth('created_at', $m)
            ->whereYear('created_at', $y)
            ->where('masuk', '>', 0);

        if ($techId) {
            $pinjamQuery->where('technician_id', $techId);
        }

        $pinjam = $pinjamQuery->get()
            ->map(fn($item) => (object) [
                'tipe'         => 'PINJAM',
                'tanggal'      => $item->created_at,
                'jumlah'       => $item->masuk,
                'part'         => $item->sparepart->nama_sparepart ?? '-',
                'nama_teknisi' => $item->technician->nama_technician ?? '-',
                'keterangan'   => null,
            ]);

        // 3. RETUR — dari tabel part_returns (sumber otoritatif untuk kejadian retur)
        $returQuery = DB::table('part_returns as pr')
            ->join('spareparts as sp', 'sp.id', '=', 'pr.sparepart_id')
            ->join('technicians as t', 't.id', '=', 'pr.technician_id')
            ->whereMonth('pr.created_at', $m)
            ->whereYear('pr.created_at', $y);

        if ($techId) {
            $returQuery->where('pr.technician_id', $techId);
        }

        $returRaw = $returQuery->select(
            'pr.sparepart_id',
            'pr.technician_id',
            'pr.jumlah',
            'pr.created_at',
            'sp.nama_sparepart',
            't.nama_technician'
        )->get();

        // Signature setiap retur yang SUDAH tercatat resmi di part_returns —
        // dipakai untuk membuang baris duplikat dari technician_stock_histories.
        $signatureReturResmi = $returRaw
            ->map(fn($item) => $this->buatSignatureRetur(
                $item->sparepart_id,
                $item->technician_id,
                $item->jumlah,
                $item->created_at
            ))
            ->flip();

        $retur = $returRaw->map(fn($item) => (object) [
            'tipe'         => 'RETUR',
            'tanggal'      => $item->created_at,
            'jumlah'       => $item->jumlah,
            'part'         => $item->nama_sparepart,
            'nama_teknisi' => $item->nama_technician ?? '-',
            'keterangan'   => null,
        ]);

        // 4. KELUAR DARI STOK TEKNISI — bisa berarti PAKAI (servis) ATAU RETUR ke gudang.
        $keluarTeknisiQuery = TechnicianStockHistory::with(['sparepart', 'technician'])
            ->whereMonth('created_at', $m)
            ->whereYear('created_at', $y)
            ->where('keluar', '>', 0);

        if ($techId) {
            $keluarTeknisiQuery->where('technician_id', $techId);
        }

        $keluarTeknisiRaw = $keluarTeknisiQuery->get();

        $pakai = $keluarTeknisiRaw
            ->reject(fn($item) => $this->adalahKeteranganRetur($item->keterangan))
            ->map(fn($item) => (object) [
                'tipe'         => 'PAKAI',
                'tanggal'      => $item->created_at,
                'jumlah'       => $item->keluar,
                'part'         => $item->sparepart->nama_sparepart ?? '-',
                'nama_teknisi' => $item->technician->nama_technician ?? '-',
                'keterangan'   => $item->keterangan ?? 'Dipakai untuk servis',
            ]);

        // Baris "keluar" yang terdeteksi retur TAPI TIDAK ADA pasangannya di
        // part_returns (fallback) — tetap ditampilkan supaya histori tidak hilang.
        // Yang SUDAH punya pasangan di part_returns dibuang di sini karena
        // sudah direpresentasikan oleh $retur di atas — mencegah 1 kejadian
        // tampil sebagai 2 baris.
        $keluarTeknisiReturTanpaPasangan = $keluarTeknisiRaw
            ->filter(fn($item) => $this->adalahKeteranganRetur($item->keterangan))
            ->reject(function ($item) use ($signatureReturResmi) {
                $signature = $this->buatSignatureRetur(
                    $item->sparepart_id,
                    $item->technician_id,
                    $item->keluar,
                    $item->created_at
                );

                return $signatureReturResmi->has($signature);
            })
            ->map(fn($item) => (object) [
                'tipe'         => 'RETUR',
                'tanggal'      => $item->created_at,
                'jumlah'       => $item->keluar,
                'part'         => $item->sparepart->nama_sparepart ?? '-',
                'nama_teknisi' => $item->technician->nama_technician ?? '-',
                'keterangan'   => 'Dikembalikan ke gudang oleh teknisi',
            ]);

        // 5. DEPLOY — Keluar via pemasangan mesin baru ke customer
        $deployQuery = DB::table('deployment_sparepart as ds')
            ->join('deployments as d', 'd.id', '=', 'ds.deployment_id')
            ->join('spareparts as sp', 'sp.id', '=', 'ds.sparepart_id')
            ->join('customers as c', 'c.id', '=', 'd.customer_id')
            ->leftJoin('technicians as t', 't.id', '=', 'd.technician_id')
            ->whereMonth('ds.created_at', $m)
            ->whereYear('ds.created_at', $y)
            ->whereNull('d.deleted_at');

        if ($techId) {
            $deployQuery->where('d.technician_id', $techId);
        }

        $deploy = $deployQuery->select(
            'ds.jumlah',
            'ds.created_at',
            'sp.nama_sparepart',
            'c.nama_customer',
            'd.tanggal_instal',
            't.nama_technician'
        )
            ->get()
            ->map(fn($item) => (object) [
                'tipe'         => 'DEPLOY',
                'tanggal'      => $item->tanggal_instal ?? $item->created_at,
                'jumlah'       => $item->jumlah,
                'part'         => $item->nama_sparepart,
                'nama_teknisi' => $item->nama_technician ?? '-',
                'keterangan'   => 'Terpasang di mesin milik: ' . $item->nama_customer,
            ]);

        // 6. ROLLING — Event ganti mesin (informasi saja, tidak memengaruhi stok part)
        $rollingQuery = DB::table('machine_replacements as mr')
            ->join('customers as c', 'c.id', '=', 'mr.customer_id')
            ->join('machines as m_old', 'm_old.id', '=', 'mr.old_machine_id')
            ->join('machines as m_new', 'm_new.id', '=', 'mr.new_machine_id')
            ->leftJoin('technicians as t', 't.id', '=', 'mr.technician_id')
            ->whereMonth('mr.tanggal', $m)
            ->whereYear('mr.tanggal', $y);

        if ($techId) {
            $rollingQuery->where('mr.technician_id', $techId);
        }

        $rolling = $rollingQuery->select(
            'mr.tanggal',
            'c.nama_customer',
            'm_old.serial_number as sn_lama',
            'm_new.serial_number as sn_baru',
            't.nama_technician'
        )
            ->get()
            ->map(fn($item) => (object) [
                'tipe'         => 'ROLLING',
                'tanggal'      => $item->tanggal,
                'jumlah'       => 0,
                'part'         => '-',
                'nama_teknisi' => $item->nama_technician ?? '-',
                'keterangan'   => "Ganti mesin {$item->sn_lama} → {$item->sn_baru} milik {$item->nama_customer}",
            ]);
        $this->historyData = collect()
            ->concat($masuk)
            ->concat($pinjam)
            ->concat($pakai)
            ->concat($retur)
            ->concat($keluarTeknisiReturTanpaPasangan)
            ->concat($deploy)
            ->concat($rolling)
            ->sortByDesc('tanggal')
            ->values();
    }

    public function getSummaryProperty(): array
    {
        $data = $this->historyData;

        return [
            'masuk'           => (int) $data->whereIn('tipe', ['MASUK', 'RETUR'])->sum('jumlah'),
            'dipinjamkan'     => (int) $data->where('tipe', 'PINJAM')->sum('jumlah'),
            'terpakai'        => (int) $data->whereIn('tipe', ['PAKAI', 'DEPLOY'])->sum('jumlah'),
            'total_transaksi' => $data->count(),
        ];
    }

    public function getGroupedHistoryProperty(): Collection
    {
        return match ($this->groupBy) {
            'teknisi'  => $this->groupByTeknisi(),
            'kategori' => $this->groupByKategori(),
            default    => $this->groupByTanggal(),
        };
    }

    private function groupByTanggal(): Collection
    {
        return $this->historyData
            ->take($this->displayLimit)
            ->groupBy(fn($item) => Carbon::parse($item->tanggal)->format('Y-m-d'));
    }

    private function groupByTeknisi(): Collection
    {
        return $this->historyData
            ->groupBy(fn($item) => ($item->nama_teknisi && $item->nama_teknisi !== '-')
                ? $item->nama_teknisi
                : 'Gudang / Tanpa Teknisi')
            ->sortKeys();
    }

    private function groupByKategori(): Collection
    {
        $urutan = array_flip(self::URUTAN_KATEGORI);

        return $this->historyData
            ->groupBy('tipe')
            ->sortBy(fn($items, $tipe) => $urutan[$tipe] ?? 99);
    }

    public function groupSummary(Collection $items): array
    {
        return [
            'jumlah_transaksi' => $items->count(),
            'pinjam'           => (int) $items->where('tipe', 'PINJAM')->sum('jumlah'),
            'pakai'            => (int) $items->whereIn('tipe', ['PAKAI', 'DEPLOY'])->sum('jumlah'),
            'retur'            => (int) $items->where('tipe', 'RETUR')->sum('jumlah'),
            'masuk'            => (int) $items->where('tipe', 'MASUK')->sum('jumlah'),
        ];
    }

    public function getHasMoreProperty(): bool
    {
        return $this->groupBy === 'tanggal' && $this->displayLimit < $this->historyData->count();
    }

    public function getTechniciansProperty(): Collection
    {
        return Technician::orderBy('nama_technician')->pluck('nama_technician', 'id');
    }

    public function typeMeta(string $tipe): array
    {
        return match ($tipe) {
            'MASUK'   => ['label' => 'Stok Masuk', 'color' => 'success', 'icon' => 'heroicon-o-arrow-down-circle', 'sign' => '+'],
            'RETUR'   => ['label' => 'Retur ke Gudang', 'color' => 'warning', 'icon' => 'heroicon-o-arrow-uturn-left', 'sign' => '+'],
            'PINJAM'  => ['label' => 'Dipinjamkan', 'color' => 'info', 'icon' => 'heroicon-o-arrow-right-circle', 'sign' => '-'],
            'PAKAI'   => ['label' => 'Terpakai (Servis)', 'color' => 'danger', 'icon' => 'heroicon-o-wrench-screwdriver', 'sign' => '-'],
            'DEPLOY'  => ['label' => 'Terpasang di Mesin Baru', 'color' => 'danger', 'icon' => 'heroicon-o-cog-6-tooth', 'sign' => '-'],
            'ROLLING' => ['label' => 'Ganti Mesin', 'color' => 'gray', 'icon' => 'heroicon-o-arrow-path', 'sign' => ''],
            default   => ['label' => $tipe, 'color' => 'gray', 'icon' => 'heroicon-o-question-mark-circle', 'sign' => ''],
        };
    }

    public function formatTanggalIndonesia(string $tanggalYmd): string
    {
        $c = Carbon::parse($tanggalYmd);

        return self::NAMA_HARI[$c->dayOfWeek] . ', ' . $c->day . ' ' . self::NAMA_BULAN[$c->month] . ' ' . $c->year;
    }

    public function formatTanggalSingkatIndonesia($tanggal): string
    {
        $c = Carbon::parse($tanggal);
        $bulanSingkat = substr(self::NAMA_BULAN[$c->month], 0, 3);

        return $c->day . ' ' . $bulanSingkat . ' ' . $c->year;
    }
}
