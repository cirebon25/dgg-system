<?php

namespace App\Filament\Resources\MachineWithdrawalResource\Pages;

use App\Filament\Resources\MachineWithdrawalResource;
use App\Models\MachineWithdrawal;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Barryvdh\DomPDF\Facade\Pdf;

class ListMachineWithdrawals extends ListRecords
{
    protected static string $resource = MachineWithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('cetak_bulanan')
                ->label('Cetak Laporan Bulanan')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('month')
                        ->label('Bulan')
                        ->options([
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember'
                        ])
                        ->default(now()->month)
                        ->required(),
                    Forms\Components\TextInput::make('year')
                        ->label('Tahun')
                        ->numeric()
                        ->default(now()->year)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $withdrawals = MachineWithdrawal::with(['machine', 'customer'])
                        ->whereMonth('tanggal_tarik', $data['month'])
                        ->whereYear('tanggal_tarik', $data['year'])
                        ->get();

                    $pdf = Pdf::loadView('print.withdrawal-rekap', [
                        'records' => $withdrawals,
                        'namaBulan' => \Carbon\Carbon::createFromFormat('m', $data['month'])->translatedFormat('F'),
                        'month' => $data['month'],
                        'year' => $data['year'],
                    ]);

                    return response()->streamDownload(
                        fn() => print($pdf->output()),
                        "Laporan-Penarikan-{$data['month']}-{$data['year']}.pdf"
                    );
                }),
        ];
    }
}
