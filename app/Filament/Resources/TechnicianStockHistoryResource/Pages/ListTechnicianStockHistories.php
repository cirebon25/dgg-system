<?php

namespace App\Filament\Resources\TechnicianStockHistoryResource\Pages;

use App\Filament\Resources\TechnicianStockHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;

class ListTechnicianStockHistories extends ListRecords
{
    protected static string $resource = TechnicianStockHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print_monthly')
                ->label('Cetak Laporan Bulanan')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('month')
                        ->label('Bulan')
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

                    Forms\Components\Select::make('year')
                        ->label('Tahun')
                        ->options(function () {
                            $years = range(date('Y'), 2023);
                            return array_combine($years, $years);
                        })
                        ->default(date('Y'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    $month = $data['month'];
                    $year = $data['year'];

                    // Redirect ke route khusus untuk generate PDF / Print view
                    return redirect()->route('technician-stock.print', [
                        'month' => $month,
                        'year' => $year,
                    ]);
                }),
        ];
    }
}
