<x-filament-panels::page>
    <div x-data="{
        kategori: 'master',
        formulir: 'customer',
        cari: '',
    }" class="dgg-manual">
        <style>
            .dgg-manual {
                --paper: #F7F4EC;
                --paper-dim: #EFEAD9;
                --ink: #1F2A1E;
                --ink-soft: #4A5D52;
                --rule: #D8D2C0;
                --stamp-red: #8B2E2E;
                --index-amber: #B07F22;
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
                color: var(--ink);
                background: var(--paper);
                border: 1px solid var(--rule);
                border-radius: 2px;
                padding: 0;
                position: relative;
            }

            .dgg-manual * {
                box-sizing: border-box;
            }

            .dgg-mono {
                font-family: 'Courier Prime', 'Courier New', ui-monospace, monospace;
            }

            /* ===== Letterhead ===== */
            .dgg-letterhead {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 1.5rem;
                padding: 2rem 2.25rem 1.25rem;
                border-bottom: 3px solid var(--ink);
                background-image:
                    repeating-linear-gradient(0deg, transparent, transparent 27px, rgba(31, 42, 30, 0.025) 28px);
            }

            .dgg-letterhead .dgg-eyebrow {
                font-size: 10.5px;
                letter-spacing: 0.18em;
                text-transform: uppercase;
                color: var(--ink-soft);
                margin: 0 0 0.4rem;
            }

            .dgg-letterhead h1 {
                font-size: 1.9rem;
                line-height: 1.1;
                font-weight: 800;
                letter-spacing: -0.01em;
                margin: 0;
                color: var(--ink);
            }

            .dgg-letterhead .dgg-sub {
                font-size: 12px;
                color: var(--ink-soft);
                margin: 0.4rem 0 0;
                max-width: 46ch;
            }

            .dgg-stampbox {
                flex: none;
                width: 116px;
                height: 116px;
                border: 2.5px solid var(--stamp-red);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                transform: rotate(-7deg);
                color: var(--stamp-red);
                position: relative;
            }

            .dgg-stampbox::before {
                content: '';
                position: absolute;
                inset: 7px;
                border: 1px solid var(--stamp-red);
                border-radius: 50%;
            }

            .dgg-stampbox span {
                font-family: 'Courier Prime', monospace;
                font-size: 9.5px;
                font-weight: 700;
                letter-spacing: 0.05em;
                line-height: 1.5;
                text-transform: uppercase;
            }

            /* ===== Body grid ===== */
            .dgg-body {
                display: grid;
                grid-template-columns: 280px 1fr;
            }

            @media (max-width: 900px) {
                .dgg-body {
                    grid-template-columns: 1fr;
                }
            }

            /* ===== Index / card catalog ===== */
            .dgg-index {
                border-right: 1px solid var(--rule);
                padding: 1.25rem 1rem;
                background: var(--paper-dim);
            }

            @media (max-width: 900px) {
                .dgg-index {
                    border-right: none;
                    border-bottom: 1px solid var(--rule);
                }
            }

            .dgg-search {
                width: 100%;
                background: var(--paper);
                border: 1px solid var(--rule);
                border-radius: 2px;
                padding: 0.5rem 0.65rem;
                font-size: 12px;
                color: var(--ink);
                margin-bottom: 1rem;
            }

            .dgg-search::placeholder {
                color: #9b9683;
            }

            .dgg-search:focus {
                outline: 2px solid var(--index-amber);
                outline-offset: 1px;
            }

            .dgg-cat-head {
                width: 100%;
                display: flex;
                align-items: baseline;
                justify-content: space-between;
                gap: 0.5rem;
                background: none;
                border: none;
                border-bottom: 1px dashed var(--rule);
                padding: 0.55rem 0.15rem;
                margin-top: 0.35rem;
                cursor: pointer;
                text-align: left;
                font-family: 'Courier Prime', monospace;
                font-size: 11px;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                color: var(--ink-soft);
            }

            .dgg-cat-head.is-active {
                color: var(--ink);
                font-weight: 700;
            }

            .dgg-cat-head .dgg-cat-code {
                color: var(--index-amber);
                font-weight: 700;
                margin-right: 0.45rem;
            }

            .dgg-cat-arrow {
                font-size: 9px;
                color: #9b9683;
            }

            .dgg-tab {
                display: flex;
                align-items: baseline;
                gap: 0.5rem;
                width: 100%;
                text-align: left;
                background: none;
                border: none;
                padding: 0.4rem 0.5rem 0.4rem 1.15rem;
                font-size: 12.5px;
                color: var(--ink-soft);
                border-left: 2px solid transparent;
                cursor: pointer;
            }

            .dgg-tab:hover {
                color: var(--ink);
                background: rgba(31, 42, 30, 0.035);
            }

            .dgg-tab.is-active {
                color: var(--ink);
                font-weight: 700;
                border-left-color: var(--stamp-red);
                background: rgba(139, 46, 46, 0.06);
            }

            .dgg-tab .dgg-tab-code {
                font-family: 'Courier Prime', monospace;
                font-size: 10px;
                color: var(--index-amber);
                flex: none;
                width: 3.6em;
            }

            /* ===== Sheet (right content) ===== */
            .dgg-sheet {
                padding: 2rem 2.25rem 2.5rem;
                min-height: 480px;
            }

            .dgg-sheet-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1rem;
                border-bottom: 1.5px solid var(--ink);
                padding-bottom: 0.85rem;
                margin-bottom: 1.25rem;
            }

            .dgg-formno {
                font-family: 'Courier Prime', monospace;
                font-size: 10.5px;
                letter-spacing: 0.06em;
                color: var(--index-amber);
                margin: 0 0 0.3rem;
            }

            .dgg-sheet-head h2 {
                font-size: 1.3rem;
                font-weight: 800;
                margin: 0;
                letter-spacing: -0.005em;
            }

            .dgg-sheet-head p {
                font-size: 12px;
                color: var(--ink-soft);
                margin: 0.3rem 0 0;
                max-width: 56ch;
            }

            .dgg-badge {
                flex: none;
                font-family: 'Courier Prime', monospace;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                padding: 0.3rem 0.55rem;
                border: 1px solid currentColor;
                border-radius: 2px;
                white-space: nowrap;
            }

            .dgg-prose {
                font-size: 13px;
                line-height: 1.65;
                color: var(--ink);
            }

            .dgg-prose p {
                margin: 0 0 0.9rem;
            }

            /* card-stok style field box */
            .dgg-binbox {
                border: 1px solid var(--rule);
                border-left: 3px solid var(--ink-soft);
                background: var(--paper-dim);
                border-radius: 2px;
                margin: 1rem 0;
            }

            .dgg-bin-title {
                font-family: 'Courier Prime', monospace;
                font-size: 10px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--ink-soft);
                padding: 0.6rem 0.85rem 0;
            }

            .dgg-bin-row {
                display: grid;
                grid-template-columns: 11.5em 1fr;
                gap: 0.5rem;
                padding: 0.45rem 0.85rem;
                border-top: 1px dashed var(--rule);
                font-size: 12.5px;
            }

            .dgg-bin-row:first-of-type {
                border-top: 1px solid var(--rule);
                margin-top: 0.5rem;
            }

            .dgg-bin-field {
                font-family: 'Courier Prime', monospace;
                font-size: 11.5px;
                color: var(--stamp-red);
                font-weight: 700;
            }

            .dgg-flow {
                display: flex;
                align-items: stretch;
                gap: 0;
                margin: 1.1rem 0;
                border: 1px solid var(--rule);
                border-radius: 2px;
                overflow: hidden;
                font-size: 11.5px;
            }

            .dgg-flow-step {
                flex: 1;
                padding: 0.65rem 0.7rem;
                background: var(--paper);
                position: relative;
            }

            .dgg-flow-step+.dgg-flow-step {
                border-left: 1px dashed var(--rule);
            }

            .dgg-flow-step b {
                display: block;
                font-size: 11.5px;
                margin-bottom: 0.15rem;
            }

            .dgg-flow-step span {
                color: var(--ink-soft);
                font-size: 11px;
            }

            .dgg-note {
                border: 1px solid var(--index-amber);
                background: rgba(176, 127, 34, 0.08);
                border-radius: 2px;
                padding: 0.7rem 0.85rem;
                font-size: 12px;
                line-height: 1.55;
                margin: 1rem 0 0;
            }

            .dgg-note b {
                color: var(--index-amber);
            }

            .dgg-lock {
                border: 1px solid var(--stamp-red);
                background: rgba(139, 46, 46, 0.06);
                border-radius: 2px;
                padding: 0.7rem 0.85rem;
                font-size: 12px;
                line-height: 1.55;
                margin: 1rem 0 0;
                color: #5e2020;
            }

            .dgg-lock b {
                color: var(--stamp-red);
            }

            /* ===== Footer formula ledger ===== */
            .dgg-ledger {
                border-top: 1.5px solid var(--ink);
                padding: 1.25rem 2.25rem;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
                background: var(--paper-dim);
            }

            @media (max-width: 700px) {
                .dgg-ledger {
                    grid-template-columns: 1fr;
                }
            }

            .dgg-ledger-card {
                border: 1px solid var(--rule);
                background: var(--paper);
                border-radius: 2px;
                padding: 0.75rem 0.9rem;
            }

            .dgg-ledger-card .dgg-led-label {
                font-family: 'Courier Prime', monospace;
                font-size: 10px;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                color: var(--ink-soft);
                margin: 0 0 0.3rem;
            }

            .dgg-ledger-card .dgg-led-formula {
                font-family: 'Courier Prime', monospace;
                font-size: 12px;
                color: var(--ink);
            }

            /* ===== Escalation strip ===== */
            .dgg-escalation {
                border-top: 3px solid var(--ink);
                padding: 1.75rem 2.25rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1.5rem;
                flex-wrap: wrap;
            }

            .dgg-escalation .dgg-eyebrow {
                font-size: 10.5px;
                letter-spacing: 0.16em;
                text-transform: uppercase;
                color: var(--stamp-red);
                margin: 0 0 0.4rem;
                font-weight: 700;
            }

            .dgg-escalation p {
                font-size: 12px;
                color: var(--ink-soft);
                max-width: 50ch;
                margin: 0;
                line-height: 1.6;
            }

            .dgg-call {
                flex: none;
                text-decoration: none;
                border: 1.5px solid var(--ink);
                background: var(--paper);
                padding: 0.85rem 1.4rem;
                border-radius: 2px;
                text-align: center;
                min-width: 220px;
                transition: background 0.15s ease;
            }

            .dgg-call:hover {
                background: var(--paper-dim);
            }

            .dgg-call .dgg-call-label {
                font-family: 'Courier Prime', monospace;
                font-size: 10px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--ink-soft);
            }

            .dgg-call .dgg-call-name {
                font-size: 1.1rem;
                font-weight: 800;
                color: var(--ink);
                margin: 0.2rem 0;
            }

            .dgg-call .dgg-call-num {
                font-family: 'Courier Prime', monospace;
                font-size: 13px;
                color: #2f5d3a;
                font-weight: 700;
            }

            .dgg-colophon {
                display: flex;
                justify-content: space-between;
                gap: 1rem;
                flex-wrap: wrap;
                padding: 0.85rem 2.25rem;
                font-size: 10.5px;
                color: #9b9683;
                font-family: 'Courier Prime', monospace;
                border-top: 1px solid var(--rule);
            }

            [x-cloak] {
                display: none !important;
            }
        </style>

        {{-- ===================================================================
             LETTERHEAD
        ==================================================================== --}}
        <div class="dgg-letterhead">
            <div>
                <p class="dgg-eyebrow">Dokumen Internal &mdash; Tidak Untuk Disebarluaskan</p>
                <h1>Buku Panduan Operasional</h1>
                <p class="dgg-sub">
                    Referensi kerja harian untuk admin gudang, kasir, teknisi lapangan, dan tim marketing
                    PT Dinamika Global Gemilang &mdash; setiap kolom dijelaskan sesuai struktur data yang sebenarnya
                    berjalan.
                </p>
            </div>
            <div class="dgg-stampbox">
                <span>DGG&nbsp;System<br>Cirebon<br>Resmi</span>
            </div>
        </div>

        <div class="dgg-body">

            {{-- ===================================================================
                 INDEX / KARTU KATALOG KIRI
            ==================================================================== --}}
            <nav class="dgg-index">
                <input type="text" x-model="cari" class="dgg-search" placeholder="Cari nama menu&hellip;">

                {{-- I. MASTER DATA --}}
                <button type="button" @click="kategori = (kategori === 'master' ? '' : 'master')"
                    :class="kategori === 'master' && 'is-active'" class="dgg-cat-head">
                    <span><span class="dgg-cat-code">I.</span>Master Data</span>
                    <span class="dgg-cat-arrow" x-text="kategori === 'master' ? '&#9650;' : '&#9660;'"></span>
                </button>
                <div x-show="kategori === 'master'" x-collapse>
                    <button type="button" @click="formulir = 'customer'"
                        :class="formulir === 'customer' && 'is-active'" class="dgg-tab"
                        x-show="!cari || 'customer instansi'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-01</span> Customer (Instansi)
                    </button>
                    <button type="button" @click="formulir = 'machine'" :class="formulir === 'machine' && 'is-active'"
                        class="dgg-tab" x-show="!cari || 'mesin unit'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-02</span> Data Unit Mesin
                    </button>
                    <button type="button" @click="formulir = 'technician'"
                        :class="formulir === 'technician' && 'is-active'" class="dgg-tab"
                        x-show="!cari || 'teknisi personel'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-03</span> Personel Teknisi
                    </button>
                    <button type="button" @click="formulir = 'sparepart'"
                        :class="formulir === 'sparepart' && 'is-active'" class="dgg-tab"
                        x-show="!cari || 'sparepart komponen'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-04</span> Master Sparepart
                    </button>
                    <button type="button" @click="formulir = 'marketingdata'"
                        :class="formulir === 'marketingdata' && 'is-active'" class="dgg-tab"
                        x-show="!cari || 'marketing sales'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-05</span> Data Marketing
                    </button>
                </div>

                {{-- II. FIELD SERVIS --}}
                <button type="button" @click="kategori = (kategori === 'field' ? '' : 'field')"
                    :class="kategori === 'field' && 'is-active'" class="dgg-cat-head">
                    <span><span class="dgg-cat-code">II.</span>Field Servis</span>
                    <span class="dgg-cat-arrow" x-text="kategori === 'field' ? '&#9650;' : '&#9660;'"></span>
                </button>
                <div x-show="kategori === 'field'" x-collapse>
                    <button type="button" @click="formulir = 'customerinstal'"
                        :class="formulir === 'customerinstal' && 'is-active'" class="dgg-tab"
                        x-show="!cari || 'pemasangan instal'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-06</span> Pemasangan Mesin
                    </button>
                    <button type="button" @click="formulir = 'servicelog'"
                        :class="formulir === 'servicelog' && 'is-active'" class="dgg-tab"
                        x-show="!cari || 'servis service log'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-07</span> Input Servis Teknisi
                    </button>
                    <button type="button" @click="formulir = 'prospect'"
                        :class="formulir === 'prospect' && 'is-active'" class="dgg-tab"
                        x-show="!cari || 'kunjungan sales prospect'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-08</span> Kunjungan Sales
                    </button>
                </div>

                {{-- III. MRC & BILLING --}}
                <button type="button" @click="kategori = (kategori === 'mrc' ? '' : 'mrc')"
                    :class="kategori === 'mrc' && 'is-active'" class="dgg-cat-head">
                    <span><span class="dgg-cat-code">III.</span>MRC &amp; Billing</span>
                    <span class="dgg-cat-arrow" x-text="kategori === 'mrc' ? '&#9650;' : '&#9660;'"></span>
                </button>
                <div x-show="kategori === 'mrc'" x-collapse>
                    <button type="button" @click="formulir = 'mrclog'" :class="formulir === 'mrclog' && 'is-active'"
                        class="dgg-tab" x-show="!cari || 'mrc counter bulanan'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-09</span> Pencatatan MRC
                    </button>
                    <button type="button" @click="formulir = 'mrcrekap'"
                        :class="formulir === 'mrcrekap' && 'is-active'" class="dgg-tab"
                        x-show="!cari || 'rekap mrc status'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-10</span> Rekap Status MRC
                    </button>
                    <button type="button" @click="formulir = 'mrckontrak'"
                        :class="formulir === 'mrckontrak' && 'is-active'" class="dgg-tab"
                        x-show="!cari || 'kontrak tagihan harga sewa'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-11</span> Kontrak &amp; Tagihan
                    </button>
                </div>

                {{-- IV. LOGISTIK --}}
                <button type="button" @click="kategori = (kategori === 'logistik' ? '' : 'logistik')"
                    :class="kategori === 'logistik' && 'is-active'" class="dgg-cat-head">
                    <span><span class="dgg-cat-code">IV.</span>Logistik</span>
                    <span class="dgg-cat-arrow" x-text="kategori === 'logistik' ? '&#9650;' : '&#9660;'"></span>
                </button>
                <div x-show="kategori === 'logistik'" x-collapse>
                    <button type="button" @click="formulir = 'droppart'"
                        :class="formulir === 'droppart' && 'is-active'" class="dgg-tab"
                        x-show="!cari || 'drop part'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-12</span> Drop Part ke Teknisi
                    </button>
                    <button type="button" @click="formulir = 'techstock'"
                        :class="formulir === 'techstock' && 'is-active'" class="dgg-tab"
                        x-show="!cari || 'tas stok teknisi'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-13</span> Tas Stok Teknisi
                    </button>
                    <button type="button" @click="formulir = 'returpart'"
                        :class="formulir === 'returpart' && 'is-active'" class="dgg-tab"
                        x-show="!cari || 'retur suku cadang'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-14</span> Retur Suku Cadang
                    </button>
                </div>

                {{-- V. PENARIKAN --}}
                <button type="button" @click="kategori = (kategori === 'penarikan' ? '' : 'penarikan')"
                    :class="kategori === 'penarikan' && 'is-active'" class="dgg-cat-head">
                    <span><span class="dgg-cat-code">V.</span>Penarikan</span>
                    <span class="dgg-cat-arrow" x-text="kategori === 'penarikan' ? '&#9650;' : '&#9660;'"></span>
                </button>
                <div x-show="kategori === 'penarikan'" x-collapse>
                    <button type="button" @click="formulir = 'withdrawal'"
                        :class="formulir === 'withdrawal' && 'is-active'" class="dgg-tab"
                        x-show="!cari || 'penarikan unit mesin'.includes(cari.toLowerCase())">
                        <span class="dgg-tab-code">FM-15</span> Penarikan Unit Mesin
                    </button>
                </div>
            </nav>

            {{-- ===================================================================
                 LEMBAR FORMULIR (KANAN)
            ==================================================================== --}}
            <div class="dgg-sheet">

                {{-- FM-01 CUSTOMER --}}
                <div x-show="formulir === 'customer'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-01 &mdash; MASTER DATA</p>
                            <h2>Customer (Instansi)</h2>
                            <p>Database identitas resmi instansi penyewa mesin, dibuat sebelum unit dikirim ke lokasi.
                            </p>
                        </div>
                        <span class="dgg-badge" style="color:#4A5D52;">Master</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Setiap kontrak sewa baru wajib didaftarkan di sini terlebih dahulu. Data ini menjadi acuan
                            seluruh modul lain &mdash; pemasangan mesin, servis, hingga tagihan MRC &mdash; sehingga
                            pengisian nama dan wilayah harus konsisten.</p>
                        <div class="dgg-binbox">
                            <p class="dgg-bin-title">Kolom Formulir</p>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">nama_customer</span><span>Nama
                                    lengkap PT/instansi penyewa. Contoh: <em>PT Abadi Cirebon</em>.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">rayon_id, kota</span><span>Pemetaan
                                    wilayah distribusi kerja teknisi.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">nomor_telp</span><span>Kontak
                                    administratif instansi.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">technician_id</span><span>Penguncian
                                    teknisi penanggung jawab wilayah.</span></div>
                        </div>
                    </div>
                </div>

                {{-- FM-02 MACHINE --}}
                <div x-show="formulir === 'machine'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-02 &mdash; MASTER DATA</p>
                            <h2>Data Unit Mesin</h2>
                            <p>Kartu inventaris fisik tiap unit fotokopi, lengkap dengan status dan lokasi terkini.</p>
                        </div>
                        <span class="dgg-badge" style="color:#4A5D52;">Master</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Menu kontrol inventaris unit fisik agar admin selalu tahu unit mana yang siap kirim dan mana
                            yang sedang terpasang di lapangan.</p>
                        <div class="dgg-binbox">
                            <p class="dgg-bin-title">Kolom Formulir</p>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">serial_number</span><span>ID unik
                                    mutlak unit mesin &mdash; tidak boleh ganda.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">tipe_model</span><span>Model unit,
                                    misal IR3300 atau IR5060.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">volt, finisher,
                                    double_scan</span><span>Spesifikasi fisik bawaan unit.</span></div>
                            <div class="dgg-bin-row"><span
                                    class="dgg-bin-field">status</span><span><strong>Ready</strong> di gudang,
                                    <strong>Rented</strong> terpasang, <strong>Refurbish</strong> perbaikan,
                                    <strong>Returned</strong> baru ditarik.</span></div>
                        </div>
                    </div>
                </div>

                {{-- FM-03 TECHNICIAN --}}
                <div x-show="formulir === 'technician'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-03 &mdash; MASTER DATA</p>
                            <h2>Personel Teknisi</h2>
                            <p>Registrasi akun teknisi pemegang inventaris logistik lapangan.</p>
                        </div>
                        <span class="dgg-badge" style="color:#4A5D52;">Master</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Daftarkan personel resmi sebelum mereka diberi tas stok sparepart atau ditugaskan menangani
                            customer tertentu.</p>
                        <div class="dgg-binbox">
                            <p class="dgg-bin-title">Kolom Formulir</p>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">nama_technician</span><span>Nama
                                    lengkap personel lapangan.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">nomor_hp</span><span>Kontak WhatsApp
                                    aktif untuk koordinasi.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">rayon_id</span><span>Mengunci area
                                    batasan tugas teknisi.</span></div>
                        </div>
                    </div>
                </div>

                {{-- FM-04 SPAREPART --}}
                <div x-show="formulir === 'sparepart'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-04 &mdash; MASTER DATA</p>
                            <h2>Master Sparepart</h2>
                            <p>Katalog induk komponen: drum, roll, toner, dan suku cadang lainnya.</p>
                        </div>
                        <span class="dgg-badge" style="color:#4A5D52;">Master</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Dipakai admin gudang untuk mencatat nama komponen, kode part, dan saldo stok yang berjalan
                            otomatis dari mutasi drop &amp; retur.</p>
                        <div class="dgg-binbox">
                            <p class="dgg-bin-title">Kolom Formulir</p>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">stok</span><span>Jumlah aktual yang
                                    ada di rak gudang pusat saat ini.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">code_part, no_part</span><span>Kode
                                    registrasi katalog, diurutkan prefiks S (sparepart) lalu P (part).</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">nama_alias</span><span>Nama yang
                                    lazim dipakai teknisi di lapangan, dipisah koma jika lebih dari satu.</span></div>
                        </div>
                    </div>
                </div>

                {{-- FM-05 MARKETING DATA --}}
                <div x-show="formulir === 'marketingdata'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-05 &mdash; MASTER DATA</p>
                            <h2>Data Marketing</h2>
                            <p>Daftar personel sales yang berhak mencatat kunjungan ke prospek perusahaan baru.</p>
                        </div>
                        <span class="dgg-badge" style="color:#4A5D52;">Master</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Setiap marketing harus terdaftar di sini sebelum bisa dipilih sebagai pencatat kunjungan pada
                            formulir Kunjungan Sales (FM-08).</p>
                        <div class="dgg-binbox">
                            <p class="dgg-bin-title">Kolom Formulir</p>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">nama_marketing</span><span>Nama
                                    lengkap personel sales.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">no_telp</span><span>Kontak aktif
                                    untuk koordinasi tim.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">aktif</span><span>Nonaktifkan tanpa
                                    menghapus riwayat kunjungan yang sudah tercatat.</span></div>
                        </div>
                    </div>
                </div>

                {{-- FM-06 PEMASANGAN --}}
                <div x-show="formulir === 'customerinstal'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-06 &mdash; FIELD SERVIS</p>
                            <h2>Pemasangan Mesin</h2>
                            <p>SOP deployment unit baru dari gudang ke kantor pelanggan.</p>
                        </div>
                        <span class="dgg-badge" style="color:#2f5d3a;">Servis</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Mengunci status mesin agar satu unit tidak terkirim dobel ke dua pelanggan berbeda.</p>
                        <div class="dgg-binbox">
                            <p class="dgg-bin-title">Kolom Formulir</p>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">customer_id</span><span>Instansi
                                    tujuan pengiriman.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">machine_id</span><span>Pilihan SN
                                    dibatasi &mdash; hanya unit berstatus <strong>Ready</strong> yang tampil.</span>
                            </div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">tanggal</span><span>Tanggal serah
                                    terima riil di lapangan.</span></div>
                        </div>
                        <div class="dgg-note">
                            <b>Efek otomatis:</b> status mesin berubah dari <em>Ready</em> menjadi <em>Rented</em>, dan
                            customer_id langsung terikat ke unit tersebut.
                        </div>
                    </div>
                </div>

                {{-- FM-07 SERVICE LOG --}}
                <div x-show="formulir === 'servicelog'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-07 &mdash; FIELD SERVIS</p>
                            <h2>Input Servis Teknisi</h2>
                            <p>Catatan kunjungan harian: counter meteran, sparepart terpakai, dan keterangan perbaikan.
                            </p>
                        </div>
                        <span class="dgg-badge" style="color:#2f5d3a;">Servis</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Setelah kunjungan selesai, teknisi wajib mengisi formulir ini. Sistem menghitung otomatis
                            selisih meteran sejak kunjungan sebelumnya.</p>
                        <div class="dgg-binbox">
                            <p class="dgg-bin-title">Kolom Formulir</p>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">counter_bw /
                                    counter_color</span><span>Angka meteran saat ini di lokasi mesin. Tidak boleh lebih
                                    kecil dari counter kunjungan sebelumnya.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">usage_bw /
                                    usage_color</span><span>Selisih pemakaian, terhitung otomatis dan tidak bisa diisi
                                    manual.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">sparepart_id,
                                    jumlah</span><span>Komponen tas teknisi yang dipasang. Memotong langsung saldo tas
                                    teknisi.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">is_mrc</span><span>Centang bila
                                    kunjungan ini adalah pencatatan resmi untuk tagihan bulanan (lihat FM-09).</span>
                            </div>
                        </div>
                        <div class="dgg-flow">
                            <div class="dgg-flow-step"><b>CM</b><span>Darurat</span></div>
                            <div class="dgg-flow-step"><b>RM</b><span>Rutin</span></div>
                            <div class="dgg-flow-step"><b>TN</b><span>Toner</span></div>
                            <div class="dgg-flow-step"><b>RN</b><span>Pasang baru</span></div>
                            <div class="dgg-flow-step"><b>RR</b><span>Ganti unit</span></div>
                        </div>
                    </div>
                </div>

                {{-- FM-08 PROSPECT --}}
                <div x-show="formulir === 'prospect'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-08 &mdash; FIELD SERVIS</p>
                            <h2>Kunjungan Sales</h2>
                            <p>Pencatatan kunjungan marketing ke perusahaan calon pelanggan baru.</p>
                        </div>
                        <span class="dgg-badge" style="color:#2f5d3a;">Servis</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Setiap kali marketing mengunjungi sebuah perusahaan, kunjungan itu ditambahkan sebagai baris
                            riwayat di bawah nama perusahaan yang sama &mdash; bukan data baru yang terpisah. Dengan
                            begitu seluruh tim bisa melihat siapa saja yang sudah pernah datang ke sana.</p>
                        <div class="dgg-binbox">
                            <p class="dgg-bin-title">Kolom Formulir</p>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">nama_perusahaan</span><span>Sistem
                                    mengecek otomatis &mdash; bila nama ini cocok dengan data lama, riwayat kunjungan
                                    sebelumnya akan ditampilkan.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">marketing_id,
                                    tanggal_kunjungan</span><span>Siapa dan kapan kunjungan dilakukan.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">jenis_mesin_existing,
                                    merk_mesin_existing</span><span>Mesin yang sedang dipakai perusahaan tersebut saat
                                    ini, bila ada.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">hasil_kunjungan</span><span>Interest,
                                    Follow Up, Closing, atau Gagal.</span></div>
                        </div>
                        <div class="dgg-note">
                            <b>Bukan rebutan, tapi transparansi:</b> siapa pun boleh mengunjungi perusahaan yang sama
                            lebih dari sekali. Yang dijaga sistem adalah agar setiap kunjungan tercatat di riwayat yang
                            sama, sehingga marketing kedua dan ketiga tahu apa yang sudah dibahas sebelumnya.
                        </div>
                    </div>
                </div>

                {{-- FM-09 MRC LOG --}}
                <div x-show="formulir === 'mrclog'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-09 &mdash; MRC &amp; BILLING</p>
                            <h2>Pencatatan MRC</h2>
                            <p>Pencatatan counter bulanan resmi yang menjadi dasar tagihan ke customer.</p>
                        </div>
                        <span class="dgg-badge" style="color:#B07F22;">MRC</span>
                    </div>
                    <div class="dgg-prose">
                        <p>MRC bukan jenis kunjungan tersendiri &mdash; tetap diinput lewat formulir Servis Teknisi
                            (FM-07) dengan menandai kotak <span class="dgg-mono">is_mrc</span>. Tandai kotak ini hanya
                            saat pencatatan counter memang dimaksudkan untuk dasar tagihan bulan tersebut.</p>
                        <div class="dgg-lock">
                            <b>Penting untuk keakuratan tagihan:</b> pemakaian bulanan dihitung dari selisih dua
                            pencatatan <em>is_mrc</em> yang berurutan, bukan dari kunjungan servis biasa di antaranya.
                            Jika bulan ini lupa ditandai, tagihan bulan depan akan menghitung mundur dari catatan MRC
                            terakhir yang ada &mdash; bisa terlalu besar.
                        </div>
                    </div>
                </div>

                {{-- FM-10 MRC REKAP --}}
                <div x-show="formulir === 'mrcrekap'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-10 &mdash; MRC &amp; BILLING</p>
                            <h2>Rekap Status MRC</h2>
                            <p>Daftar semua unit terpasang beserta status sudah atau belum dicatat MRC bulan ini.</p>
                        </div>
                        <span class="dgg-badge" style="color:#B07F22;">MRC</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Gunakan menu ini di akhir bulan untuk memastikan tidak ada unit terpasang yang terlewat
                            dicatat sebelum tagihan dibuat.</p>
                        <div class="dgg-binbox">
                            <p class="dgg-bin-title">Cara Membaca</p>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">Sudah MRC</span><span>Unit memiliki
                                    pencatatan <span class="dgg-mono">is_mrc</span> pada bulan dan tahun yang
                                    dipilih.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">Belum MRC</span><span>Unit berstatus
                                    Rented tetapi belum ada pencatatan MRC bulan ini &mdash; perlu dikejar sebelum tutup
                                    periode.</span></div>
                        </div>
                    </div>
                </div>

                {{-- FM-11 MRC KONTRAK --}}
                <div x-show="formulir === 'mrckontrak'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-11 &mdash; MRC &amp; BILLING</p>
                            <h2>Kontrak &amp; Tagihan</h2>
                            <p>Harga sewa, kuota gratis, dan harga kelebihan lembar per unit mesin.</p>
                        </div>
                        <span class="dgg-badge" style="color:#B07F22;">MRC</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Setiap unit yang disewakan dengan skema kuota harus memiliki satu kontrak di sini. Tanpa
                            kontrak aktif, unit tersebut tidak akan muncul di perhitungan tagihan.</p>
                        <div class="dgg-binbox">
                            <p class="dgg-bin-title">Kolom Formulir</p>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">harga_sewa</span><span>Biaya sewa
                                    tetap per bulan, tidak tergantung jumlah lembar.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">free_bw, harga_bw</span><span>Kuota
                                    gratis lembar hitam-putih, dan harga per lembar setelah kuota terlampaui.</span>
                            </div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">free_color,
                                    harga_color</span><span>Kuota dan harga kelebihan untuk lembar warna, bila
                                    berlaku.</span></div>
                        </div>
                        <div class="dgg-note">
                            <b>Contoh hitungan:</b> sewa Rp550.000/bulan dengan kuota 1.000 lembar gratis. Bila
                            pemakaian bulan ini 1.227 lembar, kelebihan 227 lembar dikali harga per lembar ditambahkan
                            ke tagihan sewa tetap.
                        </div>
                    </div>
                </div>

                {{-- FM-12 DROP PART --}}
                <div x-show="formulir === 'droppart'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-12 &mdash; LOGISTIK</p>
                            <h2>Drop Part ke Teknisi</h2>
                            <p>Pemberian modal sparepart dari gudang pusat ke tas motor teknisi.</p>
                        </div>
                        <span class="dgg-badge" style="color:#6b4fa0;">Logistik</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Dipakai saat teknisi mengambil bekal sparepart dari kantor sebelum keliling servis lapangan.
                        </p>
                        <div class="dgg-note">
                            <b>Alur otomatis:</b> saat data drop disimpan, stok gudang pusat berkurang dan saldo tas
                            teknisi bertambah sebesar jumlah yang sama &mdash; tidak ada langkah manual tambahan.
                        </div>
                    </div>
                </div>

                {{-- FM-13 TECH STOCK --}}
                <div x-show="formulir === 'techstock'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-13 &mdash; LOGISTIK</p>
                            <h2>Tas Stok Teknisi</h2>
                            <p>Dashboard sisa material yang sedang dibawa keliling tiap teknisi.</p>
                        </div>
                        <span class="dgg-badge" style="color:#6b4fa0;">Logistik</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Halaman pemantauan, bukan tempat input manual. Nilai di sini berubah otomatis dari drop part
                            masuk dan pemakaian di formulir servis.</p>
                        <div class="dgg-binbox">
                            <p class="dgg-bin-title">Kolom yang Ditampilkan</p>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">jumlah</span><span>Sisa saldo aktual
                                    di tas teknisi &mdash; terpotong otomatis setiap kali komponen dipasang via
                                    FM-07.</span></div>
                        </div>
                    </div>
                </div>

                {{-- FM-14 RETUR PART --}}
                <div x-show="formulir === 'returpart'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-14 &mdash; LOGISTIK</p>
                            <h2>Retur Suku Cadang</h2>
                            <p>Pengembalian sisa atau salah bawa komponen dari lapangan ke gudang pusat.</p>
                        </div>
                        <span class="dgg-badge" style="color:#6b4fa0;">Logistik</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Menjaga stok gudang dan tas teknisi tetap sinkron ketika ada komponen yang tidak terpakai.
                        </p>
                        <div class="dgg-lock">
                            <b>Validasi mutlak:</b> jumlah retur tidak boleh melebihi sisa saldo aktual di tas teknisi
                            yang bersangkutan &mdash; sistem menolak otomatis bila dipaksakan.
                        </div>
                    </div>
                </div>

                {{-- FM-15 WITHDRAWAL --}}
                <div x-show="formulir === 'withdrawal'" x-cloak>
                    <div class="dgg-sheet-head">
                        <div>
                            <p class="dgg-formno">FORMULIR FM-15 &mdash; PENARIKAN</p>
                            <h2>Penarikan Unit Mesin</h2>
                            <p>SOP pemutusan kontrak sewa dan pencetakan dokumen jalan.</p>
                        </div>
                        <span class="dgg-badge" style="color:#8B2E2E;">Penarikan</span>
                    </div>
                    <div class="dgg-prose">
                        <p>Digunakan saat masa sewa berakhir, atau unit ditarik karena rusak berat atau menunggak
                            pembayaran.</p>
                        <div class="dgg-binbox">
                            <p class="dgg-bin-title">Kolom Formulir</p>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">machine_id</span><span>Pilihan
                                    dibatasi &mdash; hanya unit berstatus <strong>Rented</strong> yang tampil, lengkap
                                    nama pelanggannya.</span></div>
                            <div class="dgg-bin-row"><span class="dgg-bin-field">kondisi_akhir</span><span>Baik, Rusak
                                    Ringan, atau Rusak Berat.</span></div>
                        </div>
                        <div class="dgg-note">
                            <b>Efek otomatis:</b> status mesin berubah kembali menjadi <em>Ready</em>, dan tersedia
                            tombol cetak Surat Penarikan A5 landscape dengan kolom tanda tangan lapangan.
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ===================================================================
             LEDGER RUMUS
        ==================================================================== --}}
        <div class="dgg-ledger">
            <div class="dgg-ledger-card">
                <p class="dgg-led-label">Rumus &mdash; Stok Gudang Pusat</p>
                <p class="dgg-led-formula">Stok Akhir = Stok Awal &minus; Drop + Retur + Unit Ditarik</p>
            </div>
            <div class="dgg-ledger-card">
                <p class="dgg-led-label">Rumus &mdash; Saldo Tas Teknisi</p>
                <p class="dgg-led-formula">Isi Tas = Drop Masuk &minus; Terpakai &minus; Diretur</p>
            </div>
        </div>

        {{-- ===================================================================
             ESKALASI
        ==================================================================== --}}
        <div class="dgg-escalation">
            <div>
                <p class="dgg-eyebrow">Jalur Eskalasi</p>
                <p>
                    Untuk koreksi data salah input, kegagalan validasi stok, atau permintaan hak akses khusus,
                    hubungi langsung penanggung jawab sistem di kantor pusat Cirebon.
                </p>
            </div>
            <a href="https://wa.me/+62895636675848" target="_blank" class="dgg-call">
                <div class="dgg-call-label">Hubungi via WhatsApp</div>
                <div class="dgg-call-name">Rudi &mdash; Person In Charge</div>
                <div class="dgg-call-num">0895-6366-75848</div>
            </a>
        </div>

        <div class="dgg-colophon">
            <span>&copy; 2026 PT Dinamika Global Gemilang</span>
            <span>DGG System &mdash; Buku Panduan Operasional, Revisi Cirebon</span>
        </div>

    </div>
</x-filament-panels::page>
