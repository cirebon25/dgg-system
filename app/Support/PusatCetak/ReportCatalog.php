<?php

declare(strict_types=1);

namespace App\Support\PusatCetak;

final class ReportCatalog
{
    /**
     * Katalog laporan Pusat Cetak, dikelompokkan per kategori.
     * Urutan kunci array = urutan tampil di halaman.
     *
     * Setiap item wajib punya salah satu:
     * - 'href'        : link langsung (tidak butuh input periode)
     * - 'urlTemplate' : link bertipe modal, mengandung placeholder __B__ (bulan) dan __T__ (tahun)
     *
     * @return array<string, array<int, array<string, string>>>
     */
    public static function groups(): array
    {
        return [
            'Sparepart & Gudang' => [
                [
                    'icon' => '⚙️',
                    'title' => 'Laporan Rekap Pemakaian Sparepart Teknisi Perbulan',
                    'description' => 'Daftar item terpakai oleh teknisi lapangan.',
                    'color' => 'danger',
                    'buttonIcon' => 'heroicon-m-printer',
                    'buttonLabel' => 'Cetak Rekap Sparepart Teknisi',
                    'urlTemplate' => route('cetak.rekap-sparepart', ['bulan' => '__B__', 'tahun' => '__T__']),
                ],
                [
                    'icon' => '⚙️',
                    'title' => 'Rekap Saldo Sparepart',
                    'description' => 'Laporan mutasi kuantitas barang masuk & keluar per bulan.',
                    'color' => 'indigo',
                    'buttonIcon' => 'heroicon-m-printer',
                    'buttonLabel' => 'Cetak Saldo Sparepart',
                    'urlTemplate' => route('saldo-sparepart', ['bulan' => '__B__', 'tahun' => '__T__']),
                ],
                [
                    'icon' => '💼',
                    'title' => 'Kartu Stok Semua Teknisi',
                    'description' => 'Saldo part yang sedang dibawa oleh seluruh teknisi lapangan.',
                    'color' => 'violet',
                    'buttonIcon' => 'heroicon-m-printer',
                    'buttonLabel' => 'Cetak Kartu Stok Semua',
                    'href' => route('cetak.kartu-stok-semua'),
                ],
                [
                    'icon' => '🔧',
                    'title' => 'Rekap Part Terpakai / Keluar',
                    'description' => 'Rekapitulasi part yang terpakai atau keluar per bulan.',
                    'color' => 'danger',
                    'buttonIcon' => 'heroicon-m-printer',
                    'buttonLabel' => 'Cetak Rekap Part Terpakai / Keluar',
                    'urlTemplate' => route('sparepart.report.outflow', ['month' => '__B__', 'year' => '__T__']),
                ],
                [
                    'icon' => '⏱️',
                    'title' => 'Rekap Saldo Sparepart Realtime',
                    'description' => 'Saldo sparepart saat ini (realtime), tanpa filter periode.',
                    'color' => 'warning',
                    'buttonIcon' => 'heroicon-m-printer',
                    'buttonLabel' => 'Cetak Rekap Saldo Realtime',
                    'href' => route('saldo-sparepart'),
                ],
                [
                    'icon' => '🏬',
                    'title' => 'Laporan Part BDG',
                    'description' => 'Laporan part gudang Bandung per bulan.',
                    'color' => 'success',
                    'buttonIcon' => 'heroicon-m-printer',
                    'buttonLabel' => 'Cetak Lap Part BDG',
                    'urlTemplate' => route('lap-part-bdg', ['month' => '__B__', 'year' => '__T__']),
                ],
                [
                    'icon' => '📒',
                    'title' => 'Histori Stok Teknisi',
                    'description' => 'Riwayat mutasi stok part masuk & keluar pada tas teknisi per bulan.',
                    'color' => 'teal',
                    'buttonIcon' => 'heroicon-m-printer',
                    'buttonLabel' => 'Cetak Histori Stok Teknisi',
                    'urlTemplate' => route('technician-stock.print', ['month' => '__B__', 'year' => '__T__']),
                ],
            ],

            'Mesin & Instalasi' => [
                [
                    'icon' => '📦',
                    'title' => 'Laporan Type Mesin',
                    'description' => 'Sebaran unit per wilayah dan tipe mesin.',
                    'color' => 'primary',
                    'buttonIcon' => 'heroicon-m-printer',
                    'buttonLabel' => 'Cetak Alokasi',
                    'href' => route('cetak.alokasi'),
                ],
                [
                    'icon' => '🏢',
                    'title' => 'Laporan Rekap Tipe Model Mesin Per Customer',
                    'description' => 'Matriks jumlah total unit aktif dari seri XX, YY, ZZ di setiap lokasi customer.',
                    'color' => 'success',
                    'buttonIcon' => 'heroicon-m-rectangle-group',
                    'buttonLabel' => 'Cetak Rekap Seri Customer',
                    'href' => route('report.rekap-mesin'),
                ],
                [
                    'icon' => '✨',
                    'title' => 'Pemasangan Baru',
                    'description' => 'Laporan unit mesin fotokopi yang baru dipasang di lokasi customer.',
                    'color' => 'blue',
                    'buttonIcon' => 'heroicon-m-printer',
                    'buttonLabel' => 'Cetak Pemasangan Baru',
                    'urlTemplate' => route('cetak.pemasangan', ['bulan' => '__B__', 'tahun' => '__T__']),
                ],
                [
                    'icon' => '🔄',
                    'title' => 'Rekap Laporan Penukaran Unit Mesin',
                    'description' => 'Riwayat pergantian unit mesin di lokasi customer.',
                    'color' => 'success',
                    'buttonIcon' => 'heroicon-m-arrows-right-left',
                    'buttonLabel' => 'Cetak Swap',
                    'urlTemplate' => route('cetak.swap', ['bulan' => '__B__', 'tahun' => '__T__']),
                ],
                [
                    'icon' => '⬆️',
                    'title' => 'Rekap Tarik Mesin',
                    'description' => 'Laporan rekapitulasi riwayat penarikan unit mesin.',
                    'color' => 'warning',
                    'buttonIcon' => 'heroicon-m-printer',
                    'buttonLabel' => 'Cetak Tarik Mesin',
                    'urlTemplate' => route('withdrawal.rekap', ['month' => '__B__', 'year' => '__T__']),
                ],
                [
                    'icon' => '📦',
                    'title' => 'Laporan Stok Mesin Gudang',
                    'description' => 'Laporan ketersediaan unit di gudang DGG (Ready & Refurbish).',
                    'color' => 'blue',
                    'buttonIcon' => 'heroicon-m-printer',
                    'buttonLabel' => 'Cetak Stok Gudang',
                    'href' => route('cetak.stok-gudang'),
                ],
                [
                    'icon' => '📊',
                    'title' => 'Stok Gudang (Excel)',
                    'description' => 'Rekap stok mesin Photo Copy & Dispenser RO dalam format Excel.',
                    'color' => 'warning',
                    'buttonIcon' => 'heroicon-m-table-cells',
                    'buttonLabel' => 'Download Excel',
                    'href' => route('cetak.stok-gudang.excel'),
                ],
            ],

            'Kinerja & Performance' => [
                [
                    'icon' => '🔄',
                    'title' => 'Laporan Performance Rayon',
                    'description' => 'Presentase performance rayon.',
                    'color' => 'fuchsia',
                    'buttonIcon' => 'heroicon-m-arrows-right-left',
                    'buttonLabel' => 'Cetak Performance Rayon',
                    'urlTemplate' => route('print.performance-rayon', ['month' => '__B__', 'year' => '__T__']),
                ],
                [
                    'icon' => '📋',
                    'title' => 'Laporan Service Log Teknisi Perbulan',
                    'description' => 'Laporan rekapitulasi riwayat aktivitas kunjungan servis teknisi harian.',
                    'color' => 'info',
                    'buttonIcon' => 'heroicon-m-printer',
                    'buttonLabel' => 'Cetak Service Log',
                    'urlTemplate' => route('rekap.horizontal', ['bulan' => '__B__', 'tahun' => '__T__']),
                ],
                [
                    'icon' => '👷',
                    'title' => 'Kinerja Teknisi Per Bulan',
                    'description' => 'Total kunjungan & breakdown tipe servis per teknisi.',
                    'color' => 'teal',
                    'buttonIcon' => 'heroicon-m-printer',
                    'buttonLabel' => 'Cetak Kinerja Teknisi',
                    'urlTemplate' => route('print.tech-performance', ['month' => '__B__', 'year' => '__T__']),
                ],
            ],
        ];
    }
}
