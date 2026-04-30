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

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'Data Mesin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // SEKSI 1: INFORMASI UMUM
                Forms\Components\Section::make('Informasi Unit Mesin')
                    ->description('Masukkan detail mesin fotokopi sesuai label SN di bodi mesin.')
                    ->schema([
                        Forms\Components\TextInput::make('serial_number')
                            ->label('Serial Number (SN)')
                            ->required()
                            ->unique(ignoreRecord: true)
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
                            ->placeholder('Misal: Kondisi drum 90%')
                            ->columnSpanFull(),
                    ])->columns(2),

                // SEKSI 2: DETAIL TEKNIS (Ini yang tadi terpisah)
                Forms\Components\Section::make('Detail Teknis Mesin')
                    ->description('Informasi tambahan untuk stok gudang')
                    ->schema([
                        Forms\Components\TextInput::make('volt')
                            ->label('Voltase')
                            ->placeholder('Contoh: 110V / 220V'),
                        Forms\Components\TextInput::make('finisher')
                            ->label('Finisher'),
                        Forms\Components\TextInput::make('cover')
                            ->label('Cover'),
                        Forms\Components\TextInput::make('kaset')
                            ->label('Jumlah Kaset'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipe_model')
                    ->label('Tipe Mesin')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ready' => 'success', // Tadi Akang tulis 'Available' makanya gak muncul warnanya
                        'Rented' => 'warning',
                        'Refurbish' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('customer.nama_customer')
                    ->label('Lokasi / Pelanggan')
                    ->placeholder('Gudang DGG'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('monitor')
                    ->label('Monitor Part')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('warning')
                    ->url(fn ($record) => route('sparepart.monitor', $record->id))
                    ->openUrlInNewTab(),

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
            'index' => Pages\ListMachines::route('/'),
            'create' => Pages\CreateMachine::route('/create'),
            'edit' => Pages\EditMachine::route('/{record}/edit'),
        ];
    }
}