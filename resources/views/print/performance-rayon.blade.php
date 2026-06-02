<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja Rayon</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            padding: 20px;
            color: #000;
        }

        .main-title {
            background: #005088;
            color: #fff;
            text-align: center;
            padding: 6px;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .sub-title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            padding: 3px;
            border: 1px solid #005088;
            border-top: none;
        }

        .periode {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            padding: 3px;
            border: 1px solid #005088;
            border-top: none;
            margin-bottom: 16px;
        }

        .rayon-block {
            margin-bottom: 20px;
        }

        .rayon-wrapper {
            display: flex;
            width: 100%;
            border: 1px solid #333;
        }

        /* Tabel kiri: data per kota + jumlah + persen */
        .tbl-left {
            flex: 1;
            border-collapse: collapse;
        }

        .tbl-left th,
        .tbl-left td {
            border: 1px solid #333;
            text-align: center;
            padding: 4px 3px;
        }

        /* Panel kanan: rata-rata + keterangan */
        .panel-right {
            display: flex;
            flex-direction: column;
            border-left: 1px solid #333;
            min-width: 320px;
        }

        .panel-right-header {
            display: flex;
            border-bottom: 1px solid #333;
        }

        .panel-right-header .ph {
            background: #92d050;
            font-weight: bold;
            text-align: center;
            padding: 4px 3px;
            flex: 1;
            border-right: 1px solid #333;
            font-size: 11px;
            line-height: 1.4;
        }

        .panel-right-header .ph:last-child {
            border-right: none;
        }

        .panel-right-body {
            display: flex;
            flex: 1;
        }

        .panel-rata {
            flex: 1;
            text-align: center;
            vertical-align: middle;
            padding: 6px;
            font-weight: bold;
            font-size: 13px;
            border-right: 1px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .panel-ket {
            flex: 1.5;
            padding: 6px;
            font-size: 10px;
            vertical-align: top;
            text-align: center;
        }

        .th-group {
            background: #92d050;
            font-weight: bold;
            font-size: 11px;
        }

        .th-tipe {
            background: #ffff00;
            font-weight: bold;
        }

        .td-kota {
            text-align: left;
            padding-left: 6px !important;
        }

        .tr-jumlah td {
            background: #92d050;
            font-weight: bold;
        }

        .tr-persen td {
            background: #ffff00;
        }

        @media print {
            body {
                padding: 10px;
            }

            .rayon-block {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="main-title">LAPORAN BULANAN KINERJA TEKNIK</div>
    <div class="sub-title">PT. DINAMIKA GLOBAL GEMILANG - CIREBON</div>
    <div class="periode">
        PERIODE : {{ strtoupper(\Carbon\Carbon::create($year, $month)->translatedFormat('F Y')) }}
    </div>

    @php $groupNum = 1; @endphp

    @foreach ($reportData as $namaRayon => $techs)
        @php
            $allLogs = $techs->flatMap(fn($t) => $t->serviceLogs);

            // Ambil kota dari relasi customer
            $kotaList = $allLogs->map(fn($log) => $log->customer->kota ?? null)->unique()->filter()->sort()->values();

            $jumlahTeknisi = $techs->count();
            $totalKunjungan = $allLogs->count();
            $rataRata =
                $hariKerja > 0 && $jumlahTeknisi > 0 ? round($totalKunjungan / $hariKerja / $jumlahTeknisi, 1) : 0;
            $namaTeknis = $techs->pluck('nama_technician')->implode(', ');

            // Data per kota per tipe
            $dataTable = [];
            foreach ($kotaList as $kota) {
                $logsKota = $allLogs->filter(fn($log) => ($log->customer->kota ?? null) === $kota);
                foreach ($tipeKolom as $tipe) {
                    $dataTable[$kota][$tipe] = $logsKota->where('tipe_kunjungan', $tipe)->count();
                }
            }

            // Total & persentase
            $totalPerTipe = [];
            foreach ($tipeKolom as $tipe) {
                $totalPerTipe[$tipe] = $allLogs->where('tipe_kunjungan', $tipe)->count();
            }
            $grandTotal = array_sum($totalPerTipe);
            $persenPerTipe = [];
            foreach ($tipeKolom as $tipe) {
                $persenPerTipe[$tipe] =
                    $grandTotal > 0 ? round(($totalPerTipe[$tipe] / $grandTotal) * 100) . '%' : '0%';
            }
        @endphp

        <div class="rayon-block">
            <div class="rayon-wrapper">

                {{-- TABEL KIRI --}}
                <table class="tbl-left">
                    <thead>
                        <tr>
                            <th class="th-group" style="width:120px">
                                GROUP-{{ $groupNum }}<br>{{ strtoupper($namaRayon) }}
                            </th>
                            @foreach ($tipeKolom as $tipe)
                                <th class="th-group">TOTAL<br>{{ strtoupper($tipe) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Baris per kota --}}
                        @forelse($kotaList as $kota)
                            <tr>
                                <td class="td-kota">{{ strtoupper($kota) }}</td>
                                @foreach ($tipeKolom as $tipe)
                                    <td>{{ $dataTable[$kota][$tipe] ?? 0 }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td class="td-kota" colspan="{{ count($tipeKolom) + 1 }}">-</td>
                            </tr>
                        @endforelse

                        {{-- Jumlah --}}
                        <tr class="tr-jumlah">
                            <td class="td-kota">JUMLAH</td>
                            @foreach ($tipeKolom as $tipe)
                                <td>{{ $totalPerTipe[$tipe] }}</td>
                            @endforeach
                        </tr>

                        {{-- Persentase --}}
                        <tr class="tr-persen">
                            <td class="td-kota">PERSENTASE</td>
                            @foreach ($tipeKolom as $tipe)
                                <td>{{ $persenPerTipe[$tipe] }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>

                {{-- PANEL KANAN --}}
                <div class="panel-right">
                    <div class="panel-right-header">
                        <div class="panel-rata">RATA - RATA<br>KUNJUNGAN / HARI</div>
                        <div class="panel-ket">KETERANGAN</div>
                    </div>
                    <div class="panel-right-body">
                        <div class="panel-rata">{{ $rataRata }}</div>
                        <div class="panel-ket">

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <br>

        @php $groupNum++; @endphp
    @endforeach
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
