@extends('print.layout')

@section('title', 'SJ - ' . $d->machine->serial_number)

@push('head')
    <style>
        @page {
            size: A5 landscape;
            margin: 0mm;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .wrapper-sj {
            font-family: sans-serif;
            font-size: 11px;
            border: 2px solid #000;
            padding: 20px;
            height: 100vh;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .content-top {
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
        }

        th {
            background: #f2f2f2;
        }

        .signature-section {
            display: flex;
            justify-content: space-around;
            margin-top: auto;
            text-align: center;
            padding-bottom: 10px;
        }
    </style>
@endpush

@section('content')
    @php
        $no = 1;
    @endphp

    <div class="wrapper-sj">
        <div class="content-top">
            <div style="display:flex; justify-content:space-between; border-bottom:2px solid #000; padding-bottom:5px;">
                <div>
                    <h2 style="margin:0 0 5px 0; font-size:16px;">PT. DINAMIKA GLOBAL GEMILANG</h2>
                    <p style="margin:0; font-weight:bold; letter-spacing:1px;">PEMASANGAN MESIN BARU</p>
                </div>
                <div style="text-align:right;">
                    <p style="margin:0 0 5px 0;"><b>Nomor: {{ $nomor }}</b></p>
                    <p style="margin:0;">Tanggal: {{ $d->created_at->format('d-m-Y') }}</p>
                </div>
            </div>

            <p style="margin:15px 0; line-height:1.4;">
                <b>Penerima:</b> {{ $d->customer->nama_customer }}<br>
                <b>Alamat :</b> {{ $d->customer->alamat }}
            </p>

            <table>
                <thead>
                    <tr>
                        <th width="30">NO</th>
                        <th>NAMA BARANG / DESKRIPSI</th>
                        <th>SN / KODE PART</th>
                        <th width="70">JUMLAH</th>
                        <th>KETERANGAN / CTR</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Mesin --}}
                    <tr>
                        <td style="text-align:center;">{{ $no++ }}</td>
                        <td><b>Mesin Fotokopi {{ $d->machine->tipe_model }}</b></td>
                        <td>{{ $d->machine->serial_number }}</td>
                        <td style="text-align:center;">1 Unit</td>
                        <td></td>
                    </tr>

                    {{-- Sparepart --}}
                    @foreach ($parts as $p)
                        <tr>
                            <td style="text-align:center;">{{ $no++ }}</td>
                            <td><b>{{ strtoupper($p->nama_sparepart) }}</b></td>
                            <td>{{ $p->code_part }}</td>
                            <td style="text-align:center;">{{ $p->jumlah }} Pcs</td>
                            <td></td>
                        </tr>
                    @endforeach

                    {{-- Baris kosong sampai total $targetBaris baris --}}
                    @for ($i = $no; $i <= $targetBaris; $i++)
                        <tr>
                            <td style="text-align:center; color:transparent;">{{ $i }}</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <div class="signature-section">
            <div>Admin,<br><br><br><br>( ________________ )</div>
            <div>Disetujui,<br><br><br><br>( ________________ )</div>
            <div>Teknisi,<br><br><br><br>( ________________ )</div>
            <div>Penerima,<br><br><br><br>( ________________ )</div>
        </div>
    </div>
@endsection
