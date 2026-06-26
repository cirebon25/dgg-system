<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kunjungan Sales - {{ $bulan }} {{ $tahun }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm 8mm;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 16px;
        }

        .header h2 {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin-top: 3px;
        }

        hr {
            border: none;
            border-top: 1.5px solid #333;
            margin: 8px 0 14px;
        }

        .marketing-block {
            margin-bottom: 24px;
            page-break-inside: avoid;
        }

        .marketing-name {
            background: #2563eb;
            color: #fff;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12px;
            border-radius: 4px 4px 0 0;
        }

        .summary-row {
            display: flex;
            gap: 8px;
            padding: 8px 12px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-top: none;
        }

        .summary-item {
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
        }

        .si-total {
            background: #e0e7ff;
            color: #3730a3;
        }

        .si-interest {
            background: #dbeafe;
            color: #1e40af;
        }

        .si-followup {
            background: #fef3c7;
            color: #92400e;
        }

        .si-closing {
            background: #d1fae5;
            color: #065f46;
        }

        .si-gagal {
            background: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            background: #e2e8f0;
            font-weight: bold;
            text-align: left;
            font-size: 10px;
        }

        td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-size: 10px;
        }

        td.center,
        th.center {
            text-align: center;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }

        .badge {
            padding: 2px 7px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 9px;
        }

        .badge-interest {
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

        .grand-summary {
            margin-top: 8px;
            padding: 10px 14px;
            background: #fffbeb;
            border: 1px solid #f59e0b;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .ttd {
            text-align: center;
            width: 180px;
        }

        .ttd .line {
            margin-top: 55px;
            border-top: 1px solid #333;
        }

        .no-print {
            width: 210mm;
            margin: 10px auto;
            padding: 10px 8mm;
        }

        @media print {
            .no-print {
                display: none;
            }

            .page {
                margin: 0;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()"
            style="padding:7px 18px; background:#2563eb; color:#fff; border:none; border-radius:4px; cursor:pointer;">
            🖨️ Print
        </button>
        <button onclick="window.close()"
            style="padding:7px 18px; background:#6b7280; color:#fff; border:none; border-radius:4px; cursor:pointer; margin-left:8px;">
            ✕ Tutup
        </button>
    </div>

    <div class="page">
        <div class="header">
            <h2>Laporan Kunjungan Sales — {{ $bulan }} {{ $tahun }}</h2>
            <p>Periode &nbsp;&nbsp;: {{ $bulan }} {{ $tahun }}</p>
            <p>Dicetak &nbsp;&nbsp;: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
        </div>
        <hr>

        @php
            $totalVisitAll = $dataPerMarketing->sum('total');
            $totalClosingAll = $dataPerMarketing->sum('closing');
        @endphp

        <div class="grand-summary">
            Total Kunjungan Semua Marketing: {{ $totalVisitAll }} visit
            &nbsp;|&nbsp;
            Total Closing: {{ $totalClosingAll }}
            &nbsp;|&nbsp;
            Conversion Rate: {{ $totalVisitAll > 0 ? round(($totalClosingAll / $totalVisitAll) * 100, 1) : 0 }}%
        </div>

        <br>

        @forelse ($dataPerMarketing as $data)
            <div class="marketing-block">
                <div class="marketing-name">
                    {{ $data['marketing']->nama_marketing }}
                </div>

                <div class="summary-row">
                    <div class="summary-item si-total">Total: {{ $data['total'] }}</div>
                    <div class="summary-item si-interest">Interest: {{ $data['interest'] }}</div>
                    <div class="summary-item si-followup">Follow Up: {{ $data['followup'] }}</div>
                    <div class="summary-item si-closing">Closing: {{ $data['closing'] }}</div>
                    <div class="summary-item si-gagal">Gagal: {{ $data['gagal'] }}</div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th class="center" style="width:25px;">No</th>
                            <th style="width:70px;">Tanggal</th>
                            <th>Nama Perusahaan</th>
                            <th style="width:90px;">Mesin Existing</th>
                            <th class="center" style="width:65px;">Hasil</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['visits'] as $i => $visit)
                            <tr>
                                <td class="center">{{ $i + 1 }}</td>
                                <td class="center">{{ $visit->tanggal_kunjungan->format('d/m/Y') }}</td>
                                <td>{{ $visit->prospect?->nama_perusahaan ?? '-' }}</td>
                                <td>
                                    {{ $visit->merk_mesin_existing ?? '-' }}
                                    @if ($visit->jenis_mesin_existing)
                                        <br><span style="color:#64748b;">{{ $visit->jenis_mesin_existing }}</span>
                                    @endif
                                </td>
                                <td class="center">
                                    @php
                                        $badgeClass = match ($visit->hasil_kunjungan) {
                                            'Interest' => 'badge-interest',
                                            'Follow Up' => 'badge-followup',
                                            'Closing' => 'badge-closing',
                                            'Gagal' => 'badge-gagal',
                                            default => 'badge-followup',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $visit->hasil_kunjungan }}</span>
                                </td>
                                <td>{{ $visit->catatan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <p style="text-align:center; color:#94a3b8; padding:20px;">
                Tidak ada data kunjungan sales untuk periode ini.
            </p>
        @endforelse

        <div class="footer">
            <div class="ttd">
                <p>Mengetahui,</p>
                <p>Manager</p>
                <div class="line"></div>
                <p>( ........................... )</p>
            </div>
            <div class="ttd">
                <p>Bandung, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</p>
                <p>Dibuat oleh,</p>
                <div class="line"></div>
                <p>( ........................... )</p>
            </div>
        </div>
    </div>

</body>

</html>
