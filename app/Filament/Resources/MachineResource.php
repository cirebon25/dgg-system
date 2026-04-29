<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MachineResource\Pages;
use App\Models\Machine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MachineResource extends Resource
{
    protected static ?string $model = Machine::class;

    // Ikon menu di navigasi samping
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    // Label menu di navigasi
    protected static ?string $navigationLabel = 'Data Mesin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Unit Mesin')
                    ->description('Masukkan detail mesin fotokopi sesuai label SN di bodi mesin.')
                    ->schema([
                        Forms\Components\TextInput::make('serial_number')
                            ->label('Serial Number (SN)')
                            ->required()
                            ->unique(ignoreRecord: true) // Mencegah SN ganda
                            ->placeholder('Contoh: WEP12345'),

                        Forms\Components\TextInput::make('tipe_model')
                            ->label('Tipe Model')
                            ->required()
                            ->placeholder('Contoh: Canon iRA 4545'),

                        Forms\Components\Select::make('status')
                            ->label('Status Mesin')
                            ->options([
                                'Ready' => 'Ready (Siap Pakai)',
                                'Rented' => 'Rented (Sedang Disewa)',
                                'Refurbish' => 'Refurbish (Dalam Perbaikan)',
                            ])
                            ->default('Ready')
                            ->required(),

                        Forms\Components\Textarea::make('keterangan_awal')
                            ->label('Catatan Kondisi')
                            ->placeholder('Misal: Kondisi drum 90%, include toner baru.')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
    return $table
        ->columns([
            // 1. Tampilkan Serial Number
            Tables\Columns\TextColumn::make('serial_number')
                ->label('Serial Number')
                ->searchable()
                ->sortable(),

            // 2. Tampilkan Tipe Model
            Tables\Columns\TextColumn::make('tipe_model')
                ->label('Tipe Mesin')
                ->searchable(),

            // 3. Tampilkan Status (Biar kelihatan unitnya ready atau tidak)
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Available' => 'success',
                    'Rented' => 'warning',
                    'Broken' => 'danger',
                    default => 'gray',
                }),

            // 4. Tampilkan Nama Customer (Opsional)
            Tables\Columns\TextColumn::make('customer.nama_customer')
                ->label('Lokasi / Pelanggan')
                ->placeholder('Gudang DGG'),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\EditAction::make(),

            // TAMBAHKAN TOMBOL MONITOR DI SINI
            Tables\Actions\Action::make('monitor')
                ->label('Monitor Part')
                ->icon('heroicon-o-cpu-chip') // Ikon chip/mesin
                ->color('warning') // Warna oranye/kuning biar beda
                ->url(fn ($record) => route('sparepart.monitor', $record->id))
                ->openUrlInNewTab(), // Biar kebuka di tab baru (laporan cetak)

            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            // ...
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMachines::route('/'),
            'create' => Pages\CreateMachine::route('/create'),
            'edit' => Pages\EditMachine::route('/{record}/edit'),
        ];
    }
}