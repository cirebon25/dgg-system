<?php

namespace App\Filament\Resources;

use App\Models\SparepartUsageRo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasRoleAccess;

class SparepartUsageRoResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];

    protected static ?string $model = SparepartUsageRo::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Pemakaian Sparepart RO';

    protected static ?string $navigationGroup = 'Manajemen Mesin RO';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('machine_air_ro_id')
                    ->label('Mesin RO')
                    ->relationship('machineAirRo', 'serial_number')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->serial_number} - {$record->tipe_mesin}")
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('sparepart_ro_id')
                    ->label('Sparepart')
                    ->relationship('sparepartRo', 'nama_sparepart')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('jumlah_pakai')
                    ->label('Jumlah Pakai')
                    ->numeric()
                    ->default(1)
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_pakai')
                    ->label('Tanggal Pakai')
                    ->default(now())
                    ->required(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan / Penggantian')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_pakai')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('machineAirRo.serial_number')
                    ->label('Serial Number Mesin')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sparepartRo.nama_sparepart')
                    ->label('Sparepart')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('jumlah_pakai')
                    ->label('Jumlah')
                    ->sortable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_pakai', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\SparepartUsageRoResource\Pages\ListSparepartUsageRos::route('/'),
            'create' => \App\Filament\Resources\SparepartUsageRoResource\Pages\CreateSparepartUsageRo::route('/create'),
            'edit' => \App\Filament\Resources\SparepartUsageRoResource\Pages\EditSparepartUsageRo::route('/{record}/edit'),
        ];
    }
}
