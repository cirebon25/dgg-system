<?php

namespace App\Filament\Resources\DeploymentResource\Pages;

use App\Filament\Resources\DeploymentResource;
use App\Models\Customer;
use App\Models\Deployment;
use App\Models\Machine;
use App\Models\Rayon;
use App\Models\Technician;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListDeployments extends ListRecords
{
    protected static string $resource = DeploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Pemasangan'),

            // --- TOMBOL IMPORT CSV SAKTI UNTUK PEMASANGAN MESIN ---
            Action::make('import_csv')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('Pilih File CSV')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                        ->disk('local')
                        ->directory('imports')
                        ->visibility('private')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $filePath = Storage::disk('local')->path($data['file']);

                    // 1. Baca Konten File & Bersihkan BOM UTF-8
                    $fileContent = file_get_contents($filePath);
                    $fileContent = preg_replace('/^\xEF\xBB\xBF/', '', $fileContent);

                    // 2. Deteksi Pemisah Otomatis (Koma ',' atau Titik Koma ';')
                    $lines = explode("\n", $fileContent);
                    $firstLine = trim($lines[0]);
                    $delimiter = ',';
                    if (strpos($firstLine, ';') !== false && strpos($firstLine, ',') === false) {
                        $delimiter = ';';
                    } elseif (strpos($firstLine, ';') !== false && strpos($firstLine, ',') !== false) {
                        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
                    }

                    // Buat file temporary baru yang sudah bersih
                    $tempFile = tempnam(sys_get_temp_dir(), 'csv_clean_d');
                    file_put_contents($tempFile, $fileContent);

                    if (($handle = fopen($tempFile, 'r')) !== false) {
                        $header = fgetcsv($handle, 1000, $delimiter);

                        if (! $header) {
                            Notification::make()
                                ->title('Gagal Impor')
                                ->body('File CSV kosong atau tidak valid.')
                                ->danger()
                                ->send();
                            fclose($handle);
                            unlink($tempFile);

                            return;
                        }

                        // Normalisasi teks header
                        $header = array_map(function ($h) {
                            $h = preg_replace('/[^a-zA-Z0-9_]/', '', $h);

                            return strtolower(trim($h));
                        }, $header);

                        $customerIdx = false;
                        $machineIdx = false;
                        $techIdx = false;
                        $dateIdx = false;
                        $ketIdx = false;
                        $kontrakIdx = false; // Penanda kolom no kontrak

                        foreach ($header as $idx => $col) {
                            if (str_contains($col, 'customer') || str_contains($col, 'pelanggan')) {
                                $customerIdx = $idx;
                            }
                            if (str_contains($col, 'serial') || str_contains($col, 'sn') || str_contains($col, 'mesin')) {
                                $machineIdx = $idx;
                            }
                            if (str_contains($col, 'tech') || str_contains($col, 'teknisi')) {
                                $techIdx = $idx;
                            }
                            if (str_contains($col, 'tanggal') || str_contains($col, 'tgl') || str_contains($col, 'date')) {
                                $dateIdx = $idx;
                            }
                            if (str_contains($col, 'ket') || str_contains($col, 'catatan')) {
                                $ketIdx = $idx;
                            }
                            if (str_contains($col, 'kontrak') || str_contains($col, 'no_kontrak')) {
                                $kontrakIdx = $idx;
                            }
                        }

                        if ($customerIdx === false || $machineIdx === false) {
                            $detectedHeaders = implode(', ', $header);
                            Notification::make()
                                ->title('Gagal Impor')
                                ->body("Kolom Customer atau SN Mesin tidak ditemukan. Kolom terdeteksi: [$detectedHeaders]")
                                ->danger()
                                ->persistent()
                                ->send();
                            fclose($handle);
                            unlink($tempFile);

                            return;
                        }

                        // Menyiapkan Rayon Default jika database kosong
                        $defaultRayon = Rayon::first();
                        if (! $defaultRayon) {
                            $defaultRayon = new Rayon;
                            $defaultRayon->nama_rayon = 'Rayon Pusat';
                            $defaultRayon->save();
                        }

                        $successCount = 0;
                        $skippedCount = 0;
                        $errorDetails = [];
                        $rowCount = 1;

                        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                            $rowCount++;
                            if (empty($row) || ! isset($row[$customerIdx]) || ! isset($row[$machineIdx])) {
                                $skippedCount++;

                                continue;
                            }

                            $custVal = trim($row[$customerIdx]);
                            $machineVal = trim($row[$machineIdx]);

                            if ($custVal === '' || $machineVal === '') {
                                $skippedCount++;

                                continue;
                            }

                            try {
                                // --- PROSES CUSTOMER ---
                                $customerId = null;
                                if (is_numeric($custVal)) {
                                    $customerId = $custVal;
                                } else {
                                    $customer = Customer::where('nama_customer', 'like', '%'.$custVal.'%')->first();
                                    if (! $customer) {
                                        $customer = new Customer;
                                        $customer->nama_customer = $custVal;
                                        $customer->alamat = '-';
                                        $customer->kota = 'Cirebon';
                                        $customer->rayon_id = $defaultRayon->id;
                                        $customer->save();
                                    }
                                    $customerId = $customer->id;
                                }

                                // --- PROSES MESIN ---
                                $machineId = null;
                                if (is_numeric($machineVal)) {
                                    $machineId = $machineVal;
                                } else {
                                    $machine = Machine::where('serial_number', $machineVal)->first();
                                    if (! $machine) {
                                        $machine = new Machine;
                                        $machine->serial_number = $machineVal;
                                        $machine->tipe_model = 'Imported';
                                        $machine->status = 'Rented';
                                        $machine->save();
                                    } else {
                                        $machine->status = 'Rented';
                                        $machine->save();
                                    }
                                    $machineId = $machine->id;
                                }

                                // --- PROSES TEKNISI ---
                                $technicianId = null;
                                if ($techIdx !== false && isset($row[$techIdx]) && trim($row[$techIdx]) !== '') {
                                    $techVal = trim($row[$techIdx]);
                                    if (is_numeric($techVal)) {
                                        $technicianId = $techVal;
                                    } else {
                                        $tech = Technician::where('nama_technician', 'like', '%'.$techVal.'%')->first();
                                        if (! $tech) {
                                            $tech = new Technician;
                                            $tech->nama_technician = $techVal;
                                            $tech->save();
                                        }
                                        $technicianId = $tech->id;
                                    }
                                }

                                // --- GARANSI VALIDASI TECHNICIAN_ID ---
                                if ($technicianId === null) {
                                    $fallbackTech = Technician::first();
                                    if (! $fallbackTech) {
                                        $fallbackTech = new Technician;
                                        $fallbackTech->nama_technician = 'Teknisi Umum';
                                        $fallbackTech->save();
                                    }
                                    $technicianId = $fallbackTech->id;
                                }

                                // --- PROSES TANGGAL ---
                                $tanggalInstal = now()->format('Y-m-d');
                                if ($dateIdx !== false && isset($row[$dateIdx]) && trim($row[$dateIdx]) !== '') {
                                    $valDate = trim($row[$dateIdx]);
                                    $parsed = \DateTime::createFromFormat('d/m/Y', $valDate);
                                    if (! $parsed) {
                                        $parsed = \DateTime::createFromFormat('d-m-Y', $valDate);
                                    }
                                    if (! $parsed) {
                                        $parsed = \DateTime::createFromFormat('Y-m-d', $valDate);
                                    }

                                    if ($parsed) {
                                        $tanggalInstal = $parsed->format('Y-m-d');
                                    }
                                }

                                $keterangan = $ketIdx !== false && isset($row[$ketIdx]) ? trim($row[$ketIdx]) : null;
                                $noKontrak = $kontrakIdx !== false && isset($row[$kontrakIdx]) ? trim($row[$kontrakIdx]) : null;

                                // Simpan ke Tabel Deployments
                                $deployment = Deployment::where('customer_id', $customerId)
                                    ->where('machine_id', $machineId)
                                    ->first();

                                if (! $deployment) {
                                    $deployment = new Deployment;
                                    $deployment->customer_id = $customerId;
                                    $deployment->machine_id = $machineId;
                                }
                                $deployment->technician_id = $technicianId;
                                $deployment->no_kontrak = $noKontrak; // <-- Menyimpan no kontrak
                                $deployment->tanggal_instal = $tanggalInstal;
                                $deployment->keterangan = $keterangan;
                                $deployment->save();

                                $successCount++;
                            } catch (\Exception $e) {
                                if (count($errorDetails) < 3) {
                                    $errorDetails[] = "Baris $rowCount: ".$e->getMessage();
                                }

                                continue;
                            }
                        }

                        fclose($handle);
                        unlink($tempFile);
                        Storage::disk('local')->delete($data['file']);

                        $msg = "$successCount data pemasangan mesin berhasil diimpor.";
                        if ($skippedCount > 0) {
                            $msg .= " ($skippedCount baris dilewati).";
                        }

                        if ($successCount === 0 && ! empty($errorDetails)) {
                            $errBody = implode("\n", $errorDetails);
                            Notification::make()
                                ->title('Impor Gagal (0 Data)')
                                ->body($msg."\nDetail Error:\n".$errBody)
                                ->danger()
                                ->persistent()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Impor Selesai')
                                ->body($msg)
                                ->success()
                                ->send();
                        }
                    }
                }),
        ];
    }
}
