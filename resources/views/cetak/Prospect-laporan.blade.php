<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Aktivitas Kunjungan Sales - PT Dinamika Global Gemilang ({{ $bulan }} {{ $tahun }})
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
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
        }

        .header p {
            margin-top: 1px;
            color: #475569;
            font-size: 8.5px;
        }

        .header-right {
            text-align: right;
        }

        /* Ringkasan */
        .summary-wrap {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
        }

        .summary-box {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 3px;
            padding: 5px 8px;
            font-size: 9px;
        }

        .summary-box .title {
            font-size: 7.5px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            letter-spacing: 0.3px;
            margin-bottom: 3px;
        }

        .summary-item {
            display: inline-block;
            margin-right: 10px;
        }

        .formula {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Kartu per marketing */
        .marketing-card {
            margin-bottom: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            overflow: hidden;
        }

        .marketing-header {
            background: #1e40af;
            color: #fff;
            padding: 4px 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .marketing-header h3 {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .marketing-stats {
            display: flex;
            gap: 4px;
        }

        .stat-badge {
            font-size: 8px;
            padding: 1px 5px;
            border-radius: 2px;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
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
            padding: 4px 5px;
            background: #f1f5f9;
            font-weight: bold;
            text-align: left;
            font-size: 7.5px;
            color: #334155;
            text-transform: uppercase;
        }

        td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            font-size: 8.5px;
            vertical-align: top;
        }

        td.center,
        th.center {
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-sub {
            color: #64748b;
            font-size: 7.5px;
            display: block;
            margin-top: 1px;
        }

        .phone-text {
            color: #2563eb;
            font-weight: 600;
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

        .badge-kunjungan-awal {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-followup {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-closing {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-gagal {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-baru {
            background: #e2e8f0;
            color: #334155;
        }

        .fu-date {
            color: #92400e;
            font-weight: bold;
        }

        /* Tanda tangan */
        .footer {
            margin-top: 12px;
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
            margin-top: 32px;
            border-top: 1px solid #333;
        }

        .no-print {
            width: 297mm;
            margin: 10px auto;
            padding: 5px 0;
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

    <div class="no-print">
        <button onclick="window.print()" class="btn" style="background:#2563eb; color:#fff;">
            🖨️ Cetak / Simpan PDF Laporan
        </button>
        <button onclick="window.close()" class="btn" style="background:#6b7280; color:#fff; margin-left:6px;">
            ✕ Tutup
        </button>
    </div>

    <div class="page">
        <!-- Kop -->
        <div class="header">
            <div>
                <h2>PT Dinamika Global Gemilang</h2>
                <p><strong>Laporan Aktivitas & Rekapitulasi Kunjungan Tim Sales / Marketing</strong></p>
                <p style="font-size: 8px; color: #64748b;">Cirebon, Jawa Barat</p>
            </div>
            <div class="header-right">
                <p>Periode: <strong>{{ $bulan }} {{ $tahun }}</strong></p>
                <p>Dicetak: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
            </div>
        </div>

        @php
            $totalVisitAll = $dataPerMarketing->sum('total');
            $totalClosingAll = $dataPerMarketing->sum('closing');
            $totalFollowUpAll = $dataPerMarketing->sum('followup');
            $totalInterestAll = $dataPerMarketing->sum('interest');
            $totalGagalAll = $dataPerMarketing->sum('gagal');
            $conversionRate = $totalVisitAll > 0 ? round(($totalClosingAll / $totalVisitAll) * 100, 1) : 0;

            // Perusahaan unik yang dikunjungi pada periode ini, dikelompokkan berdasarkan Status Prospek
            $prospekUnik = $dataPerMarketing
                ->flatMap(fn($d) => collect($d['visits']))
                ->pluck('prospect')
                ->filter()
                ->unique('id');
            $statusCount = $prospekUnik->countBy('status');

            // Jumlah visit "Follow Up" yang punya jadwal follow-up berikutnya
            $totalJadwalFu = $dataPerMarketing
                ->flatMap(fn($d) => collect($d['visits']))
                ->filter(fn($v) => $v->hasil_kunjungan === 'Follow Up' && $v->tanggal_followup)
                ->count();
        @endphp

        <!-- Rekapitulasi -->
        <div class="summary-wrap">
            <div class="summary-box" style="flex: 1.6;">
                <div class="title">Rekap Kunjungan (Visit)</div>
                <span class="summary-item">Total Visit: <strong
                        style="color:#2563eb;">{{ $totalVisitAll }}</strong></span>
                <span class="summary-item">Kunjungan Awal: <strong>{{ $totalInterestAll }}</strong></span>
                <span class="summary-item">Follow Up: <strong
                        style="color:#d97706;">{{ $totalFollowUpAll }}</strong></span>
                <span class="summary-item">Closing: <strong
                        style="color:#059669;">{{ $totalClosingAll }}</strong></span>
                <span class="summary-item">Gagal: <strong style="color:#dc2626;">{{ $totalGagalAll }}</strong></span>
                <div class="formula">
                    Conversion Rate: <strong style="color:#7c3aed;">{{ $conversionRate }}%</strong>
                    ( Rumus: (Total Closing {{ $totalClosingAll }} ÷ Total Visit {{ $totalVisitAll }}) × 100 )
                </div>
            </div>

            <div class="summary-box">
                <div class="title">Status Prospek (Perusahaan Unik: {{ $prospekUnik->count() }})</div>
                <span class="summary-item">Baru: <strong>{{ $statusCount['Baru'] ?? 0 }}</strong></span>
                <span class="summary-item">Proses: <strong
                        style="color:#d97706;">{{ $statusCount['Proses'] ?? 0 }}</strong></span>
                <span class="summary-item">Closing: <strong
                        style="color:#059669;">{{ $statusCount['Closing'] ?? 0 }}</strong></span>
                <span class="summary-item">Gagal: <strong
                        style="color:#dc2626;">{{ $statusCount['Gagal'] ?? 0 }}</strong></span>
                <div class="formula">Jadwal follow-up terdata: <strong>{{ $totalJadwalFu }}</strong> kunjungan</div>
            </div>
        </div>

        <!-- Per Marketing -->
        @forelse ($dataPerMarketing as $data)
            <div class="marketing-card">
                <div class="marketing-header">
                    <h3>Marketing: {{ $data['marketing']->nama_marketing }}</h3>
                    <div class="marketing-stats">
                        <span class="stat-badge">Total: {{ $data['total'] }}</span>
                        <span class="stat-badge">Awal: {{ $data['interest'] }}</span>
                        <span class="stat-badge">Follow Up: {{ $data['followup'] }}</span>
                        <span class="stat-badge" style="background:#059669;">Closing: {{ $data['closing'] }}</span>
                        <span class="stat-badge" style="background:#dc2626;">Gagal: {{ $data['gagal'] }}</span>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th class="center" style="width: 22px;">No</th>
                            <th class="center" style="width: 58px;">Tgl Kunjungan</th>
                            <th style="width: 150px;">Perusahaan & Alamat</th>
                            <th style="width: 120px;">Detail PIC</th>
                            <th style="width: 100px;">Mesin Existing</th>
                            <th class="center" style="width: 60px;">Status Prospek</th>
                            <th class="center" style="width: 65px;">Hasil Kunjungan</th>
                            <th class="center" style="width: 58px;">Tgl Follow-up</th>
                            <th>Catatan & Progres Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['visits'] as $i => $visit)
                            @php
                                $prospect = $visit->prospect;

                                // Hasil kunjungan
                                $hasil = $visit->hasil_kunjungan;
                                $labelHasil = $hasil === 'Interest' ? 'Kunjungan Awal' : $hasil;
                                $badgeClass = match ($hasil) {
                                    'Interest' => 'badge-kunjungan-awal',
                                    'Follow Up' => 'badge-followup',
                                    'Closing' => 'badge-closing',
                                    'Gagal' => 'badge-gagal',
                                    default => 'badge-followup',
                                };

                                // Status prospek (Baru / Proses / Closing / Gagal)
                                $statusProspek = $prospect?->status;
                                $statusClass = match ($statusProspek) {
                                    'Baru' => 'badge-baru',
                                    'Proses' => 'badge-followup',
                                    'Closing' => 'badge-closing',
                                    'Gagal' => 'badge-gagal',
                                    default => 'badge-baru',
                                };
                            @endphp
                            <tr>
                                <td class="center">{{ $i + 1 }}</td>
                                <td class="center">
                                    {{ $visit->tanggal_kunjungan ? $visit->tanggal_kunjungan->format('d/m/Y') : '-' }}
                                </td>
                                <td>
                                    <strong>{{ $prospect?->nama_perusahaan ?? '-' }}</strong>
                                    @if ($prospect?->kota)
                                        <span class="text-sub">{{ $prospect->kota }}</span>
                                    @endif
                                    @if ($prospect?->alamat)
                                        <span class="text-sub"
                                            style="color: #475569;">{{ Str::limit($prospect->alamat, 70) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $prospect?->pic_nama ?? '-' }}</strong>
                                    @if ($prospect?->pic_jabatan)
                                        <span class="text-sub">Jabatan: {{ $prospect->pic_jabatan }}</span>
                                    @endif
                                    @if ($prospect?->pic_telp)
                                        <span class="text-sub phone-text">{{ $prospect->pic_telp }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $visit->merk_mesin_existing ?: '-' }}</strong>
                                    @if ($visit->jenis_mesin_existing)
                                        <span class="text-sub">{{ $visit->jenis_mesin_existing }}</span>
                                    @endif
                                </td>
                                <td class="center">
                                    @if ($statusProspek)
                                        <span class="badge {{ $statusClass }}">{{ $statusProspek }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="center">
                                    <span class="badge {{ $badgeClass }}">{{ $labelHasil }}</span>
                                </td>
                                <td class="center">
                                    @if ($visit->tanggal_followup)
                                        <span class="fu-date">{{ $visit->tanggal_followup->format('d/m/Y') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $visit->catatan ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div style="text-align:center; padding:20px; border:1px dashed #cbd5e1; border-radius:4px; color:#94a3b8;">
                Tidak ada data kunjungan sales pada periode {{ $bulan }} {{ $tahun }}.
            </div>
        @endforelse

        <!-- Tanda Tangan -->
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

</body>

</html>
