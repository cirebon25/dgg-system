<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Ringkasan Eksekutif Kunjungan Sales - PT Dinamika Global Gemilang ({{ $bulan }} {{ $tahun }})
    </title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 8.5px;
            color: #1e293b;
            background: #fff;
            line-height: 1.3;
        }

        .page {
            width: 297mm;
            padding: 8mm 10mm;
            margin: 0 auto;
        }

        .page-break {
            page-break-after: always;
        }

        /* Kop */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        .header h2 {
            font-size: 13px;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: .5px;
        }

        .header p {
            margin-top: 1px;
            color: #475569;
            font-size: 8.5px;
        }

        .header-right {
            text-align: right;
        }

        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #1e40af;
            border-left: 3px solid #1e40af;
            padding-left: 5px;
            margin: 10px 0 5px;
        }

        .grid {
            display: flex;
            gap: 8px;
        }

        .col {
            flex: 1;
        }

        /* KPI */
        .kpi {
            flex: 1;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 8px;
            background: #f8fafc;
        }

        .kpi .label {
            font-size: 7.5px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }

        .kpi .value {
            font-size: 18px;
            font-weight: bold;
            line-height: 1.1;
            margin-top: 2px;
        }

        .kpi .note {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 1px;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th {
            border: 1px solid #cbd5e1;
            padding: 3px 5px;
            background: #f1f5f9;
            font-size: 7.5px;
            text-transform: uppercase;
            color: #334155;
            text-align: left;
        }

        td {
            border: 1px solid #cbd5e1;
            padding: 3px 5px;
            font-size: 8.5px;
            vertical-align: top;
        }

        td.c,
        th.c {
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .sub {
            display: block;
            color: #64748b;
            font-size: 7.5px;
        }

        /* Bar */
        .bar-row {
            display: flex;
            align-items: center;
            margin-bottom: 3px;
        }

        .bar-label {
            width: 85px;
            font-size: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-right: 4px;
        }

        .bar-track {
            flex: 1;
            background: #e2e8f0;
            height: 9px;
            border-radius: 2px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            background: #3b82f6;
        }

        .bar-val {
            width: 26px;
            text-align: right;
            font-weight: bold;
            font-size: 8.5px;
        }

        /* Badge */
        .badge {
            padding: 1px 4px;
            border-radius: 2px;
            font-weight: bold;
            font-size: 7.5px;
            display: inline-block;
            white-space: nowrap;
        }

        .b-awal {
            background: #dbeafe;
            color: #1e40af;
        }

        .b-fu {
            background: #fef3c7;
            color: #92400e;
        }

        .b-closing {
            background: #d1fae5;
            color: #065f46;
        }

        .b-gagal {
            background: #fee2e2;
            color: #991b1b;
        }

        .b-baru {
            background: #e2e8f0;
            color: #334155;
        }

        .late {
            color: #b91c1c;
            font-weight: bold;
        }

        /* Insight */
        .insight {
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            border-radius: 4px;
            padding: 5px 10px;
        }

        .insight li {
            margin-left: 12px;
            margin-bottom: 2px;
            font-size: 8.5px;
        }

        /* Keputusan */
        .decision {
            border: 1px solid #94a3b8;
            border-radius: 4px;
            padding: 6px 10px;
        }

        .decision .opt {
            display: inline-block;
            width: 49%;
            margin-bottom: 3px;
        }

        .decision .line {
            border-bottom: 1px dotted #94a3b8;
            height: 14px;
        }

        .footer {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .ttd {
            text-align: center;
            width: 180px;
            font-size: 8.5px;
        }

        .ttd .line {
            margin-top: 30px;
            border-top: 1px solid #333;
        }

        .no-print {
            width: 297mm;
            margin: 10px auto;
            text-align: right;
        }

        .btn {
            padding: 6px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 11px;
        }

        @media print {
            .no-print {
                display: none;
            }

            .page {
                margin: 0;
                padding: 0;
                width: 100%;
            }

            @page {
                size: A4 landscape;
                margin: 8mm;
            }

            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    @php
        $today = \Carbon\Carbon::today();

        // ====== Kumpulkan semua data ======
        $allVisits = $dataPerMarketing->flatMap(fn($d) => collect($d['visits']))->values();
        $prospekUnik = $allVisits->pluck('prospect')->filter()->unique('id');

        $totalVisit = $dataPerMarketing->sum('total');
        $totalAwal = $dataPerMarketing->sum('interest');
        $totalFu = $dataPerMarketing->sum('followup');
        $totalClosing = $dataPerMarketing->sum('closing');
        $totalGagal = $dataPerMarketing->sum('gagal');

        $convVisit = $totalVisit > 0 ? round(($totalClosing / $totalVisit) * 100, 1) : 0;

        // Status prospek (perusahaan unik)
        $statusCount = $prospekUnik->countBy('status');
        $sBaru = $statusCount['Baru'] ?? 0;
        $sProses = $statusCount['Proses'] ?? 0;
        $sClosing = $statusCount['Closing'] ?? 0;
        $sGagal = $statusCount['Gagal'] ?? 0;
        $jumlahProspek = $prospekUnik->count();
        $convProspek = $jumlahProspek > 0 ? round(($sClosing / $jumlahProspek) * 100, 1) : 0;
        $maxStatus = max(1, $sBaru, $sProses, $sClosing, $sGagal);

        // Follow-up: kunjungan terakhir per perusahaan yang masih "Follow Up" & prospek belum Closing/Gagal
        $antrianFu = $allVisits
            ->filter(fn($v) => $v->prospect && $v->hasil_kunjungan === 'Follow Up' && $v->tanggal_followup)
            ->filter(fn($v) => !in_array($v->prospect->status, ['Closing', 'Gagal']))
            ->sortByDesc('tanggal_kunjungan')
            ->unique(fn($v) => $v->prospect->id)
            ->sortBy('tanggal_followup')
            ->values();
        $fuTelat = $antrianFu->filter(fn($v) => $v->tanggal_followup->lt($today))->count();

        // Peta pasar (data dirapikan dulu: huruf besar/kecil, spasi, dan teks bebas)
        $perKota = $prospekUnik
            ->groupBy(fn($p) => filled($p->kota) ? Str::title(mb_strtolower(trim($p->kota))) : 'Tidak diisi')
            ->map->count()
            ->sortDesc()
            ->take(6);
        $maxKota = max(1, (int) $perKota->max());

        // Merk = kata pertama isian (contoh: "Canon iR525, 4..." menjadi "CANON")
        $perMerk = $allVisits
            ->filter(fn($v) => filled($v->merk_mesin_existing))
            ->groupBy(
                fn($v) => mb_strtoupper(
                    preg_split('/[\s,\/\-]+/', trim($v->merk_mesin_existing), -1, PREG_SPLIT_NO_EMPTY)[0] ?? '-',
                ),
            )
            ->map(fn($g) => $g->map(fn($v) => $v->prospect?->id)->filter()->unique()->count())
            ->sortDesc()
            ->take(6);
        $maxMerk = max(1, (int) $perMerk->max());

        // Jenis mesin: isian dipecah per koma, lalu dihitung per perusahaan
        $perJenis = $allVisits
            ->filter(fn($v) => filled($v->jenis_mesin_existing))
            ->flatMap(
                fn($v) => collect(preg_split('/[,;\/&]+/', $v->jenis_mesin_existing, -1, PREG_SPLIT_NO_EMPTY))
                    ->map(fn($j) => Str::title(trim($j)))
                    ->filter()
                    ->unique()
                    ->map(fn($j) => ['j' => $j, 'p' => $v->prospect?->id]),
            )
            ->groupBy('j')
            ->map(fn($g) => $g->pluck('p')->filter()->unique()->count())
            ->sortDesc()
            ->take(6);
        $maxJenis = max(1, (int) $perJenis->max());

        // Performa marketing
        $perfMarketing = $dataPerMarketing
            ->map(function ($d) {
                $visits = collect($d['visits']);
                $perusahaan = $visits->map(fn($v) => $v->prospect?->id)->filter()->unique()->count();
                return [
                    'nama' => $d['marketing']->nama_marketing,
                    'total' => $d['total'],
                    'perusahaan' => $perusahaan,
                    'awal' => $d['interest'],
                    'fu' => $d['followup'],
                    'closing' => $d['closing'],
                    'gagal' => $d['gagal'],
                    'conv' => $d['total'] > 0 ? round(($d['closing'] / $d['total']) * 100, 1) : 0,
                ];
            })
            ->values();
        $maxVisitMkt = max(1, (int) $perfMarketing->max('total'));

        // Closing & Gagal
        $listClosing = $allVisits->where('hasil_kunjungan', 'Closing')->sortByDesc('tanggal_kunjungan')->values();
        $listGagal = $allVisits->where('hasil_kunjungan', 'Gagal')->sortByDesc('tanggal_kunjungan')->values();

        // Insight otomatis
        $insights = [];
        if ($totalVisit > 0) {
            $insights[] = "Periode ini tercatat <strong>{$totalVisit} kunjungan</strong> ke <strong>{$jumlahProspek} perusahaan</strong>; <strong>{$totalClosing}</strong> closing (conversion visit {$convVisit}%, conversion perusahaan {$convProspek}%).";
            $terbaik = $perfMarketing->where('total', '>', 0)->sortByDesc('conv')->first();
            $terbanyak = $perfMarketing->sortByDesc('total')->first();
            if ($terbanyak) {
                $insights[] = "Visit terbanyak: <strong>{$terbanyak['nama']}</strong> ({$terbanyak['total']} visit). Conversion tertinggi: <strong>{$terbaik['nama']}</strong> ({$terbaik['conv']}%).";
            }
            if ($sProses > 0) {
                $insights[] = "<strong>{$sProses} perusahaan</strong> masih berstatus Proses dan perlu didorong agar closing.";
            }
            if ($fuTelat > 0) {
                $insights[] = "<span class=\"late\">{$fuTelat} jadwal follow-up sudah lewat tanggal</span> dan belum ada kunjungan lanjutan.";
            }
            if ($perKota->isNotEmpty()) {
                $insights[] = "Kota prospek terbanyak: <strong>{$perKota->keys()->first()}</strong> ({$perKota->first()} perusahaan).";
            }
            if ($perMerk->isNotEmpty()) {
                $insights[] = "Merk mesin existing terbanyak di customer prospek: <strong>{$perMerk->keys()->first()}</strong> ({$perMerk->first()} perusahaan).";
            }
            if ($totalGagal > 0) {
                $insights[] = "Terdapat <strong>{$totalGagal} kunjungan gagal</strong>; alasan ada di halaman 2 sebagai bahan evaluasi.";
            }
        }

        // ====== Ambang batas rekomendasi (ATUR SESUAI KEBIJAKAN PERUSAHAAN) ======
        $targetConversion = 10; // % minimal closing dari total visit
        $minVisitMarketing = 10; // jumlah visit minimal per marketing per periode
        $batasDiamHari = 14; // prospek status Proses tanpa kunjungan lebih dari N hari

        // Prospek berstatus Proses yang lama tidak dikunjungi
        $prospekDiam = $allVisits
            ->filter(fn($v) => $v->prospect && $v->prospect->status === 'Proses' && $v->tanggal_kunjungan)
            ->groupBy(fn($v) => $v->prospect->id)
            ->map(fn($g) => $g->sortByDesc('tanggal_kunjungan')->first())
            ->filter(fn($v) => $v->tanggal_kunjungan->lt($today->copy()->subDays($batasDiamHari)))
            ->values();

        $rekomendasi = [];
        if ($fuTelat > 0) {
            $namaTelat = $antrianFu
                ->filter(fn($v) => $v->tanggal_followup->lt($today))
                ->map(fn($v) => $v->marketing?->nama_marketing)
                ->filter()
                ->unique()
                ->implode(', ');
            $rekomendasi[] =
                "Selesaikan <strong>{$fuTelat} follow-up yang terlambat</strong>" .
                ($namaTelat ? " (marketing: {$namaTelat})" : '') .
                '.';
        }
        if ($prospekDiam->count() > 0) {
            $rekomendasi[] = "<strong>{$prospekDiam->count()} prospek berstatus Proses</strong> tidak dikunjungi lebih dari {$batasDiamHari} hari; putuskan dilanjutkan atau ditutup.";
        }
        if ($totalVisit > 0 && $convVisit < $targetConversion) {
            $rekomendasi[] = "Conversion <strong>{$convVisit}%</strong> di bawah target {$targetConversion}%; evaluasi pendekatan dan penawaran.";
        }
        $kurangVisit = $perfMarketing->filter(fn($m) => $m['total'] < $minVisitMarketing)->pluck('nama');
        if ($kurangVisit->isNotEmpty()) {
            $rekomendasi[] =
                "Kunjungan di bawah minimum {$minVisitMarketing} visit: <strong>" .
                $kurangVisit->implode(', ') .
                '</strong>.';
        }
        if ($listGagal->count() > 0) {
            $rekomendasi[] = "Tinjau alasan <strong>{$listGagal->count()} kunjungan gagal</strong> untuk memperbaiki penawaran.";
        }

        $badgeHasil = fn($h) => match ($h) {
            'Interest' => ['b-awal', 'Kunjungan Awal'],
            'Follow Up' => ['b-fu', 'Follow Up'],
            'Closing' => ['b-closing', 'Closing'],
            'Gagal' => ['b-gagal', 'Gagal'],
            default => ['b-fu', $h ?? '-'],
        };
    @endphp

    <div class="no-print">
        <button onclick="window.print()" class="btn" style="background:#2563eb; color:#fff;">🖨️ Cetak / Simpan
            PDF</button>
        <button onclick="window.close()" class="btn" style="background:#6b7280; color:#fff; margin-left:6px;">✕
            Tutup</button>
    </div>

    {{-- ================= HALAMAN 1 : GAMBARAN UMUM ================= --}}
    <div class="page page-break">
        <div class="header">
            <div>
                <h2>PT Dinamika Global Gemilang</h2>
                <p><strong>Ringkasan Eksekutif Kunjungan Sales / Marketing</strong> &mdash; Bahan Pengambilan Keputusan
                </p>
                <p style="font-size: 8px; color:#64748b;">Cirebon, Jawa Barat</p>
            </div>
            <div class="header-right">
                <p>Periode: <strong>{{ $bulan }} {{ $tahun }}</strong></p>
                <p>Dicetak: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
            </div>
        </div>

        @if ($totalVisit === 0)
            <div style="text-align:center; padding:30px; border:1px dashed #cbd5e1; border-radius:4px; color:#94a3b8;">
                Tidak ada data kunjungan sales pada periode {{ $bulan }} {{ $tahun }}.
            </div>
        @else
            {{-- KPI --}}
            <div class="grid">
                <div class="kpi">
                    <div class="label">Total Visit</div>
                    <div class="value" style="color:#2563eb;">{{ $totalVisit }}</div>
                    <div class="note">Awal {{ $totalAwal }} &bull; Follow Up {{ $totalFu }}</div>
                </div>
                <div class="kpi">
                    <div class="label">Perusahaan Dikunjungi</div>
                    <div class="value">{{ $jumlahProspek }}</div>
                    <div class="note">Perusahaan unik pada periode</div>
                </div>
                <div class="kpi">
                    <div class="label">Closing</div>
                    <div class="value" style="color:#059669;">{{ $totalClosing }}</div>
                    <div class="note">Gagal: {{ $totalGagal }}</div>
                </div>
                <div class="kpi">
                    <div class="label">Conversion Rate</div>
                    <div class="value" style="color:#7c3aed;">{{ $convVisit }}%</div>
                    <div class="note">Closing {{ $totalClosing }} &divide; Visit {{ $totalVisit }}</div>
                </div>
                <div class="kpi">
                    <div class="label">Follow-up Terlambat</div>
                    <div class="value" style="color:{{ $fuTelat > 0 ? '#dc2626' : '#059669' }};">{{ $fuTelat }}
                    </div>
                    <div class="note">Dari {{ $antrianFu->count() }} jadwal follow-up aktif</div>
                </div>
            </div>

            <div class="grid">
                {{-- Pipeline --}}
                <div class="col">
                    <div class="section-title">Pipeline Status Prospek (per perusahaan)</div>
                    @foreach ([['Baru', $sBaru, '#94a3b8'], ['Proses', $sProses, '#f59e0b'], ['Closing', $sClosing, '#10b981'], ['Gagal', $sGagal, '#ef4444']] as [$lbl, $val, $clr])
                        <div class="bar-row">
                            <div class="bar-label">{{ $lbl }}</div>
                            <div class="bar-track">
                                <div class="bar-fill"
                                    style="width: {{ ($val / $maxStatus) * 100 }}%; background: {{ $clr }};">
                                </div>
                            </div>
                            <div class="bar-val">{{ $val }}</div>
                        </div>
                    @endforeach
                    <div style="font-size:7.5px; color:#64748b; margin-top:2px;">
                        Conversion perusahaan: Closing {{ $sClosing }} &divide; {{ $jumlahProspek }} perusahaan =
                        {{ $convProspek }}%
                    </div>
                </div>

                {{-- Insight --}}
                <div class="col" style="flex: 1.5;">
                    <div class="section-title">Poin Penting (otomatis dari data)</div>
                    <div class="insight">
                        <ul>
                            @foreach ($insights as $text)
                                <li>{!! $text !!}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Performa Marketing --}}
            <div class="section-title">Performa Per Marketing</div>
            <table>
                <thead>
                    <tr>
                        <th>Marketing</th>
                        <th class="c" style="width:45px;">Visit</th>
                        <th class="c" style="width:60px;">Perusahaan</th>
                        <th class="c" style="width:45px;">Awal</th>
                        <th class="c" style="width:55px;">Follow Up</th>
                        <th class="c" style="width:45px;">Closing</th>
                        <th class="c" style="width:45px;">Gagal</th>
                        <th class="c" style="width:55px;">Conversion</th>
                        <th style="width:170px;">Aktivitas (visit)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($perfMarketing as $m)
                        <tr>
                            <td><strong>{{ $m['nama'] }}</strong></td>
                            <td class="c">{{ $m['total'] }}</td>
                            <td class="c">{{ $m['perusahaan'] }}</td>
                            <td class="c">{{ $m['awal'] }}</td>
                            <td class="c">{{ $m['fu'] }}</td>
                            <td class="c"><strong style="color:#059669;">{{ $m['closing'] }}</strong></td>
                            <td class="c"><strong style="color:#dc2626;">{{ $m['gagal'] }}</strong></td>
                            <td class="c"><strong>{{ $m['conv'] }}%</strong></td>
                            <td>
                                <div class="bar-track" style="height:7px;">
                                    <div class="bar-fill" style="width: {{ ($m['total'] / $maxVisitMkt) * 100 }}%;">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ================= HALAMAN 2 : TINDAK LANJUT & KEPUTUSAN ================= --}}
    @if ($totalVisit > 0)
        <div class="page">
            <div class="header">
                <div>
                    <h2>Tindak Lanjut &amp; Keputusan</h2>
                    <p>Periode {{ $bulan }} {{ $tahun }} &mdash; PT Dinamika Global Gemilang</p>
                </div>
                <div class="header-right">
                    <p>Halaman 2</p>
                </div>
            </div>

            {{-- Prioritas follow-up --}}
            <div class="section-title">Prioritas Follow-up (urut jadwal terdekat)</div>
            <table>
                <thead>
                    <tr>
                        <th class="c" style="width:22px;">No</th>
                        <th class="c" style="width:62px;">Jadwal FU</th>
                        <th>Perusahaan</th>
                        <th style="width:130px;">PIC &amp; Telp</th>
                        <th style="width:100px;">Mesin Existing</th>
                        <th class="c" style="width:60px;">Status</th>
                        <th style="width:90px;">Marketing</th>
                        <th>Catatan Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($antrianFu->take(12) as $i => $v)
                        @php
                            $p = $v->prospect;
                            $telat = $v->tanggal_followup->lt($today);
                        @endphp
                        <tr>
                            <td class="c">{{ $i + 1 }}</td>
                            <td class="c {{ $telat ? 'late' : '' }}">
                                {{ $v->tanggal_followup->format('d/m/Y') }}
                                @if ($telat)
                                    <span class="sub late">Terlambat</span>
                                @endif
                            </td>
                            <td><strong>{{ $p->nama_perusahaan }}</strong><span
                                    class="sub">{{ $p->kota ?: '-' }}</span></td>
                            <td>{{ $p->pic_nama ?: '-' }}<span class="sub">{{ $p->pic_jabatan }}
                                    {{ $p->pic_telp ? '• ' . $p->pic_telp : '' }}</span></td>
                            <td>{{ $v->merk_mesin_existing ?: '-' }}<span
                                    class="sub">{{ $v->jenis_mesin_existing }}</span></td>
                            <td class="c"><span
                                    class="badge {{ $p->status === 'Proses' ? 'b-fu' : 'b-baru' }}">{{ $p->status }}</span>
                            </td>
                            <td>{{ $v->marketing?->nama_marketing ?? '-' }}</td>
                            <td>{{ Str::limit($v->catatan ?: '-', 90) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="c" style="color:#94a3b8; padding:8px;">Tidak ada jadwal
                                follow-up aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($antrianFu->count() > 12)
                <div style="font-size:7.5px; color:#64748b; margin-top:2px;">Menampilkan 12 dari
                    {{ $antrianFu->count() }} jadwal follow-up.</div>
            @endif

            {{-- Peta pasar --}}
            <div class="grid">
                <div class="col">
                    <div class="section-title">Sebaran Kota Prospek</div>
                    @forelse ($perKota as $kota => $n)
                        <div class="bar-row">
                            <div class="bar-label" title="{{ $kota }}">{{ $kota }}</div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width: {{ ($n / $maxKota) * 100 }}%;"></div>
                            </div>
                            <div class="bar-val">{{ $n }}</div>
                        </div>
                    @empty
                        <div style="color:#94a3b8;">Belum ada data kota.</div>
                    @endforelse
                </div>
                <div class="col">
                    <div class="section-title">Merk Mesin Existing</div>
                    @forelse ($perMerk as $merk => $n)
                        <div class="bar-row">
                            <div class="bar-label" title="{{ $merk }}">{{ $merk }}</div>
                            <div class="bar-track">
                                <div class="bar-fill"
                                    style="width: {{ ($n / $maxMerk) * 100 }}%; background:#8b5cf6;"></div>
                            </div>
                            <div class="bar-val">{{ $n }}</div>
                        </div>
                    @empty
                        <div style="color:#94a3b8;">Belum ada data merk mesin.</div>
                    @endforelse
                </div>
                <div class="col">
                    <div class="section-title">Jenis Mesin Existing</div>
                    @forelse ($perJenis as $jenis => $n)
                        <div class="bar-row">
                            <div class="bar-label" title="{{ $jenis }}">{{ $jenis }}</div>
                            <div class="bar-track">
                                <div class="bar-fill"
                                    style="width: {{ ($n / $maxJenis) * 100 }}%; background:#0ea5e9;"></div>
                            </div>
                            <div class="bar-val">{{ $n }}</div>
                        </div>
                    @empty
                        <div style="color:#94a3b8;">Belum ada data jenis mesin.</div>
                    @endforelse
                </div>
            </div>
            <div style="font-size:7.5px; color:#64748b; margin-top:2px;">
                Jumlah = perusahaan unik. Merk diambil dari kata pertama isian merk mesin; jenis mesin dipecah per koma.
            </div>

            {{-- Closing & Gagal --}}
            <div class="grid">
                <div class="col">
                    <div class="section-title">Hasil Closing</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Perusahaan</th>
                                <th style="width:80px;">Marketing</th>
                                <th class="c" style="width:55px;">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($listClosing->take(8) as $v)
                                <tr>
                                    <td><strong>{{ $v->prospect?->nama_perusahaan ?? '-' }}</strong><span
                                            class="sub">{{ Str::limit($v->catatan ?: '', 60) }}</span></td>
                                    <td>{{ $v->marketing?->nama_marketing ?? '-' }}</td>
                                    <td class="c">{{ $v->tanggal_kunjungan?->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="c" style="color:#94a3b8;">Belum ada closing.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <div class="section-title">Kunjungan Gagal &amp; Alasan (bahan evaluasi)</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Perusahaan</th>
                                <th>Alasan / Catatan</th>
                                <th style="width:70px;">Marketing</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($listGagal->take(8) as $v)
                                <tr>
                                    <td><strong>{{ $v->prospect?->nama_perusahaan ?? '-' }}</strong></td>
                                    <td>{{ Str::limit($v->catatan ?: '-', 80) }}</td>
                                    <td>{{ $v->marketing?->nama_marketing ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="c" style="color:#94a3b8;">Tidak ada kunjungan
                                        gagal.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Rekomendasi berdasarkan data --}}
            <div class="section-title">Rekomendasi Berdasarkan Data</div>
            <div class="insight">
                @if (count($rekomendasi))
                    <ul>
                        @foreach ($rekomendasi as $r)
                            <li>{!! $r !!}</li>
                        @endforeach
                    </ul>
                @else
                    Semua indikator berada di atas ambang batas yang ditetapkan.
                @endif
                <div style="font-size:7.5px; color:#64748b; margin-top:3px;">
                    Ambang batas: conversion minimal {{ $targetConversion }}%, minimal {{ $minVisitMarketing }} visit
                    per marketing,
                    prospek Proses maksimal {{ $batasDiamHari }} hari tanpa kunjungan.
                </div>
            </div>

            {{-- Keputusan manajemen --}}
            <div class="section-title">Keputusan / Arahan Manajemen</div>
            <div class="decision">
                <div>
                    <span class="opt">☐ Lanjutkan follow-up prospek prioritas</span>
                    <span class="opt">☐ Tambah / ubah target kunjungan marketing</span>
                    <span class="opt">☐ Siapkan penawaran khusus untuk prospek Proses</span>
                    <span class="opt">☐ Evaluasi pendekatan pada kunjungan gagal</span>
                    <span class="opt">☐ Fokus wilayah / kota tertentu</span>
                    <span class="opt">☐ Fokus pada merk mesin existing tertentu</span>
                </div>
                <div style="margin-top:4px;">Catatan / arahan:</div>
                <div class="line"></div>
                <div class="line"></div>
            </div>

            <div class="footer">
                <div class="ttd">
                    <p>Mengetahui,</p>
                    <p><strong>Kepala Divisi / Manager</strong></p>
                    <div class="line"></div>
                    <p>( ................................... )</p>
                </div>
                <div class="ttd">
                    <p>Cirebon, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</p>
                    <p><strong>Dibuat Oleh, Admin</strong></p>
                    <div class="line"></div>
                    <p>( ................................... )</p>
                </div>
            </div>
        </div>
    @endif

</body>

</html>
