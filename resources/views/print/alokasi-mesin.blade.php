@extends('print.layout')

@section('title', 'Laporan Alokasi Mesin DGG')

@push('head')
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            width: 100%;
            font-family: Arial, sans-serif;
            color: #333;
            background: #fff;
        }

        body {
            padding: 15px 25px;
        }

        header {
            text-align: center;
            margin-bottom: 5px;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
        }

        header h1 {
            font-size: 13px;
            color: #1e3a8a;
            font-weight: bold;
        }

        header h2 {
            font-size: 10px;
            margin: 2px 0;
            color: #475569;
            font-weight: bold;
        }

        header p {
            font-size: 8px;
            color: #64748b;
        }

        .rayon-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 15px;
            align-items: stretch;
        }

        .rayon-block {
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .rayon-header {
            font-weight: bold;
            font-size: 9px;
            padding: 5px 6px;
            text-transform: uppercase;
            text-align: center;
            letter-spacing: 0.5px;
            border-radius: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        th {
            background: #0f172a;
            color: #fff;
            padding: 3px 5px;
            font-size: 8px;
            text-align: left;
        }

        th.text-right {
            text-align: right;
        }

        td {
            padding: 3px 5px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 8px;
            white-space: nowrap;
        }

        .text-right {
            text-align: right;
        }

        .bg-kota td {
            font-size: 8px;
            padding: 3.5px 5px;
            font-weight: bold;
        }

        .bg-total-kota td {
            color: #000 !important;
            font-weight: bold !important;
            font-size: 8px;
            border-bottom: 1.5px solid #000;
            background: #fdfdfd;
        }

        .empty-row td {
            border-bottom: 1px solid transparent;
            color: transparent;
        }

        .bg-total-rayon td {
            font-weight: bold;
            font-size: 8.5px;
            color: #166534;
            background-color: #e8f5e9;
            border-top: 1.5px solid #166534;
            padding: 5px 5px;
        }

        .grand-total {
            margin-top: 15px;
            background: #f97316;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            padding: 6px 12px;
            text-align: right;
            border-radius: 2px;
            page-break-inside: avoid;
        }

        @media print {
            body {
                padding: 8mm 12mm;
            }

            .rayon-grid,
            .grand-total {
                zoom: 94%;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $rayonStyles = [
            ['header_bg' => '#1e3a8a', 'text' => '#ffffff', 'kota_bg' => '#f0f9ff', 'kota_text' => '#0369a1'],
            ['header_bg' => '#b45309', 'text' => '#ffffff', 'kota_bg' => '#fffbeb', 'kota_text' => '#92400e'],
            ['header_bg' => '#047857', 'text' => '#ffffff', 'kota_bg' => '#f0fdf4', 'kota_text' => '#065f46'],
            ['header_bg' => '#be185d', 'text' => '#ffffff', 'kota_bg' => '#fdf2f8', 'kota_text' => '#9d174d'],
        ];
    @endphp

    <header>
        <h1>PT DINAMIKA GLOBAL GEMILANG</h1>
        <h2>LAPORAN ALOKASI TYPE-TYPE MESIN PER RAYON</h2>
        <p>Tanggal Cetak: {{ now()->format('d-m-Y H:i') }}</p>
    </header>

    <div class="rayon-grid">
        @foreach ($rayons as $i => $rayon)
            @php $style = $rayonStyles[$i % 4]; @endphp

            <div class="rayon-block">
                <div>
                    <div class="rayon-header"
                        style="background-color: {{ $style['header_bg'] }}; color: {{ $style['text'] }};">
                        RAYON - {{ strtoupper($rayon['nama']) }}
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>KOTA / TIPE MESIN</th>
                                <th class="text-right" width="40">UNIT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rayon['kotas'] as $kota)
                                <tr class="bg-kota"
                                    style="background-color: {{ $style['kota_bg'] }}; color: {{ $style['kota_text'] }};">
                                    <td colspan="2">{{ $kota['nama'] }}</td>
                                </tr>

                                @foreach ($kota['items'] as $item)
                                    <tr>
                                        <td>&nbsp;&nbsp;• {{ $item->tipe_model }}</td>
                                        <td class="text-right">{{ $item->qty }}</td>
                                    </tr>
                                @endforeach

                                <tr class="bg-total-kota">
                                    <td class="text-right">Total {{ $kota['nama'] }}:</td>
                                    <td class="text-right">{{ $kota['total'] }}</td>
                                </tr>
                            @endforeach

                            {{-- Baris kosong penyeimbang tinggi antar rayon --}}
                            @for ($n = $rayon['baris']; $n < $maxBaris; $n++)
                                <tr class="empty-row">
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <table>
                    <tfoot>
                        <tr class="bg-total-rayon">
                            <td class="text-right">TOTAL {{ strtoupper($rayon['nama']) }} :</td>
                            <td class="text-right" width="40">{{ $rayon['total'] }} Pcs</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach
    </div>

    <div class="grand-total">GRAND TOTAL UNIT TERPASANG: {{ $grandTotal }} UNIT</div>
@endsection
