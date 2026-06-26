<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SparepartStockSnapshotResource\Pages;
use App\Models\SparepartStockSnapshot;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;

class SparepartStockSnapshotResource extends Resource
{
    protected static ?string $model          = SparepartStockSnapshot::class;
    protected static ?string $navigationIcon  = 'heroicon-o-camera';
    protected static ?string $navigationLabel = 'Snapshot Stok Bulanan';
    protected static ?string $navigationGroup = 'Gudang & Stok';
    protected static ?bool   $canCreate       = false; // hanya via command/scheduler

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sparepart.nama_sparepart')
                    ->label('Sparepart')
                    ->searchable(),

                Tables\Columns\TextColumn::make('bulan')
                    ->label('Bulan')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::create()->month($state)->translatedFormat('F')),

                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun'),

                Tables\Columns\TextColumn::make('stok_akhir')
                    ->label('Stok Akhir')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('snapshot_at')
                    ->label('Diambil Pada')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bulan')
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
                        12 => 'Desember',
                    ]),
                Tables\Filters\SelectFilter::make('tahun')
                    ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024))),
            ])
            ->headerActions([
                Tables\Actions\Action::make('cetakPemakaian')
                    ->label('Cetak Laporan Pemakaian')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('bulan')
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
                                12 => 'Desember',
                            ])
                            ->required()
                            ->default(date('n')),
                        Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()
                            ->default(date('Y')),
                    ])
                    ->action(fn(array $data) => redirect()->route('sparepart.pemakaian-bulanan', $data)),

                Tables\Actions\Action::make('ambilSnapshotManual')
                    ->label('Ambil Snapshot Sekarang')
                    ->icon('heroicon-o-camera')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Ini akan menyimpan stok SAAT INI sebagai snapshot bulan & tahun berjalan. Gunakan untuk mengambil snapshot manual jika scheduler belum jalan.')
                    ->action(function () {
                        Artisan::call('sparepart:snapshot', [
                            '--bulan' => date('n'),
                            '--tahun' => date('Y'),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Snapshot berhasil diambil!')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('snapshot_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSparepartStockSnapshots::route('/'),
        ];
    }
}