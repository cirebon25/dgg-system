<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarketingResource\Pages;
use App\Models\Marketing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MarketingResource extends Resource
{
    protected static ?string $model = Marketing::class;
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Data Marketing';
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_marketing')
                ->label('Nama Marketing')
                ->required(),
            Forms\Components\TextInput::make('no_telp')
                ->label('No Telp')
                ->nullable(),
            Forms\Components\Toggle::make('aktif')
                ->label('Aktif')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_marketing')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('no_telp')->label('No Telp'),
                Tables\Columns\IconColumn::make('aktif')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMarketings::route('/'),
            'create' => Pages\CreateMarketing::route('/create'),
            'edit'   => Pages\EditMarketing::route('/{record}/edit'),
        ];
    }
}
