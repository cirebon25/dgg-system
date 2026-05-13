<x-filament-panels::page>
    <div class="space-y-8"> 
        
        {{-- =========================================================
             BAGIAN 1: DATA AKTIF (ETALASE DEPAN)
             ========================================================= --}}
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-success-700 flex items-center gap-2">
                    🚀 Ringkasan Data Aktif (Saat Ini)
                </h3>
                
                {{-- Tombol Export Data Aktif --}}
                <x-filament::button 
                    wire:click="downloadActiveExcel" 
                    icon="heroicon-o-arrow-down-tray" 
                    color="info"
                    size="sm">
                    Export Data Aktif
                </x-filament::button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-filament::section>
                    <x-slot name="heading">Customer Aktif</x-slot>
                    <div class="text-2xl font-bold text-success-600">{{ $activeCustomer }}</div>
                </x-filament::section>

                <x-filament::section>
                    <x-slot name="heading">Unit Terpasang</x-slot>
                    <div class="text-2xl font-bold text-success-600">{{ $activeMachine }}</div>
                </x-filament::section>

                <x-filament::section>
                    <x-slot name="heading">Service Log Aktif</x-slot>
                    <div class="text-2xl font-bold text-success-600">{{ $activeServiceLog }}</div>
                </x-filament::section>

                <x-filament::section>
                    <x-slot name="heading">Deployment Aktif</x-slot>
                    <div class="text-2xl font-bold text-success-600">{{ $activeDeployment }}</div>
                </x-filament::section>
            </div>
        </div>

        <hr class="border-gray-300 dark:border-gray-700">

        {{-- =========================================================
             BAGIAN 2: DATA ARSIP (GUDANG BELAKANG)
             ========================================================= --}}
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-danger-700 flex items-center gap-2">
                    📂 Gudang Arsip (Data Terhapus)
                </h3>
                
                {{-- Tombol Download Excel Arsip --}}
                <x-filament::button 
                    wire:click="downloadExcel" 
                    icon="heroicon-o-document-arrow-down" 
                    color="success"
                    size="sm">
                    Download Excel Arsip
                </x-filament::button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <x-filament::section>
                    <x-slot name="heading">Arsip Log Servis</x-slot>
                    <div class="text-2xl font-bold text-danger-600">{{ $countServiceLog }}</div>
                </x-filament::section>
                
                <x-filament::section>
                    <x-slot name="heading">Arsip Unit Mesin</x-slot>
                    <div class="text-2xl font-bold text-primary-600">{{ $countMachine }}</div>
                </x-filament::section>

                <x-filament::section>
                    <x-slot name="heading">Arsip Deployment</x-slot>
                    <div class="text-2xl font-bold text-warning-600">{{ $countDeployment }}</div>
                </x-filament::section>
            </div>

            {{-- TABEL DETAIL CUSTOMER TERARSIP --}}
            <x-filament::section>
                <x-slot name="heading">Detail Riwayat Customer dalam Arsip</x-slot>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800 text-xs uppercase">
                                <th class="p-3 border">Nama Customer</th>
                                <th class="p-3 border text-center">Total Unit</th>
                                <th class="p-3 border">Daftar Serial Number (SN)</th>
                                <th class="p-3 border text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($archivedCustomers as $cust)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                                    <td class="p-3 border font-bold">{{ $cust->nama_customer }}</td>
                                    <td class="p-3 border text-center">
                                        <span class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded-full text-xs font-semibold">
                                            {{ $cust->machines()->withTrashed()->count() }} Unit
                                        </span>
                                    </td>
                                    <td class="p-3 border">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($cust->machines()->withTrashed()->get() as $m)
                                                <span class="text-[10px] bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800">
                                                    {{ $m->serial_number }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="p-3 border text-center">
                                        <x-filament::button 
                                            href="/admin/customers?tableFilters[trashed][value]=only" 
                                            tag="a" 
                                            size="sm" 
                                            color="warning"
                                            icon="heroicon-m-arrow-top-right-on-square">
                                            Buka Gudang
                                        </x-filament::button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-gray-500 italic">
                                        Belum ada data customer di gudang arsip, Boss!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>