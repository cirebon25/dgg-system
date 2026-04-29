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
                        ->label('Upload File (Wajib format .csv)')
                        ->disk('local') // Disimpan sementara di server
                        ->directory('import-temp')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    // 1. Cari lokasi file yang diupload
                    $filePath = Storage::disk('local')->path($data['file_csv']);
                    $file = fopen($filePath, "r");
                    
                    // 2. Lewati baris pertama (Judul Kolom / Header)
                    fgetcsv($file); 

                    $lastCustomer = null;
                    $berhasil = 0;

                    // 3. Mulai proses masuk ke database (Pakai Transaction agar aman)
                    DB::beginTransaction();
                    try {
                        while (($row = fgetcsv($file)) !== false) {
                            $kodeCustomer = $row[1] ?? '';
                            $namaCustomer = $row[2] ?? '';

                            // JIKA KODE CUSTOMER ADA, BUAT/CARI CUSTOMER
                            if (!empty(trim($kodeCustomer))) {
                                   $$lastCustomer = Customer::firstOrCreate(
                                    ['nama_customer' => trim($namaCustomer)], // Sekarang dicari berdasarkan Nama
                                    [
                                        'alamat' => $row[3] ?? '-',
                                        'rayon_id' => 1 
                                        // Abaikan kolom kode_customer
                                    ]
                                );
                            }

                            // JIKA NO SERI MESIN ADA, BUAT MESIN & ALOKASIKAN KE CUSTOMER
                            $noSeri = trim($row[6] ?? '');
                            if (!empty($noSeri) && $lastCustomer) {
                                // Masukkan Mesin
                                $machine = Machine::firstOrCreate(
                                    ['serial_number' => $noSeri],
                                    [
                                        'tipe_model' => $row[5] ?? 'Unknown', 
                                        'status' => 'Rented'
                                    ]
                                );

                                // Masukkan ke Deployment (Alokasi)
                                Deployment::firstOrCreate(
                                    [
                                        'machine_id' => $machine->id, 
                                        'customer_id' => $lastCustomer->id
                                    ],
                                    [
                                        'tanggal_pasang' => !empty($row[4]) ? date('Y-m-d', strtotime($row[4])) : now(),
                                        'harga_sewa' => (int) preg_replace('/\D/', '', $row[7] ?? 0),
                                        'free_copy' => (int) preg_replace('/\D/', '', $row[8] ?? 0),
                                        'charge_per_lembar' => (int) preg_replace('/\D/', '', $row[9] ?? 0),
                                        'status' => 'Active'
                                    ]
                                );
                                $berhasil++;
                            }
                        }
                        
                        // Kunci data ke database
                        DB::commit();
                        fclose($file);
                        
                        // Hapus file CSV dari server setelah selesai biar tidak penuh
                        Storage::disk('local')->delete($data['file_csv']); 

                        // Tampilkan Notifikasi Sukses
                        Notification::make()
                            ->title("MANTAP BOSS! Berhasil import $berhasil Mesin.")
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        // Jika ada eror, batalkan semua perubahan database
                        DB::rollBack();
                        if (isset($file)) { fclose($file); }
                        
                        // Tampilkan Notifikasi Eror
                        Notification::make()
                            ->title('Gagal Import Data!')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // TOMBOL BAWAAN "NEW CUSTOMER"
            Actions\CreateAction::make(),
        ];
    }
}