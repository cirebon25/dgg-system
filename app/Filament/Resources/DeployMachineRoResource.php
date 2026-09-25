<?php

namespace App\Filament\Resources;

use App\Models\DeployMachineRo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasRoleAccess;

class DeployMachineRoResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];

    protected static ?string $model = DeployMachineRo::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Deploy Mesin RO';

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

                Forms\Components\Select::make('customer_ro_id')
                    ->label('Customer Tujuan')
                    ->relationship('customerRo', 'nama_customer')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_deploy')
                    ->label('Tanggal Deploy')
                    ->default(now())
                    ->required(),

                Forms\Components\Select::make('status_deploy')
                    ->label('Status Deploy')
                    ->options([
                        'Terpasang' => 'Terpasang',
                        'Ditarik' => 'Ditarik Kembali',
                    ])
                    ->default('Terpasang')
                    ->required(),

                Forms\Components\Textarea::make('catatan')
                    ->label('Catatan Pemasangan')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_deploy')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('machineAirRo.serial_number')
                    ->label('Serial Number Mesin')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('machineAirRo.tipe_mesin')
                    ->label('Tipe Mesin')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customerRo.nama_customer')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_deploy')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Terpasang' => 'success',
                        'Ditarik' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_deploy')
                    ->options([
                        'Terpasang' => 'Terpasang',
                        'Ditarik' => 'Ditarik',
                    ]),
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
            ->defaultSort('tanggal_deploy', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\DeployMachineRoResource\Pages\ListDeployMachineRos::route('/'),
            'create' => \App\Filament\Resources\DeployMachineRoResource\Pages\CreateDeployMachineRo::route('/create'),
            'edit' => \App\Filament\Resources\DeployMachineRoResource\Pages\EditDeployMachineRo::route('/{record}/edit'),
        ];
    }
}
