<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- 1. Laporan Type Mesin --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">📦 Laporan Type Mesin </h3>
            <p class="text-sm text-gray-500 mb-4">Sebaran unit per wilayah dan tipe mesin.</p>
            <x-filament::button tag="a" href="{{ route('cetak.alokasi') }}" target="_blank" icon="heroicon-m-printer"
                color="primary">
                Cetak Alokasi
            </x-filament::button>
        </div>

        {{-- 2. Pemasangan Baru (REVISI: LANGSUNG CETAK) --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">✨ Pemasangan Baru</h3>
            <p class="text-sm text-gray-500 mb-4">Laporan unit mesin fotokopi yang baru dipasang di lokasi customer.</p>
            <x-filament::button tag="a"
                href="{{ route('cetak.pemasangan', ['bulan' => date('m'), 'tahun' => date('Y')]) }}" target="_blank"
                icon="heroicon-m-printer" color="secondary">
                Cetak Pemasangan Baru
            </x-filament::button>
        </div>

        {{-- 3. Laporan Tukar Mesin --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h5 class="text-lg font-bold mb-2">🔄 Rekap Laporan Penukaran Unit Mesin</h5>
            <p class="text-sm text-gray-500 mb-4">Riwayat pergantian unit mesin di lokasi customer.</p> <br>
            <x-filament::button tag="a" href="{{ route('cetak.swap') }}" target="_blank"
                icon="heroicon-m-arrows-right-left" color="success">
                Cetak Swap
            </x-filament::button>
        </div>

        {{-- 4. Rekap Pemakaian Sparepart Perbulan (REVISI: LANGSUNG CETAK) --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h1 class="text-lg font-bold mb-2">⚙️ Laporan Rekap Pemakaian Sparepart Teknisi Perbulan</h1>
            <p class="text-sm text-gray-500 mb-4">Daftar item terpakai oleh teknisi lapangan.
            </p>
            <x-filament::button tag="a"
                href="{{ route('cetak.rekap-sparepart', ['bulan' => date('m'), 'tahun' => date('Y')]) }}"
                target="_blank" icon="heroicon-m-printer" color="danger">
                Cetak Rekap Tparepart Teknisi
            </x-filament::button>
        </div>

        {{-- 5. Laporan Service Log Perbulan (REVISI: LANGSUNG CETAK) --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">📋 Laporan Service Log Perbulan</h3>
            <p class="text-sm text-gray-500 mb-4">Laporan rekapitulasi riwayat aktivitas kunjungan servis teknisi
                harian.</p>
            <x-filament::button tag="a"
                href="{{ route('rekap.horizontal', ['bulan' => date('m'), 'tahun' => date('Y')]) }}" target="_blank"
                icon="heroicon-m-printer" color="info">
                Cetak Service Log
            </x-filament::button>
        </div>

        {{-- 6. Laporan Stok Mesin Gudang --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">📦 Laporan Stok Mesin Gudang</h3>
            <p class="text-sm text-gray-500 mb-4">Laporan ketersediaan unit di gudang DGG (Ready & Refurbish).</p>
            <x-filament::button tag="a" href="{{ route('cetak.stok-gudang') }}" target="_blank"
                icon="heroicon-m-printer" color="blue">
                Cetak Stok Gudang
            </x-filament::button>
        </div>

        {{-- 7. Rekap Saldo Sparepart --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">⚙️ Rekap Saldo Sparepart</h3>
            <p class="text-sm text-gray-500 mb-4">Laporan mutasi kuantitas barang masuk & keluar per bulan.</p>
            <x-filament::button tag="a"
                href="{{ route('saldo-sparepart', ['bulan' => date('m'), 'tahun' => date('Y')]) }}" target="_blank"
                icon="heroicon-m-printer" color="indigo">
                Cetak Bulan Ini
            </x-filament::button>
        </div>

    </div>
</x-filament-panels::page>
