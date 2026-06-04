<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan Retur #{{ str_pad($retur->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        @page { size: A5 landscape; margin: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #e5e5e5;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px;
            font-size: 11px;
            color: #1a1a1a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page {
            width: 210mm;
            height: 148mm;
            background: #fff;
            padding: 10mm 14mm 8mm;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .kop {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
        }

        .company-name { font-size: 16px; font-weight: 800; text-transform: uppercase; color: #000; }
        .company-sub  { font-size: 9.5px; color: #555; font-weight: 500; margin-top: 1px; }
        .doc-title    { font-size: 14px; font-weight: 800; text-transform: uppercase; color: #000; }
        .doc-no       { font-size: 10px; color: #444; font-weight: 600; margin-top: 2px; }

        .info-section { margin: 10px 0 6px 0; }

        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 3px 2px; vertical-align: top; font-size: 11px; }
        .info-table td.lbl { width: 130px; color: #555; font-weight: 500; }
        .info-table td.sym { width: 15px; text-align: center; color: #555; }
        .info-table td.val { font-weight: 700; color: #000; }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            border: 1.5px solid #000;
        }
        .main-table th {
            background: #ffffff;
            color: #000;
            border: 1.5px solid #000;
            padding: 6px 10px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .main-table td {
            border: 1px solid #000;
            padding: 7px 10px;
            font-size: 11px;
            vertical-align: middle;
        }
        .txt-sn { font-family: monospace; font-size: 13px; font-weight: 700; }

        /* Badge kondisi */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .badge-danger  { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .badge-warning { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
        .badge-info    { background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; }

        .ttd-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #000;
        }
        .ttd-box { text-align: center; }
        .ttd-box .ttd-lbl {
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #444;
            font-weight: 600;
            margin-bottom: 28px;
        }
        .ttd-line {
            display: inline-block;
            width: 80%;
            border-top: 1.5px solid #000;
            padding-top: 4px;
            font-size: 10px;
            font-weight: 700;
        }

        .catatan { margin-top: 4px; font-size: 8px; color: #666; text-align: center; font-weight: 500; }

        .action-bar {
            width: 210mm;
            margin: 10px auto 0;
            display: flex;
            gap: 8px;
        }
        .btn {
            flex: 1;
            padding: 10px;
            border: 1.5px solid #000;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            background: #fff;
            color: #000;
        }
        .btn-print { background: #000; color: #fff; }

        @media print {
            body { background: none; padding: 0; }
            .page { border: none; box-shadow: none; }
            .action-bar { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="page">
        <div>
            {{-- KOP --}}
            <div class="kop">
                <div>
                    <div class="company-name">PT Dinamika Global Gemilang</div>
                    <div class="company-sub">Jl. Pulasaren No.56b, Pekalipan, Kec. Pekalipan, Kota Cirebon</div>
                </div>
                <div style="text-align:right;">
                    <div class="doc-title">Surat Jalan Retur Unit</div>
                    <div class="doc-no">
                        No: SJR-{{ str_pad($retur->id, 5, '0', STR_PAD_LEFT) }}/DGG/{{ \Carbon\Carbon::parse($retur->tanggal_retur)->format('m/Y') }}
                    </div>
                </div>
            </div>

            {{-- INFO PENGIRIMAN --}}
            <div class="info-section">
                <table class="info-table">
                    <tr>
                        <td class="lbl">Tanggal Pengiriman</td>
                        <td class="sym">:</td>
                        <td class="val">{{ \Carbon\Carbon::parse($retur->tanggal_retur)->locale('id')->isoFormat('D MMMM Y') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Dari</td>
                        <td class="sym">:</td>
                        <td class="val">{{ $retur->dari_lokasi }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Tujuan</td>
                        <td class="sym">:</td>
                        <td class="val">{{ $retur->ke_lokasi }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Dikirim Oleh</td>
                        <td class="sym">:</td>
                        <td class="val">{{ $retur->dikirim_oleh ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- TABEL UNIT --}}
            <table class="main-table">
                <thead>
                    <tr>
                        <th style="width:40px; text-align:center;">NO</th>
                        <th style="width:200px;">TIPE MODEL MESIN</th>
                        <th style="width:180px;">NO SERI (SN)</th>
                        <th>KONDISI & KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:center; font-weight:700;">1</td>
                        <td style="font-weight:700; font-size:12px;">{{ $retur->machine?->tipe_model ?? '-' }}</td>
                        <td class="txt-sn">{{ $retur->machine?->serial_number ?? '-' }}</td>
                        <td>
                            {{-- @php
                                $badgeClass = match($retur->kondisi_saat_retur) {
                                    'Rusak Berat'  => 'badge-danger',
                                    'Rusak Ringan' => 'badge-warning',
                                    default        => 'badge-info',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $retur->kondisi_saat_retur }}</span>
                            @if($retur->keterangan_kerusakan)
                                <div style="margin-top:4px; font-size:10px; color:#444; line-height:1.3;">
                                    {{ $retur->keterangan_kerusakan }}
                                </div>
                            @endif --}}
                        </td>
                    </tr>
                    {{-- Baris kosong cadangan --}}
                    @for($i = 2; $i <= 4; $i++)
                    <tr>
                        <td style="text-align:center; color:#ccc;">{{ $i }}</td>
                        <td></td><td></td><td></td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        {{-- TANDA TANGAN --}}
        <div>
            <div class="ttd-section">
                <div class="ttd-box">
                    <div class="ttd-lbl">Dibuat Oleh (Cirebon)</div>
                    <div class="ttd-line">Admin Cirebon</div>
                </div>
                <div class="ttd-box">
                    <div class="ttd-lbl">Diketahui Oleh;</div>
                    <div class="ttd-line">( ............... )</div>
                </div>
                <div class="ttd-box">
                    <div class="ttd-lbl">Penerima (Bandung)</div>
                    <div class="ttd-line">( ............... )</div>
                </div>
            </div>

            <div class="catatan">
                Surat ini adalah bukti serah terima pengiriman unit mesin untuk keperluan servis/perbaikan ke Gudang Bandung.<br>
                Sistem DGG &copy; {{ date('Y') }} � Dokumen administrasi resmi dicetak otomatis oleh sistem.
            </div>
        </div>
    </div>

    {{-- TOMBOL DI LAYAR --}}
    <div class="action-bar">
        <button class="btn btn-print" onclick="window.print()">??? Cetak Surat Jalan Retur (A5 Landscape)</button>
        <button class="btn" onclick="window.location.href='/admin/machine-returns'">? Kembali ke List</button>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(() => window.print(), 600);
        });
    </script>

</body>
</html>
