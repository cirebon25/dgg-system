<?php

namespace App\Filament\Resources;

use App\Models\MachineAirRo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasRoleAccess; // <-- Pastikan trait ini di-import

class MachineAirRoResource extends Resource
{
    use HasRoleAccess; // <-- Gunakan trait di sini

    protected static array $allowedRoles = ['admin']; // <-- Batasi akses khusus admin

    protected static ?string $model = MachineAirRo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Mesin Air RO';

    protected static ?string $navigationGroup = 'Manajemen Mesin RO';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_ro_id')
                    ->label('Customer (Opsional)')
                    ->relationship('customerRo', 'nama_customer')
                    ->searchable()
                    ->placeholder('Kosongkan jika belum di-deploy'),

                Forms\Components\TextInput::make('serial_number')
                    ->label('Serial Number')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('tipe_mesin')
                    ->label('Tipe Mesin')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'Ready' => 'Ready (Siap Pakai)',
                        'Perbaikan' => 'Perbaikan (Dalam Perbaikan)',
                        'Rusak' => 'Rusak (Tidak Bisa Dipakai)',
                    ])
                    ->default('Ready')
                    ->required(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('No. Seri')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tipe_mesin')
                    ->label('Tipe Mesin')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Ready' => 'success',
                        'Perbaikan' => 'warning',
                        'Rusak' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('customerRo.nama_customer')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Ready' => 'Ready',
                        'Perbaikan' => 'Perbaikan',
                        'Rusak' => 'Rusak',
                    ]),

                Tables\Filters\SelectFilter::make('customer_ro_id')
                    ->label('Customer')
                    ->relationship('customerRo', 'nama_customer'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('serial_number', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\MachineAirRoResource\Pages\ListMachineAirRos::route('/'),
            'create' => \App\Filament\Resources\MachineAirRoResource\Pages\CreateMachineAirRo::route('/create'),
            'edit' => \App\Filament\Resources\MachineAirRoResource\Pages\EditMachineAirRo::route('/{record}/edit'),
        ];
    }
}
