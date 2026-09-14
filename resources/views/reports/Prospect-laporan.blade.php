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
            font-size: 10px;
            color: #1e293b;
            background: #fff;
            line-height: 1.35;
        }

        .page {
            width: 297mm;
            min-height: 210mm;
            padding: 10mm 12mm;
            margin: 0 auto;
        }

        /* Header Kop Surat */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2.5px solid #1e40af;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header h2 {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
        }

        .header p {
            margin-top: 2px;
            color: #475569;
            font-size: 10px;
        }

        .header-right {
            text-align: right;
        }

        /* Grand Summary Cards */
        .grand-summary {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10.5px;
        }

        .grand-summary-item {
            display: inline-block;
            margin-right: 15px;
        }

        /* Per Marketing Section Card */
        .marketing-card {
            margin-bottom: 18px;
            page-break-inside: avoid;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            overflow: hidden;
        }

        .marketing-header {
            background: #1e40af;
            color: #ffffff;
            padding: 6px 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .marketing-header h3 {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .marketing-stats {
            display: flex;
            gap: 6px;
        }

        .stat-badge {
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        /* Tabel Data Visits */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            background: #f1f5f9;
            font-weight: bold;
            text-align: left;
            font-size: 9px;
            color: #334155;
            text-transform: uppercase;
        }

        td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 9.5px;
            vertical-align: top;
        }

        td.center,
        th.center {
            text-align: center;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-sub {
            color: #64748b;
            font-size: 8.5px;
            display: block;
            margin-top: 1px;
        }

        .phone-text {
            color: #2563eb;
            font-weight: 600;
        }

        /* Badge Status / Hasil Kunjungan */
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8.5px;
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

        /* Tanda Tangan Footer */
        .footer {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .ttd {
            text-align: center;
            width: 200px;
        }

        .ttd .line {
            margin-top: 50px;
            border-top: 1px solid #333;
        }

        /* Tombol Cetak / Kontrol Tampilan Web */
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
                padding: 8mm;
            }

            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <!-- Tombol Aksi Print -->
    <div class="no-print">
        <button onclick="window.print()" class="btn" style="background:#2563eb; color:#fff;">
            🖨️ Cetak / Simpan PDF Laporan (Landscape)
        </button>
        <button onclick="window.close()" class="btn" style="background:#6b7280; color:#fff; margin-left:6px;">
            ✕ Tutup
        </button>
    </div>

    <div class="page">
        <!-- Header Dokumen -->
        <div class="header">
            <div>
                <h2>PT Dinamika Global Gemilang</h2>
                <p><strong>Laporan Aktivitas & Rekapitulasi Kunjungan Tim Sales / Marketing</strong></p>
                <p style="font-size: 9px; color: #64748b; margin-top: 1px;">Cirebon, Jawa Barat</p>
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
            $conversionRate = $totalVisitAll > 0 ? round(($totalClosingAll / $totalVisitAll) * 100, 1) : 0;
        @endphp

        <!-- Rekapitulasi Keseluruhan -->
        <div class="grand-summary">
            <div>
                <span class="grand-summary-item">Total Visit: <strong
                        style="color:#2563eb;">{{ $totalVisitAll }}</strong></span>
                <span class="grand-summary-item">Kunjungan Awal: <strong>{{ $totalInterestAll }}</strong></span>
                <span class="grand-summary-item">Follow Up: <strong
                        style="color:#d97706;">{{ $totalFollowUpAll }}</strong></span>
                <span class="grand-summary-item">Closing: <strong
                        style="color:#059669;">{{ $totalClosingAll }}</strong></span>
            </div>
            <div>
                <span>
                    Conversion Rate: <strong style="color:#7c3aed;">{{ $conversionRate }}%</strong>
                    <span style="font-size: 8px; color: #64748b; font-weight: normal;">
                        ( Rumus: (Total Closing {{ $totalClosingAll }} ÷ Total Visit {{ $totalVisitAll }}) × 100 )
                    </span>
                </span>
            </div>
        </div>

        <!-- Loop Per Marketing -->
        @forelse ($dataPerMarketing as $data)
            <div class="marketing-card">
                <div class="marketing-header">
                    <h3>👤 Marketing: {{ $data['marketing']->nama_marketing }}</h3>
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
                            <th class="center" style="width: 30px;">No</th>
                            <th class="center" style="width: 70px;">Tanggal</th>
                            <th style="width: 150px;">Perusahaan & Alamat</th>
                            <th style="width: 150px;">Detail PIC (Kontak)</th>
                            <th style="width: 120px;">Mesin Existing</th>
                            <th class="center" style="width: 90px;">Hasil</th>
                            <th>Catatan & Progres Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['visits'] as $i => $visit)
                            @php
                                $prospect = $visit->prospect;
                                $hasil = $visit->hasil_kunjungan;
                                $labelHasil = $hasil === 'Interest' ? 'Kunjungan Awal' : $hasil;
                                $badgeClass = match ($hasil) {
                                    'Interest' => 'badge-kunjungan-awal',
                                    'Follow Up' => 'badge-followup',
                                    'Closing' => 'badge-closing',
                                    'Gagal' => 'badge-gagal',
                                    default => 'badge-followup',
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
                                            style="color: #475569;">{{ Str::limit($prospect->alamat, 60) }}</span>
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
                                    {{ $visit->merk_mesin_existing ?? '-' }}
                                    @if ($visit->jenis_mesin_existing)
                                        <span class="text-sub">{{ $visit->jenis_mesin_existing }}</span>
                                    @endif
                                </td>
                                <td class="center">
                                    <span class="badge {{ $badgeClass }}">{{ $labelHasil }}</span>
                                </td>
                                <td>{{ $visit->catatan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div style="text-align:center; padding:30px; border:1px dashed #cbd5e1; border-radius:6px; color:#94a3b8;">
                Tidak ada data kunjungan sales pada periode {{ $bulan }} {{ $tahun }}.
            </div>
        @endforelse

        <!-- Tanda Tangan Footer -->
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
