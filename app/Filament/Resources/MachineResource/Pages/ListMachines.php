<?php

namespace App\Filament\Resources\MachineResource\Pages;

use App\Filament\Resources\MachineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListMachines extends ListRecords
{
    protected static string $resource = MachineResource::class;

    protected function getHeaderActions(): array
    {
    return [
        Actions\CreateAction::make(),
        
        // TOMBOL CETAK PEMASANGAN BARU
        Actions\Action::make('cetakPemasangan')
            ->label('Cetak Pemasangan Baru')
            ->icon('heroicon-m-sparkles')
            ->color('success')
            ->form([
                \Filament\Forms\Components\Select::make('bulan')
                    ->options([
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                    ])
                    ->default(date('m'))
                    ->required(),
                \Filament\Forms\Components\Select::make('tahun')
                    ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                    ->default(date('Y'))
                    ->required(),
            ])
            ->action(function (array $data) {
                // Redirect ke route cetak dengan parameter bulan & tahun
                return redirect()->route('cetak.pemasangan', [
                    'bulan' => $data['bulan'],
                    'tahun' => $data['tahun']
                ]);
            }),

        // TOMBOL CETAK (VERSI STABIL)
        Actions\Action::make('cetakAlokasi')
            ->label('Cetak Alokasi Mesin')
            ->icon('heroicon-m-printer')
            ->color('info')
            ->url(route('cetak.alokasi')) // <--- Langsung arahkan ke route tadi
            ->openUrlInNewTab(),         // <--- Biar kebuka di tab baru
    ];
    }
    public function printAllocation()
    {
    // 1. Ambil Data Gabungan (Kolom sudah disesuaikan ke 'tipe_model')
    $data = DB::table('machines')
        ->join('deployments', 'machines.id', '=', 'deployments.machine_id')
        ->join('customers', 'deployments.customer_id', '=', 'customers.id')
        ->join('rayons', 'customers.rayon_id', '=', 'rayons.id')
        ->select(
            'rayons.nama_rayon',
            'customers.kota',
            'machines.tipe_model', // <--- SUDAH SAYA GANTI KE 'tipe_model'
            DB::raw('count(*) as qty')
        )
        ->groupBy('rayons.nama_rayon', 'customers.kota', 'machines.tipe_model') // <--- INI JUGA DISESUAIKAN
        ->orderBy('rayons.nama_rayon')
        ->orderBy('customers.kota')
        ->get()
        ->groupBy(['nama_rayon', 'kota']);

        // 2. Kirim ke View (Kita buat HTML langsung di sini agar Boss tidak repot buat file blade)
        $html = "
        <html>
        <head>
            <title>Laporan Alokasi Mesin DGG</title>
            <style>
                body { font-family: sans-serif; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                .bg-rayon { background-color: #1e40af; color: white; font-weight: bold; }
                .bg-kota { background-color: #e5e7eb; font-weight: bold; }
                .bg-total-kota { background-color: #fef9c3; font-weight: bold; }
                .bg-total-rayon { background-color: #dcfce7; font-weight: bold; font-size: 14px; }
                .text-right { text-align: right; }
                header { text-align: center; margin-bottom: 20px; }
            </style>
        </head>
        <body onload='window.print()'>
            <header>
                <h1>LAPORAN ALOKASI UNIT MESIN - DGG SYSTEM</h1>
                <p>Tanggal Cetak: " . date('d-m-Y H:i') . "</p>
            </header>
            <table>
                <thead>
                    <tr>
                        <th>Rayon / Kota / Tipe Mesin</th>
                        <th width='150' class='text-right'>Jumlah Unit</th>
                    </tr>
                </thead>
                <tbody>";

        $grandTotal = 0;

        foreach ($data as $namaRayon => $kotas) {
            // Header Rayon (Warna Biru)
            $html .= "<tr class='bg-rayon'><td colspan='2'>RAYON: $namaRayon</td></tr>";
            $totalRayon = 0;

            foreach ($kotas as $namaKota => $types) {
                // Header Kota (Warna Abu-abu)
                $html .= "<tr class='bg-kota'><td colspan='2'>&nbsp;&nbsp;📍 Kota/Kab: $namaKota</td></tr>";
                $totalKota = 0;

                foreach ($types as $item) {
                    // Baris Tipe Mesin
                    $html .= "
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - {$item->tipe_model}</td>
                        <td class='text-right'>{$item->qty} Unit</td>
                    </tr>";
                    $totalKota += $item->qty;
                }

                // Total Per Kota (Warna Kuning)
                $html .= "
                <tr class='bg-total-kota'>
                    <td class='text-right'>Total Unit di $namaKota:</td>
                    <td class='text-right'>$totalKota Unit</td>
                </tr>";
                $totalRayon += $totalKota;
            }

            // Total Per Rayon (Warna Hijau)
            $html .= "
            <tr class='bg-total-rayon'>
                <td class='text-right'>TOTAL AKUMULASI RAYON $namaRayon:</td>
                <td class='text-right'>$totalRayon Unit</td>
            </tr>";
            $grandTotal += $totalRayon;
        }

        $html .= "
                </tbody>
                <tfoot>
                    <tr style='background: #000; color: #fff; font-size: 16px;'>
                        <td class='text-right'>GRAND TOTAL UNIT TERPASANG:</td>
                        <td class='text-right'>$grandTotal Unit</td>
                    </tr>
                </tfoot>
            </table>
        </body>
        </html>";

        return response($html);
    }
}