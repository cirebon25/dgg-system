<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tracking Mesin — {{ $machine->serial_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.45;
        }

        .no-print {
            padding: 10px;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .header {
            text-align: center;
            margin-bottom: 14px;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 10px;
        }

        .header h2 {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            font-size: 11px;
            color: #475569;
            margin-top: 3px;
        }

        .info-box {
            display: flex;
            gap: 16px;
            margin-bottom: 14px;
            background: #f8fafc;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }

        .info-box div {
            flex: 1;
        }

        .info-box label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            display: block;
        }

        .info-box span {
            font-weight: bold;
            font-size: 12px;
        }

        /* Ringkasan */
        .summary {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 18px;
            background: #fff;
        }

        .summary h3 {
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 6px;
            color: #334155;
        }

        .summary p {
            font-size: 11px;
            margin-bottom: 6px;
        }

        .chips {
            margin-top: 4px;
        }

        .chip {
            display: inline-block;
            padding: 2px 8px;
            margin: 0 4px 4px 0;
            border-radius: 10px;
            background: #e2e8f0;
            font-size: 9.5px;
            font-weight: bold;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #334155;
            margin-bottom: 10px;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 34px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #cbd5e1;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .timeline-dot {
            position: absolute;
            left: -32px;
            top: 4px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
            border: 2px solid #fff;
        }

        .dot-green {
            background: #16a34a;
        }

        .dot-orange {
            background: #ea580c;
        }

        .dot-red {
            background: #dc2626;
        }

        .dot-blue {
            background: #2563eb;
        }

        .dot-gray {
            background: #64748b;
        }

        .gap {
            font-size: 9px;
            color: #94a3b8;
            font-style: italic;
            margin-bottom: 4px;
        }

        .timeline-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            background: #fff;
        }

        .timeline-card.green {
            border-left: 4px solid #16a34a;
        }

        .timeline-card.orange {
            border-left: 4px solid #ea580c;
        }

        .timeline-card.red {
            border-left: 4px solid #dc2626;
        }

        .timeline-card.blue {
            border-left: 4px solid #2563eb;
        }

        .timeline-card.gray {
            border-left: 4px solid #64748b;
        }

        .tgl {
            font-size: 9.5px;
            color: #64748b;
            margin-bottom: 2px;
        }

        .judul {
            font-weight: bold;
            font-size: 11.5px;
        }

        .narasi {
            font-size: 11px;
            margin-top: 5px;
            padding: 6px 8px;
            background: #f8fafc;
            border-radius: 4px;
        }

        .rincian {
            width: 100%;
            margin-top: 6px;
            border-collapse: collapse;
        }

        .rincian td {
            padding: 2px 0;
            font-size: 10px;
            vertical-align: top;
        }

        .rincian td.k {
            width: 90px;
            color: #64748b;
        }

        .sub {
            font-size: 9.5px;
            color: #64748b;
            margin-top: 4px;
        }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            margin-left: 6px;
            vertical-align: middle;
        }

        .badge-green {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-orange {
            background: #ffedd5;
            color: #c2410c;
        }

        .badge-red {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-gray {
            background: #f1f5f9;
            color: #475569;
        }

        .status-box {
            margin-top: 18px;
            padding: 10px 14px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
            page-break-inside: avoid;
        }

        .status-rented {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }

        .status-ready {
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #93c5fd;
        }

        .status-other {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .ttd {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .ttd div {
            width: 30%;
            text-align: center;
            font-size: 10px;
        }

        .ttd .garis {
            margin-top: 45px;
            border-top: 1px solid #1e293b;
            padding-top: 3px;
        }

        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>

<body>

    @php
        use Carbon\Carbon;

        // Urutkan dari kejadian paling lama ke paling baru
        $timeline = $timeline->sortBy('tanggal')->values();

        $serial = $machine->serial_number;
        $lokasiNow = $machine->customer?->nama_customer ?? 'Gudang DGG';

        $first = $timeline->first();
        $last = $timeline->last();

        $perTipe = $timeline->groupBy('tipe')->map->count();
    @endphp

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

    <div class="header">
        <h2>Tracking Riwayat Mesin</h2>
        <p>PT Dinamika Global Gemilang — DGG System</p>
        <p>Dicetak: {{ Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
    </div>

    <div class="info-box">
        <div>
            <label>Serial Number</label>
            <span>{{ $serial }}</span>
        </div>
        <div>
            <label>Tipe Model</label>
            <span>{{ $machine->tipe_model ?? '-' }}</span>
        </div>
        <div>
            <label>Status Sekarang</label>
            <span>{{ $machine->status }}</span>
        </div>
        <div>
            <label>Lokasi Sekarang</label>
            <span>{{ $lokasiNow }}</span>
        </div>
    </div>

    @if ($timeline->isEmpty())
        <p style="text-align:center; color:#94a3b8; padding:30px;">Belum ada riwayat untuk mesin ini.</p>
    @else
        {{-- RINGKASAN PERJALANAN --}}
        <div class="summary">
            <h3>Ringkasan Perjalanan Mesin</h3>
            <p>
                Mesin dengan Serial Number <strong>{{ $serial }}</strong>
                @if ($machine->tipe_model)
                    (tipe <strong>{{ $machine->tipe_model }}</strong>)
                @endif
                tercatat pertama kali pada
                <strong>{{ Carbon::parse($first['tanggal'])->isoFormat('D MMMM YYYY') }}</strong>
                dan memiliki total <strong>{{ $timeline->count() }} kejadian</strong>.
                Kejadian terakhir pada
                <strong>{{ Carbon::parse($last['tanggal'])->isoFormat('D MMMM YYYY') }}</strong>
                ({{ Carbon::parse($last['tanggal'])->diffForHumans() }}).
                Saat ini mesin berstatus <strong>{{ $machine->status }}</strong>
                @if ($machine->status === 'Rented')
                    dan terpasang di <strong>{{ $lokasiNow }}</strong>.
                @else
                    dan berada di <strong>{{ $lokasiNow }}</strong>.
                @endif
            </p>
            <div class="chips">
                @foreach ($perTipe as $tipe => $jumlah)
                    <span class="chip">{{ $tipe }}: {{ $jumlah }}x</span>
                @endforeach
            </div>
        </div>

        {{-- TIMELINE --}}
        <div class="section-title">Kronologi Kejadian (dari yang paling awal)</div>

        <div class="timeline">
            @foreach ($timeline as $i => $event)
                @php
                    $warna = $event['warna'] ?? 'gray';
                    $tgl = Carbon::parse($event['tanggal']);

                    // Jarak dari kejadian sebelumnya
                    $gapText = null;
                    if ($i > 0) {
                        $prev = Carbon::parse($timeline[$i - 1]['tanggal']);
                        $selisih = $prev->diffInDays($tgl);
                        $gapText =
                            $selisih === 0 ? 'Di hari yang sama' : $selisih . ' hari setelah kejadian sebelumnya';
                    }

                    // Narasi: pakai 'narasi' bila ada, kalau tidak pakai 'detail'
                    $narasi = $event['narasi'] ?? ($event['detail'] ?? null);

                    // Baris rincian opsional
                    $rincian = [
                        'Customer' => $event['customer'] ?? null,
                        'Dari' => $event['dari'] ?? null,
                        'Ke' => $event['ke'] ?? null,
                        'Petugas / NS' => $event['petugas'] ?? null,
                        'No. Dokumen' => $event['no_dokumen'] ?? null,
                        'Catatan' => $event['catatan'] ?? null,
                    ];
                    $rincian = array_filter($rincian, fn($v) => filled($v));
                @endphp

                <div class="timeline-item">
                    <div class="timeline-dot dot-{{ $warna }}">{{ $i + 1 }}</div>

                    @if ($gapText)
                        <div class="gap">⏱ {{ $gapText }}</div>
                    @endif

                    <div class="timeline-card {{ $warna }}">
                        <div class="tgl">{{ $tgl->isoFormat('dddd, D MMMM YYYY') }}</div>
                        <div class="judul">
                            {{ $event['icon'] ?? '•' }} {{ $event['judul'] }}
                            <span class="badge badge-{{ $warna }}">{{ $event['tipe'] }}</span>
                        </div>

                        @if ($narasi)
                            <div class="narasi">
                                Pada tanggal <strong>{{ $tgl->isoFormat('D MMMM YYYY') }}</strong>,
                                {{ $narasi }}
                            </div>
                        @endif

                        @if (count($rincian))
                            <table class="rincian">
                                @foreach ($rincian as $label => $nilai)
                                    <tr>
                                        <td class="k">{{ $label }}</td>
                                        <td>: {{ $nilai }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif

                        @if (!empty($event['sub']))
                            <div class="sub">{{ $event['sub'] }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @php
        $statusClass = match ($machine->status) {
            'Rented' => 'status-rented',
            'Ready' => 'status-ready',
            default => 'status-other',
        };
    @endphp
    <div class="status-box {{ $statusClass }}">
        STATUS SAAT INI: {{ strtoupper($machine->status) }}
        @if ($machine->status === 'Rented')
            — {{ $machine->customer?->nama_customer ?? '-' }}
        @endif
    </div>

    <div class="ttd">
        <div>Dibuat oleh,<div class="garis">(...................)</div>
        </div>
        <div>Diperiksa oleh,<div class="garis">(...................)</div>
        </div>
        <div>Disetujui oleh,<div class="garis">(...................)</div>
        </div>
    </div>

    <div class="footer">DGG System © {{ date('Y') }} — PT Dinamika Global Gemilang Cirebon</div>

</body>

</html>
