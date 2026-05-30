<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Deployment;
use App\Models\Machine;
use App\Models\Rayon;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\Action::make('importCustom')
            //     ->label('Import Data CSV')
            //     ->color('success')
            //     ->icon('heroicon-o-arrow-down-tray')
            //     ->form([
            //         FileUpload::make('file_csv')
            //             ->label('Upload File (Format .csv)')
            //             ->disk('local')
            //             ->directory('import-temp')
            //             ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
            //             ->required(),
            //     ])
            // ->action(function (array $data) {
            //     $filePath = Storage::disk('local')->path($data['file_csv']);
            //     $file = fopen($filePath, 'r');

            //     // Lewati header
            //     fgetcsv($file);

            //     $berhasil = 0;

            //     DB::beginTransaction();
            //     try {
            //         while (($row = fgetcsv($file, 1000, ',')) !== false) {
            //             // --- MAPPING KOLOM SESUAI URUTAN EXCEL ---
            //             $namaRayon = trim($row[0] ?? '');
            //             $namaCustomer = trim($row[1] ?? '');
            //             $alamat = trim($row[2] ?? '-');
            //             $kota = trim($row[3] ?? '-');

            //             // $tanggalPasang = trim($row[4] ?? '');
            //             // $tipeModel     = trim($row[5] ?? 'Unknown');
            //             // $noSeri        = trim($row[6] ?? ''); // NO SERI di Kolom G
            //             // $hargaSewa     = trim($row[7] ?? '0');
            //             // $freeCopy      = trim($row[8] ?? '0');
            //             // $charge        = trim($row[9] ?? '0');

            //             if (empty($namaCustomer)) {
            //                 continue;
            //             }

            //             // 1. CARI/BUAT CUSTOMER
            //             $rayon = Rayon::where('nama_rayon', 'LIKE', "%$namaRayon%")->first();
            //             $customer = Customer::firstOrCreate(
            //                 ['nama_customer' => $namaCustomer],
            //                 [
            //                     'rayon_id' => $rayon ? $rayon->id : 1,
            //                     'alamat' => $alamat,
            //                     'kota' => $kota,
            //                     'nomor_telp' => '-',
            //                 ]
            //             );

            //             // 2. CARI/BUAT MESIN (Hanya jika No Seri ada)
            //             if (! empty($noSeri)) {
            //                 $machine = Machine::firstOrCreate(
            //                     ['serial_number' => $noSeri],
            //                     [
            //                         'tipe_model' => $tipeModel,
            //                         'status' => 'Rented',
            //                     ]
            //                 );

            //                 // 3. BUAT ALOKASI (DEPLOYMENT)
            //                 Deployment::updateOrCreate(
            //                     [
            //                         'machine_id' => $machine->id,
            //                         'customer_id' => $customer->id,
            //                     ],
            //                     [
            //                         'tanggal_pasang' => ! empty($tanggalPasang) ? date('Y-m-d', strtotime($tanggalPasang)) : now(),
            //                         'harga_sewa' => (int) preg_replace('/\D/', '', $hargaSewa),
            //                         'free_copy' => (int) preg_replace('/\D/', '', $freeCopy),
            //                         'charge_per_lembar' => (int) preg_replace('/\D/', '', $charge),
            //                         'status' => 'Active',
            //                     ]
            //                 );
            //                 $berhasil++;
            //             }
            //         }

            //         DB::commit();
            //         fclose($file);
            //         Storage::disk('local')->delete($data['file_csv']);

            //         Notification::make()
            //             ->title("MANTAP BOSS! Berhasil import $berhasil data.")
            //             ->success()
            //             ->send();

            //     } catch (\Exception $e) {
            //         DB::rollBack();
            //         if (isset($file)) {
            //             fclose($file);
            //         }

            //         Notification::make()
            //             ->title('Gagal Import!')
            //             ->body($e->getMessage())
            //             ->danger()
            //             ->send();
            //     }
            // }),

            Actions\CreateAction::make(),
        ];
    }
}
