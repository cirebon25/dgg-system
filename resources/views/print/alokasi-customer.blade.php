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
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #e5e7eb;
        }
    </style>
@endpush

@section('content')
    <h2 style="text-align:center">DAFTAR ALOKASI UNIT CUSTOMER</h2>

    <table>
        <thead>
            <tr>
                <th>NAMA CUSTOMER</th>
                <th>SN MESIN</th>
                <th>TGL PASANG</th>
                <th>HARGA SEWA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
                <tr>
                    <td>{{ $d->customer->nama_customer ?? '-' }}</td>
                    <td>{{ $d->machine->serial_number ?? '-' }}</td>
                    <td>{{ $d->tanggal_instal ?? '-' }}</td>
                    <td>Rp {{ number_format($d->harga_sewa, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
