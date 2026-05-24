<x-filament-panels::page>
    <div class="space-y-6 antialiased text-slate-300 select-none bg-slate-950 p-2 rounded-2xl" x-data="{
        openGroup: 'master',
        currentTab: 'customer'
    }">

        {{-- ====================================================================== --}}
        {{-- UTAMA: HEADER PUSAT BANTUAN MODERN & HIDUP --}}
        {{-- ====================================================================== --}}
        <div class="relative overflow-hidden p-6 bg-slate-900 rounded-2xl text-white shadow-2xl border border-slate-800">
            <div class="absolute -top-10 -right-10 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none">
            </div>
            <div
                class="absolute -bottom-10 -left-10 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div
                        class="flex items-center justify-center w-14 h-14 bg-slate-800 rounded-2xl text-3xl shadow-inner border border-slate-700">
                        🛟</div>
                    <div>
                        <p class="text-[10px] font-extrabold tracking-widest text-indigo-400 uppercase">Dokumentasi
                            Operasional Resmi</p>
                        <h2 class="text-3xl font-extrabold tracking-tighter text-white">Buku Panduan Operasional DGG
                            System</h2>
                        <p class="text-xs text-slate-400 mt-1">Standard Operating Procedure (SOP) Berbasis Kolom Riil
                            Database</p>
                    </div>
                </div>
                <div
                    class="flex items-center gap-2.5 px-4 py-1.5 bg-slate-800 rounded-full text-xs text-slate-400 self-start md:self-center border border-slate-700 shadow-inner">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Simpul Distribusi: <span class="font-extrabold text-slate-100">Cirebon</span>
                </div>
            </div>
        </div>

        {{-- ====================================================================== --}}
        {{-- TATA LETAK UTAMA: GRID SIDEBAR NEON & KONTEN --}}
        {{-- ====================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

            {{-- 📁 SISI KIRI: SIDEBAR MENU PANDUAN NEON (3 KOLOM GRID) --}}
            <div
                class="lg:col-span-3 space-y-3 bg-slate-900/60 p-4 rounded-3xl border border-slate-800 shadow-lg backdrop-blur-sm">

                {{-- 📁 GRUP 1: MASTER DATA (INDIGO GLOW) --}}
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'master' ? '' : 'master')"
                        :class="openGroup === 'master' ? 'border-indigo-500/60 shadow-indigo-500/20 text-white' :
                            'border-slate-800 shadow-black/30 text-slate-400'"
                        class="w-full flex items-center justify-between px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl bg-slate-950/60 hover:bg-slate-900 transition-all border shadow-lg">
                        <div class="flex items-center gap-3">
                            <span class="text-indigo-400">📁</span> Kategori I: Master Data
                        </div>
                        <span x-text="openGroup === 'master' ? '▲' : '▼'" class="text-[9px] text-slate-500"></span>
                    </button>

                    <div x-show="openGroup === 'master'" x-collapse class="pl-3 space-y-1 pt-1.5">
                        <button @click="currentTab = 'customer'"
                            :class="currentTab === 'customer' ? 'bg-indigo-600 text-white font-bold shadow-indigo-600/30' :
                                'bg-slate-900/60 hover:bg-slate-800 text-slate-300'"
                            class="w-full flex items-center gap-3 px-4 py-2 text-[11px] rounded-lg transition-all text-left shadow-inner border border-slate-800 hover:border-indigo-800">
                            <span>👤</span> Customer (Instansi)
                        </button>
                        <button @click="currentTab = 'machine'"
                            :class="currentTab === 'machine' ? 'bg-indigo-600 text-white font-bold shadow-indigo-600/30' :
                                'bg-slate-900/60 hover:bg-slate-800 text-slate-300'"
                            class="w-full flex items-center gap-3 px-4 py-2 text-[11px] rounded-lg transition-all text-left shadow-inner border border-slate-800 hover:border-indigo-800">
                            <span>🖨️</span> Data Unit Mesin
                        </button>
                        <button @click="currentTab = 'technician'"
                            :class="currentTab === 'technician' ? 'bg-indigo-600 text-white font-bold shadow-indigo-600/30' :
                                'bg-slate-900/60 hover:bg-slate-800 text-slate-300'"
                            class="w-full flex items-center gap-3 px-4 py-2 text-[11px] rounded-lg transition-all text-left shadow-inner border border-slate-800 hover:border-indigo-800">
                            <span>🔧</span> Personel Teknisi
                        </button>
                        <button @click="currentTab = 'sparepart'"
                            :class="currentTab === 'sparepart' ? 'bg-indigo-600 text-white font-bold shadow-indigo-600/30' :
                                'bg-slate-900/60 hover:bg-slate-800 text-slate-300'"
                            class="w-full flex items-center gap-3 px-4 py-2 text-[11px] rounded-lg transition-all text-left shadow-inner border border-slate-800 hover:border-indigo-800">
                            <span>📦</span> Master Sparepart
                        </button>
                    </div>
                </div>

                {{-- 📁 GRUP 2: FIELD SERVIS (EMERALD GLOW) --}}
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'field' ? '' : 'field')"
                        :class="openGroup === 'field' ? 'border-emerald-500/60 shadow-emerald-500/20 text-white' :
                            'border-slate-800 shadow-black/30 text-slate-400'"
                        class="w-full flex items-center justify-between px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl bg-slate-950/60 hover:bg-slate-900 transition-all border shadow-lg">
                        <div class="flex items-center gap-3">
                            <span class="text-emerald-400">📁</span> Kategori II: Field Servis
                        </div>
                        <span x-text="openGroup === 'field' ? '▲' : '▼'" class="text-[9px] text-slate-500"></span>
                    </button>
                    <div x-show="openGroup === 'field'" x-collapse class="pl-3 space-y-1 pt-1.5">
                        <button @click="currentTab = 'customerinstal'"
                            :class="currentTab === 'customerinstal' ?
                                'bg-emerald-600 text-white font-bold shadow-emerald-600/30' :
                                'bg-slate-900/60 hover:bg-slate-800 text-slate-300'"
                            class="w-full flex items-center gap-3 px-4 py-2 text-[11px] rounded-lg transition-all text-left shadow-inner border border-slate-800 hover:border-emerald-800">
                            <span>🚀</span> Pemasangan Mesin (Instal)
                        </button>
                        <button @click="currentTab = 'servicelog'"
                            :class="currentTab === 'servicelog' ? 'bg-emerald-600 text-white font-bold shadow-emerald-600/30' :
                                'bg-slate-900/60 hover:bg-slate-800 text-slate-300'"
                            class="w-full flex items-center gap-3 px-4 py-2 text-[11px] rounded-lg transition-all text-left shadow-inner border border-slate-800 hover:border-emerald-800">
                            <span>🛠️</span> Input Servis Teknisi
                        </button>
                    </div>
                </div>

                {{-- 📁 GRUP 3: LOGISTIK (PURPLE GLOW) --}}
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'logistik' ? '' : 'logistik')"
                        :class="openGroup === 'logistik' ? 'border-purple-500/60 shadow-purple-500/20 text-white' :
                            'border-slate-800 shadow-black/30 text-slate-400'"
                        class="w-full flex items-center justify-between px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl bg-slate-950/60 hover:bg-slate-900 transition-all border shadow-lg">
                        <div class="flex items-center gap-3">
                            <span class="text-purple-400">📁</span> Kategori III: Logistik
                        </div>
                        <span x-text="openGroup === 'logistik' ? '▲' : '▼'" class="text-[9px] text-slate-500"></span>
                    </button>
                    <div x-show="openGroup === 'logistik'" x-collapse class="pl-3 space-y-1 pt-1.5">
                        <button @click="currentTab = 'droppart'"
                            :class="currentTab === 'droppart' ? 'bg-purple-600 text-white font-bold shadow-purple-600/30' :
                                'bg-slate-900/60 hover:bg-slate-800 text-slate-300'"
                            class="w-full flex items-center gap-3 px-4 py-2 text-[11px] rounded-lg transition-all text-left shadow-inner border border-slate-800 hover:border-purple-800">
                            <span>🚚</span> Drop Part (Entry)
                        </button>
                        <button @click="currentTab = 'techstock'"
                            :class="currentTab === 'techstock' ? 'bg-purple-600 text-white font-bold shadow-purple-600/30' :
                                'bg-slate-900/60 hover:bg-slate-800 text-slate-300'"
                            class="w-full flex items-center gap-3 px-4 py-2 text-[11px] rounded-lg transition-all text-left shadow-inner border border-slate-800 hover:border-purple-800">
                            <span>💼</span> Tas Stok Teknisi
                        </button>
                        <button @click="currentTab = 'returpart'"
                            :class="currentTab === 'returpart' ? 'bg-purple-600 text-white font-bold shadow-purple-600/30' :
                                'bg-slate-900/60 hover:bg-slate-800 text-slate-300'"
                            class="w-full flex items-center gap-3 px-4 py-2 text-[11px] rounded-lg transition-all text-left shadow-inner border border-slate-800 hover:border-purple-800">
                            <span>↩️</span> Retur Suku Cadang
                        </button>
                    </div>
                </div>

                {{-- 📁 GRUP 4: PENARIKAN (AMBER GLOW) --}}
                <div class="space-y-1">
                    <button @click="openGroup = (openGroup === 'amber' ? '' : 'amber')"
                        :class="openGroup === 'amber' ? 'border-amber-500/60 shadow-amber-500/20 text-white' :
                            'border-slate-800 shadow-black/30 text-slate-400'"
                        class="w-full flex items-center justify-between px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl bg-slate-950/60 hover:bg-slate-900 transition-all border shadow-lg">
                        <div class="flex items-center gap-3">
                            <span class="text-amber-400">📁</span> Kategori IV: Penarikan
                        </div>
                        <span x-text="openGroup === 'amber' ? '▲' : '▼'" class="text-[9px] text-slate-500"></span>
                    </button>
                    <div x-show="openGroup === 'amber'" x-collapse class="pl-3 space-y-1 pt-1.5">
                        <button @click="currentTab = 'withdrawal'"
                            :class="currentTab === 'withdrawal' ? 'bg-amber-600 text-white font-bold shadow-amber-600/30' :
                                'bg-slate-900/60 hover:bg-slate-800 text-slate-300'"
                            class="w-full flex items-center gap-3 px-4 py-2 text-[11px] rounded-lg transition-all text-left shadow-inner border border-slate-800 hover:border-amber-800">
                            <span>📤</span> Penarikan Unit Mesin
                        </button>
                    </div>
                </div>

            </div>

            {{-- 📄 SISI KANAN: AREA MERENDER KONTEN KETERANGAN DETAIL SUB-MENU (9 KOLOM GRID) --}}
            <div
                class="lg:col-span-9 bg-slate-900 p-6 rounded-3xl min-h-[480px] flex flex-col justify-between border border-slate-800/40 shadow-2xl relative overflow-hidden">

                <div x-show="openGroup === 'master'"
                    class="absolute -top-10 -right-10 w-96 h-96 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"
                    x-transition.fade></div>
                <div x-show="openGroup === 'field'"
                    class="absolute -bottom-10 -left-10 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"
                    x-transition.fade></div>
                <div x-show="openGroup === 'logistik'"
                    class="absolute -top-10 -left-10 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl pointer-events-none"
                    x-transition.fade></div>
                <div x-show="openGroup === 'amber'"
                    class="absolute -bottom-10 -right-10 w-64 h-64 bg-amber-500/5 rounded-full blur-3xl pointer-events-none"
                    x-transition.fade></div>

                <div class="relative">
                    {{-- 👤 SUB-MENU KETERANGAN: CUSTOMER --}}
                    <div x-show="currentTab === 'customer'" x-transition.fade>
                        <div class="flex items-center gap-4 border-b border-indigo-900 pb-4 mb-5 shadow-inner">
                            <span class="text-4xl">👤</span>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-tight">Panduan Menu: Customer
                                    (Instansi)</h3>
                                <p class="text-[11px] text-slate-400">Database identitas resmi penyewa mesin fotokopi
                                    PT DGG.</p>
                            </div>
                        </div>
                        <div class="space-y-4 text-xs leading-relaxed text-slate-100">
                            <p>Menu ini berfungsi sebagai wadah utama pendaftaran kontrak baru instansi sebelum unit
                                mesin dikirim ke lokasi.</p>
                            <div
                                class="p-4 bg-slate-950/60 rounded-2xl space-y-2.5 border border-indigo-900 shadow-xl shadow-indigo-900/10 backdrop-blur-sm">
                                <p><span
                                        class="text-indigo-400 font-extrabold text-[12px] tracking-tight">nama_customer:</span>
                                    Diisi nama lengkap PT/Instansi penyewa (*Contoh: PT ABC Cirebon*).</p>
                                <p><span class="text-indigo-400 font-extrabold text-[12px] tracking-tight">rayon_id &
                                        kota:</span> Pemetaan wilayah distribusi kerja.</p>
                                <p><span
                                        class="text-indigo-400 font-extrabold text-[12px] tracking-tight">nomor_telp:</span>
                                    Kontak administratif instansi.</p>
                                <p><span
                                        class="text-indigo-400 font-extrabold text-[12px] tracking-tight">technician_id:</span>
                                    Penguncian teknisi penanggung jawab wilayah.</p>
                            </div>
                        </div>
                    </div>

                    {{-- 🚀 SUB-MENU KETERANGAN: PEMASANGAN MESIN (CUSTOMER INSTAL) --}}
                    <div x-show="currentTab === 'customerinstal'" x-transition.fade>
                        <div class="flex items-center gap-4 border-b border-emerald-900 pb-4 mb-5 shadow-inner">
                            <span class="text-4xl text-emerald-400">🚀</span>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-tight">Panduan Menu: Pemasangan
                                    (Customer Instal)</h3>
                                <p class="text-[11px] text-emerald-400">SOP Deployment Unit Mesin Fotokopi Baru ke
                                    Lokasi Pelanggan.</p>
                            </div>
                        </div>
                        <div class="space-y-4 text-xs leading-relaxed text-slate-100">
                            <p class="text-slate-200">
                                Menu ini digunakan saat unit mesin fotokopi akan diberangkatkan menuju kantor pelanggan.
                                Mengunci status mesin agar tidak terjadi pengiriman ganda pada unit yang sama.
                            </p>
                            <div
                                class="p-4 bg-slate-950/60 rounded-2xl space-y-2.5 border border-emerald-900 border-l-4 shadow-xl shadow-emerald-900/10 backdrop-blur-sm">
                                <p><span
                                        class="text-emerald-400 font-extrabold text-[12px] tracking-tight">customer_id:</span>
                                    Pilih nama instansi/customer.</p>
                                <p><span class="text-emerald-400 font-extrabold text-[12px] tracking-tight">machine_id
                                        (Pilih SN):</span> Saringan ketat **Hanya menampilkan unit yang berstatus GUDANG
                                    / READY**.</p>
                                <p><span
                                        class="text-emerald-400 font-extrabold text-[12px] tracking-tight">tanggal:</span>
                                    Mencatat tanggal riil unit serah terima di lapangan.</p>
                            </div>
                            <div
                                class="p-3 bg-slate-900/80 rounded-xl text-[10px] text-amber-300 font-medium border border-amber-900 shadow-amber-900/30 shadow-inner">
                                🔄 <span class="font-black text-white">Konsekuensi Logis Sistem:</span> Status mesin
                                fotokopi otomatis berbalik arah dari <span class="text-emerald-400">Gudang</span>
                                menjadi <span class="text-amber-400">Terpasang</span>, dan customer_id langsung
                                terikat.
                            </div>
                        </div>
                    </div>

                    {{-- 🖨️ SUB-MENU KETERANGAN: DATA MESIN --}}
                    <div x-show="currentTab === 'machine'" x-transition.fade>
                        <div class="flex items-center gap-4 border-b border-indigo-900 pb-4 mb-5 shadow-inner">
                            <span class="text-4xl">🖨️</span>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-tight">Panduan Menu: Data Mesin
                                    (Machines)</h3>
                                <p class="text-[11px] text-slate-400">Monitoring fisik, spesifikasi, dan lokasi mesin
                                    fotokopi.</p>
                            </div>
                        </div>
                        <div class="space-y-4 text-xs leading-relaxed text-slate-100">
                            <p>Menu kontrol inventaris unit fisik. Memastikan admin mengetahui status ketersediaan unit
                                di dalam gudang secara realtime.</p>
                            <div
                                class="p-4 bg-slate-950/60 rounded-2xl space-y-2.5 border border-indigo-900 shadow-xl shadow-indigo-900/10 backdrop-blur-sm">
                                <p><span
                                        class="text-indigo-400 font-extrabold text-[12px] tracking-tight">serial_number:</span>
                                    ID SN unik mutlak unit mesin fotokopi.</p>
                                <p><span
                                        class="text-indigo-400 font-extrabold text-[12px] tracking-tight">tipe_model:</span>
                                    Model unit (IR3300/IR5060, dll).</p>
                                <p><span class="text-indigo-400 font-extrabold text-[12px] tracking-tight">volt,
                                        finisher, double_scan:</span> Spesifikasi fisik bawaan unit.</p>
                                <p><span
                                        class="text-indigo-400 font-extrabold text-[12px] tracking-tight">status:</span>
                                    Posisi mesin saat ini (<span class="text-emerald-400">Gudang</span> / <span
                                        class="text-amber-400">Terpasang</span>).</p>
                            </div>
                        </div>
                    </div>

                    {{-- 🔧 SUB-MENU KETERANGAN: TEKNISI --}}
                    <div x-show="currentTab === 'technician'" x-transition.fade>
                        <div class="flex items-center gap-4 border-b border-indigo-900 pb-4 mb-5 shadow-inner">
                            <span class="text-4xl">🔧</span>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-tight">Panduan Menu: Personel Teknisi
                                    (Technicians)</h3>
                                <p class="text-[11px] text-slate-400">Registrasi akun teknisi pemegang inventaris
                                    logistik lapangan.</p>
                            </div>
                        </div>
                        <div class="space-y-4 text-xs leading-relaxed text-slate-100">
                            <p>Menu ini digunakan untuk mendaftarkan nama personil teknisi DGG resmi yang bertugas
                                membawa barang gudang jalan.</p>
                            <div
                                class="p-4 bg-slate-950/60 rounded-2xl space-y-2.5 border border-indigo-900 shadow-xl shadow-indigo-900/10 backdrop-blur-sm">
                                <p><span
                                        class="text-indigo-400 font-extrabold text-[12px] tracking-tight">nama_technician:</span>
                                    Nama lengkap personil lapangan.</p>
                                <p><span
                                        class="text-indigo-400 font-extrabold text-[12px] tracking-tight">nomor_hp:</span>
                                    Kontak nomor WhatsApp aktif.</p>
                                <p><span
                                        class="text-indigo-400 font-extrabold text-[12px] tracking-tight">rayon_id:</span>
                                    Mengunci area batasan tugas teknisi tersebut.</p>
                            </div>
                        </div>
                    </div>

                    {{-- 📦 SUB-MENU KETERANGAN: MASTER SPAREPART --}}
                    <div x-show="currentTab === 'sparepart'" x-transition.fade>
                        <div class="flex items-center gap-4 border-b border-indigo-900 pb-4 mb-5 shadow-inner">
                            <span class="text-4xl">📦</span>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-tight">Panduan Menu: Master Sparepart
                                </h3>
                                <p class="text-[11px] text-slate-400">Katalog induk komponen suku cadang, drum, roll,
                                    dan toner.</p>
                            </div>
                        </div>
                        <div class="space-y-4 text-xs leading-relaxed text-slate-100">
                            <p>Digunakan oleh Admin Gudang untuk mencatat nama komponen, kode barang, dan total
                                akumulasi saldo masuk/keluar.</p>
                            <div
                                class="p-4 bg-slate-950/60 rounded-2xl space-y-2.5 border border-indigo-900 shadow-xl shadow-indigo-900/10 backdrop-blur-sm">
                                <p><span class="text-indigo-400 font-extrabold text-[12px] tracking-tight">stok:</span>
                                    Jumlah barang aktual riil yang ada di rak Gudang Pusat.</p>
                                <p><span class="text-indigo-400 font-extrabold text-[12px] tracking-tight">code_part /
                                        no_part:</span> Kode registrasi katalog pabrik.</p>
                                <p><span class="text-indigo-400 font-extrabold text-[12px] tracking-tight">saldo_masuk
                                        / saldo_keluar:</span> Indikator akumulasi mutasi.</p>
                            </div>
                        </div>
                    </div>

                    {{-- 🛠️ SUB-MENU KETERANGAN: SERVICE LOG --}}
                    <div x-show="currentTab === 'servicelog'" x-transition.fade>
                        <div class="flex items-center gap-4 border-b border-emerald-900 pb-4 mb-5 shadow-inner">
                            <span class="text-4xl text-emerald-400">🛠️</span>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-tight">Panduan Menu: Input Servis
                                    (Service Logs)</h3>
                                <p class="text-[11px] text-emerald-400">Pencatatan harian harian volume pemakaian
                                    kertas (billing) & perbaikan.</p>
                            </div>
                        </div>
                        <div class="space-y-4 text-xs leading-relaxed text-slate-100">
                            <p>Setiap kunjungan lapangan selesai, teknisi wajib mengisi form ini. Sistem dilengkapi
                                kalkulasi instan selisih meteran pemakaian kertas BW & Color.</p>
                            <div
                                class="p-4 bg-slate-950/60 rounded-2xl space-y-2.5 border border-emerald-900 shadow-xl shadow-emerald-900/10 backdrop-blur-sm">
                                <p><span class="text-emerald-400 font-extrabold text-[12px] tracking-tight">counter_bw
                                        / counter_color:</span> Angka meteran saat ini di lokasi mesin.</p>
                                <p><span class="text-emerald-400 font-extrabold text-[12px] tracking-tight">usage_bw /
                                        usage_color:</span> Volume pemakaian kertas yang terhitung otomatis.</p>
                                <p><span class="text-emerald-400 font-extrabold text-[12px] tracking-tight">sparepart_id
                                        & jumlah_sparepart:</span> Komponen tas teknisi yang habis terpasang. **Memotong
                                    langsung isi tas teknisi**.</p>
                                <p><span class="text-slate-400 font-medium">tipe_kunjungan:</span> Menggunakan kode
                                    badge tegas: <span class="text-rose-400 font-extrabold">CM</span> (Darurat), <span
                                        class="text-blue-400 font-extrabold">RM</span> (Rutin), <span
                                        class="text-green-400 font-extrabold">TN</span> (Toner), <span
                                        class="text-indigo-400 font-extrabold">RN</span> (Pasang Baru), atau <span
                                        class="text-purple-400 font-extrabold">RR</span> (Ganti Unit).</p>
                            </div>
                        </div>
                    </div>

                    {{-- 🚚 SUB-MENU KETERANGAN: DROP PART --}}
                    <div x-show="currentTab === 'droppart'" x-transition.fade>
                        <div class="flex items-center gap-4 border-b border-purple-900 pb-4 mb-5 shadow-inner">
                            <span class="text-4xl text-purple-400">🚚</span>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-tight">Panduan Menu: Drop Part Ke
                                    Teknisi (Sparepart Entry)</h3>
                                <p class="text-[11px] text-slate-400">Pemberian modal stock komponen dari gudang ke tas
                                    motor lapangan.</p>
                            </div>
                        </div>
                        <div class="space-y-4 text-xs leading-relaxed text-slate-100">
                            <p>Digunakan saat teknisi mengambil barang dari kantor pusat untuk bekal keliling servis
                                lapangan.</p>
                            <div
                                class="p-4 bg-slate-950/60 rounded-2xl space-y-2 border border-purple-900 border-l-4 shadow-xl shadow-purple-900/10 backdrop-blur-sm text-purple-200">
                                <p class="font-extrabold text-white text-[13px]">🏭 Alur Otomatisasi:</p>
                                <p>Ketika data drop disimpan $\rightarrow$ Sistem otomatis **mengurangi stok Gudang
                                    Utama** (`spareparts.stok`) dan langsung **menambah saldo isi tas teknisi**
                                    (`technician_stocks.jumlah`).</p>
                            </div>
                        </div>
                    </div>

                    {{-- 💼 SUB-MENU KETERANGAN: TAS STOK TEKNISI --}}
                    <div x-show="currentTab === 'techstock'" x-transition.fade>
                        <div class="flex items-center gap-4 border-b border-purple-900 pb-4 mb-5 shadow-inner">
                            <span class="text-4xl text-purple-400">💼</span>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-tight">Panduan Menu: Tas Stok Teknisi
                                </h3>
                                <p class="text-[11px] text-slate-400">Dashboard audit sisa material berjalan per
                                    individu personil.</p>
                            </div>
                        </div>
                        <div class="space-y-4 text-xs leading-relaxed text-slate-100">
                            <p>Menu khusus admin logistik untuk melihat sisa saldo kantong barang yang sedang dibawa
                                keliling di dalam motor teknisi secara aktual.</p>
                            <div
                                class="p-4 bg-slate-950/60 rounded-2xl space-y-2.5 border border-purple-900 shadow-xl shadow-purple-900/10 backdrop-blur-sm">
                                <p><span
                                        class="text-purple-400 font-extrabold text-[12px] tracking-tight">technician_id:</span>
                                    Nama personel pemegang barang audit.</p>
                                <p><span
                                        class="text-purple-400 font-extrabold text-[12px] tracking-tight">sparepart_id:</span>
                                    Jenis nama barang suku cadang.</p>
                                <p><span
                                        class="text-purple-400 font-extrabold text-[12px] tracking-tight">jumlah:</span>
                                    **Sisa Saldo Kantong Aktual**. Nilai ini dikunci sinkron dan otomatis terpotong
                                    mandiri setiap kali teknisi mengisi menu *Service Logs*.</p>
                            </div>
                        </div>
                    </div>

                    {{-- ↩️ SUB-MENU KETERANGAN: RETUR PART --}}
                    <div x-show="currentTab === 'returpart'" x-transition.fade>
                        <div class="flex items-center gap-4 border-b border-purple-900 pb-4 mb-5 shadow-inner">
                            <span class="text-4xl text-purple-400">↩️</span>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-tight">Panduan Menu: Retur Suku
                                    Cadang (Part Returns)</h3>
                                <p class="text-[11px] text-slate-400">Pengembalian material sisa atau salah bawa dari
                                    lapangan ke rak pusat.</p>
                            </div>
                        </div>
                        <div class="space-y-4 text-xs leading-relaxed text-slate-100">
                            <p>Mekanisme pemulangan barang sisa keliling agar stok gudang kantor pusat kembali sinkron
                                dan utuh.</p>
                            <div
                                class="p-4 bg-slate-950/60 rounded-2xl space-y-2 border border-rose-900 border-l-4 shadow-xl shadow-rose-900/10 backdrop-blur-sm text-rose-300">
                                <p class="font-extrabold text-white text-[13px]">🔒 Gembok Validasi Mutlak:</p>
                                <p>Sistem memblokir otomatis jika jumlah retur yang dimasukkan melebihi sisa kapasitas
                                    aktual isi tas teknisi di tabel <span
                                        class="underline text-slate-100 font-medium">technician_stocks</span> untuk
                                    menghindari manipulasi angka.</p>
                                <p class="text-slate-400 mt-1.5 font-medium">🔄 Efek Simpan: Mengurangi isi tas teknisi
                                    & menambah kembali stok pusat gudang.</p>
                            </div>
                        </div>
                    </div>

                    {{-- 📤 SUB-MENU KETERANGAN: PENARIKAN MESIN --}}
                    <div x-show="currentTab === 'withdrawal'" x-transition.fade>
                        <div class="flex items-center gap-4 border-b border-amber-900 pb-4 mb-5 shadow-inner">
                            <span class="text-4xl text-amber-400">📤</span>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-tight">Panduan Menu: Penarikan Unit
                                    Mesin (Machine Withdrawals)</h3>
                                <p class="text-[11px] text-amber-400">SOP Pemutusan kontrak kerja sewa, penyusutan
                                    aset, dan pencetakan dokumen jalan.</p>
                            </div>
                        </div>
                        <div class="space-y-4 text-xs leading-relaxed text-slate-100">
                            <p>Digunakan saat masa kontrak sewa selesai atau unit ditarik karena rusak berat/macet
                                bayar.</p>
                            <div
                                class="p-4 bg-slate-950/60 rounded-2xl space-y-2.5 border border-amber-900 shadow-xl shadow-amber-900/10 backdrop-blur-sm">
                                <p><span
                                        class="text-amber-400 font-extrabold text-[12px] tracking-tight">machine_id:</span>
                                    Pilihan laci SN Mesin dibatasi ketat **Hanya menampilkan unit yang berstatus
                                    Terpasang** lengkap dengan nama pelanggannya.</p>
                                <p><span
                                        class="text-amber-400 font-extrabold text-[12px] tracking-tight">kondisi_akhir:</span>
                                    Klasifikasi fisik pasca penarikan (<span class="text-green-400">Baik</span>, <span
                                        class="text-amber-400">Rusak Ringan</span>, <span class="text-rose-400">Rusak
                                        Berat</span>).</p>
                                <p>⚙️ <span class="text-slate-300 font-semibold text-[11px]">Otomatisasi & Cetak
                                        Jalan:</span> Menyimpan data otomatis mengubah status mesin kembali menjadi
                                    **Gudang**. Menyediakan tombol cetak **Surat Penarikan Unit A5 Landscape Minimalis**
                                    bersih (tanpa bg hitam) dengan 6 baris kolom kosong ke bawah untuk coretan tanda
                                    tangan lapangan.</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ⚙️ FORMULA SINKRONISASI LOGISTIK (DI BAWAH KONTEN KANAN) --}}
                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-5 pt-5 border-t border-slate-800 text-[11px] font-mono shadow-inner bg-slate-950/20 p-2 rounded-2xl border border-slate-800">
                    <div class="p-3 bg-slate-950 rounded-xl border border-indigo-900 Shadow-lg">
                        <span class="text-indigo-400 font-extrabold text-[13px]">🏭 Rumus Gudang Pusat:</span><br>
                        <span class="text-slate-100 mt-1 block">Stok Akhir = Stok Awal - Drop + Retur + Unit
                            Ditarik</span>
                    </div>
                    <div class="p-3 bg-slate-950 rounded-xl border border-purple-900 shadow-lg">
                        <span class="text-purple-400 font-extrabold text-[13px]">💼 Rumus Kantong Teknisi:</span><br>
                        <span class="text-slate-100 mt-1 block">Isi Tas = Drop Masuk - Suku Cadang Terpakai -
                            Diretur</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- ====================================================================== --}}
        {{-- UTAMA: JALUR ESKALASI & ADUAN (NEON AMBER GLOW) --}}
        {{-- ====================================================================== --}}
        <div
            class="p-6 bg-slate-900 rounded-3xl shadow-2xl flex flex-col lg:flex-row items-center justify-between gap-6 mt-4 border border-slate-800 relative overflow-hidden">
            <div class="absolute -top-10 -left-10 w-64 h-64 bg-amber-500/5 rounded-full blur-3xl pointer-events-none">
            </div>
            <div class="space-y-2.5 text-center lg:text-left relative">
                <h4
                    class="text-sm font-black text-amber-500 flex items-center justify-center lg:justify-start gap-2.5 uppercase tracking-wide">
                    🚨 Jalur Eskalasi & Intervensi Validasi Data</h4>
                <p class="text-[11px] text-slate-400 leading-relaxed max-w-2xl font-medium">
                    Apabila operator depo gudang, admin kantor, atau teknisi lapangan menemui kendala salah input data
                    (*human error*), masalah cetak printer thermal, kegagalan validasi stok minus, atau perlu
                    persetujuan otorisasi pemutihan data khusus, hubungi manajemen pusat PT DGG Cirebon segera:
                </p>
                <div class="flex flex-wrap justify-center lg:justify-start gap-2 pt-1">
                    <span
                        class="text-[10px] px-2.5 py-0.5 bg-slate-800 text-slate-100 font-semibold rounded-full shadow-inner">Koreksi
                        Data</span>
                    <span
                        class="text-[10px] px-2.5 py-0.5 bg-slate-800 text-slate-100 font-semibold rounded-full shadow-inner">Reset
                        Counter Eror</span>
                    <span
                        class="text-[10px] px-2.5 py-0.5 bg-slate-800 text-slate-100 font-semibold rounded-full shadow-inner">Hak
                        Akses Sistem</span>
                </div>
            </div>

            <a href="https://wa.me/qr/4GIBMDZGZKKRJ1" target="_blank"
                class="bg-slate-800 hover:bg-slate-750 transition-colors p-5 rounded-3xl min-w-[280px] text-center no-underline block border-2 border-amber-900 shadow-amber-900/30 shadow-2xl backdrop-blur-sm transform hover:scale-105">
                <span class="text-[9px] font-bold tracking-widest text-amber-500 uppercase">Hubungi Pimpinan
                    Instan</span>
                <h3 class="text-xl font-black text-white mt-1 tracking-wide">RUDI</h3>
                <p class="text-base font-mono font-bold text-green-400 mt-1">📞 0895-6366-75848</p>
                <span class="text-[9px] text-slate-500 mt-1.5 block">Klik otomatis untuk Chat WhatsApp</span>
            </a>
        </div>

        {{-- ====================================================================== --}}
        {{-- UTAMA: FOOTER --}}
        {{-- ====================================================================== --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-1 px-2 pt-4 text-slate-600">
            <p class="text-[10px]">© 2026 PT Dinamika Global Gemilang. All Rights Reserved.</p>
            <p class="text-[10px] font-bold">DGG System Pro v3.6 — Sidebar Accordion Dropdown Tab View — Tinker Glow
                Verified</p>
        </div>

    </div>
</x-filament-panels::page>
