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
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('SN Mesin')
                    ->searchable() // Bisa dicari lewat kolom search
                    ->sortable()
                    ->copyable() // Klik untuk copy SN
                    ->description(fn (Machine $record): string => $record->tipe_model),

                Tables\Columns\TextColumn::make('status')
                    ->badge() // Membuat tampilan seperti label berwarna
                    ->color(fn (string $state): string => match ($state) {
                        'Ready' => 'success',    // Hijau
                        'Rented' => 'warning',   // Kuning
                        'Refurbish' => 'danger', // Merah
                    }),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Update Terakhir')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter cepat berdasarkan status di atas tabel
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Ready' => 'Ready',
                        'Rented' => 'Rented',
                        'Refurbish' => 'Refurbish',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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