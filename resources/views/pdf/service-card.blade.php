<style>
    @page {
        margin: 0;
        size: 297mm 210mm;
    }

    body {
        margin: 0;
        font-family: sans-serif;
    }

    .field {
        position: absolute;
        font-size: 11pt;
        font-weight: bold;
    }
</style>

<body>
    <div class="field" style="top: 29mm; left: 45mm;">{{ $nama_perusahaan }}</div>
    <div class="field" style="top: 37mm; left: 45mm;">{{ $alamat }}</div>
    <div class="field" style="top: 34mm; left: 230mm;">{{ $code_cust ?? '' }}</div>
    <div class="field" style="top: 37mm; left: 230mm;">{{ $tgl_instal }}</div>
    <div class="field" style="top: 46mm; left: 45mm;">{{ $merk_type }}</div>
    <div class="field" style="top: 46mm; left: 150mm;">{{ $no_seri }}</div>
    <div class="field" style="top: 46mm; left: 230mm;">{{ $voltage }} V</div>
</body>
