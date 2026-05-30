<?php

namespace App\Filament\Resources\MachineResource\Pages;

use App\Filament\Resources\MachineResource;
use App\Models\Machine;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ListMachines extends ListRecords
{
    protected static string $resource = MachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. TOMBOL CREATE (NEW MACHINE)
            // Actions\CreateAction::make()
            //     ->label('New Machine'),

            // 2. TOMBOL IMPORT CSV ANTI-GAGAL UNTUK DATA MESIN
            // Action::make('import_csv')
            //     ->label('Import CSV')
            //     ->icon('heroicon-o-arrow-up-tray')
            //     ->color('danger')
            //     ->form([
            //         FileUpload::make('file')
            //             ->label('Pilih File CSV')
            //             ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
            //             ->disk('local')
            //             ->directory('imports')
            //             ->visibility('private')
            //             ->required(),
            //     ])
            //     ->action(function (array $data) {
            //         $filePath = Storage::disk('local')->path($data['file']);

            //         // 1. Baca Konten File & Bersihkan BOM UTF-8
            //         $fileContent = file_get_contents($filePath);
            //         $fileContent = preg_replace('/^\xEF\xBB\xBF/', '', $fileContent);

            //         // 2. Deteksi Pemisah (Delimiter) Otomatis
            //         $lines = explode("\n", $fileContent);
            //         $firstLine = trim($lines[0]);
            //         $delimiter = ',';
            //         if (strpos($firstLine, ';') !== false && strpos($firstLine, ',') === false) {
            //             $delimiter = ';';
            //         } elseif (strpos($firstLine, ';') !== false && strpos($firstLine, ',') !== false) {
            //             $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
            //         }

            //         // Buat file temporary baru yang sudah bersih
            //         $tempFile = tempnam(sys_get_temp_dir(), 'csv_clean_m');
            //         file_put_contents($tempFile, $fileContent);

            //         if (($handle = fopen($tempFile, 'r')) !== false) {
            //             $header = fgetcsv($handle, 1000, $delimiter);

            //             if (! $header) {
            //                 Notification::make()
            //                     ->title('Gagal Impor')
            //                     ->body('File CSV kosong.')
            //                     ->danger()
            //                     ->send();
            //                 fclose($handle);
            //                 unlink($tempFile);

            //                 return;
            //             }

            //             // Normalisasi teks header (huruf kecil & hanya ambil karakter a-z, 0-9, underscore)
            //             $header = array_map(function ($h) {
            //                 $h = preg_replace('/[^a-zA-Z0-9_]/', '', $h);

            //                 return strtolower(trim($h));
            //             }, $header);

            //             $snIdx = array_search('serial_number', $header);
            //             $tipeIdx = array_search('tipe_model', $header);
            //             $statusIdx = array_search('status', $header);

            //             if ($snIdx === false || $tipeIdx === false) {
            //                 $detectedHeaders = implode(', ', $header);
            //                 Notification::make()
            //                     ->title('Gagal Impor')
            //                     ->body("Kolom 'serial_number' atau 'tipe_model' tidak ditemukan. Kolom yang terdeteksi: [$detectedHeaders]")
            //                     ->danger()
            //                     ->persistent()
            //                     ->send();
            //                 fclose($handle);
            //                 unlink($tempFile);

            //                 return;
            //             }

            //             $successCount = 0;
            //             $skippedCount = 0;
            //             $errorDetails = [];
            //             $rowCount = 1;

            //             while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            //                 $rowCount++;
            //                 if (empty($row) || ! isset($row[$snIdx]) || trim($row[$snIdx]) === '') {
            //                     $skippedCount++;

            //                     continue;
            //                 }

            //                 try {
            //                     $snVal = trim($row[$snIdx]);

            //                     $machine = Machine::where('serial_number', $snVal)->first();
            //                     if (! $machine) {
            //                         $machine = new Machine;
            //                         $machine->serial_number = $snVal;
            //                     }

            //                     $machine->tipe_model = ! empty($row[$tipeIdx]) ? trim($row[$tipeIdx]) : '-';
            //                     $machine->status = ! empty($row[$statusIdx]) ? trim($row[$statusIdx]) : 'Ready';
            //                     $machine->save();

            //                     $successCount++;
            //                 } catch (\Exception $e) {
            //                     if (count($errorDetails) < 3) {
            //                         $errorDetails[] = "Baris $rowCount: " . $e->getMessage();
            //                     }
            //                 }
            //             }

            //             fclose($handle);
            //             unlink($tempFile);
            //             Storage::disk('local')->delete($data['file']);

            //             $msg = "$successCount data mesin berhasil diimpor.";
            //             if ($skippedCount > 0) {
            //                 $msg .= " ($skippedCount baris dilewati).";
            //             }

            //             if ($successCount === 0 && ! empty($errorDetails)) {
            //                 $errBody = implode("\n", $errorDetails);
            //                 Notification::make()
            //                     ->title('Impor Gagal (0 Data)')
            //                     ->body($msg . "\nDetail Error:\n" . $errBody)
            //                     ->danger()
            //                     ->persistent()
            //                     ->send();
            //             } else {
            //                 Notification::make()
            //                     ->title('Impor Selesai')
            //                     ->body($msg)
            //                     ->success()
            //                     ->send();
            //             }
            //         }
            //     }),

            // 3. TOMBOL CETAK STOK GUDANG
            Action::make('cetak_stok_gudang')
                ->label('Cetak Stok Gudang')
                ->color('violet')
                ->icon('heroicon-o-printer')
                ->url(route('cetak.stok-gudang'))
                ->openUrlInNewTab(),

            // 4. TOMBOL CETAK PEMASANGAN BARU
            Actions\Action::make('cetakPemasangan')
                ->label('Cetak Pemasangan Baru')
                ->icon('heroicon-m-sparkles')
                ->color('lime')
                ->form([
                    \Filament\Forms\Components\Select::make('bulan')
                        ->options([
                            '01' => 'Januari',
                            '02' => 'Februari',
                            '03' => 'Maret',
                            '04' => 'April',
                            '05' => 'Mei',
                            '06' => 'Juni',
                            '07' => 'Juli',
                            '08' => 'Agustus',
                            '09' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember',
                        ])
                        ->default(date('m'))
                        ->required(),
                    \Filament\Forms\Components\Select::make('tahun')
                        ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                        ->default(date('Y'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    return redirect()->route('cetak.pemasangan', [
                        'bulan' => $data['bulan'],
                        'tahun' => $data['tahun'],
                    ]);
                }),

            // 5. TOMBOL CETAK ALOKASI MESIN
            Actions\Action::make('cetakAlokasi')
                ->label('Cetak Alokasi Mesin')
                ->icon('heroicon-m-printer')
                ->color('info')
                ->url(route('cetak.alokasi'))
                ->openUrlInNewTab(),
        ];
    }

    public function printAllocation()
    {
        $data = DB::table('machines')
            ->join('deployments', 'machines.id', '=', 'deployments.machine_id')
            ->join('customers', 'deployments.customer_id', '=', 'customers.id')
            ->join('rayons', 'customers.rayon_id', '=', 'rayons.id')
            ->select(
                'rayons.nama_rayon',
                'customers.kota',
                'machines.tipe_model',
                DB::raw('count(*) as qty')
            )
            ->groupBy('rayons.nama_rayon', 'customers.kota', 'machines.tipe_model')
            ->orderBy('rayons.nama_rayon')
            ->orderBy('customers.kota')
            ->get()
            ->groupBy(['nama_rayon', 'kota']);

        $html = '<html><head><title>Laporan Alokasi Mesin</title></head><body>...</body></html>';

        return response($html);
    }
}
