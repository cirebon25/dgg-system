<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Machine;
use App\Models\Deployment;
use Filament\Notifications\Notification;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // TOMBOL IMPORT CUSTOM DGG
            Actions\Action::make('importCustom')
                ->label('Import Data CSV')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    FileUpload::make('file_csv')
                        ->label('Upload File (Format .csv)')
                        ->disk('local')
                        ->directory('import-temp')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $filePath = Storage::disk('local')->path($data['file_csv']);
                    $file = fopen($filePath, "r");
                    
                    // Lewati header (baris pertama)
                    fgetcsv($file); 

                    $lastCustomer = null;
                    $berhasil = 0;

                    DB::beginTransaction();
                    try {
                        while (($row = fgetcsv($file)) !== false) {
                            // Sesuai file Excel Boss: index 2 adalah NAMA CUSTOMER
                            $namaCustomer = trim($row[2] ?? '');

                            // 1. CARI ATAU BUAT CUSTOMER (Pakai nama_customer sesuai HeidiSQL)
                            if (!empty($namaCustomer)) {
                                $lastCustomer = Customer::firstOrCreate(
                                    ['nama_customer' => $namaCustomer],
                                    [
                                        'rayon_id'   => 1, // Sesuaikan ID Rayon Boss
                                        'kota'       => $row[3] ?? '-', // Sesuaikan urutan kolom di CSV
                                        'alamat'     => $row[3] ?? '-', 
                                        'nomor_telp' => null,
                                    ]
                                );
                            }

                            // 2. CARI ATAU BUAT MESIN (Index 6 adalah NO SERI MESIN)
                            $noSeri = trim($row[6] ?? '');
                            if (!empty($noSeri) && $lastCustomer) {
                                $machine = Machine::firstOrCreate(
                                    ['serial_number' => $noSeri],
                                    [
                                        'tipe_model' => $row[5] ?? 'Unknown', 
                                        'status'     => 'Rented'
                                    ]
                                );

                                // 3. BUAT ALOKASI (DEPLOYMENT)
                                Deployment::firstOrCreate(
                                    [
                                        'machine_id'  => $machine->id, 
                                        'customer_id' => $lastCustomer->id
                                    ],
                                    [
                                        'tanggal_pasang'    => !empty($row[4]) ? date('Y-m-d', strtotime($row[4])) : now(),
                                        'harga_sewa'        => (int) preg_replace('/\D/', '', $row[7] ?? 0),
                                        'free_copy'         => (int) preg_replace('/\D/', '', $row[8] ?? 0),
                                        'charge_per_lembar' => (int) preg_replace('/\D/', '', $row[9] ?? 0),
                                        'status'            => 'Active'
                                    ]
                                );
                                $berhasil++;
                            }
                        }
                        
                        DB::commit();
                        fclose($file);
                        Storage::disk('local')->delete($data['file_csv']); 

                        Notification::make()
                            ->title("MANTAP BOSS! Berhasil import $berhasil Mesin.")
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        DB::rollBack();
                        if (isset($file)) { fclose($file); }
                        
                        Notification::make()
                            ->title('Gagal Import!')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }
}