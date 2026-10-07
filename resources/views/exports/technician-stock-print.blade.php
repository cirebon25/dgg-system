@php
    $startLabel  = $periodStart->copy()->locale('id')->translatedFormat('j M Y');
    $endLabel    = $periodEnd->copy()->locale('id')->translatedFormat('j M Y');
    $monthName   = $periodStart->copy()->locale('id')->translatedFormat('F Y');
    $printedText = $printedAt->copy()->locale('id')->translatedFormat('j F Y, H:i');

    $jenisLabel = [
        'Pinjam' => 'Dipinjam',
        'Retur'  => 'Retur',
        'Pakai'  => 'Dipakai',
    ];
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Histori Stok Teknisi - {{ $monthName }}</title>
    <style>
        @page { size: A4 portrait; margin: 14mm; }

        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #1e293b; line-height: 1.4; }
        h2 { margin: 0 0 2px; font-size: 20px; }
        h3 { margin: 18px 0 6px; font-size: 14px; color: #0f172a; }
        .h3-note { font-size: 11px; font-weight: normal; color: #64748b; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .muted { color: #64748b; }

        /* Kotak petunjuk */
        .guide {
            border: 1px solid #bfdbfe; background: #eff6ff; padding: 8px 12px;
            margin: 12px 0 4px; font-size: 11.5px;
        }
        .guide strong { color: #1e3a8a; }

        .technician-section { margin-top: 26px; }
        .technician-title {
            font-size: 16px; font-weight: bold; background: #e2e8f0;
            padding: 8px 12px; margin-bottom: 10px; border-left: 5px solid #3b82f6;
        }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; font-size: 11.5px; }
        th { background-color: #f1f5f9; font-size: 11px; vertical-align: middle; }
        th .sub { display: block; font-weight: normal; color: #64748b; font-size: 9.5px; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        tbody tr:nth-child(even) td:not(.stok-ada) { background-color: #f8fafc; }

        .summary td { text-align: center; padding: 8px 4px; width: 16.66%; vertical-align: top; }
        .summary .label { font-size: 10.5px; color: #475569; font-weight: bold; }
        .summary .date { font-size: 10px; color: #64748b; margin-bottom: 2px; }
        .summary .value { font-size: 20px; font-weight: bold; }

        .summary-row td { font-weight: bold; background-color: #f1f5f9 !important; }

        /* Stok akhir tas yang masih ada isinya */
        .stok-ada { background-color: #ecfdf5 !important; color: #166534; }

        .badge { font-size: 10.5px; font-weight: bold; padding: 2px 7px; border: 1px solid; white-space: nowrap; }
        .badge-pinjam { color: #15803d; border-color: #15803d; }
        .badge-retur { color: #1d4ed8; border-color: #1d4ed8; }
        .badge-pakai { color: #b91c1c; border-color: #b91c1c; }

        .ok { color: #15803d; font-weight: bold; }
        .warn { color: #b91c1c; font-weight: bold; }

        .footer { margin-top: 24px; font-size: 10.5px; color: #64748b; text-align: right; }
    </style>
</head>

<body onload="window.print()">

    <h2 class="text-center">Laporan Histori Stok Teknisi</h2>
    <p class="text-center muted" style="margin: 0; font-size: 13px;">
        Periode: <strong>{{ $monthName }}</strong> ({{ $startLabel }} s/d {{ $endLabel }})
    </p>

    <div class="guide">
        <strong>Cara membaca laporan:</strong>
        Stok Awal + Dipinjam &minus; Dipakai &minus; Retur = Stok Akhir.
        <em>Dipinjam</em> = part diambil teknisi dari gudang.
        <em>Dipakai</em> = part terpasang untuk servis.
        <em>Retur</em> = part dikembalikan ke gudang.
        Kotak <span class="stok-ada" style="padding: 0 6px; border: 1px solid #bbf7d0;">hijau</span> berarti masih ada part di tas.
    </div>

    @forelse ($reports as $report)
        @php $totals = $report['totals']; @endphp

        <div class="technician-section">
            <div class="technician-title">Teknisi: {{ $report['technician'] }}</div>

            {{-- Ringkasan --}}
            <table class="summary">
                <tr>
                    <td>
                        <div class="label">Stok Awal</div>
                        <div class="date">per {{ $startLabel }}</div>
                        <div class="value">{{ $totals['saldo_awal'] }}</div>
                    </td>
                    <td>
                        <div class="label">Total Dipinjam</div>
                        <div class="date">dari gudang</div>
                        <div class="value" style="color: #15803d;">+{{ $totals['dipinjam'] }}</div>
                    </td>
                    <td>
                        <div class="label">Total Retur</div>
                        <div class="date">ke gudang</div>
                        <div class="value" style="color: #1d4ed8;">{{ $totals['retur'] }}</div>
                    </td>
                    <td>
                        <div class="label">Total Dipakai</div>
                        <div class="date">untuk servis</div>
                        <div class="value" style="color: #b91c1c;">-{{ $totals['terpakai'] }}</div>
                    </td>
                    <td @class(['stok-ada' => $totals['saldo_akhir'] > 0])>
                        <div class="label">Stok Akhir Tas</div>
                        <div class="date">per {{ $endLabel }}</div>
                        <div class="value">{{ $totals['saldo_akhir'] }}</div>
                    </td>
                    <td>
                        <div class="label">Jenis Part</div>
                        <div class="date">&nbsp;</div>
                        <div class="value">{{ $totals['jenis_part'] }}</div>
                    </td>
                </tr>
            </table>

            {{-- Rekap per part --}}
            <h3>A. Rekap Per Part <span class="h3-note">(ringkasan satu baris untuk setiap jenis part)</span></h3>
            <table>
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Nama Part</th>
                        <th width="11%" class="text-center">Stok Awal<span class="sub">per {{ $startLabel }}</span></th>
                        <th width="11%" class="text-center">Dipinjam (+)<span class="sub">dari gudang</span></th>
                        <th width="9%" class="text-center">Retur<span class="sub">ke gudang</span></th>
                        <th width="10%" class="text-center">Dipakai (-)<span class="sub">untuk servis</span></th>
                        <th width="12%" class="text-center">Stok Akhir Tas<span class="sub">per {{ $endLabel }}</span></th>
                        <th width="11%" class="text-center">Cek Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($report['parts'] as $part)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $part['nama'] }}</td>
                            <td class="text-center">{{ $part['saldo_awal'] }}</td>
                            <td class="text-center">{{ $part['dipinjam'] }}</td>
                            <td class="text-center">{{ $part['retur'] }}</td>
                            <td class="text-center">{{ $part['terpakai'] }}</td>
                            <td @class(['text-center', 'stok-ada' => $part['saldo_akhir'] > 0])><strong>{{ $part['saldo_akhir'] }}</strong></td>
                            <td class="text-center">
                                @if ($part['selisih'] === 0)
                                    <span class="ok">Sesuai</span>
                                @else
                                    <span class="warn">Selisih {{ $part['selisih'] > 0 ? '+' : '' }}{{ $part['selisih'] }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    <tr class="summary-row">
                        <td colspan="2" class="text-right">Total:</td>
                        <td class="text-center">{{ $totals['saldo_awal'] }}</td>
                        <td class="text-center">{{ $totals['dipinjam'] }}</td>
                        <td class="text-center">{{ $totals['retur'] }}</td>
                        <td class="text-center">{{ $totals['terpakai'] }}</td>
                        <td @class(['text-center', 'stok-ada' => $totals['saldo_akhir'] > 0])>{{ $totals['saldo_akhir'] }}</td>
                        <td class="text-center">
                            @if ($totals['part_selisih'] === 0)
                                <span class="ok">Semua Sesuai</span>
                            @else
                                <span class="warn">{{ $totals['part_selisih'] }} Part Selisih</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Detail mutasi --}}
            <h3>B. Riwayat Keluar-Masuk Part <span class="h3-note">(urut dari tanggal paling awal)</span></h3>
            <table>
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="16%">Tanggal &amp; Jam</th>
                        <th width="21%">Nama Part</th>
                        <th width="10%" class="text-center">Jenis</th>
                        <th width="8%" class="text-center">Masuk (+)</th>
                        <th width="8%" class="text-center">Keluar (-)</th>
                        <th width="10%" class="text-center">Stok Tas Setelahnya</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report['mutations'] as $mutation)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $mutation['waktu']->copy()->locale('id')->translatedFormat('j M Y, H:i') }}</td>
                            <td>{{ $mutation['part'] }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ strtolower($mutation['jenis']) }}">{{ $jenisLabel[$mutation['jenis']] ?? $mutation['jenis'] }}</span>
                            </td>
                            <td class="text-center">{{ $mutation['masuk'] }}</td>
                            <td class="text-center">{{ $mutation['keluar'] }}</td>
                            <td class="text-center"><strong>{{ $mutation['saldo'] }}</strong></td>
                            <td>{{ $mutation['keterangan'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center muted">Tidak ada pergerakan part pada periode ini.</td>
                        </tr>
                    @endforelse

                    <tr class="summary-row">
                        <td colspan="4" class="text-right">Total Pergerakan:</td>
                        <td class="text-center" style="color: #15803d;">+{{ $totals['dipinjam'] }}</td>
                        <td class="text-center" style="color: #b91c1c;">-{{ $totals['total_keluar'] }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @empty
        <p class="text-center" style="margin-top: 40px;">Tidak ada data histori stok pada periode ini.</p>
    @endforelse

    <div class="footer">Dicetak pada {{ $printedText }}</div>

</body>

</html>