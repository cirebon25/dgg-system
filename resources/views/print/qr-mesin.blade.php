@extends('print.layout')

@section('title', 'QR Mesin - ' . $machine->serial_number)

@push('head')
    <style>
        /* Stiker thermal 50mm x 40mm */
        @page {
            size: 50mm 40mm;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 5px;
            width: 50mm;
            height: 40mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
        }

        .title {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        .sn {
            font-size: 10px;
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
        }

        .qr-box svg {
            width: 70px;
            height: 70px;
        }

        .footer {
            font-size: 7px;
            font-weight: bold;
            margin-top: 3px;
            border-top: 1px solid #ccc;
            padding-top: 2px;
            width: 100%;
        }
    </style>
@endpush

@section('content')
    <div class="title">{{ $machine->tipe_model }}</div>
    <div class="sn">SN: {{ $machine->serial_number }}</div>
    <div class="qr-box">{!! $qrCode !!}</div>
    <div class="footer">DGG - WORKSHOP HUB</div>
@endsection
