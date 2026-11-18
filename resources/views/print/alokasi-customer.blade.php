@extends('print.layout')

@section('title', 'Laporan Alokasi Customer')

@push('head')
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #e5e7eb;
            text-align: center;
        }

        .rayon-header {
            background: #f3f4f6;
            font-weight: bold;
        }

        .subtotal-row {
            background: #f9fafb;
            font-weight: bold;
        }

        .grand-total-row {
            background: #e5e7eb;
            font-weight: bold;
        }

        /* 🌟 FITUR: Setiap tabel rayon baru akan otomatis pindah halaman saat dicetak (kecuali tabel pertama) */
        @media print {
            .page-break {
                break-before: page;
                page-break-before: always;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $loopIndex = 0;
        $grandTotalHarga = 0;
        $grandTotalUnit = 0;
    @endphp

    @foreach ($rayons as $namaRayon => $items)
        @php
            $loopIndex++;
            $subtotalHarga = 0;
            $subtotalUnit = $items->count();

            $grandTotalUnit += $subtotalUnit;
        @endphp

        <!-- Bungkus setiap tabel rayon dengan div yang memiliki class page-break mulai rayon ke-2 -->
        <div class="{{ $loopIndex > 1 ? 'page-break' : '' }}">
            <h2 style="text-align:center; margin-bottom: 5px;">DAFTAR ALOKASI UNIT CUSTOMER</h2>
            <div style="text-align: center; font-weight: bold; margin-bottom: 10px;">
                RAYON: {{ strtoupper($namaRayon) }}
            </div>

            <table>
                <thead>
                    <tr>
                        <th>NAMA CUSTOMER</th>
                        <th>TIPE MESIN</th>
                        <th>SN MESIN</th>
                        <th>TGL PASANG</th>
                        <th>HARGA SEWA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $d)
                        @php
                            $subtotalHarga += $d->harga_sewa ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $d->customer->nama_customer ?? '-' }}</td>
                            <td>{{ $d->machine->tipe_model ?? '-' }}</td>
                            <td>{{ $d->machine->serial_number ?? '-' }}</td>
                            <td>
                                {{ optional($d->machine->deployment)->tanggal_instal
                                    ? \Carbon\Carbon::parse($d->machine->deployment->tanggal_instal)->format('d/m/Y')
                                    : ($d->tanggal_mulai
                                        ? $d->tanggal_mulai->format('d/m/Y')
                                        : '-') }}
                            </td>
                            <td style="text-align: right;">Rp {{ number_format($d->harga_sewa ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    @php
                        $grandTotalHarga += $subtotalHarga;
                    @endphp

                    <!-- Baris Subtotal per Rayon -->
                    <tr class="subtotal-row">
                        <td colspan="4" style="text-align: right;">TOTAL RAYON {{ strtoupper($namaRayon) }}
                            ({{ $subtotalUnit }} Unit):</td>
                        <td style="text-align: right;">Rp {{ number_format($subtotalHarga, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    <!-- Tabel Grand Total Keseluruhan (biasanya dicetak di lembar paling akhir) -->
    <div class="page-break" style="margin-top: 20px;">
        <h2 style="text-align:center">REKAPITULASI KESELURUHAN RAYON</h2>
        <table>
            <tr class="grand-total-row">
                <td style="text-align: right; padding: 10px;">GRAND TOTAL KESELURUHAN ({{ $grandTotalUnit }} Unit):</td>
                <td style="text-align: right; padding: 10px; width: 25%;">Rp
                    {{ number_format($grandTotalHarga, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
@endsection
