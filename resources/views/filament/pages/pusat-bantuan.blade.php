<x-filament-panels::page>
    <div class="space-y-8">
        
        <div class="p-6 bg-gradient-to-r from-amber-500 to-yellow-600 rounded-2xl shadow-md text-white">
            <div class="flex items-center gap-4">
                <span class="text-4xl"></span>
                <div>
                    <h2 class="text-2xl font-black tracking-wide">PANDUAN OPERASIONAL UTAMA</h2>
                    <p class="text-sm text-amber-100 font-medium mt-1">PT DINAMIKA GLOBAL GEMILANG (DGG SYSTEM)</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <span class="p-1.5 bg-amber-100 dark:bg-amber-950 rounded-lg text-amber-600">🔄</span> 
                Urutan Alur Kerja Penggunaan Sistem (Rekomendasi)
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex gap-3">
                    <span class="font-black text-amber-500 text-xl">01</span>
                    <div>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Input Data Master</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Isi data Customer, Rayon, Tipe, Mesin, Part, & Teknisi.</p>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex gap-3">
                    <span class="font-black text-amber-500 text-xl">02</span>
                    <div>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Buat Deployment</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Pasang mesin pertama kali agar unit terikat ke Customer.</p>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex gap-3">
                    <span class="font-black text-amber-500 text-xl">03</span>
                    <div>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Input Service Log</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Catat servis harian harian teknisi & pemakaian sparepart.</p>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex gap-3">
                    <span class="font-black text-amber-500 text-xl">04</span>
                    <div>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Pantau Stok Tas</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Cek sisa saldo di Technician Stock & riwayat mutasinya.</p>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex gap-3">
                    <span class="font-black text-amber-500 text-xl">05</span>
                    <div>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Logistik Gudang</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Gunakan Part Borrowing / Return untuk transaksi part teknisi.</p>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex gap-3">
                    <span class="font-black text-amber-500 text-xl">06</span>
                    <div>
                        <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Sparepart Entry</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Gunakan menu ini jika ada barang baru masuk ke gudang pusat.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            
            <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-extrabold text-amber-600 flex items-center gap-2">
                        <span>📂</span> 1. KELOMPOK DATA MASTER
                    </h3>
                    <span class="px-2.5 py-1 text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 rounded-full">Fondasi Data</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800">
                        <span class="text-xs font-bold px-2 py-0.5 bg-blue-100 text-blue-800 rounded-md">Customer</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2"><b>Fungsi:</b> Kelola data pelanggan (Alamat, Kota, Kontak). <span class="text-danger-600">*Input sebelum deployment.</span></p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800">
                        <span class="text-xs font-bold px-2 py-0.5 bg-blue-100 text-blue-800 rounded-md">Rayon</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2"><b>Fungsi:</b> Kelompok wilayah / area kerja. Dipakai sekali di awal untuk mempermudah filter statistik laporan.</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800">
                        <span class="text-xs font-bold px-2 py-0.5 bg-blue-100 text-blue-800 rounded-md">Type Model</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2"><b>Fungsi:</b> Master tipe mesin pabrikan (misal: iR Advance, dll). Digunakan sebelum menginput Serial Number mesin.</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800">
                        <span class="text-xs font-bold px-2 py-0.5 bg-blue-100 text-blue-800 rounded-md">Machine</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2"><b>Fungsi:</b> Gudang Serial Number unit. Pastikan status diisi <b>"Ready"</b> jika mesin siap dikirim untuk disewakan.</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800">
                        <span class="text-xs font-bold px-2 py-0.5 bg-blue-100 text-blue-800 rounded-md">Technician</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2"><b>Fungsi:</b> Manajemen data teknisi resmi DGG pelaksana lapangan untuk penugasan pasang / servis.</p>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-extrabold text-emerald-600 flex items-center gap-2">
                        <span>🛠️</span> 2. KELOMPOK TRANSAKSI & SERVIS
                    </h3>
                    <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 rounded-full">Aktivitas Lapangan</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-emerald-50/40 dark:bg-gray-900 rounded-xl border border-emerald-100 dark:border-gray-800">
                        <span class="text-xs font-bold px-2 py-0.5 bg-emerald-600 text-white rounded-md">Deployment (Pasang Mesin)</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                            <b>Cara Pakai:</b> Hubungkan Customer + SN Mesin + Teknisi. Isi nilai Voltase, Tanggal, Counter Awal (BW & Color), serta Sparepart Tambahan bawaan unit (japa-jaga). 
                            <br><span class="text-emerald-600 font-medium">*Mengubah status mesin otomatis jadi Rented.</span>
                        </p>
                    </div>
                    <div class="p-4 bg-emerald-50/40 dark:bg-gray-900 rounded-xl border border-emerald-100 dark:border-gray-800">
                        <span class="text-xs font-bold px-2 py-0.5 bg-emerald-600 text-white rounded-md">Service Log (Input Servis)</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                            <b>Cara Pakai:</b> Pilih SN Mesin (Customer & Counter lama muncul otomatis). Tentukan Tipe Kunjungan (RN, CM, RM, RR, TN), isi counter terkini, kendala kerusakan, solusi perbaikan, & sparepart yang diganti di tas teknisi.
                            <br><span class="text-primary-600 font-medium">*Dilengkapi cetak nota thermal & rekap bulanan langsung di halaman.</span>
                        </p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 md:col-span-2">
                        <span class="text-xs font-bold px-2 py-0.5 bg-emerald-600 text-white rounded-md">Sparepart</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2"><b>Fungsi:</b> Katalog induk nama suku cadang, update harga/deskripsi, dan pengecekan jumlah saldo total gudang pusat DGG.</p>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-extrabold text-purple-600 flex items-center gap-2">
                        <span>📦</span> 3. KELOMPOK LOGISTIK & MANAGEMENT STOK
                    </h3>
                    <span class="px-2.5 py-1 text-xs font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-400 rounded-full">Alur Barang</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="p-4 bg-purple-50/40 dark:bg-gray-900 rounded-xl border border-purple-100 dark:border-gray-800">
                        <span class="text-xs font-bold px-2 py-0.5 bg-purple-600 text-white rounded-md">Sparepart Entry</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2"><b>Fungsi:</b> Pintu masuk stok barang baru dari supplier. Berfungsi menambah nominal angka stok utama di gudang pusat.</p>
                    </div>
                    <div class="p-4 bg-purple-50/40 dark:bg-gray-900 rounded-xl border border-purple-100 dark:border-gray-800">
                        <span class="text-xs font-bold px-2 py-0.5 bg-purple-600 text-white rounded-md">Part Borrowing (Pinjam Part)</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2"><b>Fungsi:</b> Teknisi bon barang dari gudang. <span class="font-semibold text-purple-700">Mendukung input hingga 10+ barang sekaligus!</span> Stok gudang langsung terpotong, dan saldo tas teknisi langsung bertambah otomatis.</p>
                    </div>
                    <div class="p-4 bg-purple-50/40 dark:bg-gray-900 rounded-xl border border-purple-100 dark:border-gray-800">
                        <span class="text-xs font-bold px-2 py-0.5 bg-purple-600 text-white rounded-md">Part Return (Retur Barang)</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2"><b>Fungsi:</b> Pengembalian part sisa/rusak dari lapangan. Mengurangi saldo tas teknisi dan otomatis membalikkan/menambah stok ke gudang utama kembali.</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold px-2 py-0.5 bg-gray-600 text-white rounded-md">Technician Stock</span>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2"><b>Fungsi:</b> Halaman monitor saldo sisa isi tas masing-masing teknisi saat ini. *(Laporan murni, tidak bisa diinput manual).*</p>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 flex flex-col justify-between lg:col-span-2">
                        <div>
                            <span class="text-xs font-bold px-2 py-0.5 bg-gray-600 text-white rounded-md">Technician Stock History</span>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2"><b>Fungsi:</b> Buku jurnal audit kelola stok. Berfungsi melacak histori pergerakan barang keluar-masuk di dalam tas teknisi agar transparan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gray-900 dark:bg-black rounded-3xl text-white border border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl">
            <div class="space-y-2 text-center sm:text-left">
                <h4 class="text-xl font-black text-amber-400 flex items-center justify-center sm:justify-start gap-2">
                    <span></span> PUSAT BANTUAN TEKNIS DGG SYSTEM
                </h4>
                <p class="text-xs text-gray-400 max-w-xl">Jika tim admin gudang atau teknisi mengalami error validasi data, kendala cetak nota, atau butuh persetujuan khusus akun, silakan hubungi WhatsApp Owner:</p>
            </div>
            <div class="bg-gray-800/80 p-4 rounded-2xl border border-gray-700 min-w-[260px] text-center backdrop-blur-sm shadow-md">
                <span class="text-[10px] font-bold tracking-widest text-amber-500 uppercase">WhatsApp Emergency Call</span>
                <h3 class="text-xl font-black tracking-wide text-white mt-1">RUDI</h3>
                <p class="text-xl font-mono font-bold text-green-400 mt-0.5">📞 0895636675848</p>
            </div>
        </div>

        <p class="text-center text-[10px] text-gray-400 font-medium">DGG System v3.0 • Cirebon • Hak Cipta Dilindungi PT. Dinamika Global Gemilang</p>
    </div>
</x-filament-panels::page>