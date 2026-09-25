<?php

namespace App\Filament\Resources;

use App\Models\SparepartRo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Traits\HasRoleAccess;

class SparepartRoResource extends Resource
{
    use HasRoleAccess;

    protected static array $allowedRoles = ['admin'];

    protected static ?string $model = SparepartRo::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    protected static ?string $navigationLabel = 'Sparepart RO';

    protected static ?string $navigationGroup = 'Manajemen Mesin RO';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no_part')
                    ->label('No Part')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('kode_part')
                    ->label('Kode Part')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('nama_sparepart')
                    ->label('Nama Sparepart')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_part')
                    ->label('No Part')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kode_part')
                    ->label('Kode Part')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_sparepart')
                    ->label('Nama Sparepart')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->sortable(),
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\SparepartRoResource\Pages\ListSparepartRos::route('/'),
            'create' => \App\Filament\Resources\SparepartRoResource\Pages\CreateSparepartRo::route('/create'),
            'edit' => \App\Filament\Resources\SparepartRoResource\Pages\EditSparepartRo::route('/{record}/edit'),
        ];
    }
}
