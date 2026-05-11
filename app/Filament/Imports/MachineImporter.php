<?php

namespace App\Filament\Imports;

use App\Models\Machine;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class MachineImporter extends Importer
{
    protected static ?string $model = Machine::class;

    public static function getColumns(): array
    {
        return [
            // Membaca kolom 'serial_number' dari file CSV Akang
            ImportColumn::make('serial_number')
                ->label('Serial Number')
                ->requiredMapping()
                ->rules(['required', 'string']),

            // Membaca kolom 'tipe_model' dari file CSV Akang
            ImportColumn::make('tipe_model')
                ->label('Tipe Model')
                ->rules(['nullable', 'string']),

            // Membaca kolom 'status' dari file CSV Akang
            ImportColumn::make('status')
                ->label('Status')
                ->rules(['nullable', 'string']),
        ];
    }

    public static function resolveRecord(): ?Machine
    {
        // LOGIKA BIAR TIDAK DUPLIKAT:
        // Jika SN sudah ada di database, sistem akan mengupdate datanya.
        // Jika belum ada, sistem akan memasukkan data baru.
        return Machine::firstOrNew([
            'serial_number' => $this->data['serial_number'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor data mesin selesai. '.number_format($import->successful_rows).' baris berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' baris gagal dimasukkan.';
        }

        return $body;
    }
}
