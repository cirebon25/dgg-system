<?php

namespace App\Filament\Resources\AttendanceLogResource\Pages;

use App\Filament\Resources\AttendanceLogResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceLogs extends ListRecords
{
    protected static string $resource = AttendanceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak')
                ->label('Cetak Rincian Harian')
                ->icon('heroicon-o-printer')
                ->url(fn() => route('cetak.rincian-harian', [
                    'dari'   => data_get($this->tableFilters, 'tanggal.dari'),
                    'sampai' => data_get($this->tableFilters, 'tanggal.sampai'),
                ]))
                ->openUrlInNewTab(),
        ];
    }
}