<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan Rolling DGG</title>
    <style>
        @page { size: A5 landscape; margin: 0.5cm; }
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #000; margin: 0; padding: 10px; }
        
        /* Layout Header */
        .header-table { width: 100%; border: none; margin-bottom: 10px; }
        .logo-area { width: 60%; text-align: left; vertical-align: top; }
        .no-sj-area { width: 40%; text-align: right; vertical-align: top; }
        
        .customer-info { margin-bottom: 15px; }
        .customer-info strong { font-size: 14px; text-transform: uppercase; }

        /* Unified Table Full Border */
        table.main-table { 
            width: 100%; 
            border-collapse: collapse; 
            border: 2px solid #000; 
        }
        table.main-table th, table.main-table td { 
            border: 1px solid #000; 
            padding: 8px; 
            text-align: center; 
        }
        .bg-gray { background-color: #f2f2f2; font-weight: bold; }
        .text-left { text-align: left; }

        /* Tanda Tangan */
        .ttd-table { width: 100%; margin-top: 30px; text-align: center; }
        .ttd-table td { height: 70px; vertical-align: top; width: 33%; border: none; }
    </style>
</head>
<body onload="window.print()">

    <table class="header-table">
        <tr>
            <td class="logo-area">
                <strong style="font-size: 18px;">DINAMIKA GLOBAL GEMILANG (DGG)</strong><br>
                <span>Rental & Service Fotokopi Digital</span><br>
                <small>Jl. Raya Cirebon - Kuningan No. 12, Cirebon</small>
            </td>
            <td class="no-sj-area">
                <strong style="font-size: 14px; text-decoration: underline;">SURAT JALAN ROLLING</strong><br>
                <span>No: {{ $nomor_sj }}</span><br>
                <span>Tanggal: {{ $tanggal }}</span>
            </td>
        </tr>
    </table>

    <div class="customer-info">
        Kepada Yth:<br>
        <strong>{{ $d['cust'] }}</strong><br>
        <span>{{ $d['alamat'] }}</span>
    </div>

    <table class="main-table">
        <thead>
            <tr class="bg-gray">
                <th style="width: 5%;">No</th>
                <th style="width: 45%;">Deskripsi Barang / Unit</th>
                <th style="width: 25%;">SN / Spesifikasi</th>
                <th style="width: 25%;">Qty / Counter</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td class="text-left font-bold italic">PENARIKAN UNIT (LAMA)</td>
                <td><strong>{{ $d['old_sn'] }}</strong></td>
                <td>
                    BW: {{ number_format($d['bw']) }}<br>
                    CL: {{ number_format($d['cl']) }}
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td class="text-left font-bold italic">PENGIRIMAN UNIT (BARU)</td>
                <td><strong>{{ $d['new_sn'] }}</strong></td>
                <td>1 Unit (Start 0)</td>
            </tr>

            @php $no = 3; @endphp
            @forelse($d['parts'] as $p)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td class="text-left">{{ $p['nama_part'] ?? 'Sparepart' }}</td>
                    <td>-</td>
                    <td>{{ $p['jumlah'] }} Pcs {{ $p['ket_part'] ? '('.$p['ket_part'].')' : '' }}</td>
                </tr>
            @empty
                <tr><td>3</td><td>-</td><td>-</td><td>-</td></tr>
                <tr><td>4</td><td>-</td><td>-</td><td>-</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="ttd-table">
        <tr>
            <td>
                Penerima / Customer,<br><br><br><br><br>
                ( ___________________ )
            </td>
            <td>
                Teknisi Pelaksana,<br><br><br><br><br>
                ( ___________________ )
            </td>
            <td>
                Hormat Kami,<br><br><br><br><br>
                ( Admin DGG )
            </td>
        </tr>
    </table>

</body>
</html>