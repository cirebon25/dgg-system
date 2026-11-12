<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PusatCetak extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationLabel = 'Pusat Cetak Dokumen';
    protected static ?string $title = '🖨️ Pusat Cetak Dokumen';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.pusat-cetak';
    protected static ?string $navigationGroup = 'Pusat Cetak';

    public int $bulan;
    public int $tahun;

    public function mount(): void
    {
        $this->bulan = now()->month;
        $this->tahun = now()->year;
    }

    /**
     * 'filter'    => true  → link ditambah ?bulan=&tahun= dari filter di atas halaman
     * 'highlight' => true  → kartu diberi tanda khusus (bintang + garis aksen)
     */
    public function getDaftarCetak(): array
    {
        return [
            [
                'judul' => 'Mesin & Gudang',
                'icon'  => 'heroicon-o-cube',
                'warna' => '59 130 246', // biru
                'items' => [
                    ['label' => 'Stok Gudang',              'desc' => 'Daftar mesin di gudang',            'route' => 'cetak.stok-gudang',      'icon' => 'heroicon-o-archive-box'],
                    ['label' => 'Stok Gudang (PDF)',        'desc' => 'Unduh stok gudang format PDF',      'route' => 'cetak.stok-gudang.pdf',  'icon' => 'heroicon-o-document-text'],
                    ['label' => 'Stok Gudang (Excel)',      'desc' => 'Unduh stok gudang format Excel',    'route' => 'cetak.stok-gudang.excel', 'icon' => 'heroicon-o-table-cells'],
                    ['label' => 'Rincian Customer',         'desc' => 'Mesin per customer',                'route' => 'cetak.alokasi-customer', 'icon' => 'heroicon-o-users'],
                    ['label' => 'Alokasi Mesin Rayon',      'desc' => 'Penempatan mesin di lapangan',      'route' => 'cetak.alokasi',          'icon' => 'heroicon-o-map-pin'],
                    ['label' => 'Rekap Mesin per Customer', 'desc' => 'Jumlah unit tiap customer',         'route' => 'report.rekap-mesin',     'icon' => 'heroicon-o-clipboard-document-list'],
                    ['label' => 'Tukar Mesin',              'desc' => 'Riwayat tukar guling mesin',        'route' => 'cetak.swap',             'icon' => 'heroicon-o-arrow-path', 'highlight' => true],
                    ['label' => 'Pemasangan Baru',          'desc' => 'Customer pasang baru',              'route' => 'cetak.pemasangan',       'icon' => 'heroicon-o-truck', 'filter' => true, 'highlight' => true],
                    // ['label' => 'Rekap Penarikan',       'desc' => 'Rekap mesin yang ditarik',          'route' => 'withdrawal.rekap',       'icon' => 'heroicon-o-arrow-uturn-left'],
                    ['label' => 'Service Card Tahunan',     'desc' => 'Kartu service per tahun',           'route' => 'admin.deployments.print-yearly', 'icon' => 'heroicon-o-wrench-screwdriver'],
                ],
            ],
            [
                'judul' => 'Sparepart',
                'icon'  => 'heroicon-o-cog-6-tooth',
                'warna' => '245 158 11', // kuning
                'items' => [
                    ['label' => 'Rekap Pengeluaran',        'desc' => 'Gabungan service log + deployment', 'route' => 'cetak.rekap-sparepart',  'icon' => 'heroicon-o-clipboard-document-list', 'highlight' => true],
                    ['label' => 'Pemakaian Bulanan',        'desc' => 'Pemakaian part per bulan',          'route' => 'sparepart.pemakaian-bulanan', 'icon' => 'heroicon-o-calendar-days'],
                    ['label' => 'Pemakaian Matrix',         'desc' => 'Tabel matrix pemakaian part',       'route' => 'sparepart.pemakaian-matrix',  'icon' => 'heroicon-o-table-cells'],
                    ['label' => 'Saldo Sparepart',          'desc' => 'Sisa stok tiap part',               'route' => 'saldo-sparepart',        'icon' => 'heroicon-o-scale'],
                    // ['label' => 'Laporan Outflow',          'desc' => 'Part keluar',                       'route' => 'sparepart.report.outflow', 'icon' => 'heroicon-o-arrow-up-tray'],
                    ['label' => 'Laporan Part Bandung',     'desc' => 'Part kiriman Bandung',              'route' => 'lap-part-bdg',           'icon' => 'heroicon-o-document-chart-bar'],
                    // ['label' => 'Part Keluar',              'desc' => 'Cetak part keluar',                 'route' => 'part-keluar.cetak',      'icon' => 'heroicon-o-arrow-right-on-rectangle'],
                    // ['label' => 'Part per Mesin',           'desc' => 'Ganti part tiap mesin',             'route' => 'cetak.part.mesin',       'icon' => 'heroicon-o-cog-8-tooth'],
                    ['label' => 'Part per Bulan',           'desc' => 'Ganti part per bulan',              'route' => 'cetak.part.bulan',       'icon' => 'heroicon-o-calendar'],
                    ['label' => 'Kartu Stok Semua Teknisi', 'desc' => 'Stok part semua teknisi',           'route' => 'cetak.kartu-stok-semua', 'icon' => 'heroicon-o-identification'],
                    ['label' => 'Riwayat Stok Teknisi',     'desc' => 'Histori stok teknisi',              'route' => 'technician-stock.print', 'icon' => 'heroicon-o-clock'],
                ],
            ],
            [
                'judul' => 'Rayon & Teknisi',
                'icon'  => 'heroicon-o-user-group',
                'warna' => '16 185 129', // hijau
                'items' => [
                    ['label' => 'Rekap Rayon',          'desc' => 'Ringkasan per rayon',    'route' => 'cetak.rekap-rayon',       'icon' => 'heroicon-o-map'],
                    ['label' => 'Service Rayon',        'desc' => 'Service per rayon',      'route' => 'cetak.service-rayon',     'icon' => 'heroicon-o-wrench'],
                    ['label' => 'Top Usage',            'desc' => 'Pemakaian tertinggi',    'route' => 'cetak.top-usage',         'icon' => 'heroicon-o-fire'],
                    ['label' => 'Kinerja Teknisi',      'desc' => 'Performa tiap teknisi',  'route' => 'print.tech-performance',  'icon' => 'heroicon-o-chart-bar'],
                    ['label' => 'Kinerja Rayon',        'desc' => 'Performa tiap rayon',    'route' => 'print.performance-rayon', 'icon' => 'heroicon-o-chart-bar-square', 'filter' => true, 'highlight' => true],
                    ['label' => 'Rekap Laporan Teknik', 'desc' => 'Rekap format melebar',   'route' => 'rekap.horizontal',        'icon' => 'heroicon-o-arrows-right-left', 'highlight' => true],
                    ['label' => 'Rincian Absen Harian', 'desc' => 'Aktivitas harian',       'route' => 'cetak.rincian-harian',    'icon' => 'heroicon-o-sun'],
                    ['label' => 'Service Log (Bulk)',   'desc' => 'Cetak banyak service log', 'route' => 'print.service.bulk',    'icon' => 'heroicon-o-document-duplicate'],
                ],
            ],
            [
                'judul' => 'MRC & Keuangan',
                'icon'  => 'heroicon-o-banknotes',
                'warna' => '139 92 246', // ungu
                'items' => [
                    ['label' => 'Rekap MRC',       'desc' => 'Ringkasan kontrak MRC', 'route' => 'mrc.rekap',       'icon' => 'heroicon-o-document-chart-bar', 'filter' => true],
                    ['label' => 'Cetak Rekap MRC', 'desc' => 'Versi cetak rekap',     'route' => 'mrc.cetak-rekap', 'icon' => 'heroicon-o-printer', 'filter' => true],
                    ['label' => 'Tagihan MRC',     'desc' => 'Laporan tagihan + PPN', 'route' => 'mrc.tagihan',     'icon' => 'heroicon-o-receipt-percent', 'filter' => true],
                    ['label' => 'Cetak Semua MRC',     'desc' => 'Semua data MRC',             'route' => 'mrc.cetak-semua',   'icon' => 'heroicon-o-rectangle-stack'],
                    ['label' => 'Rekap Semua Invoice', 'desc' => 'Daftar invoice per periode', 'route' => 'invoice.print-all', 'icon' => 'heroicon-o-document-currency-dollar', 'filter' => true],
                    ['label' => 'Kas Bulanan',         'desc' => 'Buku kas per bulan',         'route' => 'cetak.kas-bulanan', 'icon' => 'heroicon-o-book-open', 'filter' => true],
                ],
            ],
            [
                'judul' => 'Sales',
                'icon'  => 'heroicon-o-presentation-chart-line',
                'warna' => '244 63 94', // merah muda
                'items' => [
                    ['label' => 'Kunjungan (Ringkasan)', 'desc' => 'Ringkasan kunjungan sales', 'route' => 'prospect.laporan',         'icon' => 'heroicon-o-clipboard-document-check', 'filter' => true],
                    ['label' => 'Kunjungan (Rincian)',   'desc' => 'Detail per marketing',      'route' => 'prospect.laporan.rincian', 'icon' => 'heroicon-o-list-bullet', 'filter' => true],
                ],
            ],
        ];
    }

    public function urlCetak(array $item): string
    {
        $params = ($item['filter'] ?? false)
            ? [
                'bulan' => $this->bulan,
                'tahun' => $this->tahun,
                'month' => $this->bulan, // untuk controller yang memakai nama month/year
                'year'  => $this->tahun,
            ]
            : [];

        return route($item['route'], $params);
    }
}
