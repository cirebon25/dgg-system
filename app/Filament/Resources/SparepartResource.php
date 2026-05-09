<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SparepartResource\Pages;
use App\Models\Sparepart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel; // Pastikan library excel sudah terinstall

class SparepartResource extends Resource
{
    protected static ?string $model = Sparepart::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Barang')
                    ->schema([
                        Forms\Components\TextInput::make('nama_sparepart')
                            ->label('Nama Sparepart')
                            ->required(),
                        Forms\Components\TextInput::make('code_part')
                            ->label('Code Part'),
                        Forms\Components\TextInput::make('no_part')
                            ->label('No Part')
                            ->required(), // Diwajibkan karena jadi kunci saat import
                    ])->columns(3),

                Forms\Components\Section::make('Manajemen Stok')
                    ->schema([
                        Forms\Components\TextInput::make('saldo_masuk')
                            ->label('Saldo Masuk (Stok Baru)')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('saldo_keluar')
                            ->label('Saldo Keluar (Terpakai)')
                            ->numeric()
                            ->default(0)
                            ->readOnly(),
                        Forms\Components\TextInput::make('stok')
                            ->label('Sisa Saldo / Stok Akhir')
                            ->numeric()
                            ->readOnly()
                            ->placeholder('Otomatis'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_sparepart')
                    ->label('Nama Sparepart')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code_part')
                    ->label('Kode Part')
                    ->searchable(),

                TextColumn::make('no_part')
                    ->label('No Part')
                    ->searchable(),

                TextColumn::make('stok')
                    ->label('Stok')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state <= 2 => 'danger',  // Merah kalau kritis
                        $state <= 5 => 'warning', // Kuning kalau menipis
                        default => 'success',     // Hijau kalau aman
                    })
                    ->sortable(),
            ])
            ->headerActions([
                // 🟢 1. TOMBOL IMPORT CSV (Ditambahkan sesuai permintaan sebelumnya)
                Tables\Actions\Action::make('import_sparepart')
                    ->label('Import CSV')
                    ->icon('heroicon-m-arrow-up-tray')
                    ->color('info')
                    ->form([
                        Forms\Components\FileUpload::make('file_csv')
                            ->label('Pilih File CSV/Excel')
                            ->disk('public')
                            ->directory('imports')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $filePath = storage_path('app/public/'.$data['file_csv']);
                        $rows = Excel::toArray([], $filePath)[0];
                        array_shift($rows); // Buang header

                        foreach ($rows as $row) {
                            Sparepart::updateOrCreate(
                                ['no_part' => $row[1]], // Kunci: No Part
                                [
                                    'nama_sparepart' => $row[0],
                                    'stok' => $row[2],
                                    'code_part' => $row[3],
                                ]
                            );
                        }
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Import Berhasil!')
                            ->success()
                            ->send();
                    }),

                // 🔵 2. TOMBOL REKAP KELUAR BULANAN
                Tables\Actions\Action::make('rekapKeluar')
                    ->label('Cetak Rekap Keluar')
                    ->color('success')
                    ->icon('heroicon-o-printer')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->label('Pilih Bulan')
                            ->options([
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                            ])->required()->default(date('m')),
                        Forms\Components\Select::make('year')
                            ->label('Pilih Tahun')
                            ->options(array_combine(range(date('Y'), 2024), range(date('Y'), 2024)))
                            ->required()->default(date('Y')),
                    ])
                    ->action(function (array $data) {
                        return redirect()->route('sparepart.report.outflow', [
                            'month' => $data['month'],
                            'year' => $data['year'],
                        ]);
                    }),

                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpareparts::route('/'),
            'create' => Pages\CreateSparepart::route('/create'),
            'edit' => Pages\EditSparepart::route('/{record}/edit'),
        ];
    }
}
