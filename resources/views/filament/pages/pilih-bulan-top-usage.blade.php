<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pilih Periode Top Usage</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background: #0f1115;
            color: #e5e7eb;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: #181b22;
            border: 1px solid #2a2e38;
            border-radius: 14px;
            padding: 1.75rem;
        }

        h1 {
            font-size: 1.15rem;
            margin: 0 0 .25rem;
        }

        .sub {
            margin: 0 0 1.5rem;
            color: #9ca3af;
            font-size: .9rem;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem;
        }

        label {
            display: block;
            font-size: .85rem;
            color: #9ca3af;
            margin-bottom: .4rem;
        }

        select {
            width: 100%;
            padding: .6rem .7rem;
            margin-bottom: 1.25rem;
            border-radius: 8px;
            border: 1px solid #2a2e38;
            background: #0f1115;
            color: #e5e7eb;
            font-size: .95rem;
        }

        button {
            width: 100%;
            padding: .7rem;
            border: 0;
            border-radius: 8px;
            background: #16a34a;
            color: #fff;
            font-weight: 600;
            font-size: .95rem;
            cursor: pointer;
        }

        button:hover {
            background: #15803d;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>Top Usage</h1>
        <p class="sub">Pilih periode laporan pemakaian tertinggi.</p>

        <form method="GET" action="{{ route('cetak.top-usage') }}">
            <div class="row">
                <div>
                    <label for="month">Bulan</label>
                    <select id="month" name="month">
                        @foreach ($daftarBulan as $nomor => $namaBulan)
                            <option value="{{ $nomor }}" @selected($nomor === $bulanTerpilih)>
                                {{ $namaBulan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="year">Tahun</label>
                    <select id="year" name="year">
                        @foreach ($daftarTahun as $tahun)
                            <option value="{{ $tahun }}" @selected($tahun === $tahunTerpilih)>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit">Tampilkan Laporan</button>
        </form>
    </div>
</body>

</html>
