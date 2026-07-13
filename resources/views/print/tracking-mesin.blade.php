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
            margin-bottom: 16px;
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
            margin-bottom: 20px;
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

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #cbd5e1;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 16px;
        }

        .timeline-dot {
            position: absolute;
            left: -24px;
            top: 3px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
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

        .tgl {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 2px;
        }

        .judul {
            font-weight: bold;
            font-size: 11px;
        }

        .detail {
            font-size: 11px;
            margin-top: 2px;
        }

        .sub {
            font-size: 9.5px;
            color: #64748b;
            margin-top: 3px;
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

        .status-box {
            margin-top: 20px;
            padding: 10px 14px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
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

        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #94a3b8;
            text-align: right;
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

    <div class="header">
        <h2>Tracking Riwayat Mesin</h2>
        <p>PT Dinamika Global Gemilang — DGG System</p>
        <p>Dicetak: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</p>
    </div>

    <div class="info-box">
        <div>
            <label>Serial Number</label>
            <span>{{ $machine->serial_number }}</span>
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
            <span>{{ $machine->customer?->nama_customer ?? 'Gudang DGG' }}</span>
        </div>
    </div>

    @if ($timeline->isEmpty())
        <p style="text-align:center; color:#94a3b8; padding:30px;">Belum ada riwayat untuk mesin ini.</p>
    @else
        <div class="timeline">
            @foreach ($timeline as $event)
                @php
                    $warna = $event['warna'];
                    $badgeClass = 'badge-' . $warna;
                    $dotClass = 'dot-' . $warna;
                @endphp
                <div class="timeline-item">
                    <div class="timeline-dot {{ $dotClass }}"></div>
                    <div class="timeline-card {{ $warna }}">
                        <div class="tgl">{{ \Carbon\Carbon::parse($event['tanggal'])->isoFormat('D MMMM YYYY') }}
                        </div>
                        <div class="judul">
                            {{ $event['icon'] }} {{ $event['judul'] }}
                            <span class="badge {{ $badgeClass }}">{{ $event['tipe'] }}</span>
                        </div>
                        <div class="detail">{{ $event['detail'] }}</div>
                        @if ($event['sub'])
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

    <div class="footer">DGG System © {{ date('Y') }} — PT Dinamika Global Gemilang Cirebon</div>

</body>

</html>
