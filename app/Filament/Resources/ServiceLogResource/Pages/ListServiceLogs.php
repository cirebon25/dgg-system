<?php

namespace App\Filament\Resources\ServiceLogResource\Pages;

use App\Filament\Resources\ServiceLogResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListServiceLogs extends ListRecords
{
    protected static string $resource = ServiceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('cetak_rekap_service')
                ->label('Cetak Rekap Service')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->form([
                    Select::make('bulan')
                        ->label('Pilih Bulan')
                        ->options([
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                        ])
                        ->default(date('m'))
                        ->required(),
                    Select::make('tahun')
                        ->label('Pilih Tahun')
                        ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                        ->default(date('Y'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    // DIALIKAN KEDALAM JALUR ROUTE V2 BARU
                    return redirect()->route('cetak.service-log-v2', [
                        'bulan' => $data['bulan'],
                        'tahun' => $data['tahun']
                    ]);
                }),
        ];
    }
}