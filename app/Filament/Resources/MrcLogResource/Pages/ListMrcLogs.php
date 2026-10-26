<?php

namespace App\Filament\Resources\MrcLogResource\Pages;

use App\Filament\Resources\MrcLogResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;

class ListMrcLogs extends ListRecords
{
    protected static string $resource = MrcLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cetakMrc')
                ->label('Cetak Tagihan MRC')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->modalHeading('Cetak Tagihan MRC')
                ->modalSubmitActionLabel('Cetak')
                ->form([
                    Forms\Components\Select::make('month')
                        ->label('Bulan')
                        ->options(MrcLogResource::bulanOptions())
                        ->required()
                        ->default(date('m')),

                    Forms\Components\Select::make('year')
                        ->label('Tahun')
                        ->options(MrcLogResource::tahunOptions())
                        ->required()
                        ->default(date('Y')),
                ])
                ->action(function (array $data) {
                    $url = route('mrc.tagihan', [
                        'month' => $data['month'],
                        'year'  => $data['year'],
                    ]);

                    $this->js("window.open('{$url}', '_blank')");
                }),

            Actions\CreateAction::make()
                ->label('Tambah Data MRC'),
        ];
    }
}
