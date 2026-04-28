<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SparepartResource\Pages;
use App\Models\Sparepart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SparepartResource extends Resource
{
    protected static ?string $model = Sparepart::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Data'; // Saya masukkan ke grup Master Data

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
                            ->label('No Part'),
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
                            ->label('Sisa Saldo')
                            ->numeric()
                            ->readOnly()
                            ->placeholder('Otomatis'),
                    ])->columns(3),
            ]);
    }

    // Cari bagian public static function table
    public static function table(Table $table): Table
    {
    return $table
        ->columns([
            // ... kolom-kolom yang sudah ada ...
        ])
        ->headerActions([
            // TOMBOL REKAP BULANAN
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
                    // Ini mengarahkan ke route cetak yang kita buat di web.php
                    return redirect()->route('sparepart.report.outflow', [
                        'month' => $data['month'],
                        'year' => $data['year'],
                    ]);
                }),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
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