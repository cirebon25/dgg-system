<x-filament-panels::page>

    {{-- MODAL PILIH BULAN (dipakai semua tombol) --}}
    <div id="modal-bulan" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4">
            <h2 class="text-lg font-bold mb-4" id="modal-title">Pilih Periode</h2>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Bulan</label>
                    <select id="modal-month"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm">
                        <option value="01">Januari</option>
                        <option value="02">Februari</option>
                        <option value="03">Maret</option>
                        <option value="04">April</option>
                        <option value="05">Mei</option>
                        <option value="06">Juni</option>
                        <option value="07">Juli</option>
                        <option value="08">Agustus</option>
                        <option value="09">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tahun</label>
                    <select id="modal-year"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm">
                        @for ($y = now()->year; $y >= 2024; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button onclick="tutupModal()"
                    class="flex-1 px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700">
                    Batal
                </button>
                <button onclick="cetakDariModal()"
                    class="flex-1 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-bold hover:bg-blue-700">
                    🖨️ Cetak
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Laporan Performance Rayon --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h5 class="text-lg font-bold mb-2">🔄 Laporan Performance Rayon</h5>
            <p class="text-sm text-gray-500 mb-4">Presentase performance rayon.</p>
            <x-filament::button onclick="bukaModal('print.performance-rayon', 'Performance Rayon', 'month', 'year')"
                icon="heroicon-m-arrows-right-left" color="fuchsia">
                Cetak Performance Rayon
            </x-filament::button>
        </div>

        {{-- 1. Laporan Type Mesin (tidak perlu bulan) --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">📦 Laporan Type Mesin</h3>
            <p class="text-sm text-gray-500 mb-4">Sebaran unit per wilayah dan tipe mesin.</p>
            <x-filament::button tag="a" href="{{ route('cetak.alokasi') }}" target="_blank"
                icon="heroicon-m-printer" color="primary">
                Cetak Alokasi
            </x-filament::button>
        </div>

        {{-- SISIPAN BARU: Laporan Rekap Seri Mesin Per Customer (tidak perlu bulan) --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">🏢 Laporan Rekap Seri Mesin Per Customer</h3>
            <p class="text-sm text-gray-500 mb-4">Matriks jumlah total unit aktif dari seri XX, YY, ZZ di setiap lokasi
                customer.</p>
            <x-filament::button tag="a" href="{{ route('report.rekap-mesin') }}" target="_blank"
                icon="heroicon-m-rectangle-group" color="success">
                Cetak Rekap Seri Customer
            </x-filament::button>
        </div>

        {{-- 2. Pemasangan Baru --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">✨ Pemasangan Baru</h3>
            <p class="text-sm text-gray-500 mb-4">Laporan unit mesin fotokopi yang baru dipasang di lokasi customer.</p>
            <x-filament::button onclick="bukaModal('cetak.pemasangan', 'Pemasangan Baru', 'bulan', 'tahun')"
                icon="heroicon-m-printer" color="blue">
                Cetak Pemasangan Baru
            </x-filament::button>
        </div>

        {{-- 3. Laporan Tukar Mesin --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h5 class="text-lg font-bold mb-2">🔄 Rekap Laporan Penukaran Unit Mesin</h5>
            <p class="text-sm text-gray-500 mb-4">Riwayat pergantian unit mesin di lokasi customer.</p>
            <x-filament::button onclick="bukaModal('cetak.swap', 'Rekap Swap Mesin', 'bulan', 'tahun')"
                icon="heroicon-m-arrows-right-left" color="success">
                Cetak Swap
            </x-filament::button>
        </div>

        {{-- 4. Rekap Pemakaian Sparepart --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">⚙️ Laporan Rekap Pemakaian Sparepart Teknisi Perbulan</h3>
            <p class="text-sm text-gray-500 mb-4">Daftar item terpakai oleh teknisi lapangan.</p>
            <x-filament::button
                onclick="bukaModal('cetak.rekap-sparepart', 'Rekap Sparepart Teknisi', 'bulan', 'tahun')"
                icon="heroicon-m-printer" color="danger">
                Cetak Rekap Sparepart Teknisi
            </x-filament::button>
        </div>

        {{-- 5. Laporan Service Log --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">📋 Laporan Service Log Teknisi Perbulan</h3>
            <p class="text-sm text-gray-500 mb-4">Laporan rekapitulasi riwayat aktivitas kunjungan servis teknisi
                harian.</p>
            <x-filament::button onclick="bukaModal('rekap.horizontal', 'Laporan Service Log', 'bulan', 'tahun')"
                icon="heroicon-m-printer" color="info">
                Cetak Service Log
            </x-filament::button>
        </div>

        {{-- 6. Laporan Stok Mesin Gudang (tidak perlu bulan) --}}
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
            <x-filament::button onclick="bukaModal('saldo-sparepart', 'Rekap Saldo Sparepart', 'bulan', 'tahun')"
                icon="heroicon-m-printer" color="indigo">
                Cetak Saldo Sparepart
            </x-filament::button>
        </div>

        {{-- 8. Rekap Tarik Mesin --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">⬆️ Rekap Tarik Mesin</h3>
            <p class="text-sm text-gray-500 mb-4">Laporan rekapitulasi riwayat penarikan unit mesin.</p>
            <x-filament::button onclick="bukaModal('withdrawal.rekap', 'Rekap Tarik Mesin', 'month', 'year')"
                icon="heroicon-m-printer" color="warning">
                Cetak Tarik Mesin
            </x-filament::button>
        </div>

        {{-- 9. Kinerja Teknisi Per Bulan --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">👷 Kinerja Teknisi Per Bulan</h3>
            <p class="text-sm text-gray-500 mb-4">Total kunjungan & breakdown tipe servis per teknisi.</p>
            <x-filament::button onclick="bukaModal('print.tech-performance', 'Kinerja Teknisi', 'month', 'year')"
                icon="heroicon-m-printer" color="teal">
                Cetak Kinerja Teknisi
            </x-filament::button>
        </div>

        {{-- 10. Kartu Stok Semua Teknisi --}}
        <div class="p-6 bg-white border rounded-xl shadow-sm dark:bg-gray-800">
            <h3 class="text-lg font-bold mb-2">💼 Kartu Stok Semua Teknisi</h3>
            <p class="text-sm text-gray-500 mb-4">Saldo part yang sedang dibawa oleh seluruh teknisi lapangan.</p>
            <x-filament::button tag="a" href="{{ route('cetak.kartu-stok-semua') }}" target="_blank"
                icon="heroicon-m-printer" color="violet">
                Cetak Kartu Stok Semua
            </x-filament::button>
        </div>

    </div>

    <script>
        // Nama parameter query string tiap route berbeda, simpan di sini
        let modalRoute = '';
        let modalParamBulan = 'bulan';
        let modalParamTahun = 'tahun';

        // Set bulan & tahun default ke bulan berjalan
        const bulanSekarang = String(new Date().getMonth() + 1).padStart(2, '0');
        const tahunSekarang = String(new Date().getFullYear());

        function bukaModal(routeName, judul, paramBulan, paramTahun) {
            modalRoute = routeName;
            modalParamBulan = paramBulan;
            modalParamTahun = paramTahun;

            document.getElementById('modal-title').innerText = '🖨️ Cetak ' + judul;
            document.getElementById('modal-month').value = bulanSekarang;
            document.getElementById('modal-year').value = tahunSekarang;

            const modal = document.getElementById('modal-bulan');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function tutupModal() {
            const modal = document.getElementById('modal-bulan');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Mapping nama route ke URL Laravel (digenerate server-side)
        const routeMap = {
            'cetak.pemasangan': "{{ route('cetak.pemasangan', ['bulan' => '__B__', 'tahun' => '__T__']) }}",
            'cetak.rekap-sparepart': "{{ route('cetak.rekap-sparepart', ['bulan' => '__B__', 'tahun' => '__T__']) }}",
            'rekap.horizontal': "{{ route('rekap.horizontal', ['bulan' => '__B__', 'tahun' => '__T__']) }}",
            'saldo-sparepart': "{{ route('saldo-sparepart', ['bulan' => '__B__', 'tahun' => '__T__']) }}",
            'withdrawal.rekap': "{{ route('withdrawal.rekap', ['month' => '__B__', 'year' => '__T__']) }}",
            'print.tech-performance': "{{ route('print.tech-performance', ['month' => '__B__', 'year' => '__T__']) }}",
            'sparepart.report.outflow': "{{ route('sparepart.report.outflow', ['month' => '__B__', 'year' => '__T__']) }}",
            'print.performance-rayon': "{{ route('print.performance-rayon', ['month' => '__B__', 'year' => '__T__']) }}",
            'cetak.swap': "{{ route('cetak.swap', ['bulan' => '__B__', 'tahun' => '__T__']) }}",
        };

        function cetakDariModal() {
            const bulan = document.getElementById('modal-month').value;
            const tahun = document.getElementById('modal-year').value;

            let url = routeMap[modalRoute];
            if (!url) {
                alert('Route tidak ditemukan!');
                return;
            }

            url = url.replace('__B__', bulan).replace('__T__', tahun);
            window.open(url, '_blank');
            tutupModal();
        }

        // Tutup modal jika klik backdrop
        document.getElementById('modal-bulan').addEventListener('click', function(e) {
            if (e.target === this) tutupModal();
        });
    </script>

</x-filament-panels::page>
