<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">📦 Laporan Type Mesin </h3>
            <p class="text-sm text-gray-500 mb-4">Sebaran unit per wilayah dan tipe mesin.</p>
            <x-filament::button tag="a" href="{{ route('cetak.alokasi') }}" target="_blank" icon="heroicon-m-printer" color="info">
                Cetak Alokasi
            </x-filament::button>
        </div>

        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">✨  Pemasangan Baru</h3>
            <p class="text-sm text-gray-500 mb-4">Unit yang baru dipasang (Filter per bulan).</p>
            <x-filament::button tag="a" href="{{ route('filament.admin.resources.machines.index') }}" icon="heroicon-m-magnifying-glass">
                Ke Menu Mesin
            </x-filament::button>
        </div>

        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">🔄 Laporan Tukar Mesin</h3>
            <p class="text-sm text-gray-500 mb-4">Riwayat pergantian unit di lokasi.</p>
            <x-filament::button tag="a" href="{{ route('cetak.swap') }}" target="_blank" icon="heroicon-m-arrows-right-left" color="warning">
                Cetak Swap
            </x-filament::button>
        </div>

        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">⚙️ Laporan Rekap Pemakaian Sparepart Perbulan</h3>
            <p class="text-sm text-gray-500 mb-4">Daftar sparepart yang keluar/diganti per bulan.</p>
            <x-filament::button tag="a" href="{{ route('filament.admin.resources.spareparts.index') }}" icon="heroicon-m-cog-6-tooth" color="danger">
                Ke Menu Sparepart
            </x-filament::button>
        </div>

        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">📋Laporan Service Log Perbulan</h3>
            <p class="text-sm text-gray-500 mb-4">Laporan aktivitas teknisi harian/bulanan.</p>
            <x-filament::button tag="a" href="{{ route('filament.admin.resources.service-logs.index') }}" icon="heroicon-m-wrench-screwdriver" color="success">
                Ke Service Log
            </x-filament::button>
        </div>
    <!-- Card: Stok Mesin Gudang -->
<div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
    <h3 class="text-lg font-bold mb-2">📦Laporan Stok Mesin Gudang</h3>
    <p class="text-sm text-gray-500 mb-4">Laporan ketersediaan unit di gudang DGG (Ready & Refurbish).</p>
    <x-filament::button 
        tag="a" 
        href="{{ route('cetak.stok-gudang') }}" 
        target="_blank"
        icon="heroicon-m-printer" 
        color="danger">
        Cetak Stok
    </x-filament::button>
</div>

<div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
    <h3 class="text-lg font-bold mb-2">⚙️ Rekap Saldo Sparepart</h3>
    <p class="text-sm text-gray-500 mb-4">Laporan mutasi barang masuk & keluar per bulan.</p>
    
    <div class="flex gap-2">
        <x-filament::button 
            tag="a" 
            href="{{ route('cetak.rekap-sparepart', ['bulan' => date('m'), 'tahun' => date('Y')]) }}" 
            target="_blank"
            icon="heroicon-m-printer" 
            color="warning">
            Cetak Bulan Ini
        </x-filament::button>
    </div>
</div>
    
    </div>
</x-filament-panels::page>