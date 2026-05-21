<x-filament-panels::page>
    <div class="space-y-6">

        {{-- ===== HERO HEADER ===== --}}
        <div
            class="relative overflow-hidden p-6 bg-gradient-to-br from-amber-500 via-yellow-500 to-orange-500 rounded-2xl shadow-lg text-white">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2">
            </div>
            <div class="absolute bottom-0 left-1/3 w-32 h-32 bg-white/5 rounded-full translate-y-1/2"></div>
            <div class="relative flex items-center gap-5">
                <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-sm shadow-inner text-4xl select-none">📋</div>
                <div>
                    <p class="text-xs font-bold tracking-widest text-amber-100 uppercase mb-1">Dokumentasi Sistem</p>
                    <h2 class="text-2xl font-black tracking-wide leading-tight">Panduan Operasional Utama</h2>
                    <p class="text-sm text-amber-100 font-medium mt-0.5">PT Dinamika Global Gemilang — DGG System</p>
                </div>
            </div>
        </div>

        {{-- ===== ALUR KERJA ===== --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-5">
                <span class="p-2 bg-amber-100 dark:bg-amber-950 rounded-xl text-xl">🔄</span>
                <div>
                    <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Alur Kerja yang Direkomendasikan
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Ikuti urutan berikut agar data sistem terhubung dengan benar
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                <div
                    class="group p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 hover:border-amber-300 hover:shadow-md transition-all duration-200 flex gap-3">
                    <span class="font-black text-amber-500 text-2xl leading-none">01</span>
                    <div>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Input Data Master</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">Lengkapi data Customer,
                            Rayon, Tipe Mesin, Sparepart, dan Teknisi terlebih dahulu sebagai fondasi sistem.</p>
                    </div>
                </div>

                <div
                    class="group p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 hover:border-amber-300 hover:shadow-md transition-all duration-200 flex gap-3">
                    <span class="font-black text-amber-500 text-2xl leading-none">02</span>
                    <div>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Buat Deployment</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">Pasang mesin ke lokasi
                            customer. Proses ini mengikat unit ke pelanggan dan mengubah status mesin menjadi
                            <b>Rented</b>.
                        </p>
                    </div>
                </div>

                <div
                    class="group p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 hover:border-amber-300 hover:shadow-md transition-all duration-200 flex gap-3">
                    <span class="font-black text-amber-500 text-2xl leading-none">03</span>
                    <div>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Catat Service Log</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">Rekam kunjungan servis
                            harian teknisi, termasuk counter terkini dan pemakaian sparepart dari tas.</p>
                    </div>
                </div>

                <div
                    class="group p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 hover:border-amber-300 hover:shadow-md transition-all duration-200 flex gap-3">
                    <span class="font-black text-amber-500 text-2xl leading-none">04</span>
                    <div>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Pantau Stok Teknisi</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">Periksa saldo sparepart
                            di tas masing-masing teknisi melalui menu <b>Technician Stock</b> dan riwayat mutasinya.</p>
                    </div>
                </div>

                <div
                    class="group p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 hover:border-amber-300 hover:shadow-md transition-all duration-200 flex gap-3">
                    <span class="font-black text-amber-500 text-2xl leading-none">05</span>
                    <div>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Kelola Logistik Gudang</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">Proses peminjaman
                            (<b>Borrowing</b>) dan pengembalian (<b>Return</b>) sparepart antara gudang pusat dan
                            teknisi lapangan.</p>
                    </div>
                </div>

                <div
                    class="group p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 hover:border-amber-300 hover:shadow-md transition-all duration-200 flex gap-3">
                    <span class="font-black text-amber-500 text-2xl leading-none">06</span>
                    <div>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Entri Barang Masuk</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">Gunakan <b>Sparepart
                                Entry</b> setiap ada pengadaan atau penerimaan barang baru dari supplier ke gudang
                            pusat.</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- ===== KELOMPOK MODUL ===== --}}
        <div class="space-y-4">

            {{-- DATA MASTER --}}
            <div
                class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-extrabold text-amber-600 flex items-center gap-2">
                        <span>📂</span> 1. Modul Data Master
                    </h3>
                    <span
                        class="px-3 py-1 text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 rounded-full border border-amber-200 dark:border-amber-800">Fondasi
                        Data</span>
                </div>
                <p class="text-xs text-gray-400 -mt-2">Semua modul di bawah wajib diisi sebelum memulai operasional
                    harian.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">

                    <div
                        class="p-4 bg-amber-50/50 dark:bg-gray-900 rounded-xl border border-amber-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                class="text-xs font-bold px-2 py-0.5 bg-amber-500 text-white rounded-md">Customer</span>
                            <span class="text-[10px] text-red-500 font-semibold">⚠ Input Pertama</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Kelola data pelanggan
                            meliputi nama instansi, alamat, kota, dan kontak. Harus diisi sebelum proses deployment.</p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-bold px-2 py-0.5 bg-blue-500 text-white rounded-md">Rayon</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Pengelompokan wilayah kerja
                            lapangan. Diatur sekali di awal untuk mempermudah filter laporan dan penugasan teknisi.</p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-bold px-2 py-0.5 bg-blue-500 text-white rounded-md">Type
                                Model</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Master katalog tipe mesin
                            pabrikan (contoh: iR Advance C3530, dll). Wajib diisi sebelum mendaftarkan Serial Number
                            mesin.</p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-bold px-2 py-0.5 bg-blue-500 text-white rounded-md">Machine</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Registrasi Serial Number
                            setiap unit mesin. Pastikan status disetel <b class="text-green-600">Ready</b> agar mesin
                            dapat dipilih saat deployment.</p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                class="text-xs font-bold px-2 py-0.5 bg-blue-500 text-white rounded-md">Technician</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Data resmi teknisi lapangan
                            DGG. Digunakan untuk penugasan pemasangan mesin dan pencatatan servis harian.</p>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                class="text-xs font-bold px-2 py-0.5 bg-blue-500 text-white rounded-md">Sparepart</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Katalog induk nama suku
                            cadang. Digunakan sebagai referensi di Service Log, Borrowing, dan Entry stok gudang.</p>
                    </div>

                </div>
            </div>

            {{-- TRANSAKSI & SERVIS --}}
            <div
                class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-extrabold text-emerald-600 flex items-center gap-2">
                        <span>🛠️</span> 2. Modul Transaksi & Servis
                    </h3>
                    <span
                        class="px-3 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 rounded-full border border-emerald-200 dark:border-emerald-800">Aktivitas
                        Lapangan</span>
                </div>
                <p class="text-xs text-gray-400 -mt-2">Modul utama untuk pencatatan operasional harian teknisi di
                    lapangan.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                    <div
                        class="p-4 bg-emerald-50/50 dark:bg-gray-900 rounded-xl border border-emerald-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                class="text-xs font-bold px-2 py-0.5 bg-emerald-600 text-white rounded-md">Deployment</span>
                            <span class="text-[10px] text-emerald-600 font-semibold">Pemasangan Unit</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Hubungkan Customer + Serial
                            Number Mesin + Teknisi dalam satu formulir. Isi voltase, tanggal pasang, counter awal BW &
                            Color, serta sparepart bawaan unit jika ada.</p>
                        <p class="text-[11px] text-emerald-600 font-semibold mt-2">✅ Status mesin otomatis berubah
                            menjadi Rented setelah disimpan.</p>
                    </div>

                    <div
                        class="p-4 bg-emerald-50/50 dark:bg-gray-900 rounded-xl border border-emerald-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-bold px-2 py-0.5 bg-emerald-600 text-white rounded-md">Service
                                Log</span>
                            <span class="text-[10px] text-emerald-600 font-semibold">Servis Harian</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Pilih Serial Number mesin —
                            data customer dan counter lama tampil otomatis. Tentukan tipe kunjungan (RN / CM / RM / RR /
                            TN), isi counter terkini, keluhan, solusi, dan sparepart yang digunakan.</p>
                        <p class="text-[11px] text-blue-500 font-semibold mt-2">🖨 Dilengkapi fitur cetak nota thermal &
                            rekap servis bulanan.</p>
                    </div>

                </div>
            </div>

            {{-- LOGISTIK & STOK --}}
            <div
                class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-extrabold text-purple-600 flex items-center gap-2">
                        <span>📦</span> 3. Modul Logistik & Manajemen Stok
                    </h3>
                    <span
                        class="px-3 py-1 text-xs font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-400 rounded-full border border-purple-200 dark:border-purple-800">Alur
                        Barang</span>
                </div>
                <p class="text-xs text-gray-400 -mt-2">Kelola pergerakan sparepart dari gudang pusat hingga ke tangan
                    teknisi secara terstruktur dan transparan.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">

                    <div
                        class="p-4 bg-purple-50/40 dark:bg-gray-900 rounded-xl border border-purple-100 dark:border-gray-800">
                        <div class="mb-2">
                            <span class="text-xs font-bold px-2 py-0.5 bg-purple-600 text-white rounded-md">Sparepart
                                Entry</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Pintu masuk resmi stok dari
                            supplier. Setiap entri akan menambah jumlah saldo sparepart di gudang pusat DGG.</p>
                    </div>

                    <div
                        class="p-4 bg-purple-50/40 dark:bg-gray-900 rounded-xl border border-purple-100 dark:border-gray-800">
                        <div class="mb-2">
                            <span class="text-xs font-bold px-2 py-0.5 bg-purple-600 text-white rounded-md">Part
                                Borrowing</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Teknisi mengambil sparepart
                            dari gudang. Mendukung <b class="text-purple-600">10+ item sekaligus</b> dalam satu
                            transaksi. Stok gudang terpotong otomatis dan saldo tas teknisi bertambah.</p>
                    </div>

                    <div
                        class="p-4 bg-purple-50/40 dark:bg-gray-900 rounded-xl border border-purple-100 dark:border-gray-800">
                        <div class="mb-2">
                            <span class="text-xs font-bold px-2 py-0.5 bg-purple-600 text-white rounded-md">Part
                                Return</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Pengembalian sparepart sisa
                            atau tidak terpakai dari lapangan. Saldo tas teknisi berkurang dan stok gudang pusat
                            dipulihkan secara otomatis.</p>
                    </div>

                    <div
                        class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-bold px-2 py-0.5 bg-gray-600 text-white rounded-md">Technician
                                Stock</span>
                            <span class="text-[10px] text-gray-400 font-semibold">Read-only</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Dasbor monitor saldo
                            sparepart yang sedang dibawa masing-masing teknisi saat ini. Bersifat laporan — tidak dapat
                            diinput manual.</p>
                    </div>

                    <div
                        class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 md:col-span-1 lg:col-span-2">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-bold px-2 py-0.5 bg-gray-600 text-white rounded-md">Technician
                                Stock History</span>
                            <span class="text-[10px] text-gray-400 font-semibold">Audit Trail</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">Buku jurnal lengkap seluruh
                            pergerakan sparepart masuk dan keluar per teknisi. Digunakan untuk audit, rekonsiliasi, dan
                            memastikan transparansi alur barang lapangan.</p>
                    </div>

                </div>
            </div>
        </div>

        {{-- ===== KONTAK BANTUAN ===== --}}
        <div
            class="relative overflow-hidden p-6 bg-gray-900 dark:bg-black rounded-2xl text-white border border-gray-800 shadow-xl">
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-amber-500/5 rounded-full -translate-y-1/3 translate-x-1/3 pointer-events-none">
            </div>
            <div class="relative flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="space-y-2 text-center sm:text-left">
                    <h4
                        class="text-lg font-black text-amber-400 flex items-center justify-center sm:justify-start gap-2">
                        🛟 Pusat Bantuan Teknis DGG System
                    </h4>
                    <p class="text-xs text-gray-400 leading-relaxed max-w-lg">Jika tim admin, gudang, atau teknisi
                        lapangan mengalami kendala validasi data, kesalahan cetak nota, atau membutuhkan otorisasi akun
                        khusus — segera hubungi Owner melalui WhatsApp di bawah ini.</p>
                    <div class="flex flex-wrap gap-2 pt-1">
                        <span
                            class="text-[10px] px-2 py-0.5 bg-gray-800 text-gray-400 rounded-full border border-gray-700">Error
                            Validasi</span>
                        <span
                            class="text-[10px] px-2 py-0.5 bg-gray-800 text-gray-400 rounded-full border border-gray-700">Kendala
                            Cetak Nota</span>
                        <span
                            class="text-[10px] px-2 py-0.5 bg-gray-800 text-gray-400 rounded-full border border-gray-700">Reset
                            Akses Akun</span>
                        <span
                            class="text-[10px] px-2 py-0.5 bg-gray-800 text-gray-400 rounded-full border border-gray-700">Persetujuan
                            Khusus</span>
                    </div>
                </div>
                <a href="https://wa.me/qr/4GIBMDZGZKKRJ1" target="_blank"
                    class="bg-gray-800 hover:bg-gray-700 transition-colors p-5 rounded-2xl border border-gray-700 min-w-[240px] text-center shadow-md cursor-pointer no-underline block">
                    <span class="text-[10px] font-bold tracking-widest text-amber-500 uppercase">WhatsApp — Hubungi
                        Langsung</span>
                    <h3 class="text-xl font-black tracking-wide text-white mt-1.5">RUDI</h3>
                    <p class="text-lg font-mono font-bold text-green-400 mt-0.5">📞 0895-6366-75848</p>
                    <span class="text-[10px] text-gray-500 mt-1 block">Klik untuk membuka WhatsApp</span>
                </a>
            </div>
        </div>

        {{-- ===== FOOTER ===== --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-1 px-1">
            <p class="text-[10px] text-gray-400">© 2025 PT Dinamika Global Gemilang. Hak Cipta Dilindungi.</p>
            <p class="text-[10px] text-gray-400">DGG System v3.0 — Cirebon, Jawa Barat</p>
        </div>

    </div>
</x-filament-panels::page>
